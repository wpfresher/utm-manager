<?php
/**
 * Plugin Name:  UTM Source Tracker
 * Description:  UTM Source Tracker.
 * Version:      1.0.0
 * Plugin URI:   https://urldev.com/plugins/utm-source-tracker/
 * Author:       UrlDev
 * Author URI:   https://urldev.com/
 * Text Domain:  utm-source-tracker
 * Domain Path: /languages/
 * Requires PHP: 5.6
 *
 * @package UTMSourceTracker
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

use UTMSourceTracker\Plugin;

defined( 'ABSPATH' ) || exit(); // Exit if accessed directly.

// Autoload function.
spl_autoload_register(
	function ( $class_name ) {

		$prefix = 'UTMSourceTracker\\';
		$len    = strlen( $prefix );

		// Bail out if the class name doesn't start with our prefix.
		if ( strncmp( $prefix, $class_name, $len ) !== 0 ) {
			return;
		}

		// Remove the prefix from the class name.
		$relative_class = substr( $class_name, $len );
		// Replace the namespace separator with the directory separator.
		$file = str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		// Look for the file in the inc and lib directories.
		$file_paths = array(
			__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $file,
			__DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $file,
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
 * @return Plugin plugin initialize class.
 */
function utm_source_tracker() { // phpcs:ignore
	$data = array(
		'file'             => __FILE__,
		'item_id'          => 123456789,
		'settings_url'     => admin_url( 'admin.php?page=utm-source-tracker' ),
		'support_url'      => 'https://urldev.com/support/',
		'docs_url'         => 'https://urldev.com/docs/utm-source-tracker/',
		'premium_url'      => 'https://urldev.com/plugins/utm-source-tracker/',
		'premium_basename' => 'utm-source-tracker',
	);
	return Plugin::create( $data );
}

// Initialize the plugin.
utm_source_tracker();
