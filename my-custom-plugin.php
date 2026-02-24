<?php
/**
 * Plugin Name:       My Custom Plugin (Starter)
 * Version:           1.0.1
 * Requires           PHP: 7.2
 * Plugin URI:        https://codigo.co.uk/
 * Description:       Reusable starter plugin scaffold.
 * Author:            Pablo Rica
 * Author URI:        https://codigo.co.uk/team/pablo-rica
 * Text Domain:       my-custom-plugin
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

 /**
  * Reuse checklist (what you rename every time)
  *  - Class prefix MCP_ → your plugin prefix (e.g., ACME_)
  *  - Text domain my-custom-plugin
  *  - Option keys mcp_*
  *  - Menu slug mcp-settings
  *  - Constants MCP_*
  */

final class MCP_Starter_Plugin {

	/** @var MCP_Starter_Plugin|null */
	private static $instance = null;

	/** Plugin version */
	const VERSION = '1.0.1';

	/** Minimum WP version (optional) */
	const MIN_WP_VERSION = '6.0';

	/** Singleton */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->define_constants();

		// Load files
		$this->includes();

		// Hooks
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	private function define_constants() {
		if ( ! defined( 'MCP_FILE' ) ) {
			define( 'MCP_FILE', __FILE__ );
		}
		if ( ! defined( 'MCP_DIR' ) ) {
			define( 'MCP_DIR', plugin_dir_path( __FILE__ ) );
		}
		if ( ! defined( 'MCP_URL' ) ) {
			define( 'MCP_URL', plugin_dir_url( __FILE__ ) );
		}
		if ( ! defined( 'MCP_BASENAME' ) ) {
			define( 'MCP_BASENAME', plugin_basename( __FILE__ ) );
		}
	}

	private function includes() {
		require_once MCP_DIR . 'includes/functions.php';
		require_once MCP_DIR . 'includes/class-plugin.php';

		if ( is_admin() ) {
			require_once MCP_DIR . 'admin/class-admin.php';
		}
	}

	public function init() {
		// i18n (optional)
		load_plugin_textdomain( 'my-custom-plugin', false, dirname( MCP_BASENAME ) . '/languages' );

		// Kick off core
		MCP_Plugin::instance();

		// Kick off admin
		if ( is_admin() ) {
			MCP_Admin::instance();
		}
	}

	public function activate() {
		// Example: store install time
		if ( ! get_option( 'mcp_installed_at' ) ) {
			add_option( 'mcp_installed_at', time() );
		}

		// Example: rewrite rules if you add CPT/rewrites later
		// flush_rewrite_rules();
	}

	public function deactivate() {
		// Example: if you flushed on activate
		// flush_rewrite_rules();
	}
}

MCP_Starter_Plugin::instance();