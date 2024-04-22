<?php
/**
 * UTM Manager Uninstall
 *
 * Uninstalling UTM Manager deletes user roles, pages, tables, and options.
 *
 * @package UTMManager
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Remove all the options starting with utmm_.
$delete_all_options = get_option( 'utmm_delete_data' );
if ( empty( $delete_all_options ) ) {
	return;
}
// Delete all the options.
global $wpdb;
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'utmm_%';" );
