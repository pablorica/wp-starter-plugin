<?php
if ( ! defined( 'ABSPATH' ) ) exit;

final class MCP_Admin {

	/** @var MCP_Admin|null */
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
		add_filter( 'plugin_action_links_' . MCP_BASENAME, array( $this, 'add_settings_link' ) );
	}

	public function add_menu() {
		add_options_page(
			__( 'My Custom Plugin', 'my-custom-plugin' ),
			__( 'My Custom Plugin', 'my-custom-plugin' ),
			'manage_options',
			'mcp-settings',
			array( $this, 'render_page' )
		);
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'My Custom Plugin', 'my-custom-plugin' ) . '</h1>';
		echo '<p>' . esc_html__( 'Starter settings page.', 'my-custom-plugin' ) . '</p>';
		echo '</div>';
	}

	public function enqueue_admin( $hook ) {
		// Only load on our settings page
		if ( 'settings_page_mcp-settings' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'mcp-admin',
			MCP_URL . 'assets/admin.css',
			array(),
			MCP_Starter_Plugin::VERSION
		);

		wp_enqueue_script(
			'mcp-admin',
			MCP_URL . 'assets/admin.js',
			array( 'jquery' ),
			MCP_Starter_Plugin::VERSION,
			true
		);

		wp_localize_script( 'mcp-admin', 'MCP', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'mcp_nonce' ),
		) );
	}

	public function add_settings_link( $links ) {
		$url = admin_url( 'options-general.php?page=mcp-settings' );
		$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'my-custom-plugin' ) . '</a>';
		return $links;
	}
}