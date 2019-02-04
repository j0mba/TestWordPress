<?php

class HappyForms_Message_Admin {

	/**
	 * The singleton instance.
	 *
	 * @since 1.0
	 *
	 * @var HappyForms_Message_Admin
	 */
	private static $instance;

	/**
	 * The form the form filter is pointing to.
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	private $current_form;

	/**
	 * The IDs of the forms the form filter is pointing to.
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	private $current_form_ids = array();

	/**
	 * The name of the Column Count option in the
	 * Screen Options tab.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	private $column_count_option = 'happyforms-message-admin-col-count';

	/**
	 * The default amount of rows to show.
	 *
	 * @var int
	 */
	private $row_count = 20;

	/**
	 * The default amount of columns to show.
	 *
	 * @var int
	 */
	private $column_count = 1;

	/**
	 * The singleton constructor.
	 *
	 * @since 1.0
	 *
	 * @return HappyForms_Message_Admin
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
		$controller = happyforms_get_message_controller();
		$post_type = $controller->post_type;

		add_action( 'parse_request', array( $this, 'parse_request' ) );
		add_action( 'load-post.php', array( $this, 'reply_and_mark' ) );
		add_action( 'admin_head', array( $this, 'output_styles' ) );
		add_action( 'admin_head', array( $this, 'screen_title' ) );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );
		add_action( 'load-edit.php', array( $this, 'define_screen_settings' ) );
		add_filter( 'screen_settings', array( $this, 'render_screen_settings' ), 10, 2 );
		add_filter( 'the_title', array( $this, 'filter_row_title' ), 10, 2 );
		add_filter( "manage_{$post_type}_posts_columns", array( $this, 'column_headers' ), PHP_INT_MAX );
		add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'column_content' ), 10, 2 );
		add_filter( 'post_date_column_status', array( $this, 'post_date_column_status' ) );
		add_filter( 'post_class', array( $this, 'post_class' ), 10, 3 );
		add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ), PHP_INT_MAX );
		add_filter( 'parse_query', array( $this, 'parse_query' ) );
		add_filter( "bulk_actions-edit-{$post_type}", array( $this, 'bulk_actions' ) );
		add_filter( "handle_bulk_actions-edit-{$post_type}", array( $this, 'handle_bulk_actions' ), 10, 3 );
		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 10, 2 );
		add_action( 'edit_form_after_title', array( $this, 'edit_screen' ) );
		add_action( 'add_meta_boxes', array( $this, 'setup_metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
		add_filter( 'admin_footer_text', 'happyforms_admin_footer' );

		// CSV part value formatting
		add_filter( 'happyforms_get_csv_value', array( $this, 'get_csv_value' ), 10, 4 );
	}

	/**
	 * Action: set the current form and form ids
	 * depending on the value of the form filter.
	 *
	 * @since 1.0
	 *
	 * @hooked action parse_request
	 *
	 * @return void
	 */
	public function parse_request() {
		$form_id = isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : 0;

		if ( $form_id ) {
			$this->current_form = happyforms_get_form_controller()->get( $form_id );
			$this->current_form_ids = array( $form_id );
		} else {
			$this->current_form_ids = happyforms_get_form_controller()->get( array(), true );
		}
	}

	/**
	 * Handles a reply-and-mark-as-read link.
	 *
	 * @return void
	 */
	public function reply_and_mark() {
		$message_controller = happyforms_get_message_controller();
		$action = $message_controller->reply_and_mark_action;

		if ( ! isset( $_REQUEST[ $action ] )
			|| 1 !== intval( $_REQUEST[ $action ] ) ) {
			return;
		}

		$post = get_post( $_REQUEST['post'] );

		if ( ! current_user_can( $message_controller->capability , $post->ID ) ) {
			wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
		}

		if ( $message_controller->post_type !== $post->post_type ) {
			return;
		}

		happyforms_update_meta( $post->ID, 'read', 1 );

		$form_controller = happyforms_get_form_controller();
		$form = $form_controller->get( happyforms_get_meta( $post->ID, 'form_id', true ) );

		if ( ! $form ) {
			return;
		}

		$email_part = $form_controller->get_first_part_by_type( $form, 'email' );
		$email_part_id = $email_part['id'];

		if ( ! $email_part ) {
			return;
		}

		$message = $message_controller->get( $post->ID );
		$email = happyforms_get_message_part_value( $message['parts'][$email_part_id] );
		$url = "mailto: {$email}";

		if ( wp_redirect( $url ) ) {
			exit;
		}
	}

