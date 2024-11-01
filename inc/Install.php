<?php

namespace W3SCloud\WooZoho;

/**
 * Class Install.
 *
 * @package W3SCloud\WooZoho
 */
class Install {
	/**
	 * Install constructor.
	 */
	public function __construct() {
	}

	/**
	 * Install functionality.
	 *
	 * @return void
	 */
	public function run() {
		$this->set_installed_time();
		$this->create_tables();
	}

	/**
	 * Set Plugin First Installed Time.
	 *
	 * @return void
	 */
	private function set_installed_time() {
		$installed_time = get_option( 'w3swoozoho_installed' );
		if ( ! $installed_time ) {
			update_option( 'w3swoozoho_installed', time() );
		}
	}

	/**
	 * Create necessary database tables.
	 *
	 * @return void
	 */
	private function create_tables() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$schema          = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}w3swoozoho_auths` (
							  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
							  `useridentifier` varchar(100) NOT NULL DEFAULT '',
							  `accesstoken` varchar(100) NOT NULL DEFAULT '',
							  `refreshtoken` varchar(100) NOT NULL DEFAULT '',
							  `expirytime` bigint(20) unsigned NOT NULL,
							  PRIMARY KEY (`id`)
							) {$charset_collate}";

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . '/wp-admin/includes/upgrade.php';
		}
		dbDelta( $schema );
	}
}
