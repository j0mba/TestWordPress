<?php

class HappyForms_Part_Title extends HappyForms_Form_Part {

	public $type = 'title';

	public function __construct() {
		$this->label = __( 'Title', 'happyforms' );
		$this->description = __( 'For displaying personal honorifics.', 'happyforms' );

		add_filter( 'happyforms_stringify_part_value', array( $this, 'stringify_value' ), 10, 3 );
		add_filter( 'happyforms_frontend_dependencies', array( $this, 'script_dependencies' ), 10, 2 );
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
				'default' => __( 'Title', 'happyforms' ),
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
			'required' => array(
				'default' => 1,
				'sanitize' => 'happyforms_sanitize_checkbox',
			)
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
		require_once( happyforms_get_include_folder() . '/core/templates/parts/customize-title.php' );
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
		$part['options'] = $this->get_honorifics();
		$form = $form_data;

		include( happyforms_get_include_folder() . '/core/templates/parts/frontend-title.php' );
    }

    /**
     * Get honorifics as possible options for the dropdown.
     *
     * @since 1.1.0.
     *
     * @return array List of honorifics.
     */
    public function get_honorifics() {
        return array(
            array( 'id' => 'mr', 'label' => __( 'Mr.', 'happyforms' ) ),
            array( 'id' => 'mrs', 'label' => __( 'Mrs.', 'happyforms' ) ),
            array( 'id' => 'ms', 'label' => __( 'Ms.', 'happyforms' ) ),
            array( 'id' => 'miss', 'label' => __( 'Miss', 'happyforms' ) ),
            array( 'id' => 'prof', 'label' => __( 'Prof.', 'happyforms' ) ),
            array( 'id' => 'dr', 'label' => __( 'Dr.', 'happyforms' ) ),
        );
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
			'part-title',
			happyforms_get_plugin_url() . 'inc/core/assets/js/parts/part-title.js',
			$deps, HAPPYFORMS_VERSION, true
		);
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

			return $sanitized_value;
		}
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

		if ( 1 === $part['required'] && '' === $validated_value ) {
			return new WP_Error( 'error', __( 'This field is required.', 'happyforms' ) );
		}

		if ( '' !== $validated_value ) {
			if ( ! is_numeric( $validated_value ) ) {
				return new WP_Error( 'error', __( 'Selected value is not valid.', 'happyforms' ) );
			}

			$options = range( 0, count( $this->get_honorifics() ) - 1 );

			if ( ! in_array( intval( $validated_value ), $options ) ) {
				return new WP_Error( 'error', __( 'Selected value is not valid.', 'happyforms' ) );
			}
		}

		return $validated_value;
	}

	public function stringify_value( $value, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			if ( '' !== $value ) {
				$options = $this->get_honorifics();
				$value = $options[$value]['label'];
			}
		}

		return $value;
	}

	public function html_part_class( $class, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			$class[] = 'happyforms-part--select';

			if ( happyforms_get_part_value( $part, $form ) ) {
				$class[] = 'happyforms-part--filled';
			}

			if ( 1 === intval( $part['required'] ) ) {
				$class[] = 'happyforms-part-select--required';
			}
		}

		return $class;
	}

	public function script_dependencies( $deps, $forms ) {
		$contains_title = false;
		$form_controller = happyforms_get_form_controller();

		foreach ( $forms as $form ) {
			if ( $form_controller->get_first_part_by_type( $form, $this->type ) ) {
				$contains_title = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $contains_title ) {
			return $deps;
		}

		wp_register_script(
			'happyforms-title',
			happyforms_get_plugin_url() . 'inc/core/assets/js/frontend/title.js',
			array( 'happyforms-select' ), HAPPYFORMS_VERSION, true
		);

		$deps[] = 'happyforms-title';

		return $deps;
	}

}
