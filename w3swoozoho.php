<?php
/**
 * WooCommerce to Zoho CRM
 *
 * @package           W3SWooZoho
 * @author            W3SCloud Technology
 * @copyright         2020 W3SCloud Technology
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce to Zoho CRM
 * Plugin URI:        https://w3scloud.com/woozoho/
 * Description:       Connect Zoho with WooCommerce. Send customer information to your Zoho CRM.
 * Version:           1.3.1
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            W3SCloud Technology
 * Author URI:        https://w3scloud.com
 * Text Domain:       w3swoozoho
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * WC requires at least: 5.2
 * WC tested up to: 6.6.2
 */

use W3SCloud\WooZoho\Admin;
use W3SCloud\WooZoho\Install;
use W3SCloud\WooZoho\Operations;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/*----------------------------------------*/

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Class W3SWooZoho.
 * Main Plugin class.
 */
final class W3SWooZoho {
	/**
	 * Plugin version
	 */
	const VERSION = '1.2.0';

	/**
	 * W3SWooZoho constructor.
	 */
	private function __construct() {
		$this->define_constants();
		register_activation_hook( __FILE__, array( $this, 'plugin_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivate' ) );
		$this->run_functionality();
	}

	/**
	 * Initiate an singleton instance
	 *
	 * @return W3SWooZoho
	 */
	public static function get_instance() {
		static $instance = false;
		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Define all the constants
	 */
	public function define_constants() {
		define( 'W3S_WZ_VERSION', self::VERSION );
		define( 'W3S_WZ_FILE', __FILE__ );
		define( 'W3S_WZ_PATH', __DIR__ );
		define( 'W3S_WZ_ASSETS', plugins_url( '', W3S_WZ_FILE ) . '/assets' );
	}

	/**
	 * Define all the plugin assets
	 */
	public function enqueue_scripts() {
		// wp_enqueue_style( 'w3swoozohocss', W3S_WZ_ASSETS . '/css/button.css', false, filemtime( W3S_WZ_PATH . '/assets/css/button.css' ) );
	}

	/**
	 * Run all the plugin functionality.
	 */
	private function run_functionality() {
		if ( is_admin() ) {
			new Admin();
		}

		new Operations();
		new Operations\CRMOperations();
	}

	/**
	 * Plugin Install function.
	 */
	public function plugin_activate() {
		$install = new Install();
		$install->run();
	}

	/**
	 * Deactivation Code.
	 */
	public function plugin_deactivate() {
		// run code.
	}
}

/**
 * Initiate W3SWooZoho Instance
 */
function run_w3s_woo_zoho() {
	W3SWooZoho::get_instance();
}

/**
 * Run w3swoozoho
 */
run_w3s_woo_zoho();

