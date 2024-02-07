<?php
/**
 * UTM Source Tracker Uninstall
 *
 * Uninstalling UTM Source Tracker deletes user roles, pages, tables, and options.
 *
 * @package UTMSourceTracker
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Remove all the options starting with wpsp_.
$delete_all_options = get_option( 'wpsp_delete_data' );
if ( empty( $delete_all_options ) ) {
	return;
}
// Delete all the options.
global $wpdb;
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wpsp_%';" );
