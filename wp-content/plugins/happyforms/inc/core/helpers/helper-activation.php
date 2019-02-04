<?php

if ( ! function_exists( 'happyforms_first_run' ) ):
/**
 * Action: First run routine.
 *
 * Creates an example form with example submissions
 * and sets up user feedback options.
 *
 * @since  1.0
 *
 * @hooked register_activation_hook
 *
 * @return void
 */
function happyforms_first_run() {
	require_once( happyforms_get_include_folder() . '/core/classes/class-tracking.php' );
	require_once( happyforms_get_include_folder() . '/core/helpers/helper-misc.php' );
	require_once( happyforms_get_include_folder() . '/core/classes/class-form-controller.php' );

	$tracking = happyforms_get_tracking();
	$status = $tracking->get_status();

	if ( 0 < intval( $status['status'] ) ) {
		return;
	}

	require_once( happyforms_get_include_folder() . '/core/classes/class-form-part-library.php' );
	require_once( happyforms_get_include_folder() . '/core/classes/class-form-styles.php' );
	require_once( happyforms_get_include_folder() . '/core/classes/class-session.php' );
	require_once( happyforms_get_include_folder() . '/core/classes/class-message-controller.php' );
	require_once( happyforms_get_include_folder() . '/core/helpers/helper-form-templates.php' );
	require_once( happyforms_get_include_folder() . '/core/helpers/helper-validation.php' );

	$part_library = happyforms_get_part_library();
	$form_controller = happyforms_get_form_controller();
	$message_controller = happyforms_get_message_controller();

	// Create a new form
	$form = $form_controller->create();

	// Get the new form default data
	$form_data = $form_controller->get( $form->ID );

	$form_data['post_title'] = __( 'Sample Contact Form', 'happyforms' );

	// Prepare age dropdown options
	$age_options = array();
	for ( $age = 1; $age < 100; $age ++ ) {
		$age_options[] = array( 'label' => $age );
	}

	$form_parts = array(
		array(
			'type' => 'single_line_text',
			'label' => __( 'First name', 'happyforms' ),
			'width' => 'half',
		),
		array(
			'type' => 'single_line_text',
			'label' => __( 'Last name', 'happyforms' ),
			'width' => 'half',
		),
		array(
			'type' => 'checkbox',
			'label' => __( 'What\'s your reason for contacting us?', 'happyforms' ),
			'options' => array(
				array(
					'label' => __( 'Need technical help', 'happyforms' ),
				),
				array(
					'label' => __( 'Want to suggest a feature', 'happyforms' ),
				),
				array(
					'label' => __( 'Asking about my account', 'happyforms' ),
				),
			),
		),
		array(
			'type' => 'select',
			'label' => __( 'Age', 'happyforms' ),
			'options' => $age_options
		),
		array(
			'type' => 'multi_line_text',
			'label' => __( 'Your message', 'happyforms' ),
		),
	);

	foreach( $form_parts as $part_id => $part_data ) {
		$part_type = $part_data['type'];
		$part_complete_id = "{$part_type}_$part_id";
		$part_data['id'] = $part_complete_id;
		$part = $part_library->get_part( $part_type );
		$part_defaults = $part->get_customize_defaults();
		$part_data = wp_parse_args( $part_data, $part_defaults );

		if ( isset( $part_data['options'] ) ) {
			foreach( $part_data['options'] as $option_id => $option_data ) {
				$option_data['id'] = "{$part_complete_id}_{$option_id}";
				$part_data['options'][$option_id] = $option_data;
			}
		}

		$form_data['parts'][] = $part_data;
	}

	// Update the new form with default parts
	$form_data = $form_controller->update( $form_data );

	// Create example submissions
	$messages_data = array(
		array(
			'First name' => 'Martha',
			'Last name' => 'Daniel',
			'What\'s your reason for contacting us?' => 0,
			'Age' => 31,
			'Your message' => __( 'It would be great if I could use Google ReCaptcha instead of Honeypot.', 'happyforms' ),
		),
		array(
			'First name' => 'Willie',
			'Last name' => 'Crawford',
			'What\'s your reason for contacting us?' => 1,
			'Age' => 45,
			'Your message' => __( 'How do I embed a HappyForm in my sidebar?', 'happyforms' ),
		),
		array(
			'First name' => 'Bonnie',
			'Last name' => 'Mccarthy',
			'What\'s your reason for contacting us?' => 2,
			'Age' => 37,
			'Your message' => __( 'How can I access my premium upgrade credentials?', 'happyforms' ) ,
		)
	);

	foreach ( $messages_data as $message_data ) {
		$submission = array();

		foreach ( $message_data as $label => $value ) {
			foreach ( $form_data['parts'] as $part ) {
				if ( $label === $part['label'] ) {
					$part_id = $part['id'];

					if ( is_numeric( $value ) ) {
						$value = array( $part['options'][$value]['label'] );
					}

					$submission[$part_id] = $value;

					break;
				}
			}
		}

		$message_controller->create( $form_data, $submission );
	}

	$message_controller->update_badge_transient();

	// Store an option to avoid creating new forms on reactivation
	$tracking->update_status( 1 );

	// Force a permalinks refresh
	flush_rewrite_rules();
}

endif;

if ( ! function_exists( 'happyforms_deactivate' ) ):
/**
 * Action: Deactivation routine.
 *
 * Updates user feedback options.
 *
 * @since  1.0
 *
 * @hooked register_deactivation_hook
 *
 * @return void
 */
function happyforms_deactivate() {
	require_once( happyforms_get_include_folder() . '/core/classes/class-tracking.php' );

	$tracking = happyforms_get_tracking();
	$status = $tracking->get_status();

	if ( ! empty( $status['email'] ) ) {
		wp_remote_post( $tracking->monitor_action , array(
			'body' => array(
				$tracking->monitor_email_field => $status['email'],
				$tracking->monitor_status_field => 'inactive',
			)
		) );
	}
}

endif;

register_activation_hook( happyforms_plugin_file(), 'happyforms_first_run' );
register_deactivation_hook( happyforms_plugin_file(), 'happyforms_deactivate' );
