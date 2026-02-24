<?php
if ( ! defined( 'ABSPATH' ) ) exit;

final class MCP_Plugin {

	/** @var MCP_Plugin|null */
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		// Front-end hooks
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front' ) );

		// Example shortcode
		add_shortcode( 'mcp_hello', array( $this, 'shortcode_hello' ) );

		// Example REST route (optional)
		// add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	public function enqueue_front() {
		// Only enqueue if needed (example: only when shortcode exists)
		// if ( ! is_singular() ) return;

		// wp_enqueue_style( 'mcp-front', MCP_URL . 'assets/front.css', array(), MCP_Starter_Plugin::VERSION );
		// wp_enqueue_script( 'mcp-front', MCP_URL . 'assets/front.js', array('jquery'), MCP_Starter_Plugin::VERSION, true );
	}

	public function shortcode_hello( $atts = array(), $content = '' ) {
		$atts = shortcode_atts(
			array(
				'name' => 'world',
			),
			$atts,
			'mcp_hello'
		);

		return '<div class="mcp-hello">Hello ' . esc_html( $atts['name'] ) . ' 👋</div>';
	}

	// public function register_rest_routes() {
	// 	register_rest_route('mcp/v1', '/ping', array(
	// 		'methods'  => 'GET',
	// 		'callback' => function() { return array('pong' => true); },
	// 		'permission_callback' => '__return_true',
	// 	));
	// }
}