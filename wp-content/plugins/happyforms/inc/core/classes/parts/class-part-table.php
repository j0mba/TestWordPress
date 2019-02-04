<?php

class HappyForms_Part_Table extends HappyForms_Form_Part {

	public $type = 'table';

	public function __construct() {
		$this->label = __( 'Table', 'happyforms' );
		$this->description = __( 'For radios and checkboxes displaying in a grid of rows and columns.', 'happyforms' );

		add_filter( 'happyforms_part_value', array( $this, 'get_part_value' ), 10, 3 );
		add_filter( 'happyforms_part_class', array( $this, 'html_part_class' ), 10, 3 );
		add_filter( 'happyforms_stringify_part_value', array( $this, 'stringify_value' ), 10, 3 );
		add_filter( 'happyforms_message_part_value', array( $this, 'message_part_value' ), 10, 4 );
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
				'default' => __( 'Options', 'happyforms' ),
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
			),
			'allow_multiple_selection' => array(
				'default' => 0,
				'sanitize' => 'happyforms_sanitize_checkbox'
			),
			'columns' => array(
				'default' => array(),
				'sanitize' => 'happyforms_sanitize_array'
			),
			'rows' => array(
				'default' => array(),
				'sanitize' => 'happyforms_sanitize_array'
			),
		);
	}

	private function get_column_defaults() {
		return array(
			'is_default' => 0,
			'label' => '',
		);
	}

	private function get_row_defaults() {
		return array(
			'label' => '',
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
		require_once( happyforms_get_include_folder() . '/core/templates/parts/customize-table.php' );
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

		foreach( $part['columns'] as $c => $column ) {
			$part['columns'][$c] = wp_parse_args( $column, $this->get_column_defaults() );
		}

		foreach( $part['rows'] as $r => $row ) {
			$part['rows'][$r] = wp_parse_args( $row, $this->get_row_defaults() );
		}

		include( happyforms_get_include_folder() . '/core/templates/parts/frontend-table.php' );
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
		wp_register_script(
			'part-table',
			happyforms_get_plugin_url() . 'inc/core/assets/js/parts/part-table.js',
			$deps, HAPPYFORMS_VERSION, true
		);

		$settings = array(
			'column' => $this->get_column_defaults(),
			'row' => $this->get_row_defaults(),
		);

		wp_localize_script( 'part-table', '_happyFormsTableSettings', $settings );
		wp_enqueue_script( 'part-table' );
	}

	public function get_default_value( $part_data = array() ) {
		$value = array();

		if ( $part_data['allow_multiple_selection'] ) {
			foreach( $part_data['rows'] as $row ) {
				$value[$row['id']] = array();
			}
		}

		return $value;
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
			$requested_data = $request[$part_name];

			if ( is_array( $requested_data ) ) {
				if ( $part_data['allow_multiple_selection'] ) {
					// Checkbox mode
					foreach( $requested_data as $row_id => $row ) {
						$sanitized_value[$row_id] = array_map( 'intval', $requested_data[$row_id] );
					}
				} else {
					// Radio mode
					$sanitized_value = array_map( 'intval', $requested_data );
				}
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

		$rows = happyforms_get_part_options( $part['rows'], $part, $form );
		$columns = happyforms_get_part_options( $part['columns'], $part, $form );
		$row_ids = wp_list_pluck( $rows, 'id' );

		if ( 1 === $part['required'] ) {
			$validated_rows = array_keys( $validated_value );

			if ( count( $row_ids ) !== count( array_intersect( $row_ids, $validated_rows ) ) ) {
				return new WP_Error( 'error', __( 'This field is required.', 'happyforms' ) );
			}

			if ( $part['allow_multiple_selection'] ) {
				$non_empty_rows = array_filter( $validated_value );

				if ( count( $non_empty_rows ) !== count( $row_ids ) ) {
					return new WP_Error( 'error', __( 'This field is required.', 'happyforms' ) );
				}
			}
		}

		$columns = range( 0, count( $columns ) - 1 );

		if ( $part['allow_multiple_selection'] ) {
			// Checkbox mode
			foreach( $validated_value as $row_id => $row ) {
				$intersection = array_intersect( $columns, $row );

				if ( count( $row ) !== count( $intersection ) ) {
					return new WP_Error( 'error', __( 'Checkbox values are not valid.', 'happyforms' ) );
				}
			}
		} else {
			// Radio mode
			foreach( $validated_value as $value ) {
				if ( ! in_array( $value, $columns ) ) {
					return new WP_Error( 'error', __( 'Radio values are not valid.', 'happyforms' ) );
				}
			}
		}

		return $validated_value;
	}

	public function get_part_value( $value, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			foreach ( $part['columns'] as $c => $column ) {
				if ( ! happyforms_is_falsy( $column['is_default'] ) ) {
					if ( ! $part['allow_multiple_selection'] ) {
						$value = $c;
					} else {
						foreach( $value as $r => $row ) {
							$value[$r][] = $c;
						}
					}
				}
			}
		}

		return $value;
	}

	public function html_part_class( $class, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			if ( $part['allow_multiple_selection'] ) {
				$class[] = 'happyforms-selection--multiple';
			}
		}

		return $class;
	}

	public function stringify_value( $value, $part, $form ) {
		if ( $this->type === $part['type'] ) {
			$columns = happyforms_get_part_options( $part['columns'], $part, $form );
			$rows = happyforms_get_part_options( $part['rows'], $part, $form );
			$column_labels = wp_list_pluck( $columns, 'label' );
			$row_labels = wp_list_pluck( $rows, 'label', 'id' );
			$string_value = array();

			foreach ( $value as $row_id => $row_values ) {
				$row_label = $row_labels[$row_id];
				$row_values = $part['allow_multiple_selection'] ? $row_values : array( $row_values );

				foreach ( $row_values as $r => $row_value ) {
					$row_values[$r] = $column_labels[$row_value];
				}

				if ( count( $row_values ) ) {
					$row_values = implode( ', ', $row_values );
					$string_value[] = "{$row_label}: $row_values";
				}
			}

			$value = implode( '<br>', $string_value );
		}

		return $value;
	}

	public function message_part_value( $value, $original_value, $part, $destination ) {
		if ( isset( $part['type'] )
			&& $this->type === $part['type']
			&& 'email' === $destination ) {

			$value = "<br>{$value}";
		}

		return $value;
	}

	public function script_dependencies( $deps, $forms ) {
		$contains_table = false;
		$form_controller = happyforms_get_form_controller();

		foreach ( $forms as $form ) {
			if ( $form_controller->get_first_part_by_type( $form, $this->type ) ) {
				$contains_table = true;
				break;
			}
		}

		if ( ! happyforms_is_preview() && ! $contains_table ) {
			return $deps;
		}

		wp_register_script(
			'happyforms-part-table',
			happyforms_get_plugin_url() . 'inc/core/assets/js/frontend/table.js',
			array(), HAPPYFORMS_VERSION, true
		);

		$deps[] = 'happyforms-part-table';

		return $deps;
	}

}
