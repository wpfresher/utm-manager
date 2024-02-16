<?php

namespace UTMSourceTracker\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Menus class.
 *
 * @since 1.0.0
 * @package UTMSourceTracker
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
		// add_action( 'wc_donation_manager_campaigns_content', array( $this, 'render_campaigns_content' ) );
		// add_action( 'wc_donation_manager_donors_content', array( $this, 'render_donors_content' ) );

		add_action( utm_source_tracker()->get_data('prefix') . '_admin_field_custom_field_type', array( $this, 'render_custom_field_type' ) );
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
			esc_html__( 'UTM Source', 'utm-source-tracker' ),
			esc_html__( 'UTM Source', 'utm-source-tracker' ),
			'manage_options',
			'utm-source-tracker',
			null,
			'dashicons-money-alt',
			'55.5'
		);

		add_submenu_page(
			'utm-source-tracker',
			esc_html__( 'UTM Logs', 'utm-source-tracker' ),
			esc_html__( 'UTM Logs', 'utm-source-tracker' ),
			'manage_options',
			'utm-source-tracker',
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
			'utm-source-tracker',
			__( 'Settings', 'utm-source-tracker' ),
			__( 'Settings', 'utm-source-tracker' ),
			'manage_options',
			'utmst-settings',
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
		$page_hook = 'utm-logs';
		include __DIR__ . '/views/admin-page.php';
	}

	/**
	 * Render campaigns content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_campaigns_content() {
		$add_campaign  = isset( $_GET['new'] ) ? true : false;
		$edit_campaign = isset( $_GET['edit_campaign'] ) ? absint( wp_unslash( $_GET['edit_campaign'] ) ) : '';
		$view_campaign = isset( $_GET['view_campaign'] ) ? absint( wp_unslash( $_GET['view_campaign'] ) ) : '';

		if ( $edit_campaign && ! wcdm_get_campaign( $edit_campaign ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=utm-source-tracker' ) );
			exit();
		}

		if ( $view_campaign && ! wcdm_get_campaign( $view_campaign ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=utm-source-tracker' ) );
			exit();
		}

		if ( $add_campaign ) {
			include __DIR__ . '/views/add-campaign.php';
		} elseif ( $edit_campaign ) {
			include __DIR__ . '/views/edit-campaign.php';
		} elseif ( $view_campaign ) {
			include __DIR__ . '/views/view-campaign.php';
		} else {
			include __DIR__ . '/views/list-campaigns.php';
		}
	}
}
