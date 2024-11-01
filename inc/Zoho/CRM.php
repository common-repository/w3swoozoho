<?php

namespace W3SCloud\WooZoho\Zoho;

use W3SCloud\WooZoho\Admin\Notice;
use zcrmsdk\crm\api\response\APIResponse;
use zcrmsdk\crm\crud\ZCRMModule;
use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\crm\exception\ZCRMException;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\oauth\exception\ZohoOAuthException;

/**
 * Class CRM
 *
 * @package W3SCloud\WooZoho\Zoho
 */
class CRM {

	/**
	 * Zoho configuration
	 *
	 * @var array|bool
	 */
	private $config;

	/**
	 * Api response after creating record
	 *
	 * @var APIResponse | null
	 */
	private $response = null;

	/**
	 * Zoho Exception.
	 *
	 * @var null|ZCRMException
	 */
	private $exception = null;

	/**
	 * W3s_Custom_Contact constructor.
	 */
	public function __construct() {
		$this->config = get_option( 'w3swoozoho_zoho_crm_config' );
	}

	/**
	 * Initiate Zoho sdk.
	 */
	private function initiate() {
		if ( ! $this->config ) {
			return;
		}
		try {
			ZCRMRestClient::initialize( $this->config );
		} catch ( ZohoOAuthException $exception ) {
			Notice::error( 'WooZoho plugin needs Zoho credentials and Authorization to function.' );
			exit();
		}
	}

	/**
	 * Send data to Zoho and create records
	 *
	 * @since 1.0.0
	 * @param string $module_name CRM Module api name.
	 * @param array  $record_data Data array for the record.
	 *
	 * @return $this
	 */
	public function send( $module_name = 'Leads', $record_data = array() ) {
		$this->initiate();

		$record = ZCRMRecord::getInstance( $module_name, null );

		foreach ( $record_data as $api_name => $api_data ) {
			$record->setFieldValue( $api_name, $api_data );
		}

		try {
			$this->response = $record->create();
		} catch ( ZCRMException $e ) {
			$this->exception = $e;
		}

		return $this;
	}

	/**
	 * Search record From CRM.
	 *
	 * @param string $search Search Word or Criteria.
	 * @param string $module CRM module api name.
	 * @param bool   $by_word Search is by word or by criteria.
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function search( $search, $module = 'Leads', $by_word = false ) {
		$this->initiate();
		$module_instance = ZCRMModule::getInstance( $module );
		try {
			if ( $by_word ) {
				$response = $module_instance->searchRecordsByWord( $search );
			} else {
				$response = $module_instance->searchRecordsByCriteria( $search );
			}
			return $response->getData();
		} catch ( ZCRMException $exception ) {
			return array();
		}
	}

	/**
	 * Get Record from crm.
	 *
	 * @param string      $module ZohoCRM Module API name.
	 * @param null|string $id ID of a Record.
	 * @since 1.0.0
	 *
	 * @return array|object
	 */
	public static function get_record( $module = 'Leads', $id = null ) {

		$crm = new self();
		$crm->initiate();

		if ( ! isset( $id ) ) {
			$module = ZCRMModule::getInstance( $module );
			return $module->getRecords()->getData();
		}

		return ZCRMModule::getInstance( $module )
			->getRecord( $id )
			->getData();

	}

	/**
	 * Get the id of the added record.
	 *
	 * @return string |null the id of the record
	 */
	public function get_id() {
		if ( ! isset( $this->response ) ) {
			return null;
		}

		return $this->response->getData()->getEntityId();
	}

	/**
	 * Sends data to zoho and return record id.
	 *
	 * @param string $module_name Module Name.
	 * @param array  $record_data Record Data.
	 *
	 * @return string|null
	 */
	public function send_get_id( $module_name = 'Leads', $record_data = array() ) {
		return $this
			->send( $module_name, $record_data )
			->get_id();
	}
}
