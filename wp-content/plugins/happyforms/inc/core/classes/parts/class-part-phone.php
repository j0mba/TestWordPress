<?php

class HappyForms_Part_Phone extends HappyForms_Form_Part {

	public $type = 'phone';

	public function __construct() {
		$this->label = __( 'Phone', 'happyforms' );
		$this->description = __( 'For phone numbers. Includes country specific formatting.', 'happyforms' );

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
				'default' => __( 'Phone', 'happyforms' ),
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
			'masked' => array(
				'default' => 0,
				'sanitize' => 'intval'
			),
			'mask_phone_country' => array(
				'default' => 'US',
				'sanitize' => 'sanitize_text_field'
			),
			'mask_allow_all_countries' => array(
				'default' => 0,
				'sanitize' => 'intval'
			),
			'confirmation_field' => array(
				'default' => 0,
				'sanitize' => 'intval'
			),
			'confirmation_field_label' => array(
				'default' => __( 'Confirm Phone', 'happyforms' ),
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
		require_once( happyforms_get_include_folder() . '/core/templates/parts/customize-phone.php' );
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

		include( happyforms_get_include_folder() . '/core/templates/parts/frontend-phone.php' );
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
			$sanitized_value = wp_parse_args( $request[$part_name], $sanitized_value );

			if ( '' !== implode( '', array_values( $sanitized_value ) ) ) {
				foreach( $sanitized_value as $component => $value ) {
					if ( 'country' !== $component ) {
						$sanitized_value[$component] = preg_replace( '/[^0-9]/', '', $value );
					} else {
						$sanitized_value[$component] = sanitize_text_field( $value );
					}
				}
			}
		}

		return $sanitized_value;
	}

	/**
	 * Validate value before submitting it. If it fails validation,
	 * return WP_Error object, showing respective error message.
	 *
	 * @since 1.0.0.
	 *
	 * @param array $part_data Form part data.
	 * @param string $value Submitted value.
	 *
	 * @return string|object
	 */
	public function validate_value( $value, $part = array(), $form = array() ) {
		$validated_values = $value;

		if ( $part['required'] && empty( $validated_values ) ) {
			return new WP_Error( 'error', __( 'This field is required.', 'happyforms' ) );
		}

		if ( $part['required'] && empty( $validated_values['number'] ) ) {
			return new WP_Error( 'error', __( 'This field is required.', 'happyforms' ) );
		}

		if ( isset( $validated_values['confirmation'] ) && $validated_values['number'] !== $validated_values['confirmation'] ) {
			return new WP_Error( 'error', __( 'Number and confirmation number are not matching.', 'happyforms' ) );
		}

		$phone_string = $validated_values['number'];

		if ( $this->is_masked( $part ) ) {
			$phone_string = '+' . $validated_values['code'] . ' ' . $validated_values['number'];
		}

		return $phone_string;
	}

	public function is_masked( $part ) {
		// back compatibility with "Mask this input" option
		if ( 1 == $part['masked'] ) {
			return true;
		}

		return false;
	}

	public function html_part_class( $class, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			if ( $this->is_masked( $part )
				|| happyforms_get_part_value( $part, $form, 0 )
				|| happyforms_get_part_value( $part, $form, 1 ) ) {
				$class[] = 'happyforms-part--filled';
			}

			if ( $this->is_masked( $part ) ) {
				$class[] = 'happyforms-is-masked';
			}

			if ( $this->is_masked( $part ) && $part['mask_allow_all_countries'] ) {
				$class[] = 'happyforms-has-country-select';
			}

			if ( ! $this->is_masked( $part ) ) {
				$class[] = 'happyforms-phone--plain';
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

		if ( $this->is_masked( $part ) ) {
			$attributes['mask'] = 'true';
			$attributes['country'] = $part['mask_phone_country'];
		}

		return $attributes;
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
			'part-phone',
			happyforms_get_plugin_url() . 'inc/core/assets/js/parts/part-phone.js',
			$deps, HAPPYFORMS_VERSION, true
		);
	}

	/**
	 * Action: enqueue additional scripts on the frontend.
	 *
	 * @since 1.0.0.
	 *
	 * @hooked action happyforms_frontend_dependencies
	 *
	 * @param array	List of dependencies.
	 *
	 * @return array
	 */
	public function script_dependencies( $deps, $forms ) {
		$contains_phone = false;
		$form_controller = happyforms_get_form_controller();

		foreach ( $forms as $form ) {
			if ( $form_controller->get_first_part_by_type( $form, $this->type ) ) {
				$contains_phone = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $contains_phone ) {
			return $deps;
		}

		wp_register_script(
			'cleave',
			happyforms_get_plugin_url() . 'inc/core/assets/js/lib/cleave.min.js',
			array(), HAPPYFORMS_VERSION
		);

		wp_register_script(
			'cleave-phone',
			happyforms_get_plugin_url() . 'inc/core/assets/js/lib/cleave-phone.i18n.js',
			array( 'cleave' ), HAPPYFORMS_VERSION, true
		);

		wp_register_script(
			'happyforms-part-phone',
			happyforms_get_plugin_url() . 'inc/core/assets/js/frontend/phone.js',
			array( 'cleave-phone' ), HAPPYFORMS_VERSION, true
		);

		$codes = wp_list_pluck( happyforms_get_phone_countries(), 'code' );
		$codes = array_flip( $codes );
		$settings = array( 'codes' => $codes );

		wp_localize_script(
			'happyforms-part-phone',
			'HappyFormsPhoneSettings', $settings
		);

		$deps[] = 'happyforms-part-phone';

		return $deps;
	}

}
