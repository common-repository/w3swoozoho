<?php

namespace W3SCloud\WooZoho\Admin;

use W3SCloud\WooZoho\Admin\Settings\Authorize;
use W3SCloud\WooZoho\Options;
use W3SCloud\WooZoho\Zoho\CRM;
use W3SCloud\WooZoho\Zoho\Settings_API;

/**
 * Class Settings.
 *
 * @package W3SCloud\WooZoho\Admin\Settings
 */
class Settings {
	/**
	 * Settings api.
	 *
	 * @var WeDevs_Settings_API
	 */
	private $settings_api;
	/**
	 * Redirect URL for zoho auth use.
	 *
	 * @var string
	 */
	private $redirect_url;
	/**
	 * Redirect URL for zoho auth use Deep encoded.
	 *
	 * @var string
	 */
	private $redirect_url_encoded;
	/**
	 * Site url of current site.
	 *
	 * @var mixed
	 */
	private $site_url;

	/**
	 * Settings constructor.
	 */
	public function __construct() {
		$this->settings_api         = new Settings_API();
		$this->redirect_url         = admin_url( 'admin.php?page=w3swoozoho_settings' );
		$this->redirect_url_encoded = urlencode_deep( $this->redirect_url );
		$this->site_url             = wp_parse_url( site_url() )['host'];
		new Authorize();
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	/**
	 * Initialization of the class.
	 */
	public function admin_init() {

		// set the settings.
		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->get_settings_fields() );

		// initialize settings.
		$this->settings_api->admin_init();
	}

