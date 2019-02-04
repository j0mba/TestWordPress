<?php

class HappyForms_Message_Controller {

	/**
	 * The singleton instance.
	 *
	 * @since 1.0
	 *
	 * @var HappyForms_Message_Controller
	 */
	private static $instance;

	/**
	 * The message post type slug.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $post_type = 'happyforms-message';

	/**
	 * Response editing capability.
	 *
	 */
	public $capability = 'happyforms_manage_response';

	/**
	 * The parameter name used to identify a
	 * submission form
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $form_parameter = 'happyforms_form_id';

	/**
	 * The parameter name used to identify a
	 * submission form
	 *
	 * @var string
	 */
	public $form_step_parameter = 'happyforms_step';

	/**
	 * The action name used to identify a
	 * message submission request.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $submit_action = 'happyforms_message';

	/**
	 * The nonce prefix used in forms.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $nonce_prefix = 'happyforms_message_nonce_';

	/**
	 * The nonce name used in forms.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $nonce_name = 'happyforms_message_nonce';

	/**
	 * The transient key used to store
	 * the counter of unread messages.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	public $unread_transient = 'happyforms_unread_messages';

	/**
	 * The url used to verify a Google ReCaptcha request.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	public $captcha_verify_url = 'https://www.google.com/recaptcha/api/siteverify';

	/**
	 * The name of the field containing Google ReCaptcha data.
	 *
	 * @since 1.1
	 *
	 * @var string
	 */
	public $captcha_field = 'g-recaptcha-response';

	/**
	 * The reply-and-mark-as-read action name.
	 */
	public $reply_and_mark_action = 'happyforms_reply_and_mark';

