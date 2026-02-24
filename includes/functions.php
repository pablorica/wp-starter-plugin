<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Small logger helper (only logs when WP_DEBUG is true).
 */
function mcp_log( $message ) {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( '[MCP] ' . ( is_string( $message ) ? $message : wp_json_encode( $message ) ) );
	}
}