	/**
	 * Action: output styles in the admin header of the Messages screen.
	 *
	 * @since 1.0
	 *
	 * @hooked action admin_head
	 *
	 * @return void
	 */
	public function output_styles() {
		global $pagenow;
		$post_type = happyforms_get_message_controller()->post_type;

		if ( 'edit.php' === $pagenow ) : ?>
		<style>
		fieldset:not(.screen-options),
		fieldset.screen-options,
		ul.subsubsub {
			display: none;
		}
		</style>
		<?php endif;
	}

	/**
	 * Action: customize the edit screen title.
	 *
	 * @since 1.0
	 *
	 * @hooked action admin_head
	 *
	 * @return void
	 */
	public function screen_title() {
		global $pagenow, $post, $title;

		$post_type = happyforms_get_message_controller()->post_type;

		if ( 'post.php' === $pagenow && $post_type === get_post_type() ) {
			$title = __( 'View Response #' ) . $post->ID;
		}
	}

	/**
	 * Filter: tweak the text of the message post actions admin notices.
	 *
	 * @since 1.0
	 *
	 * @hooked filter post_updated_messages
	 *
	 * @param array $messages The messages configuration.
	 *
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		$post_type = happyforms_get_message_controller()->post_type;
		$permalink = get_permalink();
		$preview_url = get_preview_post_link();
		$view_form_link_html = sprintf(
			' <a href="%1$s">%2$s</a>',
			esc_url( $permalink ),
			__( 'View response' )
		);
		$preview_post_link_html = sprintf(
			' <a target="_blank" href="%1$s">%2$s</a>',
			esc_url( $preview_url ),
			__( 'Preview response' )
		);

		$messages[$post_type] = array(
			'',
			__( 'Response updated.' ) . $view_form_link_html,
			__( 'Custom field updated.' ),
			__( 'Custom field deleted.' ),
			__( 'Response updated.' ),
			isset($_GET['revision']) ? sprintf( __( 'Response restored to revision from %s.' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			__( 'Response published.' ) . $view_form_link_html,
			__( 'Response saved.' ),
			__( 'Response submitted.' ),
			__( 'Response scheduled.' ),
			__( 'Response draft updated.' ) . $preview_post_link_html,
		);

		return $messages;
	}

	/**
	 * Filter: tweak the text of the message post
	 * bulk actions admin notices.
	 *
	 * @since 1.0
	 *
	 * @hooked filter bulk_post_updated_messages
	 *
	 * @param array $messages The messages configuration.
	 * @param int   $count    The amount of posts for each bulk action.
	 *
	 * @return array
	 */
	public function bulk_post_updated_messages( $messages, $count ) {
		$post_type = happyforms_get_message_controller()->post_type;

		$messages[$post_type] = array(
			'updated'   => _n( '%s response updated.', '%s responses updated.', $count['updated'] ),
			'locked'    => _n( '%s response not updated, somebody is editing it.', '%s responses not updated, somebody is editing them.', $count['locked'] ),
			'deleted'   => _n( '%s response permanently deleted.', '%s responses permanently deleted.', $count['deleted'] ),
			'trashed'   => _n( '%s response moved to the Trash.', '%s responses moved to the Trash.', $count['trashed'] ),
			'untrashed' => _n( '%s response restored from the Trash.', '%s responses restored from the Trash.', $count['untrashed'] ),
		);

		return $messages;
	}