	/**
	 * The singleton constructor.
	 *
	 * @since 1.0
	 *
	 * @return HappyForms_Message_Controller
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function hook() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'add_role_capabilities' ) );
		add_action( 'parse_request', array( $this, 'admin_post' ) );
		add_action( 'admin_init', array( $this, 'admin_post' ) );
		add_action( 'delete_post', array( $this, 'delete_post' ) );

		// Core multi-step hooks
		add_action( 'happyforms_step', array( $this, 'default_submission_step' ) );
		// Submission preview and review
		add_action( 'happyforms_step', array( $this, 'preview_submission_step' ) );
		add_action( 'happyforms_step', array( $this, 'review_submission_step' ) );
		// Unique IDs
		add_action( 'happyforms_response_created', array( $this, 'response_stamp_unique_id' ), 10, 2 );
		add_action( 'happyforms_submission_success', array( $this, 'notice_append_unique_id' ), 10, 3 );
	}

	public function get_post_fields() {
		$fields = array(
			'post_title' => '',
			'post_type' => $this->post_type,
			'post_status' => 'publish',
		);

		return $fields;
	}

	public function get_meta_fields() {
		$fields = array(
			'form_id' => 0,
			'read' => false,
			'tracking_id' => '',
		);

		return $fields;
	}

	/**
	 * Get the default values of the message post object fields.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	private function get_defaults( $group = '' ) {
		$fields = array();

		switch ( $group ) {
			case 'post':
				$fields = $this->get_post_fields();
				break;
			case 'meta':
				$fields = $this->get_meta_fields();
				break;
			default:
				$fields = array_merge(
					$this->get_post_fields(),
					$this->get_meta_fields()
				);
				break;
		}

		return $fields;
	}

	/**
	 * Action: register the message custom post type.
	 *
	 * @hooked action init
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => __( 'Responses', 'happyforms' ),
			'singular_name'      => __( 'Response', 'happyforms' ),
			'edit_item'          => __( 'Edit response', 'happyforms' ),
			'view_item'          => __( 'View response', 'happyforms' ),
			'view_items'         => __( 'View Responses', 'happyforms' ),
			'search_items'       => __( 'Search Responses', 'happyforms' ),
			'not_found'          => __( 'No response found', 'happyforms' ),
			'not_found_in_trash' => __( 'No response found in Trash', 'happyforms' ),
			'all_items'          => __( 'All Responses', 'happyforms' ),
			'menu_name'          => __( 'All Responses', 'happyforms' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'query_var'           => true,
			'capability_type'     => 'page',
			'has_archive'         => false,
			'hierarchical'        => false,
			'supports'            => array( 'custom-fields' ),
		);

		register_post_type( $this->post_type, $args );
	}

	public function add_role_capabilities() {
		$admin_role = get_role( 'administrator' );
		$admin_role->add_cap( $this->capability );
	}

	/**
	 * Action: handle a form submission.
	 *
	 * @hooked action parse_request
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function admin_post() {
		// Exit early if we're not submitting any form
		if ( ! isset ( $_REQUEST['action'] ) || $this->submit_action != $_REQUEST['action'] ) {
			return;
		}

		// Check form_id parameter
		if ( ! isset ( $_REQUEST[$this->form_parameter] ) ) {
			wp_send_json_error();
		}

		$form_id = intval( $_REQUEST[$this->form_parameter] );

		// Validate nonce
		if ( ! isset( $_REQUEST[$this->nonce_name] )
			|| ! $this->verify_nonce( $_REQUEST[$this->nonce_name], $form_id ) ) {

			wp_send_json_error();
		}

		$form_controller = happyforms_get_form_controller();
		$form = $form_controller->get( $form_id );

		// Check if form found
		if ( ! $form || is_wp_error( $form ) ) {
			wp_send_json_error();
		}

		// Set form step
		$step = isset( $_REQUEST[$this->form_step_parameter] ) ?
			intval( $_REQUEST[$this->form_step_parameter] ) : 0;

		happyforms_get_session()->set_step( $step );

		// Validate honeypot
		if ( happyforms_get_form_controller()->has_spam_protection( $form ) ) {
			if ( ! $this->validate_honeypot( $form ) ) {
				wp_send_json_error();
			}
		}

		do_action( 'happyforms_step', $form );
	}

	public function default_submission_step( $form ) {
		if ( 'submit' !== happyforms_get_current_step( $form ) ) {
			return;
		}

		// Validate ReCaptcha
		if ( happyforms_get_form_controller()->has_recaptcha_protection( $form ) ) {
			$captcha_value = $this->validate_captcha( $form );

			if ( is_wp_error( $captcha_value ) ) {
				wp_send_json_error();
			}
		}

		$form_id = $form['ID'];
		$form_controller = happyforms_get_form_controller();
		$session = happyforms_get_session();
		$submission = $this->validate_submission( $form, $_REQUEST );
		$response = array();

		if ( false === $submission ) {
			// Add a general error notice at the top
			$session->add_error( $form_id, __( 'Your submission contains errors.', 'happyforms' ) );

			// Reset to start step
			$session->reset_step();

			// Render the form
			$response['html'] = $form_controller->render( $form );

			/**
			 * This action fires upon an invalid submission.
			 *
			 * @since 1.4
			 *
			 * @param WP_Error $submission Error data.
			 * @param array    $form   Current form data.
			 *
			 * @return void
			 */
			do_action( 'happyforms_submission_error', $submission, $form );

