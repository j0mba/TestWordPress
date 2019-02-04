<?php

class HappyForms_Part_Legal extends HappyForms_Form_Part {

	public $type = 'legal';
	public $template_id = 'happyforms-legal-template';

	public function __construct() {
		$this->label = __( 'Legal', 'happyforms' );
		$this->description = __( 'For requiring fine print before accepting submission.', 'happyforms' );

		add_filter( 'happyforms_part_class', array( $this, 'html_part_class' ), 10, 3 );
		add_filter( 'happyforms_part_value', array( $this, 'get_part_value' ), 10, 4 );
		add_filter( 'happyforms_message_part_value', array( $this, 'message_part_value' ), 10, 4 );
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
            'legal_text' => array(
                'default' => __( 'I accept terms and conditions.', 'happyforms' ),
                'sanitize' => 'esc_html'
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
			'label' => array(
				'default' => __( 'Legal', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
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
		require_once( happyforms_get_include_folder() . '/core/templates/parts/customize-legal.php' );
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

		include( happyforms_get_include_folder() . '/core/templates/parts/frontend-legal.php' );
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
			$sanitized_value = sanitize_text_field( $request[$part_name] );
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

		if ( empty( $validated_value ) ) {
			$validated_value = new WP_Error( 'error', __( 'This field is required.', 'happyforms' ) );
			return $validated_value;
        }

        if ( $validated_value !== 'yes' ) {
			$validated_value = new WP_Error( 'error', __( 'Please accept terms before proceeding.', 'happyforms' ) );
			return $validated_value;
        }

		return $validated_value;
	}

	public function get_part_value( $value, $part, $form ) {
		if ( $this->type === $part['type']
			&& ( 'review' !== happyforms_get_current_step( $form ) ) ) {

			$value = '';
		}

		return $value;
	}

	public function message_part_value( $value, $original_value, $part, $destination ) {
		if ( isset( $part['type'] )
			&& $this->type === $part['type']
			&& 'email' === $destination ) {

			$value = 'yes' === $value ? htmlspecialchars_decode( $part['legal_text'] ) : '';
		}

		return $value;
	}

	public function html_part_class( $class, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			$part_index = array_search( $part['id'], wp_list_pluck( $form['parts'], 'id' ) );

			if ( $part_index ) {
				$previous_part = $form['parts'][$part_index-1];
				$next_part = $form['parts'][$part_index+1];

				if ( isset( $previous_part ) ) {
					if ( 'left' === $previous_part['label_placement'] && 'full' === $previous_part['width'] ) {
						$class[] = 'happyforms-part-legal--align-right';
					}
				}

				if ( isset( $next_part ) ) {
					if ( 'left' === $next_part['label_placement'] && 'full' === $next_part['width'] ) {
						$class[] = 'happyforms-part-legal--align-right';
					}
				}
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
			'part-legal',
			happyforms_get_plugin_url() . 'inc/core/assets/js/parts/part-legal.js',
			$deps, HAPPYFORMS_VERSION, true
		);
	}

}
