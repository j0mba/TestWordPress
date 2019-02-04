<?php

class HappyForms_Migrations {

	/**
	 * The singleton instance.
	 *
	 * @var HappyForms_Migrations
	 */
	private static $instance;

	private $migrations;

	/**
	 * The name of the version option entry.
	 *
	 * @var string
	 */
	public $option = 'happyforms-data-version';

	/**
	 * The singleton constructor.
	 *
	 * @return HappyForms_Migrations
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		$this->add_migration( '1.0', array( $this, 'migrate_1_0' ) );

		$this->migrate();
	}

	public function get_current_version() {
		$version = get_option( $this->option, '0' );

		return $version;
	}

	public function update_current_version( $version = '0' ) {
		update_option( $this->option, $version );
	}

	public function add_migration( $version, $callback ) {
		$this->migrations[$version] =
			isset( $this->migrations[$version] ) ?
			$this->migrations[$version] :
			array();

		$this->migrations[$version][] = $callback;
	}

	public function migrate() {
		$current_version = $this->get_current_version();

		uksort( $this->migrations, 'version_compare' );

		foreach( $this->migrations as $version => $migrations ) {
			if ( version_compare( $version, $current_version, '>' ) ) {
				foreach( $migrations as $callback ) {
					if ( is_callable( $callback ) ) {
						call_user_func( $callback, $version, $current_version );
					}
				}
			}

			$current_version = $version;
		}

		$this->update_current_version( $current_version );
	}

	public function migrate_1_0( $version, $current_version ) {
		global $wpdb;

		$form_controller = happyforms_get_form_controller();
		$message_controller = happyforms_get_message_controller();
		$forms = $form_controller->get();

		// Migrate forms
		foreach( $forms as $form ) {
			$form_id = $form['ID'];
			$fields = array_keys( $form_controller->get_meta_fields() );

			if ( 0 === count( $fields ) ) {
				continue;
			}

			$fields = array_merge( $fields, $form['layout'] );
			$fields = '(\'' . implode( '\', \'', $fields ) . '\')';

			$sql = "
				UPDATE $wpdb->postmeta meta JOIN $wpdb->posts posts
				ON meta.post_id = posts.ID
				SET meta.meta_key = CONCAT('_happyforms_', meta.meta_key)
				WHERE posts.ID = $form_id
				AND meta.meta_key IN $fields
				";

			$wpdb->query( $sql );
		}

		// Migrate messages
		foreach( $forms as $form ) {
			$form_id = $form['ID'];
			$messages = $message_controller->get_by_form( $form_id );

			if ( 0 === count( $messages ) ) {
				continue;
			}

			$message_ids = wp_list_pluck( $messages, 'ID' );
			$message_ids = implode( ', ', $message_ids );
			$parts = wp_list_pluck( $form['parts'], 'id' );
			$fields = array_keys( $message_controller->get_meta_fields() );
			$fields = array_merge( $fields, $parts );
			$fields = '(\'' . implode( '\', \'', $fields ) . '\')';

			$sql = "
				UPDATE $wpdb->postmeta meta JOIN $wpdb->posts posts
				ON meta.post_id = posts.ID
				SET meta.meta_key = CONCAT('_happyforms_', meta.meta_key)
				WHERE posts.ID IN ($message_ids)
				AND meta.meta_key IN $fields
				";

			$wpdb->query( $sql );

			foreach( $messages as $message ) {
				// Move tracking_id to meta field
				$tracking_id =
					( intval( $form['unique_id'] ) ) ?
					$message['post_title'] : '';

				happyforms_update_meta( $message['ID'], 'tracking_id', $tracking_id );
			}
		}

		// Reword titles
		$sql = "
			UPDATE $wpdb->posts posts
			SET posts.post_title = CONCAT('response #', posts.ID)
			WHERE posts.post_type = '$message_controller->post_type'
			";

		$wpdb->query( $sql );
	}

}

if ( ! function_exists( 'happyforms_get_migrations' ) ):
/**
 * Get the HappyForms_Migrations class instance.
 *
 * @return HappyForms_Migrations
 */
function happyforms_get_migrations() {
	return HappyForms_Migrations::instance();
}

endif;

/**
 * Initialize the HappyForms_Migrations class immediately.
 */
happyforms_get_migrations();