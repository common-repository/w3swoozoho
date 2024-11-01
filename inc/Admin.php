<?php

namespace W3SCloud\WooZoho;

use W3SCloud\WooZoho\Admin\Notice;
use W3SCloud\WooZoho\Admin\Settings;

/**
 * Class Admin.
 *
 * @package W3SCloud\WooZoho
 */
class Admin {

	/**
	 * Admin constructor.
	 */
	public function __construct() {
		new Settings();
		add_action( 'tgmpa_register', array( $this, 'register_required_plugins' ) );
		if ( ! get_option( 'w3swoozoho_zoho_crm_authorized' ) ) {
			Notice::error( 'WooZoho Plugin require Zoho CRM account authenticated.' );
		}
	}

	public function register_required_plugins() {
		/*
		* Array of plugin arrays. Required keys are name and slug.
		* If the source is NOT from the .org repo, then source is also required.
		*/
		$plugins = array(

			// This is an example of how to include a plugin from the WordPress Plugin Repository.
			array(
				'name'             => 'WooCommerce',
				'slug'             => 'woocommerce',
				'required'         => true, // If false, the plugin is only 'recommended' instead of required.
				'version'          => '3.7.0', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
				'force_activation' => true,
			),

		);

		/*
		 * Array of configuration settings. Amend each line as needed.
		 *
		 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
		 * strings available, please help us make TGMPA even better by giving us access to these translations or by
		 * sending in a pull-request with .po file(s) with the translations.
		 *
		 * Only uncomment the strings in the config array if you want to customize the strings.
		 */
		$config = array(
			'id'           => 'w3swoozoho',                 // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'w3swoozoho_settings_required_plugins', // Menu slug.
			'parent_slug'  => 'w3swoozoho_settings',            // Parent menu slug.
			'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissible'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissible' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
			'strings'      => array(
				'page_title'                      => __( 'Install Required Plugins', 'w3swoozoho' ),
				'menu_title'                      => __( 'Install Plugins', 'w3swoozoho' ),
				/* translators: %s: plugin name. */
				'installing'                      => __( 'Installing Plugin: %s', 'w3swoozoho' ),
				/* translators: %s: plugin name. */
				'updating'                        => __( 'Updating Plugin: %s', 'w3swoozoho' ),
				'oops'                            => __( 'Something went wrong with the plugin API.', 'w3swoozoho' ),
				'notice_can_install_required'     => _n_noop(
					/* translators: 1: plugin name(s). */
					'This theme requires the following plugin: %1$s.',
					'This theme requires the following plugins: %1$s.',
					'w3swoozoho'
				),
				'notice_can_install_recommended'  => _n_noop(
					/* translators: 1: plugin name(s). */
					'This theme recommends the following plugin: %1$s.',
					'This theme recommends the following plugins: %1$s.',
					'w3swoozoho'
				),
				'notice_ask_to_update'            => _n_noop(
					/* translators: 1: plugin name(s). */
					'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
					'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
					'w3swoozoho'
				),
				'notice_ask_to_update_maybe'      => _n_noop(
					/* translators: 1: plugin name(s). */
					'There is an update available for: %1$s.',
					'There are updates available for the following plugins: %1$s.',
					'w3swoozoho'
				),
				'notice_can_activate_required'    => _n_noop(
					/* translators: 1: plugin name(s). */
					'The following required plugin is currently inactive: %1$s.',
					'The following required plugins are currently inactive: %1$s.',
					'w3swoozoho'
				),
				'notice_can_activate_recommended' => _n_noop(
					/* translators: 1: plugin name(s). */
					'The following recommended plugin is currently inactive: %1$s.',
					'The following recommended plugins are currently inactive: %1$s.',
					'w3swoozoho'
				),
				'install_link'                    => _n_noop(
					'Begin installing plugin',
					'Begin installing plugins',
					'w3swoozoho'
				),
				'update_link'                     => _n_noop(
					'Begin updating plugin',
					'Begin updating plugins',
					'w3swoozoho'
				),
				'activate_link'                   => _n_noop(
					'Begin activating plugin',
					'Begin activating plugins',
					'w3swoozoho'
				),
				'return'                          => __( 'Return to Required Plugins Installer', 'w3swoozoho' ),
				'plugin_activated'                => __( 'Plugin activated successfully.', 'w3swoozoho' ),
				'activated_successfully'          => __( 'The following plugin was activated successfully:', 'w3swoozoho' ),
				/* translators: 1: plugin name. */
				'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'w3swoozoho' ),
				/* translators: 1: plugin name. */
				'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'w3swoozoho' ),
				/* translators: 1: dashboard link. */
				'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'w3swoozoho' ),
				'dismiss'                         => __( 'Dismiss this notice', 'w3swoozoho' ),
				'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'w3swoozoho' ),
				'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'w3swoozoho' ),

				'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
			),

		);

		tgmpa( $plugins, $config );
	}
}
