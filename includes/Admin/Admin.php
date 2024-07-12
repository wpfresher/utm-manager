<?php

namespace WpFreshers\UTMManager\Admin;

use UTMManager\Admin\ListTables\ThingsListTable;

defined( 'ABSPATH' ) || exit;

/**
 * Class Admin.
 *
 * @since 1.0.0
 * @package WpFreshers\UTMManager\Admin
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
			array( 'WpFreshers\UTMManager\Admin\ListTables\LeadsListTable', 'define_columns' ),
			10,
			0
		);
		// add_action( 'load-toplevel_page_utm-manager', array( $this, 'handle_list_table_actions' ) );
		add_action( 'admin_menu', array( $this, 'my_things_admin_menu' ) );
		add_action( 'load-toplevel_page_things-list-table', array( $this, 'things_custom_bulk_action' ) );
	}

	/**
	 * Things menu.
	 */
	public function my_things_admin_menu() {
		add_menu_page(
			'Things List Table',
			'Things List Table',
			'manage_options',
			'things-list-table',
			array( $this, 'render_things_list_table_page' ),
		);
	}

	/**
	 * Render things menu.
	 */
	public function render_things_list_table_page() {
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
			include __DIR__ . '/views/view-thing.php';
		} else {
			$list_table = new \WpFreshers\UTMManager\Admin\ListTables\ThingsListTable();
			$list_table->prepare_items();
			include __DIR__ . '/views/things.php';
		}
	}

	/**
	 * Handle things list table.
	 */
	public function things_custom_bulk_action() {
		$wp_list_table = new \WpFreshers\UTMManager\Admin\ListTables\ThingsListTable();
		$wp_list_table->process_bulk_action();

		if ( 'delete' === $wp_list_table->current_action() ) {
			// Verify nonce.
			check_admin_referer( 'bulk-things' );

			// Get selected item IDs.
			$item_ids = isset( $_REQUEST['ids'] ) ? (array) $_REQUEST['ids'] : array();

			// Process delete action.
			$performed = 0;
			foreach ( $item_ids as $item_id ) {
				$lead = utmm_get_lead( $item_id );
				if ( $lead && wp_delete_post( $lead->ID, true ) ) {
					++$performed;
				}
			}

			if ( ! empty( $performed ) ) {
				// translators: %s: number of accounts.
				utm_manager()->add_flash_notice( sprintf( __( '%s item(s) deleted successfully.', 'utm-manager' ), number_format_i18n( $performed ) ), 'success' );
			}

			// Redirect to avoid resubmission.
			$redirect_url = remove_query_arg( array( 'action', 'custom_item', '_wpnonce' ) );

			if ( headers_sent() ) {
				var_dump( headers_sent() );
				wp_die();
			}

			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Updating settings.
	 */
	public function handle_list_table_actions() {
		$wp_list_table = new \WpFreshers\UTMManager\Admin\ListTables\LeadsListTable();
		$wp_list_table->process_bulk_action();

		// var_dump( 'Hi Rakhi' );
		// wp_die();
	}

	/**
	 * Add menu page.
	 */
	public function add_menu() {
		add_menu_page(
			__( 'UTM Manager', 'utm-manager' ),
			__( 'UTM Manager', 'utm-manager' ),
			'manage_options',
			'utm-manager',
			array( $this, 'render_page' ),
			'dashicons-admin-links',
			'55.9',
		);

		// $load = add_submenu_page(
		// 'utm-manager',
		// __( 'Leads', 'utm-manager' ),
		// __( 'Leads', 'utm-manager' ),
		// 'manage_options',
		// 'utm-manager',
		// array( $this, 'render_page' ),
		// 1
		// );

		// Load screen options.
		// add_action( 'load-' . $load, array( __CLASS__, 'load_leads_page' ) );
	}

	/**
	 * Add settings submenu.
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
