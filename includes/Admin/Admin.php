<?php

namespace UrlDev\UTMManager\Admin;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Admin class.
 *
 * @since 1.0.0
 * @package UrlDev\UTMManager\Admin
 */
class Admin {

	/**
	 * Admin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		// Add screen options.
		add_filter(
			'manage_toplevel_page_utm-manager_columns',
			array( 'UrlDev\UTMManager\Admin\ListTables\LeadsListTable', 'define_columns' ),
			10,
			0
		);
	}

	/**
	 * Admin init.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		// utm_manager()->services->add( Settings::instance() );
		// utm_manager()->services->add( Menus::class );

		new Menus();
//		$settings = new Settings();
	}

	/**
	 * Get screen ids.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_screen_ids() {
		$screen_ids = array(
			'toplevel_page_utm-manager',
			'admin_page_plugin-utm-manager',
			'utm-manager_page_utmm-settings',
		);

		return apply_filters( 'utm_manager_screen_ids', $screen_ids );
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @param string $hook Hook name.
	 *
	 * @since 1.0.0
	 */
	public function admin_scripts( $hook ) {
		$screen_ids = self::get_screen_ids();
		// utm_manager()->register_style( 'utmm-admin', 'css/utmm-admin.css' );
		// utm_manager()->register_script( 'utmm-admin', 'js/utmm-admin.js' );

		if ( in_array( $hook, $screen_ids, true ) ) {
			wp_enqueue_style( 'utmm-admin' );
			wp_enqueue_script( 'utmm-admin' );

			wp_enqueue_script(
				'iris',
				admin_url( 'js/iris.min.js' ),
				array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
				true,
				1
			);
		}
	}
}
