<?php

class HappyForms_Part_Email extends HappyForms_Form_Part {

	public $type = 'email';

	public function __construct() {
		$this->label = __( 'Email', 'happyforms' );
		$this->description = __( 'For formatted email addresses. The \'@\' symbol is required.', 'happyforms' );

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
				'default' => __( 'Email', 'happyforms' ),
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
			'autocomplete_domains' => array(
				'default' => 1,
				'sanitize' => 'intval'
			),
			'confirmation_field' => array(
				'default' => 0,
				'sanitize' => 'intval'
			),
			'confirmation_field_label' => array(
				'default' => __( 'Confirm Email', 'happyforms' ),
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
		require_once( happyforms_get_include_folder() . '/core/templates/parts/customize-email.php' );
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

		include( happyforms_get_include_folder() . '/core/templates/parts/frontend-email.php' );
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
			$sanitized_value[0] = sanitize_text_field( $request[$part_name] );
		}

		if ( isset( $request[$part_name . '_confirmation'] ) ) {
			$sanitized_value[1] = sanitize_text_field( $request[$part_name . '_confirmation'] );
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
		$validated_values = $value;

		if ( $part['required'] && empty( $validated_values ) ) {
			return new WP_Error( 'error', __( 'This field is required.', 'happyforms' ) );
		}

		if ( $part['required'] && empty( $validated_values[0] ) ) {
			return new WP_Error( 'error', __( 'This field is required.', 'happyforms' ) );
		}

		if ( ! empty( $validated_values[0] ) && ! is_email( $validated_values[0] ) ) {
			return new WP_error( 'error', __( 'Not a valid e-mail address.', 'happyforms' ) );
		}

		if ( isset( $validated_values[1] ) && $validated_values[0] !== $validated_values[1] ) {
			return new WP_Error( 'error', __( 'Email and confirmation email are not matching.', 'happyforms' ) );
		}

		return $validated_values[0];
	}

	public function html_part_data_attributes( $attributes, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			if ( $part['confirmation_field'] ) {
				$attributes['happyforms-require-confirmation'] = '';
			}
			if ( $part['autocomplete_domains'] ) {
				$attributes['mode'] = 'autocomplete';
			}
		}

		return $attributes;
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

			$class[] = 'happyforms-part--with-autocomplete';
		}

		return $class;
	}

	public function get_domains_for_autocomplete() {
		$domains = array(
			'gmail.com',
			'yahoo.com',
			'hotmail.com',
			'aol.com',
			'icloud.com',
			'outlook.com'
		);

		return apply_filters( 'happyforms_email_domains_autocomplete', $domains );
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
			'part-email',
			happyforms_get_plugin_url() . 'inc/core/assets/js/parts/part-email.js',
			$deps, HAPPYFORMS_VERSION, true
		);
	}

	public function script_dependencies( $deps, $forms ) {
		$contains_email = false;
		$form_controller = happyforms_get_form_controller();

		foreach ( $forms as $form ) {
			if ( $form_controller->get_first_part_by_type( $form, $this->type ) ) {
				$contains_email = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $contains_email ) {
			return $deps;
		}

		wp_register_script(
			'happyforms-email',
			happyforms_get_plugin_url() . 'inc/core/assets/js/frontend/email.js',
			array( 'happyforms-select' ), HAPPYFORMS_VERSION, true
		);

		$settings = array(
			'url' => admin_url( 'admin-ajax.php' ),
			'autocompleteSource' => $this->get_domains_for_autocomplete()
		);

		wp_localize_script(
			'happyforms-email',
			'_happyFormsEmailSettings',
			$settings
		);

		$deps[] = 'happyforms-email';

		return $deps;
	}
}
