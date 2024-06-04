<?php
/**
 * Plugin Name: UTM Manager
 * Description: UTM Manager will help to track visitors as a lead.
 * Plugin URI:  https://wpfreshers.com
 * Author:      wpfreshers
 * Author URI:  https://wpfreshers.com
 * Version:     1.0.0
 * Textdomain:  utm-manager
 * License:     GPL2
 *
 * @package WpFreshers\UTMManager
 */

use WpFreshers\UTMManager\Plugin;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Autoload function.
spl_autoload_register(
	function ( $class_name ) {
		$prefix = 'WpFreshers\\UTMManager\\';
		$len    = strlen( $prefix );

		// Bail out if the class name doesn't start with our prefix.
		if ( strncmp( $prefix, $class_name, $len ) !== 0 ) {
			return;
		}

		// Remove the prefix from the class name.
		$relative_class = substr( $class_name, $len );
		// Replace the namespace separator with the directory separator.
		$file = str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		// Look for the file in the src and lib directories.
		$file_paths = array(
			__DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . $file,
		);

		foreach ( $file_paths as $file_path ) {
			if ( file_exists( $file_path ) ) {
				require_once $file_path;
				break;
			}
		}
	}
);

/**
 * Get the plugin instance.
 *
 * @since 1.0.0
 * @return Plugin
 */
function utm_manager() {
	return Plugin::create( __FILE__ );
}

// Initialize the plugin.
utm_manager();
