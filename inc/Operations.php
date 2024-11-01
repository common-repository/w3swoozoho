<?php

namespace W3SCloud\WooZoho;

/**
 * Class Operations.
 *
 * @package W3SCloud\WooZoho
 */
class Operations {
	/**
	 * Operations constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// add_action( 'woocommerce_order_status_processing', array( $this, 'handle_checkout' ) );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'handle_checkout' ) );
	}

	/**
	 * Here we will handle all the WooCommerce related functionality.
	 *
	 * @since 1.0.0
	 * @param string $order_id WooCommerce Order ID.
	 */
	public function handle_checkout( $order_id ) {
		$order = wc_get_order( $order_id );
		do_action( 'w3swoozoho_handle_checkout_operations', $order );
	}
}