	/**
	 * Action: configure additional options for the Screen Options tab.
	 *
	 * @since 1.0
	 *
	 * @hooked action load-edit.php
	 *
	 * @return void
	 */
	public function define_screen_settings() {
		if ( isset( $_REQUEST[$this->column_count_option] ) ) {
			$column_count = max( intval( $_REQUEST[$this->column_count_option] ), 1 );
			update_user_option( get_current_user_id(), $this->column_count_option, $column_count, true );
		}

		$post_type = happyforms_get_message_controller()->post_type;
		$row_count_option = 'edit_' . $post_type . '_per_page';

		if ( isset( $_REQUEST[$row_count_option] ) ) {
			$row_count = max( intval( $_REQUEST[$row_count_option] ), 1 );
			update_user_option( get_current_user_id(), $row_count_option, $row_count, true );
		}

		$row_count = get_user_option( $row_count_option, get_current_user_id() );
		$column_count = get_user_option( $this->column_count_option, get_current_user_id() );
		$row_count = ( false !== $row_count ) ? $row_count : $this->row_count;
		$column_count = ( false !== $column_count ) ? $column_count : $this->column_count;
		$this->row_count = max( intval( $row_count ), 1 );
		$this->column_count = max( intval( $column_count ), 1 );
	}

	/**
	 * Filter: output additional options in the Screen Options tab.
	 *
	 * @since 1.0
	 *
	 * @hooked filter screen_settings
	 *
	 * @param array     $settings The currently configured options.
	 * @param WP_Screen $count    The current screen object.
	 *
	 * @return void
	 */
	public function render_screen_settings( $settings, $screen ) {
		$post_type = happyforms_get_message_controller()->post_type;

		if ( 'edit-' . $post_type !== $screen->id ) {
			return $settings;
		}

		ob_start();
		?>
		<fieldset style="display: block;">
			<legend><?php _e( 'Pagination', 'happyforms' ); ?></legend>
			<label for=""><?php _e( 'Number of responses per page:', 'happyforms' ); ?></label>
			<input type="number" min="1" max="99" maxlength="2" name="edit_<?php echo esc_attr( $post_type ); ?>_per_page" value="<?php echo esc_attr( $this->row_count ); ?>">
		</fieldset>
		<fieldset style="display: block;">
			<legend><?php _e( 'Fields', 'happyforms' ); ?></legend>
			<label for=""><?php _e( 'Number of response fields to show:', 'happyforms' ); ?></label>
			<input type="number" min="1" max="99" maxlength="2" name="<?php echo esc_attr( $this->column_count_option ); ?>" value="<?php echo esc_attr( $this->column_count ); ?>">
			<input type="hidden" name="wp_screen_options[option]" value="<?php echo esc_attr( $this->column_count_option ); ?>">
			<input type="hidden" name="wp_screen_options[value]" value="1">
		</fieldset>
		<?php
		return ob_get_clean();
	}

