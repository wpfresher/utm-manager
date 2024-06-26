<?php
/**
 * Plugin Name: UTM Manager
 * Description: UTM Manager is a powerful and user-friendly WordPress plugin designed to help you efficiently track and manage UTM parameters across your website. With UTM Manager, you can effortlessly monitor the performance of your marketing campaigns, understand the source of your traffic, and gain valuable insights to optimize your strategies.
 * Version:     1.0.0
 * Plugin URI:  https://wpfreshers.com/plugins/utm-manager/
 * Author:      WpFreshers
 * Author URI:  https://wpfreshers.com
 * Textdomain:  utm-manager
 * Domain Path: /languages/
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP: 5.6
 * Tested up to: 6.5
 * Requires at least: 5.2
 *
 * @package UTMManager
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 */

use UTMManager\Plugin;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Autoload function.
spl_autoload_register(
	function ( $class_name ) {
		$prefix = 'UTMManager\\';
		$len    = strlen( $prefix );

		// Bail out if the class name doesn't start with our prefix.
		if ( strncmp( $prefix, $class_name, $len ) !== 0 ) {
			return;
		}

		// Remove the prefix from the class name.
		$relative_class = substr( $class_name, $len );
		// Replace the namespace separator with the directory separator.
		$file = str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		// Look for the file in the includes directories.
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
