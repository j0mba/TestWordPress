<?php

class HappyForms_Session {

	/**
	 * The singleton instance.
	 *
	 * @since 1.0
	 *
	 * @var HappyForms_Session
	 */
	private static $instance;

	/**
	 * The list of registered errors.
	 *
	 * @since 1.4.6
	 *
	 * @var array
	 */
	private $errors = array();

	/**
	 * The list of registered notices.
	 *
	 * @since 1.4.6
	 *
	 * @var array
	 */
	private $notices = array();

	/**
	 * A list of submit values.
	 *
	 * @since 1.4.6
	 *
	 * @var array
	 */
	private $values = array();

	/**
	 * Current form step
	 *
	 * @var int
	 */
	private $step = 0;

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

		return self::$instance;
	}

	public function add_error( $key, $message ) {
		$this->errors[$key] = $message;
	}

	/**
	 * Add a notice to be displayed on the next refresh.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function add_notice( $key, $message ) {
		$this->notices[$key] = $message;
	}

	public function add_value( $key, $value ) {
		$this->values[$key] = $value;
	}

	/**
	 * Get the messages for the given form and location.
	 *
	 * @since 1.0
	 *
	 * @param string $location  The location to fetch messages for.
	 *
	 * @return array
	 */
	public function get_messages( $location = '' ) {
		$messages = array();

		if ( isset( $this->notices[$location] ) ) {
			$messages[] = array(
				'type' => 'success',
				'message' => $this->notices[$location],
			);
		}

		if ( isset( $this->errors[$location] ) ) {
			$messages[] = array(
				'type' => 'error',
				'message' => $this->errors[$location],
			);
		}

		return $messages;
	}

	public function get_values() {
		return $this->values;
	}

	public function get_value( $location = '', $component = false ) {
		$value = false;

		if ( isset( $this->values[$location] ) ) {
			$value = $this->values[$location];

			if ( false !== $component ) {
				$value = isset( $value[$component] ) ? $value[$component] : '';
			}
		}

		return $value;
	}

	public function clear_values() {
		$this->values = array();
	}

	public function current_step() {
		return $this->step;
	}

	public function next_step() {
		$this->step = $this->step + 1;
	}

	public function previous_step() {
		$this->step = max( 0, $this->step - 1 );
	}

	public function set_step( $step ) {
		$this->step = $step;
	}

	public function reset_step() {
		$this->step = 0;
	}

}

if ( ! function_exists( 'happyforms_get_session' ) ):
/**
 * Get the HappyForms_Session class instance.
 *
 * @since 1.0
 *
 * @return HappyForms_Session
 */
function happyforms_get_session() {
	return HappyForms_Session::instance();
}

endif;
