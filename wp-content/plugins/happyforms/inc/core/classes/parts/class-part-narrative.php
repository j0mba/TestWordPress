<?php

class HappyForms_Part_Narrative extends HappyForms_Form_Part {

	public $type = 'narrative';

	public function __construct() {
		$this->label = __( 'Story', 'happyforms' );
		$this->description = __( 'For adding fill-in-the-blank style inputs to a paragraph of text.', 'happyforms' );

		add_filter( 'happyforms_stringify_part_value', array( $this, 'stringify_value' ), 10, 3 );
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
				'default' => __( 'Story', 'happyforms' ),
				'sanitize' => 'sanitize_text_field',
			),
			'label_placement' => array(
				'default' => 'above',
				'sanitize' => 'sanitize_text_field'
			),
			'format' => array(
				'default' => '',
				'sanitize' => 'sanitize_text_field'
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
		require_once( happyforms_get_include_folder() . '/core/templates/parts/customize-narrative.php' );
	}

	/**
	 * Get front end part template with parsed data.
	 *
	 * @since 1.0.0.
	 *
	 * @param array $part_data  Form part data.
	 * @param array $form_data  Form (post) data.
	 *
	 * @return string   Markup for the form part.
	 */
	public function frontend_template( $part_data = array(), $form_data = array() ) {
		$part = wp_parse_args( $part_data, $this->get_customize_defaults() );
		$form = $form_data;

		include( happyforms_get_include_folder() . '/core/templates/parts/frontend-narrative.php' );
	}

	public function get_default_value( $part_data = array() ) {
		$tokens = happyforms_get_narrative_tokens( $part_data['format'] );

		return $tokens;
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
			$sanitized_value = array_map( 'sanitize_text_field', $request[$part_name] );
		}

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
		$tokens = happyforms_get_narrative_tokens( $part['format'] );

		if ( 1 === $part['required'] ) {
			if ( count( $value ) < count( $tokens ) ) {
				return new WP_Error( 'error', __( 'This field is required.', 'happyforms' ) );
			}

			foreach( $value as $component ) {
				if ( empty( $component ) ) {
					return new WP_Error( 'error', __( 'This field is required.', 'happyforms' ) );
				}
			}
		}

		return $value;
	}

	public function stringify_value( $value, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			$tokens = happyforms_get_narrative_tokens( $part['format'] );
			$format = str_replace( '[]', '%s', $part['format'] );
			$value = vsprintf( html_entity_decode( stripslashes( $format ) ), $value );
			$value = sanitize_text_field( $value );
		}

		return $value;
	}

	/**
	 * Enqueue scripts in customizer area.
	 *
	 * @since 1.0.0.
	 *
	 * @param array List of dependencies.
	 *
	 * @return void
	 */
	public function customize_enqueue_scripts( $deps = array() ) {
		wp_enqueue_script(
			'part-narrative',
			happyforms_get_plugin_url() . 'inc/core/assets/js/parts/part-narrative.js',
			$deps, HAPPYFORMS_VERSION, true
		);
	}

	public function script_dependencies( $deps, $forms ) {
		$contains_narrative = false;
		$form_controller = happyforms_get_form_controller();

		foreach ( $forms as $form ) {
			if ( $form_controller->get_first_part_by_type( $form, $this->type ) ) {
				$contains_narrative = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $contains_narrative ) {
			return $deps;
		}

		wp_register_script(
			'happyforms-part-narrative',
			happyforms_get_plugin_url() . 'inc/core/assets/js/frontend/narrative.js',
			array(), HAPPYFORMS_VERSION, true
		);

		$deps[] = 'happyforms-part-narrative';

		return $deps;
	}

}
