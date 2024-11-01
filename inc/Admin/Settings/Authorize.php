<?php

namespace W3SCloud\WooZoho\Admin\Settings;

use W3SCloud\WooZoho\Admin\Notice;
use W3SCloud\WooZoho\Options;
use W3SCloud\WooZoho\Zoho\Auth\CRMOAuthPersistence;
use zcrmsdk\crm\exception\ZCRMException;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\oauth\exception\ZohoOAuthException;
use zcrmsdk\oauth\ZohoOAuth;

/**
 * Class Authorize.
 *
 * @package W3SCloud\WooZoho\Admin\Settings
 */
class Authorize {
	/**
	 * Authorize constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'buffer_output' ) );
		add_action( 'wp_footer', array( $this, 'buffer_end' ) );
		add_action( 'admin_head-toplevel_page_w3swoozoho_settings', array( $this, 'authorize' ) );
		add_action( 'admin_head-toplevel_page_w3swoozoho_settings', array( $this, 'show_notice' ) );
	}

	/**
	 * Handle Auth token creation of Zoho CRM.
	 */
	public function authorize() {
		if ( ( ! is_user_logged_in() ) || ( ! current_user_can( 'manage_options' ) ) ) {
			return;
		}

		if ( ! isset( $_GET['code'] ) || ! isset( $_GET['accounts-server'] ) ) {
			return;
		}

		$upload          = wp_upload_dir();
		$upload_dir      = trailingslashit( $upload['basedir'] );
		$log             = $upload_dir . 'ZCRMClientLibrary.log';
		$code            = sanitize_text_field( $_GET['code'] );
		$redirect_url    = admin_url( 'admin.php?page=w3swoozoho_settings' );
		$account_url     = esc_url( $_GET['accounts-server'] );
		$server_location = sanitize_text_field( $_GET['location'] );
		$reflector       = new \ReflectionClass( CRMOAuthPersistence::class );
		$auth_class_path = $reflector->getFileName();

		if ( ! file_exists( $log ) ) {
			touch( $log );
		}

		switch ( $server_location ) {
			case 'us':
				$api_base = 'www.zohoapis.com';
				break;
			case 'eu':
				$api_base = 'www.zohoapis.eu';
				break;
			case 'cn':
				$api_base = 'www.zohoapis.com.cn';
				break;
			case 'in':
				$api_base = 'www.zohoapis.in';
				break;
			case 'au':
				$api_base = 'www.zohoapis.au';
				break;
			default:
				$api_base = 'www.zohoapis.com';
		}

		$config = array(
			'apiBaseUrl'                     => $api_base,
			'client_id'                      => Options::get_option( 'client_id', 'w3swoozoho_crm' ),
			'client_secret'                  => Options::get_option( 'client_secret', 'w3swoozoho_crm' ),
			'redirect_uri'                   => $redirect_url,
			'accounts_url'                   => $account_url,
			'currentUserEmail'               => Options::get_option( 'client_email', 'w3swoozoho_crm' ),
			'persistence_handler_class_name' => CRMOAuthPersistence::class,
			'persistence_handler_class'      => $auth_class_path,
			'applicationLogFilePath'         => $upload_dir,
			'access_type'                    => 'offline',
			'apiVersion'                     => 'v2',
		);

		update_option( 'w3swoozoho_zoho_crm_config', $config );
		update_option( 'w3swoozoho_zoho_crm_authorized', true );
		try {
			ZCRMRestClient::initialize( $config );
			ZohoOAuth::getClientInstance()->generateAccessToken( $code );
			wp_safe_redirect( $redirect_url . '&authorized=true' );
			exit();
		} catch ( ZCRMException $exception ) {
			wp_safe_redirect( $redirect_url . '&authorized=false' );
			exit();
		} catch ( ZohoOAuthException $e ) {
			wp_safe_redirect( $redirect_url . '&authorized=false' );
			exit();
		}
	}

	/**
	 * Show notice message for authorization
	 */
	public function show_notice() {
		if ( ! isset( $_GET['authorized'] ) ) {
			return;
		}
		if ( sanitize_text_field( $_GET['authorized'] ) == 'true' ) {
			Notice::success( 'Authorization Successful! ' );
		} else {
			Notice::error( 'Error! We could not authorized the integration. Please try again.' );
		}
	}

	/**
	 * Buffer unwanted output before redirect.
	 */
	public function buffer_output() {
		ob_start();
	}

	/**
	 * Buffer ended.
	 */
	public function buffer_end() {
		ob_end_flush();
	}
}
