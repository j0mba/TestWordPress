<?php

class HappyForms_Part_Scale extends HappyForms_Form_Part {

	public $type = 'scale';

	public function __construct() {
		$this->label = __( 'Scale', 'happyforms' );
		$this->description = __( 'For collecting opinions using a horizontal slider.', 'happyforms' );

		add_filter( 'happyforms_frontend_dependencies', array( $this, 'script_dependencies' ), 10, 2 );
		add_filter( 'happyforms_part_value', array( $this, 'part_value' ), 10, 2 );
		add_filter( 'happyforms_part_class', array( $this, 'html_part_class' ), 10, 3 );
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
				'default' => __( 'Scale', 'happyforms' ),
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
			'width' => array(
				'default' => 'full',
				'sanitize' => 'sanitize_key'
			),
			'min_label' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field'
			),
			'max_label' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field'
			),
            'min_value' => array(
                'default' => 0,
                'sanitize' => 'intval'
            ),
            'max_value' => array(
                'default' => 100,
                'sanitize' => 'intval'
            ),
            'multiple' => array(
                'default' => 0,
                'sanitize' => 'happyforms_sanitize_checkbox'
			),
			'default_range_from' => array(
				'default' => 0,
				'sanitize' => 'intval'
			),
			'default_range_to' => array(
				'default' => 50,
				'sanitize' => 'intval'
			),
            'step' => array(
                'default' => 1,
                'sanitize' => 'sanitize_key'
            ),
            'default_value' => array(
                'default' => 50,
                'sanitize' => 'intval'
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
		require_once( happyforms_get_include_folder() . '/core/templates/parts/customize-scale.php' );
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

		include( happyforms_get_include_folder() . '/core/templates/parts/frontend-scale.php' );
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
			if ( is_array( $request[$part_name] ) && isset( $request[$part_name][0] ) ) {
				$array_value = explode( ',', $request[$part_name][0] );
				$sanitized_value = array_map( 'intval', $array_value );
			} else {
				$sanitized_value = intval( $request[$part_name] );
			}
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
			return new WP_Error( 'error', __( 'This field is required.', 'happyforms' ) );
		}

		// handle multiple range
		if ( is_array( $validated_value ) && count( $validated_value ) !== count( array_filter( $validated_value, 'is_numeric' ) ) ) {
			$validated_value = new WP_Error( 'not_a_number' );
		}

		if ( ! is_array( $validated_value ) && ! is_numeric( $validated_value ) ) {
			$validated_value = new WP_Error( 'not_a_number' );
		}

		return $validated_value;
	}

	public function part_value( $value, $part )  {
		if ( $this->type === $part['type'] ) {
			if ( ! empty( $value ) ) {
				if ( is_array( $value ) ) {
					$value = implode( ',', $value );
				}
			} else {
				if ( 1 === intval( $part['multiple'] ) ) {
					$value = $part['default_range_from'] . ',' . $part['default_range_to'];
				} else {
					$value = $part['default_value'];
				}
			}
		}

		return $value;
	}

	public function html_part_class( $class, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			if ( 1 === intval( $part['multiple'] ) ) {
				$class[] = 'happyforms-part--scale-multiple';
			}
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
			'part-scale',
			happyforms_get_plugin_url() . 'inc/core/assets/js/parts/part-scale.js',
			$deps, HAPPYFORMS_VERSION, true
		);
	}

	public function script_dependencies( $deps, $forms ) {
		$contains_scale = false;
		$form_controller = happyforms_get_form_controller();

		foreach ( $forms as $form ) {
			if ( $form_controller->get_first_part_by_type( $form, $this->type ) ) {
				$contains_scale = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $contains_scale ) {
			return $deps;
		}

		wp_register_script(
			'multirange-polyfill',
			happyforms_get_plugin_url() . 'inc/core/assets/js/lib/multirange.js',
			'',
			false,
			true
		);

		wp_register_script(
			'happyforms-part-scale',
			happyforms_get_plugin_url() . 'inc/core/assets/js/frontend/scale.js',
			array( 'multirange-polyfill' ), HAPPYFORMS_VERSION, true
		);

		$deps[] = 'happyforms-part-scale';

		return $deps;
	}

}
