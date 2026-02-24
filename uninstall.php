<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete options you created
delete_option( 'mcp_installed_at' );

// If you create CPT data etc, you can clean it here (careful).