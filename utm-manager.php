<?php
/**
 * Plugin Name:       UTM Manager
 * Plugin URI:        https://urldev.com/plugins/utm-manager/
 * Description:       UTM Manager is a powerful and user-friendly WordPress plugin designed to help you efficiently track and manage UTM parameters across your website. With UTM Manager, you can effortlessly monitor the performance of your marketing campaigns, understand the source of your traffic, and gain valuable insights to optimize your strategies.
 * Version:           1.3.1
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Tested up to:      6.9
 * Author:            UrlDev
 * Author URI:        https://urldev.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       utm-manager
 * Domain Path:       /languages
 *
 * @package UTMManager
 *
 * UTM Manager is a free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * UTM Manager is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

use UTMManager\Plugin;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Autoload optimized classes.
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Get the plugin instance.
 *
 * @since 1.0.0
 * @return Plugin The plugin instance.
 */
function utm_manager() {
	return Plugin::create( __FILE__, '1.3.1' );
}

// Initialize the plugin.
utm_manager();
