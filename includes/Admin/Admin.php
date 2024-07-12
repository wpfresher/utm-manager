<?php

namespace WpFreshers\UTMManager\Admin;

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
		add_action( 'load-toplevel_page_things-list-table', array( $this, 'handle_list_table_actions' ) );
	}

	/**
	 * Add menu page.
	 */
	public function add_menu() {
		add_menu_page(
			'UTM Manager',
			'UTM Manager',
			'manage_options',
			'things-list-table',
			null,
			'dashicons-admin-links',
			'100',
		);

		$load = add_submenu_page(
			'things-list-table',
			'Things',
			'Things',
			'manage_options',
			'things-list-table',
			array( $this, 'render_page' ),
		);

		// Load screen options.
		add_action( 'load-' . $load, array( __CLASS__, 'load_leads_page' ) );
	}

	/**
	 * Add settings submenu.
	 */
	public function settings_menu() {
		add_submenu_page(
			'things-list-table',
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
//		if ( 'toplevel_page_utm-manager' === $screen->id ) {
		if ( 'toplevel_page_things-list-table' === $screen->id ) {
			add_screen_option(
				'per_page',
				array(
					// 'label'   => __( 'Leads per page', 'utm-manager' ),
					'label'   => __( 'Things per page', 'utm-manager' ),
					'default' => 20,
					'option'  => 'utmm_things_per_page',
					// 'option'  => 'utmm_leads_per_page',
				)
			);
		}
	}

	/**
	 * Render things menu.
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
	public function handle_list_table_actions() {
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
}
