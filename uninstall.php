<?php
/**
 * Essential Elements Uninstall
 *
 * Uninstalling Essential Elements deletes user roles, pages, tables, and options.
 *
 * @package EssentialElementsPro
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Remove all the options starting with wpeep_.
$delete_all_options = get_option( 'wpeep_delete_data' );
if ( empty( $delete_all_options ) ) {
	return;
}
// Delete all the options.
global $wpdb;
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wpeep_%';" );
