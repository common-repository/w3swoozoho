<?php
/**
 * This class defines all the functionality for Zoho CRM.
 * Here we create customer to crm or get from crm.
 * Other operation can be achieved by hooking
 * into action on the create methods.
 *
 * @since 1.0.0
 * @package W3SCloud\WooZoho\Operations
 */

namespace W3SCloud\WooZoho\Operations;

use W3SCloud\WooZoho\Admin\Notice;
use W3SCloud\WooZoho\Options;
use W3SCloud\WooZoho\Zoho\CRM;

/**
 * Class CRMOperations.
 *
 * @since 1.0.0
 * @package W3SCloud\WooZoho\Operations
 */
class CRMOperations implements OperationsInterface {
	/**
	 * CRMOperations constructor.
	 */
	public function __construct() {
		add_action( 'w3swoozoho_handle_checkout_operations', array( $this, 'create' ) );
	}

	/**
	 * @inheritDoc
	 */
	public function get_customer( $order ) {

		$customer_email = $order->get_billing_email();

		$crm             = new CRM();
		$customer_in_crm = $crm->search( "Email=={$customer_email}", 'Contacts' );

		if ( ! $customer_in_crm ) {
			return $this->create_customer( $order );
		} else {
			$contact    = $customer_in_crm[0];
			$contact_id = $contact->getEntityId();
			$account_id = $contact->getFieldValue( 'Accounts' );
			if ( isset( $account_id ) ) {
				$account_id = '';
			}
		}
		return array(
			'Contacts' => $contact_id,
			'Accounts' => $account_id,
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_products( $order ) {
		// TODO: Implement get_products() method.
	}

	/**
	 * @inheritDoc
	 */
	public function create( $order ) {



		if ( ! get_option( 'w3swoozoho_zoho_crm_authorized' ) ) {
			Notice::error( 'Zoho CRM need to be Authorized.' );
			return false;
		}

		if ( Options::get_option( 'is_active', 'w3swoozoho_crm' ) != 'yes' ) {
			return false;
		}

		$customer = $this->get_customer( $order );
		do_action( 'w3swoozoho_after_customer_create', $customer, $order );

		return true;
	}

	/**
	 * Create Customer to CRM.
	 *
	 * @param \WC_Order $order WooCommerce order.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	private function create_customer( $order ) {
		$crm        = new CRM();
		$company    = $order->get_billing_company();
		$account_id = null;
		if ( isset( $company ) && $company != '' ) {
			$account_id = $this->get_account( $order );
		}
		$contact_id = $crm->send_get_id(
			'Contacts',
			array(
				'First_Name'      => $order->get_billing_first_name(),
				'Last_Name'       => $order->get_billing_last_name(),
				'Account_Name'    => $account_id,
				'Email'           => $order->get_billing_email(),
				'Phone'           => $order->get_billing_phone(),
				'Mailing_Street'  => $order->get_billing_address_1(),
				'Mailing_City'    => $order->get_billing_city(),
				'Mailing_State'   => $order->get_billing_state(),
				'Mailing_Zip'     => $order->get_billing_postcode(),
				'Mailing_Country' => $order->get_billing_country(),
				'Other_Street'    => $order->get_shipping_address_1(),
				'Other_City'      => $order->get_shipping_city(),
				'Other_State'     => $order->get_shipping_state(),
				'Other_Zip'       => $order->get_shipping_postcode(),
				'Other_Country'   => $order->get_shipping_country(),
			)
		);
		return array(
			'Contacts' => $contact_id,
			'Accounts' => $account_id,
		);
	}

	/**
	 * Search and create Accounts to CRM.
	 *
	 * @param \WC_Order $order WooCommerce Order.
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_account( $order ) {

		$crm      = new CRM();
		$company  = $order->get_billing_company();
		$accounts = $crm->search( 'Account_Name==' . $company, 'Accounts' );
		if ( ! $accounts ) {
			return $crm->send_get_id(
				'Accounts',
				array(
					'Account_Name'     => $company,
					'Phone'            => $order->get_billing_phone(),
					'Billing_Street'   => $order->get_billing_address_1(),
					'Billing_City'     => $order->get_billing_city(),
					'Billing_State'    => $order->get_billing_state(),
					'Billing_Code'     => $order->get_billing_postcode(),
					'Billing_Country'  => $order->get_billing_country(),
					'Shipping_Street'  => $order->get_shipping_address_1(),
					'Shipping_City'    => $order->get_shipping_city(),
					'Shipping_State'   => $order->get_shipping_state(),
					'Shipping_Code'    => $order->get_shipping_postcode(),
					'Shipping_Country' => $order->get_shipping_country(),
				)
			);
		}
		return $accounts[0]->getEntityId();
	}
}
