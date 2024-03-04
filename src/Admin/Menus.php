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
		// add_action( 'wc_donation_manager_leads_content', array( $this, 'render_leads_content' ) );
		// add_action( 'wc_donation_manager_donors_content', array( $this, 'render_donors_content' ) );

		add_action( utm_manager()->get_data('prefix') . '_admin_field_custom_field_type', array( $this, 'render_custom_field_type' ) );
	}

	public function render_custom_field_type( $value ){
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

		add_submenu_page(
			'utm-manager',
			esc_html__( 'UTM Logs', 'utm-manager' ),
			esc_html__( 'UTM Logs', 'utm-manager' ),
			'manage_options',
			'utm-manager',
			array( $this, 'output_main_page' )
		);
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
		$add_lead  = isset( $_GET['new'] ) ? true : false;
		$edit_lead = isset( $_GET['edit_lead'] ) ? absint( wp_unslash( $_GET['edit_lead'] ) ) : '';
		$view_lead = isset( $_GET['view_lead'] ) ? absint( wp_unslash( $_GET['view_lead'] ) ) : '';

		if ( $edit_lead && ! wcdm_get_lead( $edit_lead ) ) {
//			wp_safe_redirect( admin_url( 'admin.php?page=utm-manager' ) );
//			exit();
		}

		if ( $view_lead && ! wcdm_get_lead( $view_lead ) ) {
//			wp_safe_redirect( admin_url( 'admin.php?page=utm-manager' ) );
//			exit();
		}

		if ( $add_lead ) {
			include __DIR__ . '/views/add-lead.php';
		} elseif ( $edit_lead ) {
			include __DIR__ . '/views/edit-lead.php';
		} elseif ( $view_lead ) {
			include __DIR__ . '/views/view-lead.php';
		} else {
			include __DIR__ . '/views/list-lead.php';
		}
	}
}