			// Send error response
			wp_send_json_error( $response );
		} else {
			// Add a general success notice at the top
			$session->add_notice( $form_id, html_entity_decode( $form['confirmation_message'] ) );

			// Reset to start step
			$session->reset_step();

			// Empty submitted values
			$session->clear_values();

			// Create message post
			$message_id = $this->create( $form, $submission );

			if ( ! is_wp_error( $message_id ) ) {
				// Update the unread badge
				$this->update_badge_transient();

				if ( ! empty( $form['redirect_url'] ) ) {
					$response['redirect'] = $form['redirect_url'];
				}

				$message = $this->get( $message_id );

				if ( 1 === intval( $form['receive_email_alerts'] ) ) {
					$this->email_owner_confirmation( $form, $message );
				}

				if ( 1 === intval( $form['send_confirmation_email'] ) ) {
					$this->email_user_confirmation( $form, $message );
				}

				if ( ! $form['save_entries'] ) {
					wp_delete_post( $message_id, true);
				}

				/**
				 * This action fires once a message is succesfully submitted.
				 *
				 * @since 1.4
				 *
				 * @param array $submission Submission data.
				 * @param array $form   Current form data.
				 *
				 * @return void
				 */
				do_action( 'happyforms_submission_success', $submission, $form, $message );

				// Render the form
				$response['html'] = $form_controller->render( $form );

				// Send success response
				$this->send_json_success( $response, $submission, $form );
			}
		}
	}

	public function preview_submission_step( $form ) {
		if ( 'preview' !== happyforms_get_current_step( $form ) ) {
			return;
		}

		// Validate ReCaptcha
		if ( happyforms_get_form_controller()->has_recaptcha_protection( $form ) ) {
			$captcha_value = $this->validate_captcha( $form );

			if ( is_wp_error( $captcha_value ) ) {
				wp_send_json_error();
			}
		}

		$form_id = $form['ID'];
		$form_controller = happyforms_get_form_controller();
		$session = happyforms_get_session();
		$submission = $this->validate_submission( $form, $_REQUEST );
		$response = array();

		if ( false === $submission ) {
			// Add a general error notice at the top
			$session->add_error( $form_id, __( 'Your submission contains errors.', 'happyforms' ) );

			// Reset to start step
			$session->reset_step();

			// Render the form
			$response['html'] = $form_controller->render( $form );

			// Send error response
			wp_send_json_error( $response );
		} else {
			// Advance step
			$session->next_step();

			// Render the form
			$response['html'] = $form_controller->render( $form );

			// Send success response
			$this->send_json_success( $response, $submission, $form );
		}
	}

	public function review_submission_step( $form ) {
		if ( 'review' !== happyforms_get_current_step( $form ) ) {
			return;
		}

		$form_id = $form['ID'];
		$form_controller = happyforms_get_form_controller();
		$session = happyforms_get_session();
		$submission = $this->validate_submission( $form, $_REQUEST );
		$response = array();

		if ( false === $submission ) {
			// Add a general error notice at the top
			$session->add_error( $form_id, __( 'Your submission contains errors.', 'happyforms' ) );
		}

		// Reset to start step
		$session->reset_step();

		// Render the form
		$response['html'] = $form_controller->render( $form );

		if ( false === $submission ) {
			// Send error response
			wp_send_json_error( $response );
		}

		// Send success response
		$this->send_json_success( $response, $submission, $form );
	}

	private function send_json_success( $response = array(), $submission = array(), $form = array() ) {
		$response = apply_filters( 'happyforms_json_response', $response, $submission, $form );

		wp_send_json_success( $response );
	}

	/**
	 * Action: update the unread badge upon message deletion.
	 *
	 * @since 1.1
	 *
	 * @hooked action delete_post
	 *
	 * @param int|string $post_id The ID of the message object.
	 *
	 * @return void
	 */
	public function delete_post( $post_id ) {
		$post = get_post( $post_id );

		if ( $this->post_type !== $post->post_type ) {
			return;
		}

		$this->update_badge_transient();
	}

	/**
	 * Verify a message nonce.
	 *
	 * @since 1.0
	 *
	 * @param string $nonce   The submitted value.
	 * @param string $form_id The ID of the form being submitted.
	 *
	 * @return boolean
	 */
	private function verify_nonce( $nonce, $form_id ) {
		return wp_verify_nonce( $nonce, $this->nonce_prefix . $form_id );
	}

	/**
	 * Verify honeypot data.
	 *
	 * @since 1.3
	 *
	 * @param array $form Current form data.
	 *
	 * @return boolean
	 */
	private function validate_honeypot( $form ) {
		$honeypot_name = $form['ID'] . 'single_line_text_-1';
		$validated = ! isset( $_REQUEST[$honeypot_name] );

		return $validated;
	}

	private function validate_captcha( $form ) {
		$secret_key = $form['captcha_secret_key'];
		$captcha_value = isset ( $_REQUEST[$this->captcha_field] ) ? $_REQUEST[$this->captcha_field] : '';
		$captcha_value = sanitize_text_field( $captcha_value );
		$request_body = array(
			'secret' => $secret_key,
			'response' => $captcha_value,
			'ip' => $_SERVER['REMOTE_ADDR'],
		);

		$request = wp_remote_post( $this->captcha_verify_url, array( 'body' => $request_body ) );
		$response = wp_remote_retrieve_body( $request );

		if ( empty( $response ) ) {
			return new WP_Error( 'captcha', 'captcha_invalid_configuration' );
		}

		$response = json_decode( $response, true );

		if ( ! $response['success'] ) {
			$configuration_errors = array_intersect( array(
				'missing-input-secret', 'invalid-input-secret', 'bad-request'
			), $response['error-codes'] );
			$value_errors = array_intersect( array(
				'missing-input-response', 'invalid-input-response'
			), $response['error-codes'] );
			if ( count( $configuration_errors ) > 0 ) {
				return new WP_Error( 'captcha', 'captcha_invalid_configuration' );
			} else if ( count( $value_errors ) > 0 ) {
				return new WP_Error( 'captcha', 'captcha_not_verified' );
			}
		}

		return $captcha_value;
	}

	public function validate_part( $form, $part, $request ) {
		$part_class = happyforms_get_part_library()->get_part( $part['type'] );

		if ( false !== $part_class ) {
			$part_id = $part['id'];
			$part_name = happyforms_get_part_name( $part, $form );
			$sanitized_value = $part_class->sanitize_value( $part, $form, $request );
			$validated_value = $part_class->validate_value( $sanitized_value, $part, $form );

			$session = happyforms_get_session();
			$session->add_value( $part_name, $sanitized_value );

			if ( ! is_wp_error( $validated_value ) ) {
				return $validated_value;
			} else {
				$session->add_error( $part_name, $validated_value->get_error_message() );
			}
		}

		return false;
	}

	public function validate_submission( $form, $request = array() ) {
		$submission = array();
		$is_valid = true;

		foreach( $form['parts'] as $part ) {
			$part_id = $part['id'];
			$validated_value = $this->validate_part( $form, $part, $request );

			if ( false !== $validated_value ) {
				$string_value = happyforms_stringify_part_value( $validated_value, $part, $form );
				$submission[$part_id] = $string_value;
			} else {
				$is_valid = false;
			}
		}

		return $is_valid ? $submission : false;
	}

	/**
	 * Create a new message post object.
	 *
	 * @since 1.0
	 *
	 * @param array $form       The message form data.
	 * @param array $submission The message form data.
	 *
	 * @return int|boolean
	 */
	public function create( $form, $submission ) {
		$defaults = $this->get_post_fields();
		$defaults_meta = $this->get_meta_fields();
		$message_meta = wp_parse_args( array(
			'form_id' => $form['ID'],
		), $defaults_meta );
		$message_meta = array_merge( $message_meta, $submission );
		$message_meta = happyforms_prefix_meta( $message_meta );
		$attrs = array_merge( $defaults, array(
			'meta_input' => $message_meta
		) );
		$message_id = wp_insert_post( wp_slash( $attrs ), true );

		wp_update_post( array(
			'ID' => $message_id,
			'post_title' => happyforms_get_message_title( $message_id ),
		) );

		do_action( 'happyforms_response_created', $message_id, $form );

		return $message_id;
	}

	public function response_stamp_unique_id( $response_id, $form ) {
		if ( intval( $form['unique_id'] ) ) {
			$increment = $form['unique_id_start_from'];
			$prefix = $form['unique_id_prefix'];
			$suffix = $form['unique_id_suffix'];
			$tracking_id = "{$prefix}{$increment}{$suffix}";

			happyforms_update_meta( $response_id, 'tracking_id', $tracking_id );
		}
	}

	public function notice_append_unique_id( $submission, $form, $message ) {
		if ( intval( $form['unique_id'] ) ) {
			$tracking_id = $message['tracking_id'];
			$notice = $form['confirmation_message'];
			$label = __( 'Tracking number', 'happyforms' );
			$notice = "{$notice}<span>{$label}: {$tracking_id}</span>";
			$notice = html_entity_decode( $notice );

			happyforms_get_session()->add_notice( $form['ID'], $notice );
		}
	}

	/**
	 * Get one or more message post objects.
	 *
	 * @since 1.0
	 *
	 * @param string $post_ids The IDs of the messages to retrieve.
	 *
	 * @return array
	 */
	public function get( $post_ids = '' ) {
		$query_params = array(
			'post_type'   => $this->post_type,
			'post_status' => 'any',
			'posts_per_page' => -1,
		);

		if ( ! empty( $post_ids ) ) {
			$query_params['post__in'] = is_numeric( $post_ids ) ? array( $post_ids ) : $post_ids;
		}

		$messages = get_posts( $query_params );
		$message_entries = array_map( array( $this, 'to_array'), $messages );

		if ( is_numeric( $post_ids ) ) {
			if ( count( $message_entries ) > 0 ) {
				return $message_entries[0];
			} else {
				return false;
			}
		}

		return $message_entries;
	}

	/**
	 * Get all messages relative to a form.
	 *
	 * @since 1.0
	 *
	 * @param string $form_id The ID of the form.
	 *
	 * @return array
	 */
	public function get_by_form( $form_id ) {
		$query_params = array(
			'post_type'   => $this->post_type,
			'post_status' => 'any',
			'posts_per_page' => -1,
			'meta_query' => array( array(
				'field' => '_happyforms_form_id',
				'value' => $form_id,
			) )
		);

		$messages = get_posts( $query_params );
		$message_entries = array_map( array( $this, 'to_array'), $messages );

		return $message_entries;
	}

	/**
	 * Turn a message post object into an array.
	 *
	 * @since 1.0
	 *
	 * @param WP_Post $message The message post object.
	 *
	 * @return array
	 */
	public function to_array( $message ) {
		$message_array = $message->to_array();
		$message_meta = happyforms_unprefix_meta( get_post_meta( $message->ID ) );
		$form_id = $message_meta['form_id'];
		$form = happyforms_get_form_controller()->get( $form_id );
		$meta_defaults = $this->get_meta_fields();
		$message_array = array_merge( $message_array, wp_parse_args( $message_meta, $meta_defaults ) );
		$message_array['parts'] = array();

		if ( $form ) {
			foreach ( $form['parts'] as $part_data ) {
				$part = happyforms_get_part_library()->get_part( $part_data['type'] );
				$part_id = $part_data['id'];
				$part_value = $part->get_default_value( $part_data );

				if ( isset( $message_meta[$part_id] ) ) {
					$part_value = $message_meta[$part_id];
				}

				$message_array['parts'][$part_id] = $part_value;
				unset( $message_array[$part_id] );
			}
		}

		return $message_array;
	}

	/**
	 * Send a confirmation email to the site owner.
	 *
	 * @since 1.0
	 *
	 * @param array  $form    The message form data.
	 * @param string $message The message contents.
	 *
	 * @return void
	 */
	private function email_owner_confirmation( $form, $message ) {
		if ( ! empty( $form['email_recipient'] )
			&& ! empty( $form['alert_email_subject'] ) ) {

			// Compose an email message
			$email_message = new HappyForms_Email_Message( $message );
			$to = explode( ',', $form['email_recipient'] );

			$email_message->set_to( $to[0] );

			if ( count( $to ) > 1 ) {
				$email_message->set_ccs( array_slice( $to, 1 ) );
			}

			$email_message->set_subject( $form['alert_email_subject'] );

			// Compose content with submit data
			$content_lines = array();

			foreach ( $form['parts'] as $part_data ) {
				$visible = apply_filters( 'happyforms_message_part_visible', true, $part_data, $form, $message );

				if ( ! $visible ) {
					continue;
				}

				$part_id = $part_data['id'];
				$label = happyforms_get_email_part_label( $message, $part_data, $form );
				$value = happyforms_get_email_part_value( $message, $part_data, $form );

				if ( isset( $part_data['use_as_subject'] ) && 1 === $part_data['use_as_subject'] ) {
					$email_message->set_subject( $value );
				} else {
					$content_lines[] = "<b>{$label}</b><br>{$value}";
				}
			}

			// Append the tracking number for this message,
			// if present.
			if ( intval( $form['unique_id'] ) ) {
				$label = __( 'Tracking number', 'happyforms' );
				$tracking_id = $message['tracking_id'];
				$content_lines[] = "<b>{$label}</b><br>{$tracking_id}";
			}

			// Add a From header and a reply-and-mark-as-read link
			// if the form includes an email part
			$email_part = happyforms_get_form_controller()->get_first_part_by_type( $form, 'email' );

			if ( false !== $email_part ) {
				$part_id = $email_part['id'];
				$from = happyforms_get_message_part_value( $message['parts'][$part_id] );

				// Reply and mark as read link
				if ( 1 === intval( $form['email_mark_and_reply'] ) ) {
					$content_lines[] = sprintf(
						'<a href="%s">%s</a>',
						happyforms_get_reply_and_mark_link( $message['ID'] ),
						__( 'Reply to this message and mark it as read', 'happyforms' )
					);
				}

				$email_message->set_reply_to( $from );
			}

			$content = implode( '<br><br>', $content_lines );
			$email_message->set_content( $content );

			$email_message->send();
		}
	}

	/**
	 * Send a confirmation email to the user submitting the form.
	 *
	 * @since 1.0
	 *
	 * @param array  $form    The message form data.
	 * @param string $message The message contents.
	 *
	 * @return void
	 */
	private function email_user_confirmation( $form, $message ) {
		$email_part = happyforms_get_form_controller()->get_first_part_by_type( $form, 'email' );

		if ( false !== $email_part
			&& ! empty( $form['confirmation_email_subject'] )
			&& ! empty( $form['confirmation_email_content'] )
			&& ! empty( $form['email_recipient'] ) ) {

			// Compose an email message
			$email_message = new HappyForms_Email_Message( $message );
			$senders = explode( ',', $form['email_recipient'] );
			$name = $form['confirmation_email_from_name'];
			$from = $senders[0];
			$reply_to = $senders;

			$email_message->set_from( $from, $name );
			$email_message->set_reply_to( $senders );
			$email_message->set_subject( $form['confirmation_email_subject'] );
			$part_id = $email_part['id'];
			$to = happyforms_get_message_part_value( $message['parts'][$part_id] );
			$email_message->set_to( $to );

			$content = html_entity_decode( $form['confirmation_email_content'] );

			if ( intval( $form['unique_id'] ) ) {
				$label = __( 'Tracking number', 'happyforms' );
				$tracking_id = $message['tracking_id'];
				$content .= "<br><br>{$label}: {$tracking_id}";
			}

			$email_message->set_content( $content );

			$email_message->send();
		}
	}

	/**
	 * Update the counter in unread messages badge.
	 *
	 * @since 1.1
	 *
	 * @return void
	 */
	public function update_badge_transient() {
		$forms = happyforms_get_form_controller()->get( array(), true );

		$messages = get_posts( array(
			'post_type' => $this->post_type,
			'post_status' => 'any',
			'posts_per_page' => -1,
			'fields' => 'ids',
			'meta_query' => array( array(
				'key' => '_happyforms_read',
				'value' => 1,
				'compare' => '!=',
			), array(
				'key' => '_happyforms_form_id',
				'value' => $forms,
				'compare' => 'IN',
			) )
		) );

		if ( count( $messages ) > 0 && count( $forms ) > 0 ) {
			set_transient( $this->unread_transient, count( $messages ), 0 );
		} else {
			delete_transient( $this->unread_transient );
		}
	}

}

if ( ! function_exists( 'happyforms_get_message_controller' ) ):
/**
 * Get the HappyForms_Message_Controller class instance.
 *
 * @since 1.0
 *
 * @return HappyForms_Message_Controller
 */
function happyforms_get_message_controller() {
	return HappyForms_Message_Controller::instance();
}

endif;

/**
 * Initialize the HappyForms_Message_Controller class immediately.
 */
happyforms_get_message_controller();
