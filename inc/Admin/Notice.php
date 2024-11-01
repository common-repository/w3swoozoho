<?php

namespace W3SCloud\WooZoho\Admin;

/**
 * Class Notice.
 *
 * @package W3SCloud\WooZoho\Admin
 */
class Notice {
	/**
	 * Message for the notice.
	 *
	 * @var string
	 */
	private $message;
	/**
	 * Type of the notice.
	 * success|error
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Notice constructor.
	 *
	 * @param string $message Message for the notice.
	 * @param string $type Type of the notice.
	 */
	private function __construct( $message, $type ) {
		$this->message = $message;
		$this->type    = $type;

		add_action( 'admin_notices', array( $this, 'notice' ) );

	}

	/**
	 * Display success admin notice.
	 *
	 * @param string $message Message of the success notice.
	 */
	public static function success( $message ) {
		self::show( $message, 'success' );
	}

	/**
	 * Display error admin notice.
	 *
	 * @param string $message Message of the success notice.
	 */
	public static function error( $message ) {
		self::show( $message, 'success' );
	}

	/**
	 * Show Admin notice.
	 *
	 * @param string $message Notice message.
	 * @param string $type Notice type.
	 */
	public static function show( $message, $type ) {
		$notice = new self( $message, $type );
	}

	/**
	 * Actual notice body.
	 */
	public function notice() {
		?>
		<div class="notice notice-<?php echo esc_attr( $this->type ); ?> is-dismissible">
			<p><?php echo esc_html( $this->message ); ?></p>
		</div>
		<?php
	}
}