	/**
	 * Menu Function.
	 */
	public function admin_menu() {
		add_menu_page(
			'Connect WooCommerce to Zoho',
			'W3S Woo Zoho',
			'manage_options',
			'w3swoozoho_settings',
			array( $this, 'plugin_page' ),
			'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzNjAuMzQyIiBoZWlnaHQ9IjM2Ni40NSIgdmlld0JveD0iMCAwIDM2MC4zNDIgMzY2LjQ1Ij48ZyB0cmFuc2Zvcm09InRyYW5zbGF0ZSgtMiAtMSkiPjxwYXRoIGQ9Ik00MC4yMTQsNjAuMDExSDYzLjdBMTI5LDEyOSwwLDAsMSw4NC4xNzYsMjUuMzIsNjcuMzY0LDY3LjM2NCwwLDAsMCw0MC4yMTQsNjAuMDExWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTk1LjE3OCAxMjQuMjE0KSIgZmlsbD0iI2ZmZiIvPjxwYXRoIGQ9Ik00NS45NTMsNDFjNC45OSwxMS44NzMsMTMuMjcyLDIzLjMzMSwyNC43MTcsMzQuM0M4Mi4xMjIsNjQuMzMxLDkwLjQsNTIuODczLDk1LjM4Nyw0MVoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDIyNC40OSAyMDQuMykiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJNNjkuMjQsMjUuNDEyQTEyNS43MTEsMTI1LjcxMSwwLDAsMCw0Ni4yMzMsNTkuNTQxSDkyLjI0N0ExMjUuODUxLDEyNS44NTEsMCwwLDAsNjkuMjQsMjUuNDEyWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMjI1LjkyIDEyNC42ODQpIiBmaWxsPSIjZmZmIi8+PHBhdGggZD0iTTM1LDMzVjY5LjY0NUg0OS40MzJhNzkuMTU5LDc5LjE1OSwwLDAsMS0yLjIxNy0xOC4zMjJBNzkuMTU5LDc5LjE1OSwwLDAsMSw0OS40MzIsMzNaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxNjguNTQ3IDE2My40NCkiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJNMzcuMzIyLDMxQTE4LjM0NSwxOC4zNDUsMCwwLDAsMTksNDkuMzIzdjI0LjQzQTE4LjM0NSwxOC4zNDUsMCwwLDAsMzcuMzIyLDkyLjA3NWg2Ny4xODlMMTA0LjUsMzFaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSg4Ni44MjcgMTUzLjIyNSkiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJNNzMuMDYsNjAuMDExSDk2LjU1QTY3LjM2NCw2Ny4zNjQsMCwwLDAsNTIuNTg4LDI1LjMyLDEyOSwxMjksMCwwLDEsNzMuMDYsNjAuMDExWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMjU4LjM3OCAxMjQuMjE0KSIgZmlsbD0iI2ZmZiIvPjxwYXRoIGQ9Ik04My4xNzQsNzUuNDI4QzczLjQyNyw2NC40MjIsNjYuMzY2LDUyLjkzNCw2Mi4xMzQsNDFINDAuMjE0QTY3LjM2NSw2Ny4zNjUsMCwwLDAsODMuMTc0LDc1LjQyOFoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDE5NS4xNzggMjA0LjMpIiBmaWxsPSIjZmZmIi8+PHBhdGggZD0iTTI0NS4wODUsOTkuNzYzYy0yLjAyMi4xNTktNC4wNDMuMzExLTYuMTA3LjMxMWE3OS41LDc5LjUsMCwwLDEtNzMuMjcyLTQ4Ljg2SDE0Ny4zNjVBMTIuMjI3LDEyLjIyNywwLDAsMSwxMzUuMTUsNjMuNDNINjcuOTY3QTMwLjYsMzAuNiwwLDAsMSwzOC4wNDcsMzlIMTN2ODUuNUgyNDUuMDg1WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoNTYuMTgyIDE5NC4wODUpIiBmaWxsPSIjZmZmIi8+PHBhdGggZD0iTTI1LDU1aDg1LjVWNjcuMjE1SDI1WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTE3LjQ3MiAyNzUuODA1KSIgZmlsbD0iI2ZmZiIvPjxwYXRoIGQ9Ik00MS42LDMzYTY1LjgxNyw2NS44MTcsMCwwLDAsMCwzNi42NDVINjUuMDYxQTgyLjkxNyw4Mi45MTcsMCwwLDEsNjMuNDMsNTQuMDM0LDg4LDg4LDAsMCwxLDY2LjEsMzNaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxODguOTc3IDE2My40NCkiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJNMTMsODkuMTgySDM4LjA0N2EzMC41OSwzMC41OSwwLDAsMSwyOS45MjEtMjQuNDNIMTM1LjE1YTEyLjIyNywxMi4yMjcsMCwwLDEsMTIuMjE1LDEyLjIxNWgxOC4zNDFhNzkuNSw3OS41LDAsMCwxLDczLjI3Mi00OC44NmMyLjA2NCwwLDQuMDg2LjE1Myw2LjEwNy4zMTFWMjJIMTNaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSg1Ni4xODIgMTA3LjI1NykiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJNNTIuNzUyLDc1LjQyOEE2Ny40LDY3LjQsMCwwLDAsOTUuNzEyLDQxSDczLjc5MkM2OS41Niw1Mi45MzQsNjIuNSw2NC40MjgsNTIuNzUyLDc1LjQyOFoiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDI1OS4yMTYgMjA0LjMpIiBmaWxsPSIjZmZmIi8+PHBhdGggZD0iTTIxNC42NTUsNjcuMjE1QTEyLjIyNywxMi4yMjcsMCwwLDEsMjAyLjQ0LDc5LjQzaC04NS41QTEyLjIyNywxMi4yMjcsMCwwLDEsMTA0LjcyLDY3LjIxNVY1NUg3VjY3LjIxNWEyNC40NTUsMjQuNDU1LDAsMCwwLDI0LjQzLDI0LjQzSDI4Ny45NDVhMjQuNDU1LDI0LjQ1NSwwLDAsMCwyNC40My0yNC40M1Y1NWgtOTcuNzJabTQ4Ljg2LDBIMjc1LjczVjc5LjQzSDI2My41MTVabS0yNC40MywwSDI1MS4zVjc5LjQzSDIzOS4wODVaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgyNS41MzcgMjc1LjgwNSkiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJNMTA2LjA3NSw1NC4xMkE3Ni41LDc2LjUsMCwwLDAsMTAzLjAxNSwzM0g0OC4wNkE3Ni41LDc2LjUsMCwwLDAsNDUsNTQuMTJhNjkuNjY0LDY5LjY2NCwwLDAsMCwxLjg1NywxNS41MjVoNTcuMzYyQTY5LjY2Myw2OS42NjMsMCwwLDAsMTA2LjA3NSw1NC4xMloiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDIxOS42MjIgMTYzLjQ0KSIgZmlsbD0iI2ZmZiIvPjxwYXRoIGQ9Ik04MS4wNiwzM2gtMjQuNWE4OC4wMDYsODguMDA2LDAsMCwxLDIuNjY5LDIxLjAzNEE4Mi40NzIsODIuNDcyLDAsMCwxLDU3LjYsNjkuNjQ1SDgxLjA1NGE2Ni43NDQsNjYuNzQ0LDAsMCwwLDIuNjA4LTE4LjMyMkE2Ni45NzQsNjYuOTc0LDAsMCwwLDgxLjA2LDMzWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMjc4LjY4IDE2My40NCkiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJNNTMsNTEuOTA2Vjc4LjU1M0g2NS4yMTVWNDhBNzkuMyw3OS4zLDAsMCwxLDUzLDUxLjkwNloiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDI2MC40ODIgMjQwLjAzNykiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJNOSwzOUgyMS4yMTV2ODUuNUg5WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMzUuNzUyIDE5NC4wODUpIiBmaWxsPSIjZmZmIi8+PHBhdGggZD0iTTIsMzVIOTMuNjEyVjQ3LjIxNUgyWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMCAxNzMuNjU1KSIgZmlsbD0iI2ZmZiIvPjxwYXRoIGQ9Ik0yMS4yMTUsMzAuMjE1SDI3Ny43M3YyMC41NGE3OCw3OCwwLDAsMSwxMi4yMTUsMy45MDlWMjQuMTA4QTYuMTE4LDYuMTE4LDAsMCwwLDI4My44MzcsMThIMTUuMTA3QTYuMTE4LDYuMTE4LDAsMCwwLDksMjQuMTA4djg1LjVIMjEuMjE1WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMzUuNzUyIDg2LjgyNykiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJNMzguMjE1LDM2LjUyMVY5Mi42MTJINTAuNDNWMzYuNTIxYTE4LjMyMywxOC4zMjMsMCwxLDAtMTIuMjE1LDBaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxMjIuNTggMCkiIGZpbGw9IiNmZmYiLz48cGF0aCBkPSJNMzIuMjE1LDQwLjUyMVY3Mi4xODJINDQuNDNWNDAuNTIxYTE4LjMyMywxOC4zMjMsMCwxLDAtMTIuMjE1LDBaIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSg5MS45MzUgMjAuNDMpIiBmaWxsPSIjZmZmIi8+PHBhdGggZD0iTTUwLjIxNSwzNi41MjFWOTIuNjEySDYyLjQzVjM2LjUyMWExOC4zMjMsMTguMzIzLDAsMSwwLTEyLjIxNSwwWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTgzLjg3IDApIiBmaWxsPSIjZmZmIi8+PHBhdGggZD0iTTQ0LjIxNSw0MC41MjFWNzIuMTgySDU2LjQzVjQwLjUyMWExOC4zMjMsMTguMzIzLDAsMSwwLTEyLjIxNSwwWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTUzLjIyNSAyMC40MykiIGZpbGw9IiNmZmYiLz48L2c+PC9zdmc+',
			40
		);
	}

