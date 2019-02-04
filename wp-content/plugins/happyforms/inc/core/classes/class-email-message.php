<?php

class HappyForms_Email_Message {

	/**
	 * The sender address.
	 *
	 * @since 1.4.3
	 *
	 * @var string
	 */
	private $from;

	/**
	 * The reply-to address.
	 *
	 * @since 1.4.3
	 *
	 * @var string
	 */
	private $reply_to;

	/**
	 * The list of recipients.
	 *
	 * @since 1.4.3
	 *
	 * @var array
	 */
	private $to;

	/**
	 * The list of cc addresses.
	 *
	 * @since 1.4.3
	 *
	 * @var array
	 */
	private $ccs;

	/**
	 * The email subject.
	 *
	 * @since 1.4.3
	 *
	 * @var string
	 */
	private $subject;

	/**
	 * The email content type.
	 *
	 * @since 1.4.3
	 *
	 * @var string
	 */
	private $content_type = 'text/html';

	/**
	 * The email message content.
	 *
	 * @since 1.4.3
	 *
	 * @var string
	 */
	private $content;

	/**
	 * The submission message this email is linked to.
	 *
	 * @since 1.4.3
	 *
	 * @var array
	 */
	public $message;

	public function __construct( $message = array() ) {
		$this->from = '';
		$this->to = '';
		$this->ccs = array();
		$this->subject = '';
		$this->content = '';
		$this->message = $message;
	}

	public function set_from( $email, $name = '' ) {
		$from = empty( $name ) ? $email : "{$name} <$email>";

		/**
		 * Filter the list of senders for this email message.
		 *
		 * @since 1.4.3
		 *
		 * @param array $senders Current list of senders.
		 * @param array $message The submission this email was triggered from.
		 *
		 * @return array
		 */
		$from = apply_filters( 'happyforms_email_from', $from, $this->message );

		$this->from = $from;
	}

	public function set_to( $to ) {
		/**
		 * Filter the list of recipients for this email message.
		 *
		 * @since 1.4.3
		 *
		 * @param array $recipients Current list of recipients.
		 * @param array $message    The submission this email was triggered from.
		 *
		 * @return array
		 */
		$to = apply_filters( 'happyforms_email_to', $to, $this->message );

		$this->to = trim( $to );
	}

	public function set_ccs( $ccs = array() ) {
		if ( is_string( $ccs ) ) {
			$ccs = array( $ccs );
		}

		/**
		 * Filter the list of recipients for this email message.
		 *
		 * @since 1.4.3
		 *
		 * @param array $recipients Current list of recipients.
		 * @param array $message    The submission this email was triggered from.
		 *
		 * @return array
		 */
		$ccs = apply_filters( 'happyforms_email_ccs', $ccs, $this->message );

		$this->ccs = array_map( 'trim', $ccs );
	}

	public function set_reply_to( $reply_to = array() ) {
		if ( is_string( $reply_to ) ) {
			$reply_to = array( $reply_to );
		}

		/**
		 * Filter the list of recipients for this email message.
		 *
		 * @since 1.4.3
		 *
		 * @param array $recipients Current list of recipients.
		 * @param array $message    The submission this email was triggered from.
		 *
		 * @return array
		 */
		$reply_to = apply_filters( 'happyforms_email_reply_to', $reply_to, $this->message );

		$this->reply_to = array_map( 'trim', $reply_to );
	}

	public function set_subject( $subject = '' ) {
		$subject = trim( $subject );

		/**
		 * Filter the subject for this email message.
		 *
		 * @since 1.4.3
		 *
		 * @param string $subject Current subject.
		 * @param array  $message    The submission this email was triggered from.
		 * @param array  $recipients The address this email is being sent to.
		 *
		 * @return string
		 */
		$subject = apply_filters( 'happyforms_email_subject', $subject, $this->message, $this->to );

		$this->subject = $subject;
	}

	public function set_content( $content = '' ) {
		$content = trim( $content );

		/**
		 * Filter the content for this email message.
		 *
		 * @since 1.4.3
		 *
		 * @param string $content    Current content.
		 * @param array  $message    The submission this email was triggered from.
		 * @param array  $recipients The address this email is being sent to.
		 *
		 * @return string
		 */
		$content = apply_filters( 'happyforms_email_content', $content, $this->message, $this->to );

		$this->content = $content;
	}

	private function get_headers() {
		$headers = array();

		array_push( $headers, 'From: ' . $this->from );

		if ( ! empty( $this->reply_to ) ) {
			array_push( $headers, 'Reply-To: ' . implode( ', ', $this->reply_to ) );
		}

		if ( ! empty( $this->ccs ) ) {
			array_push( $headers, 'Cc: ' . implode( ', ', $this->ccs ) );
		}

		return $headers;
	}

	public function get_content_type() {
		/**
		 * Filter the content type for this email message.
		 *
		 * @since 1.4.3
		 *
		 * @param string $content_type Current content type.
		 * @param array  $message      The submission this email was triggered from.
		 *
		 * @return string
		 */
		$content_type = apply_filters( 'happyforms_email_content_type', $this->content_type, $this->message );

		return $content_type;
	}

	public function send() {
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		$headers = $this->get_headers();

		$result = wp_mail( $this->to, $this->subject, $this->content, $headers );

		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		return $result;
	}

}
