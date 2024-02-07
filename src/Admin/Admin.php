<?php

namespace UTMSourceTracker\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 *
 * @since 1.0.0
 * @package UTMSourceTracker
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
		utm_source_tracker()->services->add( Settings::instance() );
		utm_source_tracker()->services->add( Menus::class );
	}
}
