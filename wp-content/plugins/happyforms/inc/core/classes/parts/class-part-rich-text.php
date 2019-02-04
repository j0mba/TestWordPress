<?php

class HappyForms_Part_RichText extends HappyForms_Form_Part {

	public $type = 'rich_text';

	public function __construct() {
		$this->label = __( 'Text Editor', 'happyforms' );
		$this->description = __( 'For formatting text, code blocks, lists and more.', 'happyforms' );

		add_filter( 'happyforms_the_part_value', array( $this, 'output_part_value' ), 10, 3 );
		add_filter( 'happyforms_part_class', array( $this, 'html_part_class' ), 10, 3 );
		add_filter( 'happyforms_frontend_dependencies', array( $this, 'script_dependencies' ), 10, 2 );
		add_action( 'happyforms_footer', array( $this, 'footer_templates' ) );
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
				'default' => __( 'Text', 'happyforms' ),
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
		require_once( happyforms_get_include_folder() . '/core/templates/parts/customize-rich-text.php' );
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

		if ( ! happyforms_is_block_context() ) {
			include( happyforms_get_include_folder() . '/core/templates/parts/frontend-rich-text.php' );
		} else {
			include( happyforms_get_include_folder() . '/core/templates/parts/block-rich-text.php' );
		}
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
			'part-rich-text',
			happyforms_get_plugin_url() . 'inc/core/assets/js/parts/part-rich-text.js',
			$deps, HAPPYFORMS_VERSION, true
		);
	}

	private function get_allowed_html() {
		$allowed = array(
			'b' => array(),
			'strong' => array(),
			'i' => array(),
			'ul' => array(),
			'ol' => array(),
			'li' => array(),
			'p' => array(),
			'a' => array( 'href' => array() ),
			'code' => array(),
			'hr' => array(),
			'u' => array(),
			'strike' => array(),
			'blockquote' => array(),
		);

		return $allowed;
	}

	private function get_editor_valid_elements() {
		$tags = $this->get_allowed_html();
		$elements = array();

		foreach( $tags as $tag => $attributes ) {
			$element = $tag;
			$element_attributes = implode( '|', array_keys( $attributes ) );

			if ( ! empty( $element_attributes ) ) {
				$element = "{$element}[{$element_attributes}]";
			}

			$elements[] = $element;
		}

		$elements = implode( ',', $elements );

		return $elements;
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
			$allowed_html = $this->get_allowed_html();
			$sanitized_value = wp_kses( $request[$part_name], $allowed_html );
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
		}

		return $validated_value;
	}

	public function output_part_value( $value, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			$value = stripslashes( $value );
		}

		return $value;
	}

	public function html_part_class( $class, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			if ( 'focus-reveal' === $part['description_mode'] ) {
				$class[] = 'happyforms-part--focus-reveal-description';
			}
		}

		return $class;
	}

	public function script_dependencies( $deps, $forms ) {
		$contains_rich_text = false;
		$form_controller = happyforms_get_form_controller();

		foreach ( $forms as $form ) {
			if ( $form_controller->get_first_part_by_type( $form, $this->type ) ) {
				$contains_rich_text = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $contains_rich_text ) {
			return $deps;
		}

		wp_enqueue_editor();

		wp_register_script(
			'happyforms-part-rich-text',
			happyforms_get_plugin_url() . 'inc/core/assets/js/frontend/rich-text.js',
			array( 'editor' ), HAPPYFORMS_VERSION, true
		);

		$content_css = array( happyforms_get_plugin_url() . 'inc/core/assets/css/rich-text-editor.css' );
		/**
		 * Filters the list of CSS files to load
		 * in the editor document.
		 *
		 * @param array $content_css Current list of files.
		 *
		 * @return array
		 */
		$content_css = apply_filters( 'happyforms_text_editor_content_css', $content_css );
		$css_vars = array_values( happyforms_get_styles()->form_css_vars() );
		$allowed_elements = $this->get_editor_valid_elements();
		$editor_settings = array(
			'contentCSS' => $content_css,
			'cssVars' => $css_vars,
			'allowedElements' => $allowed_elements,
		);

		wp_localize_script(
			'happyforms-part-rich-text',
			'happyFormsRichTextSettings',
			$editor_settings
		);

		$deps[] = 'happyforms-part-rich-text';

		return $deps;
	}

	public function footer_templates( $forms ) {
		$contains_rich_text = false;
		$form_controller = happyforms_get_form_controller();

		foreach ( $forms as $form ) {
			if ( $form_controller->get_first_part_by_type( $form, $this->type ) ) {
				$contains_rich_text = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $contains_rich_text ) {
			return;
		}

		require_once( happyforms_get_include_folder() . '/core/templates/partials/frontend-rich-text-toolbar-icons.php' );
	}

}
