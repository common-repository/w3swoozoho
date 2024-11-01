<?php

namespace W3SCloud\WooZoho\Operations;

/**
 * Interface OperationsInterface.
 *
 * @since 1.0.0
 * @package W3SCloud\WooZoho\Operations
 */
interface OperationsInterface {
	/**
	 * Get Customer from Connection.
	 *
	 * @param \WC_Order $order WooCommerce order.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_customer( $order );

	/**
	 * Get all associated products with order from connection.
	 *
	 * @param \WC_Order $order WooCommerce Order.
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_products( $order );

	/**
	 * Create recurred records.
	 *
	 * @param \WC_Order $order WooCommerce Order.
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function create( $order );
}