	/**
	 * Filter: filter the column headers for the
	 * All Messages admin screen table.
	 *
	 * @since 1.0
	 *
	 * @hooked filter manage_happyforms-message_posts_columns
	 *
	 * @param array $columns  The original table headers.
	 *
	 * @return array          The filtered table headers.
	 */
	public function column_headers( $columns ) {
		$cb_column = $columns['cb'];
		$date_column = $columns['date'];
		$columns = array( 'cb' => $cb_column );

		$forms = happyforms_get_form_controller()->get();
		$part_lists = wp_list_pluck( $forms, 'parts' );
		$part_counts = array_map( 'count', $part_lists );
		$max_column_count = $this->column_count;

		if ( count( $forms ) > 0 ) {
			$max_column_count = min( max( $part_counts ), $this->column_count );
		}

		for ( $column = 0; $column < $max_column_count; $column ++ ) {
			$header = "column_{$column}";
			$columns[$header] = '';
		}

		if ( $this->current_form && $this->current_form['unique_id'] ) {
			$columns['unique_id'] = __( 'Tracking number', 'happyforms' );
		}

		$columns['form'] = __( 'Form', 'happyforms' );
		$columns = $columns + array( 'date' => $date_column );

		if ( ! $this->current_form || empty( $this->current_form['parts'] ) ) {
			return $columns;
		}

		for ( $column = 0; $column < $this->column_count; $column ++ ) {
			if ( $column < count( $this->current_form['parts'] ) ) {
				$header = "column_{$column}";
				$columns[$header] = $this->current_form['parts'][$column]['label'];
			}
		}

		/**
		 * Filter the column headers of responses admin table.
		 *
		 * @since 1.4.5
		 *
		 * @param array  $columns Current column headers.
		 *
		 * @return array
		 */
		$columns = apply_filters( 'happyforms_manage_response_column_headers', $columns );

		return $columns;
	}

	/**
	 * Filter: output the columns content for the
	 * All Messages admin screen table.
	 *
	 * @since 1.0
	 *
	 * @hooked filter manage_happyforms-message_posts_custom_column
	 *
	 * @param array      $column   The current column header.
	 * @param int|string $id       The current message post object ID.
	 *
	 * @return void
	 */
	public function column_content( $column, $id ) {
		$message = happyforms_get_message_controller()->get( $id );
		$form = happyforms_get_form_controller()->get( $message['form_id'] );

		switch( $column ) {
			case 'form':
				if ( $form ) {
					$link = sprintf(
						'<a href="%s">%s</a>',
						happyforms_get_form_edit_link( $form['ID'] ),
						$form['post_title']
					);
					echo $link;
				}
				break;

			case 'unique_id':
				echo $message['tracking_id'];
				break;

			default:
				if ( $form ) {
					$column_index = preg_match( '/column_(\d+)?/', $column, $matches );

					if ( $column_index ) {
						$column_index = intval( $matches[1] );
					}

					if ( count( $form['parts'] ) > $column_index ) {
						$part_id = $form['parts'][$column_index]['id'];

						if ( isset( $message['parts'][$part_id] ) ) {
							echo happyforms_get_message_part_value( $message['parts'][$part_id] );
						}
					}
				}

				break;
		}
	}

	/**
	 * Filter: silence the standard date column content.
	 *
	 * @since 1.0
	 *
	 * @hooked filter post_date_column_status
	 *
	 * @return void
	 */
	public function post_date_column_status() {
		return '';
	}

	/**
	 * Filter: add custom HTML classes to message entries
	 * in the All Form admin screen table to represent
	 * read/unread status.
	 *
	 * @since 1.0
	 *
	 * @hooked filter post_date_column_status
	 *
	 * @param array      $class   Array of post classes.
	 * @param array      $classes Array of additional post classes.
	 * @param int|string $id      The message post object ID.
	 *
	 * @return array
	 */
	public function post_class( $class, $classes, $id ) {
		$message = happyforms_get_message_controller()->get( $id );

		if ( ! $message['read'] ) {
			$classes[] = 'happyforms-message-unread';
		}

		return $classes;
	}

	/**
	 * Filter: tweak the Title column content in the
	 * All Messages admin screen table.
	 *
	 * @since 1.0
	 *
	 * @hooked filter the_title
	 *
	 * @param string $title The current post object title.
	 * @param int    $id    The message post object ID.
	 *
	 * @return string
	 */
	public function filter_row_title( $title, $id ) {
		$message = happyforms_get_message_controller()->get( $id );
		$form = happyforms_get_form_controller()->get( $message['form_id'] );

		if ( empty( $form['parts'] ) ) {
			return $title;
		}

		$first_form_part = $form['parts'][0];
		$title = $first_form_part['label'];

		return $title;
	}