	/**
	 * Settings Section of Settings page.
	 *
	 * @return array
	 */
	public function get_settings_sections() {
		$sections = array(
			array(
				'id'    => 'w3swoozoho_crm',
				'title' => __( 'CRM Settings', 'w3swoozoho' ),
			),
		);

		$sections = apply_filters( 'w3swoozoho_settings_sections', $sections );
		return $sections;
	}

	/**
	 * Returns all the settings fields
	 *
	 * @return array settings fields
	 */
	public function get_settings_fields() {

		$settings_fields = array(
			'w3swoozoho_crm' => array(
				array(
					'name' => 'help_top_html',
					'desc' => '<h2>' . __( 'Information to create Client ID', 'w3swoozoho' ) . "</h2>
								<table class='form-table'>
							        <thead>
										<tr>
											<th>Fields</th>
											<th>Value</th>
										</tr>
							        </thead>
							        <tbody>
										<tr>
											<td><div>Client Name:</div></td>
											<td><code>Connect WooCommerce</code></td>
										</tr>        
										<tr>
											<td><div>Client Domain:</div></td>
											<td><code>{$this->site_url}</code></td>
										</tr>
										<tr>
											<td><div>Authorized redirect URI:</div></td>
											<td><code>{$this->redirect_url}</code></td>
										</tr>
										<tr>
											<td><div>Client Type</div></td>
											<td><code>Web Based</code></td>
										</tr>
									</tbody>
							    </table>",
					'type' => 'html',
				),
				array(
					'name' => 'create_app_button',
					'desc' => '<a class="button button-primary" target="_blank" href="https://accounts.zoho.com/signin?servicename=AaaServer&serviceurl=%2Fdeveloperconsole">' . __( 'Get Application Credentials', 'w3swoozoho' ) . '</a>',
					'type' => 'html',
				),
				array(
					'name'    => 'is_active',
					'label'   => __( 'Integration Status', 'w3swoozoho' ),
					'desc'    => __( 'Activate or Deactivate the integration.', 'w3swoozoho' ),
					'type'    => 'select',
					'default' => 'yes',
					'options' => array(
						'yes' => 'Activate',
						'no'  => 'Deactivate',
					),
				),
				array(
					'name'    => 'data_center',
					'label'   => __( 'Data Center', 'w3swoozoho' ),
					'desc'    => __( 'Choose your data center', 'w3swoozoho' ),
					'type'    => 'select',
					'default' => '.com',
					'options' => array(
						'.com'    => 'zoho.com',
						'.eu'     => 'zoho.eu',
						'.com.au' => 'zoho.com.au',
						'.in'     => 'zoho.in',
					),
				),

				array(
					'name'              => 'client_id',
					'label'             => __( 'Client ID', 'w3swoozoho' ),
					'desc'              => __( 'Insert your application Client ID here.', 'w3swoozoho' ),
					'placeholder'       => __( 'Your application Client ID here', 'w3swoozoho' ),
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'name'    => 'client_secret',
					'label'   => __( 'Client Secret', 'w3swoozoho' ),
					'desc'    => __( 'Your application client Secret here.', 'w3swoozoho' ),
					'type'    => 'password',
					'default' => '',
				),
				array(
					'name'              => 'client_email',
					'label'             => __( 'Zoho User Email', 'w3swoozoho' ),
					'desc'              => __( 'Insert email address associated with your Zoho user id.', 'w3swoozoho' ),
					'placeholder'       => __( 'your@email.ltd', 'w3swoozoho' ),
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_email',
				),
			),
		);

		if ( Options::get_option( 'client_id', 'w3swoozoho_crm' ) && Options::get_option( 'client_secret', 'w3swoozoho_crm' ) && Options::get_option( 'client_email', 'w3swoozoho_crm' ) ) {

			$client_id   = Options::get_option( 'client_id', 'w3swoozoho_crm' );
			$data_center = Options::get_option( 'data_center', 'w3swoozoho_crm' );
			$zoho_scopes = 'ZohoCRM.modules.ALL,ZohoCRM.settings.ALL,aaaserver.profile.READ';
			$auth_button = array(
				'name' => 'authorize_button',
				'desc' => '<a class="button button-primary" href="https://accounts.zoho' . $data_center . '/oauth/v2/auth?scope=' . $zoho_scopes . '&client_id=' . $client_id . '&response_type=code&access_type=offline&prompt=consent&redirect_uri=' . $this->redirect_url_encoded . '">' . __( 'Authorize Integration', 'w3swoozoho' ) . '</a>',
				'type' => 'html',
			);

			array_push( $settings_fields['w3swoozoho_crm'], $auth_button );
		}

		return apply_filters( 'w3swoozoho_settings_fields', $settings_fields );

	}

	/**
	 * Plugin Settings Page.
	 */
	public function plugin_page() {
		echo '<div class="wrap">';
		settings_errors();
		$this->settings_api->show_navigation();
		$this->settings_api->show_forms();

		echo '</div>';
	}
}
