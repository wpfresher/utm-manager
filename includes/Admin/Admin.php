<?php

namespace UTMManager\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Admin.
 *
 * @since 1.0.0
 * @package UTMManager\Admin
 */
class Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 100 );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
		add_filter(
			'manage_toplevel_page_utm-manager_columns',
			array( 'UTMManager\Admin\ListTables\LeadsListTable', 'define_columns' ),
			10,
			0
		);
	}

	/**
	 * Add menu page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_menu() {
		add_menu_page(
			__( 'UTM Manager', 'utm-manager' ),
			__( 'UTM Manager', 'utm-manager' ),
			'manage_options',
			'utm-manager',
			null,
			'dashicons-admin-links',
			'55.9',
		);

		$load = add_submenu_page(
			'utm-manager',
			__( 'Leads', 'utm-manager' ),
			__( 'Leads', 'utm-manager' ),
			'manage_options',
			'utm-manager',
			array( $this, 'render_page' ),
			1
		);

		// Load screen options.
		add_action( 'load-' . $load, array( __CLASS__, 'load_leads_page' ) );
	}

	/**
	 * Add settings submenu.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function settings_menu() {
		add_submenu_page(
			'utm-manager',
			__( 'Settings', 'utm-manager' ),
			__( 'Settings', 'utm-manager' ),
			'manage_options',
			'utmm-settings',
			array( $this, 'settings_page' ),
		);
	}

	/**
	 * Render settings page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function settings_page() {
		include __DIR__ . '/views/settings.php';
	}

	/**
	 * Load master keys page & set screen options.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function load_leads_page() {
		$screen = get_current_screen();
		if ( 'toplevel_page_utm-manager' === $screen->id ) {
			add_screen_option(
				'per_page',
				array(
					'label'   => __( 'Leads per page', 'utm-manager' ),
					'default' => 20,
					'option'  => 'utmm_leads_per_page',
				)
			);
		}
	}

	/**
	 * Set screen options.
	 *
	 * @param bool       $screen_option Whether it is true or false.
	 * @param string     $option Option id.
	 * @param string|int $value The option value.
	 *
	 * @since 1.0.0
	 * @return mixed|int
	 */
	public static function set_screen( $screen_option, $option, $value ) {
		return $value;
	}

	/**
	 * Render menu page content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_page() {
		wp_verify_nonce( '_nonce' );
		$view = isset( $_GET['view'] ) ? absint( $_GET['view'] ) : 0;

		if ( $view ) {
			$lead = utmm_get_lead( $view );

			if ( ! $lead instanceof \WP_Post ) {
				wp_safe_redirect( remove_query_arg( 'view' ) );
				exit();
			}
		}

		if ( $view ) {
			include __DIR__ . '/views/view-lead.php';
		} else {
			include __DIR__ . '/views/leads.php';
		}
	}
}
