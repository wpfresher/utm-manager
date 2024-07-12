<?php

namespace UTMManager\Admin;

use UTMManager\Admin\ListTables\LeadsListTable;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Class Admin.
 *
 * @since 1.0.0
 * @package UTMManager\Admin
 */
class Admin {

	/**
	 * Admin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 100 );
		add_filter( 'set-screen-option', array( $this, 'screen_option' ), 10, 3 );
		add_action( 'load-toplevel_page_utm-manager', array( $this, 'handle_list_table_actions' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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
			array( $this, 'render_leads_page' ),
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
	 * Set screen option.
	 *
	 * @param mixed  $status Screen option value. Default false.
	 * @param string $option Option name.
	 * @param mixed  $value New option value.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function screen_option( $status, $option, $value ) {
		$options = apply_filters(
			'utmm_set_screen_options',
			array(
				'utmm_leads_per_page',
			)
		);
		if ( in_array( $option, $options, true ) ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Load leads page & set screen options.
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
	 * Render leads page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_leads_page() {
		wp_verify_nonce( '_nonce' );
		$view = isset( $_GET['view'] ) ? absint( wp_unslash( $_GET['view'] ) ) : 0;

		if ( $view ) {
			$lead = utmm_get_lead( $view );

			if ( ! $lead instanceof \WP_Post ) {
				wp_safe_redirect( remove_query_arg( 'view' ) );
				exit();
			}

			include __DIR__ . '/views/view-lead.php';
		} else {
			$list_table = new LeadsListTable();
			$list_table->prepare_items();
			include __DIR__ . '/views/leads.php';
		}
	}

	/**
	 * Handle list table actions.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function handle_list_table_actions() {

		if ( ! current_user_can( 'manage_options' ) ) {
			utm_manager()->add_flash_notice( esc_html__( 'You do not have permission to perform this action.', 'utm-manager' ), 'error' );
			$redirect_url = remove_query_arg( array( 'action', 'action2', 'ids', '_wpnonce', '_wp_http_referer' ) );
			wp_safe_redirect( $redirect_url );
			exit;
		}

		$list_table = new LeadsListTable();
		$list_table->process_bulk_action();

		if ( 'delete' === $list_table->current_action() ) {
			check_admin_referer( 'bulk-leads' );

			$ids       = isset( $_GET['ids'] ) ? map_deep( wp_unslash( $_GET['ids'] ), 'intval' ) : array();
			$ids       = wp_parse_id_list( $ids );
			$performed = 0;

			foreach ( $ids as $id ) {
				$lead = utmm_get_lead( $id );
				if ( $lead && wp_delete_post( $lead->ID, true ) ) {
					++$performed;
				}
			}

			if ( ! empty( $performed ) ) {
				// translators: %s: number of accounts.
				utm_manager()->add_flash_notice( sprintf( esc_html__( '%s item(s) deleted successfully.', 'utm-manager' ), number_format_i18n( $performed ) ) );
			}

			if ( ! headers_sent() ) {
				// Redirect to avoid resubmission.
				$redirect_url = remove_query_arg( array( 'action', 'action2', 'ids', '_wpnonce', '_wp_http_referer' ) );
				wp_safe_redirect( $redirect_url );
				exit;
			}
		}
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @param string $hook Hook name.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts( $hook ) {
		$screens = array(
			'toplevel_page_utm-manager',
			'utm-manager_page_utmm-settings',
		);

		wp_register_style( 'utmm-admin', UTMM_ASSETS_URL . 'css/utmm-admin.css', array(), UTMM_VERSION );

		if ( in_array( $hook, $screens, true ) ) {
			wp_enqueue_style( 'utmm-admin' );
		}
	}
}
