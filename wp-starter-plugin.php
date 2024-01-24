<?php
/**
 * Plugin Name:  WP Starter Plugin
 * Description:  WP Starter Plugin.
 * Version:      1.0.0
 * Plugin URI:   https://urldev.com/plugins/wp-starter-plugin/
 * Author:       UrlDev
 * Author URI:   https://urldev.com/
 * Text Domain:  wp-starter-plugin
 * Domain Path: /languages/
 * Requires PHP: 5.6
 *
 * @package WpStarterPlugin
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

use WpStarterPlugin\Plugin;

defined( 'ABSPATH' ) || exit(); // Exit if accessed directly.

// Autoload function.
spl_autoload_register(
	function ( $class_name ) {

		$prefix = 'WpStarterPlugin\\';
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
function wp_starter_plugin() { // phpcs:ignore
	$data = array(
		'file'             => __FILE__,
		'item_id'          => 123456789,
		'settings_url'     => admin_url( 'admin.php?page=wp-starter-plugin' ),
		'support_url'      => 'https://urldev.com/support/',
		'docs_url'         => 'https://urldev.com/docs/wp-starter-plugin/',
		'premium_url'      => 'https://urldev.com/plugins/wp-starter-plugin/',
		'premium_basename' => 'wp-starter-plugin',
	);
	return Plugin::create( $data );
}

// Initialize the plugin.
wp_starter_plugin();
