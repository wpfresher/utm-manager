<?php
/**
 * Plugin Name:  Essential Elements
 * Description:  Essential elements for WordPress website.
 * Version:      1.0.0
 * Plugin URI:   https://wpfresher.com/plugins/essential-elements/
 * Author:       WpFresher
 * Author URI:   https://wpfresher.com/
 * Text Domain:  essential-elements
 * Domain Path: /languages/
 * Requires PHP: 5.6
 *
 * @package EssentialElements
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

use EssentialElements\Plugin;

// don't call the file directly.
defined( 'ABSPATH' ) || exit();

// Autoload function.
spl_autoload_register(
    function ( $class_name ) {

        $prefix = 'EssentialElements\\';
        $len    = strlen( $prefix );

        // Bail out if the class name doesn't start with our prefix.
        if ( strncmp( $prefix, $class_name, $len ) !== 0 ) {
            return;
        }

        // Remove the prefix from the class name.
        $relative_class = substr( $class_name, $len );
//        // Replace the namespace separator with the directory separator.
        $file = str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

        require_once( __DIR__ . DIRECTORY_SEPARATOR .'src' . DIRECTORY_SEPARATOR . $file );


//        wp_die();

//        var_dump($class_name);
//        wp_die();
//
//        // Look for the file in the inc and lib directories.
//        $file_paths = array(
//            __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $file,
//            __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $file,
//        );
//
//        foreach ( $file_paths as $file_path ) {
//            if ( file_exists( $file_path ) ) {
//                require_once $file_path;
//                break;
//            }
//        }
    }
);




/**
 * Get the plugin instance.
 *
 * @since 1.0.0
 * @return Plugin plugin initialize class.
 */
function essential_elements() { // phpcs:ignore
    $data = array(
        'file'         => __FILE__,
        'settings_url' => admin_url( 'admin.php?page=essential-elements' ),
        'support_url'  => 'https://wpfresher.com/support/',
        'docs_url'     => 'https://wpfresher.com/docs/essential-elements/',
    );
    return new Plugin;
}

// Initialize the plugin.
essential_elements();