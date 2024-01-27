<?php

namespace WpStarterPlugin\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 *
 * @since 1.0.0
 * @package WpStarterPlugin
 */
class Admin {

	/**
	 * Admin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 1 );
	}

	/**
	 * Admin init.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		// var_dump(wp_starter_plugin()->get_data('version'));
		// wp_die();
	}
}