	/**
	 * Action: output the Form filter dropdown
	 * above the All Messages admin screen table.
	 *
	 * @since 1.0
	 *
	 * @hooked action restrict_manage_posts
	 *
	 * @return void
	 */
	public function restrict_manage_posts( $post_type ) {
		if ( happyforms_get_message_controller()->post_type === $post_type ) {
			// Remove any previous output.
			ob_clean();
			$forms = happyforms_get_form_controller()->get();
			$form_id = isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : '';
			?>
			<select name="form_id" id="">
				<option value=""><?php _e( 'All forms', 'happyforms' ); ?></option>
				<?php foreach( $forms as $form ): ?>
				<option value="<?php echo esc_attr( $form['ID'] ); ?>" <?php selected( $form_id, $form['ID'] ); ?>><?php echo esc_html( $form['post_title'] ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php

			/**
			 * Output additional content in the
			 * responses admin table filters area.
			 *
			 * @since 1.4.5
			 *
			 * @param string $post_type Response post type.
			 *
			 * @return void
			 */
			do_action( 'happyforms_restrict_manage_responses', $post_type );
		}
	}

	/**
	 * Filter: modify the post query to account for
	 * the Form filter.
	 *
	 * @since 1.0
	 *
	 * @hooked filter parse_query
	 *
	 * @param WP_Query $query The current post query.
	 *
	 * @return void
	 */
	public function parse_query( $query ) {
		global $pagenow;

		if ( 'edit.php' !== $pagenow  ) {
			return;
		}

		$query_vars = &$query->query_vars;
		$query_vars['meta_query'] = array();
		$post_type = happyforms_get_message_controller()->post_type;

		if ( $post_type === $query->query['post_type'] ) {
			$query_vars['meta_key'] = '_happyforms_form_id';

			if ( count( $this->current_form_ids ) > 0 ) {
				$query_vars['meta_value'] = $this->current_form_ids;
				$query_vars['meta_compare'] = 'IN';
				$query_vars['meta_type'] = 'NUMERIC';
			} else {
				$query_vars['meta_value'] = 0;
				$query_vars['meta_compare'] = '=';
			}
		}
	}

	/**
	 * Filter: add custom bulk actions for the
	 * All Messages admin screen table.
	 *
	 * @since 1.0
	 *
	 * @hooked filter bulk_actions-edit-happyforms-message
	 *
	 * @param array $actions Original bulk actions.
	 *
	 * @return array
	 */
	public function bulk_actions( $actions ) {
		$actions = array(
			'mark_read' => __( 'Mark read', 'happyforms' ),
			'mark_unread' => __( 'Mark unread', 'happyforms' ),
			'delete' => __( 'Trash', 'happyforms' ),
		);

		if ( $this->current_form ) {
			$actions['export_csv'] = __( 'Export to CSV', 'happyforms' );
		}

		return $actions;
	}

	/**
	 * Filter: handle messages custom bulk actions.
	 *
	 * @since 1.0
	 *
	 * @hooked filter handle_bulk_actions-edit-happyforms-message
	 *
	 * @param string $redirect_to The url to redirect to
	 *                            after actions have been handled.
	 * @param string $action      The current bulk action.
	 * @param array  $ids         The array of message post object IDs.
	 *
	 * @return string
	 */
	public function handle_bulk_actions( $redirect_to, $action, $ids ) {
		switch( $action ) {
			case 'mark_read':
				foreach ( $ids as $id ) {
					happyforms_update_meta( $id, 'read', 1 );
				}
				happyforms_get_message_controller()->update_badge_transient();
				break;
			case 'mark_unread':
				foreach ( $ids as $id ) {
					happyforms_update_meta( $id, 'read', '' );
				}
				happyforms_get_message_controller()->update_badge_transient();
				break;
			case 'delete':
				foreach ( $ids as $id ) {
					wp_delete_post( $id, true );
				}
				break;
			case 'export_csv':
				$this->export_csv( $ids );
				break;
		}

		return $redirect_to;
	}

	/**
	 * Filter: filter the row actions contents for the
	 * All Messages admin screen table.
	 *
	 * @since 1.0
	 *
	 * @hooked filter post_row_actions
	 *
	 * @param array   $actions The original array of action contents.
	 * @param WP_Post $post    The current post object.
	 *
	 * @return array           The filtered array of action contents.
	 */
	public function row_actions( $actions, $post ) {
		$post_type = happyforms_get_message_controller()->post_type;

		if ( $post->post_type === $post_type ) {
			$actions = array();
			$link_template = '<a href="%s">%s</a>';
			$links = array(
				'edit' => array(
					__( 'View', 'makeplus' ),
					get_edit_post_link()
				),
				'trash' => array(
					__( 'Delete Permanently', 'makeplus' ),
					get_delete_post_link( $post->ID, '', true )
				),
			);

			foreach( $links as $key => $values ) {
				$actions[$key] = sprintf( $link_template, $values[1], $values[0] );
			}
		}

		return $actions;
	}

	/**
	 * Export the message post objects with the given IDs as CSV.
	 *
	 * @since 1.0
	 *
	 * @param array $ids The message post object IDs.
	 *
	 * @return void
	 */
	private function export_csv( $ids = array() ) {
		global $wpdb;

		$this->parse_request();

		$controller = happyforms_get_message_controller();
		$messages = get_posts( array(
			'post_type' => $controller->post_type,
			'posts_per_page' => -1,
			'post__in' => $ids,
			'post_status' => 'any',
		) );

		$parts = wp_list_pluck( $this->current_form['parts'], 'id' );
		$parts = array_combine( $parts, $this->current_form['parts'] );
		$messages = array_map( array( $controller, 'to_array' ), $messages );
		$headers = array();
		$rows = array();

		foreach ( $parts as $part_id => $part ) {
			$headers[$part_id] = happyforms_get_csv_header( $part );
		}

		$headers = apply_filters( 'happyforms_csv_headers', $headers, $this->current_form );

		foreach( $messages as $message ) {
			$row = array();

			foreach( $headers as $part_id => $header ) {
				$value = $message['parts'][$part_id];
				$part = $parts[$part_id];
				$row[] = happyforms_get_csv_value( $value, $message, $part, $this->current_form );
			}

			$rows[] = $row;
		}

		// Append tracking numbers if needed
		if ( intval( $this->current_form['unique_id'] ) ) {
			$headers[] = __( 'Tracking number', 'happyforms' );

			foreach( $rows as $r => $row ) {
				$row[] = $messages[$r]['tracking_id'];
				$rows[$r] = $row;
			}
		}

		$filename = 'messages.csv';
		$output = fopen( 'php://output', 'w' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );
		fputcsv( $output, array_values( $headers ) );

		foreach( $rows as $row ) {
			fputcsv( $output, array_values( $row ) );
		}

		exit();
	}

	public function get_csv_value( $value, $message, $part, $form ) {
		switch( $part[ 'type' ] ) {
			case 'table':
				$value = str_replace( '<br>', "\n", $value );
				$value = strip_tags( $value );
				break;
			default:
				break;
		}

		return $value;
	}

	private function setup_message_navigation( $post_id, $form_id ) {
		global $happyforms_message_nav;

		$post_type = happyforms_get_message_controller()->post_type;

		$posts = get_posts( array(
			'post_type' => $post_type,
			'post_status' => 'any',
			'posts_per_page' => -1,
			'orderby' => 'ID',
			'order' => 'ASC',
			'fields' => 'ids',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => '_happyforms_form_id',
					'value' => $form_id,
				),
				array(
					'key' => '_happyforms_read',
					'value' => '',
				),
			)
		) );

