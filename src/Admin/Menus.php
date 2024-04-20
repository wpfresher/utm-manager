<?php

namespace UTMManager\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Menus class.
 *
 * @since 1.0.0
 * @package UTMManager
 */
class Menus {

	/**
	 * Menus constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'main_menu' ) );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 100 );
		add_action( 'utm_manager_leads_content', array( $this, 'output_leads_content' ) );

		// Screen options for leads.
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );

		// Settings custom fields example.
		add_action( utm_manager()->get_data('prefix') . '_admin_field_custom_field_type', array( $this, 'render_custom_field_type' ) );
	}

	public function render_custom_field_type( $value ){
		// Write your custom fields code here...
		echo 'Title: ' . $value['title'] . ', Type: ' . $value['type'];
	}

	/**
	 * Main menu.
	 *
	 * @since 1.0.0
	 */
	public function main_menu() {
		add_menu_page(
			esc_html__( 'UTM Manager', 'utm-manager' ),
			esc_html__( 'UTM Manager', 'utm-manager' ),
			'manage_options',
			'utm-manager',
			null,
			'dashicons-money-alt',
			'55.5'
		);

		$load = add_submenu_page(
			'utm-manager',
			esc_html__( 'UTM Leads', 'utm-manager' ),
			esc_html__( 'UTM Leads', 'utm-manager' ),
			'manage_options',
			'utm-manager',
			array( $this, 'output_main_page' )
		);

		add_action( 'load-' . $load, array( __CLASS__, 'load_leads_page' ) );
	}

	/**
	 * Settings menu.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function settings_menu() {
		add_submenu_page(
			'utm-manager',
			__( 'Settings', 'utm-manager' ),
			__( 'Settings', 'utm-manager' ),
			'manage_options',
			'utmm-settings',
			array( Settings::class, 'output' )
		);
	}

	/**
	 * Load Leads page
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
					'label'   => __( 'Leads per page', 'utmm-manager' ),
					'default' => 20,
					'option'  => 'utmm_leads_per_page',
				)
			);
		}
	}

	/**
	 * Set screen options.
	 *
	 * @param bool $screen_option Whether it is true or false.
	 * @param string $option Option id.
	 * @param string|int $value The option value.
	 *
	 * @since 1.0.0
	 * @return mixed|int
	 */
	public static function set_screen( $screen_option, $option, $value ) {
		return $value;
	}

	/**
	 * Output main page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function output_main_page() {
		$page_hook = 'leads';
		include __DIR__ . '/views/admin-page.php';
	}

	/**
	 * Render leads content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function output_leads_content() {
		$view_lead = isset( $_GET['view_lead'] ) ? absint( wp_unslash( $_GET['view_lead'] ) ) : '';

		if ( $view_lead && ! utmm_get_lead( $view_lead ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=utm-manager' ) );
			exit();
		}

		if ( $view_lead ) {
			include __DIR__ . '/views/view-lead.php';
		} else {
			include __DIR__ . '/views/list-lead.php';
		}
	}
}
