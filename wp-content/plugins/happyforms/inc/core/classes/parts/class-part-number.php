<?php

class HappyForms_Part_Number extends HappyForms_Form_Part {

	public $type = 'number';

	public function __construct() {
		$this->label = __( 'Number', 'happyforms' );
		$this->description = __( 'For numeric fields.', 'happyforms' );

		add_filter( 'happyforms_part_class', array( $this, 'html_part_class' ), 10, 3 );
		add_filter( 'happyforms_part_data_attributes', array( $this, 'html_part_data_attributes' ), 10, 3 );
		add_filter( 'happyforms_frontend_dependencies', array( $this, 'script_dependencies' ), 10, 2 );
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
				'default' => __( 'Number', 'happyforms' ),
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
			'placeholder' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field',
			),
			'width' => array(
				'default' => 'full',
				'sanitize' => 'sanitize_key'
			),
			'css_class' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field'
			),
			'min_value' => array(
				'default' => 0,
				'sanitize' => 'intval'
			),
			'max_value' => array(
				'default' => 10,
				'sanitize' => 'intval'
			),
			'masked' => array(
				'default' => 0,
				'sanitize' => 'intval'
			),
			'mask_numeric_thousands_delimiter' => array(
				'default' => ',',
				'sanitize' => 'sanitize_text_field'
			),
			'mask_numeric_decimal_mark' => array(
				'default' => '.',
				'sanitize' => 'sanitize_text_field'
			),
			'mask_numeric_prefix' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field'
			),
			'confirmation_field' => array(
				'default' => 0,
				'sanitize' => 'intval'
			),
			'confirmation_field_label' => array(
				'default' => __( 'Confirm Number', 'happyforms' ),
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
		require_once( happyforms_get_include_folder() . '/core/templates/parts/customize-number.php' );
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

		include( happyforms_get_include_folder() . '/core/templates/parts/frontend-number.php' );
	}

	public function get_default_value( $part_data = array() ) {
		return array();
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
			$sanitized_value[0] = $request[$part_name];
		}

		if ( isset( $request[$part_name . '_confirmation'] ) ) {
			$sanitized_value[1] = $request[$part_name . '_confirmation'];
		}

		$sanitized_value = array_map( 'sanitize_text_field' , $sanitized_value );

		return $sanitized_value;
	}

	/**
	 * Validate value before submitting it. If it fails validation,
	 * return WP_Error object, showing respective error message.
	 *
	 * @since 1.0.0.
	 *
	 * @param array $part Form part data.
	 * @param string $value Submitted value.
	 *
	 * @return string|object
	 */
	public function validate_value( $value, $part = array(), $form = array() ) {
		$validated_values = $value;

		if ( $part['required'] && ( empty( $validated_values ) || '' === $validated_values[0] ) ) {
			return new WP_Error( 'error', __( 'This field is required.', 'happyforms' ) );
		}

		$validation_number = $validated_values[0];

		if ( $part['masked'] && '' !== $validation_number ) {
			$validation_number = str_replace( $part['mask_numeric_prefix'], '', $validation_number );
			$validation_number = trim( $validation_number );
			$validation_number = str_replace( $part['mask_numeric_thousands_delimiter'], '', $validation_number );
			$validation_number = str_replace( $part['mask_numeric_decimal_mark'], '.', $validation_number );
		}

		// Bounds check
		if ( ! empty( $validated_values ) && '' !== $validation_number ) {
			$validation_number = floatval( $validation_number );
			$min_value = intval( $part['min_value'] );
			$max_value = intval( $part['max_value'] );

			if ( $validation_number < $min_value || $validation_number > $max_value ) {
				return new WP_Error( 'error', __( 'This field does not match minimum and maximum allowed value.', 'happyforms' ) );
			}
		}

		// Check confirmation
		if ( $part['confirmation_field'] ) {
			if ( ! isset( $validated_values[1] )
				|| ( $validated_values[0] !== $validated_values[1] ) ) {
				return new WP_Error( 'error', __( 'Number and confirmation number are not matching.', 'happyforms' ) );
			}
		}

		return $validated_values[0];
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
			'part-number',
			happyforms_get_plugin_url() . 'inc/core/assets/js/parts/part-number.js',
			$deps, HAPPYFORMS_VERSION, true
		);
	}

	public function html_part_class( $class, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			if ( happyforms_get_part_value( $part, $form, 0 )
				|| happyforms_get_part_value( $part, $form, 1 ) ) {
				$class[] = 'happyforms-part--filled';
			}

			if ( 'focus-reveal' === $part['description_mode'] ) {
				$class[] = 'happyforms-part--focus-reveal-description';
			}
		}

		return $class;
	}

	public function html_part_data_attributes( $attributes, $part, $form ) {
		if ( $this->type !== $part['type'] ) {
			return $attributes;
		}

		if ( $part['confirmation_field'] ) {
			$attributes['happyforms-require-confirmation'] = '';
		}

		if ( $part['masked'] ) {
			$attributes['mask'] = 'true';
			$attributes['thousands-delimiter'] = $part['mask_numeric_thousands_delimiter'];
			$attributes['decimal-mark'] = $part['mask_numeric_decimal_mark'];
			$attributes['prefix'] = $part['mask_numeric_prefix'];
		}

		return $attributes;
	}

	/**
	 * Action: enqueue additional scripts on the frontend.
	 *
	 * @since 1.3.0.
	 *
	 * @hooked action happyforms_frontend_dependencies
	 *
	 * @param array	List of dependencies.
	 *
	 * @return array
	 */
	public function script_dependencies( $deps, $forms ) {
		$contains_number = false;
		$form_controller = happyforms_get_form_controller();

		foreach ( $forms as $form ) {
			if ( $form_controller->get_first_part_by_type( $form, $this->type ) ) {
				$contains_number = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $contains_number ) {
			return $deps;
		}

		wp_register_script(
			'cleave',
			happyforms_get_plugin_url() . 'inc/core/assets/js/lib/cleave.min.js',
			array(), HAPPYFORMS_VERSION
		);

		wp_register_script(
			'happyforms-part-number',
			happyforms_get_plugin_url() . 'inc/core/assets/js/frontend/number.js',
			array( 'cleave' ), HAPPYFORMS_VERSION, true
		);

		$deps[] = 'happyforms-part-number';

		return $deps;
	}

}
