<?php

class HappyForms_Part_Rating extends HappyForms_Form_Part {

	public $type = 'rating';
	public $template_id = 'happyforms-rating-template';

	public function __construct() {
		$this->label = __( 'Rating', 'happyforms' );
		$this->description = __( 'For collecting opinions using stars and emoji scales.', 'happyforms' );

		add_filter( 'happyforms_part_class', array( $this, 'html_part_class' ), 10, 3 );
		add_filter( 'happyforms_frontend_dependencies', array( $this, 'script_dependencies' ), 10, 2 );
		add_filter( 'happyforms_stringify_part_value', array( $this, 'stringify_value' ), 10, 3 );
	}

	/**
	 * Get all part meta fields defaults.
	 *
	 * @since 1.0.0.
	 *
	 * @return array
	 */
	public function get_customize_fields() {
		return array(
			'type' => array(
				'default' => $this->type,
				'sanitize' => 'sanitize_text_field',
			),
			'label' => array(
				'default' => __( 'Rating', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'label_placement' => array(
				'default' => 'above',
				'sanitize' => 'sanitize_text_field'
			),
			'description' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field'
			),
			'description_mode' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field'
			),
			'rating_type' => array(
				'default' => 'scale',
				'sanitize' => 'sanitize_text_field'
			),
			'rating_visuals' => array(
				'default' => 'stars',
				'sanitize' => 'sanitize_text_field'
			),
			'rating_labels_yesno' => array(
				'default' => array(
					__( 'Disappointed', 'happyforms' ),
					__( 'Happy', 'happyforms' )
				),
				'sanitize' => 'happyforms_sanitize_array'
			),
			'rating_labels_scale' => array(
				'default' => array(
					__( 'Disliked', 'happyforms' ),
					__( 'Disappointed', 'happyforms' ),
					__( 'Okay', 'happyforms' ),
					__( 'Happy', 'happyforms' ),
					__( 'Loved it', 'happyforms' )
				),
				'sanitize' => 'happyforms_sanitize_array'
			),
			'stars_num' => array(
				'default' => 5,
				'sanitize' => 'intval'
			),
			'width' => array(
				'default' => 'full',
				'sanitize' => 'sanitize_key'
			),
			'css_class' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field'
            ),
			'required' => array(
				'default' => 1,
				'sanitize' => 'happyforms_sanitize_checkbox',
			),
		);
	}

	/**
	 * Get template for part item in customize pane.
	 *
	 * @since 1.0.0.
	 *
	 * @return string
	 */
	public function customize_templates() {
		require_once( happyforms_get_include_folder() . '/core/templates/parts/customize-rating.php' );
	}

	/**
	 * Get front end part template with parsed data.
	 *
	 * @since 1.0.0.
	 *
	 * @param array	$part_data 	Form part data.
	 * @param array	$form_data	Form (post) data.
	 *
	 * @return string	Markup for the form part.
	 */
	public function frontend_template( $part_data = array(), $form_data = array() ) {
		$part = wp_parse_args( $part_data, $this->get_customize_defaults() );
		$form = $form_data;

		include( happyforms_get_include_folder() . '/core/templates/parts/frontend-rating.php' );
	}

	/**
	 * Sanitize submitted value before storing it.
	 *
	 * @since 1.0.0.
	 *
	 * @param array $part_data Form part data.
	 *
	 * @return string
	 */
	public function sanitize_value( $part_data = array(), $form_data = array(), $request = array() ) {
		$sanitized_value = $this->get_default_value( $part_data );
		$part_name = happyforms_get_part_name( $part_data, $form_data );

		if ( isset( $request[$part_name] ) ) {
			$sanitized_value = intval( $request[$part_name] );
		}

		return $sanitized_value;
	}

	/**
	 * Validate value before submitting it. If it fails validation, return WP_Error object, showing respective error message.
	 *
	 * @since 1.0.0.
	 *
	 * @param array $part Form part data.
	 * @param string $value Submitted value.
	 *
	 * @return string|object
	 */
	public function validate_value( $value, $part = array(), $form = array() ) {
		$validated_value = $value;

		if ( 1 === $part['required'] && empty( $validated_value ) ) {
			$validated_value = new WP_Error( 'error', __( 'This field is required.', 'happyforms' ) );
			return $validated_value;
		}

		if ( 'scale' === $part['rating_type'] ) {
			if ( $validated_value > 0 && $validated_value < 1 || $validated_value > 5 ) {
				$validated_value = new WP_Error( 'error', __( 'Value should be between 1 and 5.', 'happyforms' ) );
				return $validated_value;
			}
		}

		return $validated_value;
	}

	public function stringify_value( $value, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			switch( $part['rating_type'] ) {
				case 'scale':
					$rating_labels = ( 'stars' === $part['rating_visuals'] ) ? array() : $part['rating_labels_scale'];

					if ( ! empty( $rating_labels ) && ! empty( $rating_labels[$value-1] ) ) {
						$value = $rating_labels[$value-1];
					}
					break;
				case 'yesno':
					$rating_labels = $part['rating_labels_yesno'];

					if ( ! empty( $rating_labels ) && ! empty( $rating_labels[$value-1] ) ) {
						$value = $rating_labels[$value-1];
					} else if ( 1 === $value ) {
						$value = __( 'No', 'happyforms' );
					} else if ( 2 === $value ) {
						$value = __( 'Yes', 'happyforms' );
					}
					break;
			}

			if ( 0 === $value ) {
				$value = '';
			}
		}

		return $value;
	}

	public function html_part_class( $class, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			$class[] = 'happyforms-rating--' . $part['rating_type'];
			$class[] = 'happyforms-rating--' . $part['rating_visuals'];
		}

		return $class;
	}

	/**
	 * Enqueue scripts in customizer area.
	 *
	 * @since 1.0.0.
	 *
	 * @param array	List of dependencies.
	 *
	 * @return void
	 */
	public function customize_enqueue_scripts( $deps = array() ) {
		wp_enqueue_script(
			'part-rating',
			happyforms_get_plugin_url() . 'inc/core/assets/js/parts/part-rating.js',
			$deps, HAPPYFORMS_VERSION, true
		);
	}

	public function script_dependencies( $deps, $forms ) {
		$contains_rating = false;
		$form_controller = happyforms_get_form_controller();

		foreach ( $forms as $form ) {
			if ( $form_controller->get_first_part_by_type( $form, $this->type ) ) {
				$contains_rating = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $contains_rating ) {
			return $deps;
		}

		wp_register_script(
			'happyforms-part-rating',
			happyforms_get_plugin_url() . 'inc/core/assets/js/frontend/rating.js',
			array(), HAPPYFORMS_VERSION, true
		);

		$deps[] = 'happyforms-part-rating';

		return $deps;
	}

}