		$previous_id = -1;
		$next_id = -1;

		// Find previous post ID
		for ( $p = 0; $p < count( $posts ); $p ++ ) {
			if ( $posts[$p] >= $post_id ) {
				break;
			}

			$previous_id = $posts[$p];
		}

		// Find next post ID
		for ( $p = 0; $p < count( $posts ); $p ++ ) {
			if ( $posts[$p] > $post_id ) {
				$next_id = $posts[$p];
				break;
			}
		}

		$happyforms_message_nav = array( $post_id );

		if ( $previous_id ) {
			array_unshift( $happyforms_message_nav, $previous_id );
		}

		if ( $next_id ) {
			array_push( $happyforms_message_nav, $next_id );
		}
	}

	/**
	 * Action: output custom markup for the
	 * Message Edit admin screen.
	 *
	 * @since 1.0
	 *
	 * @hooked action edit_form_after_title
	 *
	 * @param WP_Post $post The message post object.
	 *
	 * @return void
	 */
	public function edit_screen( $post ) {
		global $message, $form;

		$message = happyforms_get_message_controller()->get( $post->ID );
		$form = happyforms_get_form_controller()->get( $message['form_id'] );
		$this->setup_message_navigation( $post->ID, $form['ID'] );

		require_once( happyforms_get_include_folder() . '/core/templates/admin-message-edit.php' );
	}

	public function setup_metaboxes( $post_type ) {
		global $wp_meta_boxes;

		// Clear standard metaboxes
		$wp_meta_boxes[$post_type] = array();

		add_meta_box(
			'happyforms-message-details',
			__( 'Details' ),
			array( $this, 'metabox_message_details' ),
			$post_type,
			'side',
			'high'
		);
	}

	public function metabox_message_details( $post, $metabox ) {
		global $message, $form;

		$message_status = happyforms_get_meta( $post->ID, 'read', true );
		?>
		<div class="misc-pub-section happyforms-message-form">
			<span>
				<i class="logo dashicons dashicons-format-status"></i> <?php _e( 'Form', 'happyforms' ); ?>: <a href="<?php echo happyforms_get_form_edit_link( $form['ID'] ); ?>"><?php echo esc_html( $form['post_title'] ); ?></a>
			</span>
		</div>
		<div class="misc-pub-section curtime misc-pub-curtime">
			<span id="timestamp">
				<?php _e( 'Submitted on', 'happyforms' ); ?>: <b><?php echo date_i18n( __( 'M j, Y @ H:i' ), strtotime( $post->post_date ) ); ?></b>
			</span>
		</div>
		<div class="misc-pub-section misc-pub-visibility" id="visibility">
			<input type="hidden" name="message-status" value="<?php echo ( $message_status ) ? 1 : 0; ?>">
			<?php _e( 'Status', 'happyforms' ); ?>: <strong><?php echo ( $message_status ) ? __( 'Read', 'happyforms' ) : __( 'Unread', 'happyforms' ); ?></strong> <a href="#" id="happyforms-message-status-toggle"><?php echo ( $message_status ) ? __( 'Mark unread', 'happyforms' ) : __( 'Mark read', 'happyforms' ); ?></a>
		</div>

		<div class="misc-pub-section misc-pub-trash">
			<span id="trash">
				<a href="<?php echo get_delete_post_link( $post->ID, '', true ); ?>"><?php _e( 'Trash', 'happyforms' ); ?></a>
			</span>
		</div>
		<?php
	}

	public function save_post( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['message-status'] ) ) {
			$value = intval( $_POST['message-status'] );

			happyforms_update_meta( $post_id, 'read', 1 === $value && 1 || '' );
			happyforms_get_message_controller()->update_badge_transient();
		}
	}

}

/**
 * Initialize the HappyForms_Message_Admin class immediately.
 */
HappyForms_Message_Admin::instance();
