<?php

namespace UTMManager\Admin\ListTables;

use PhpParser\Node\Expr\Cast\Object_;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// WP_List_Table is not loaded automatically, so we need to load it in our application.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class LeadsListTable.
 *
 * @since 1.0.0
 * @package UTMManager\Admin\ListTables
 */
class LeadsListTable extends \WP_List_Table {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->screen = get_current_screen();
		parent::__construct(
			array(
				'singular' => 'lead',
				'plural'   => 'leads',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Prepare items.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {
		wp_verify_nonce( '_wpnonce' );
		$columns               = $this->get_columns();
		$hidden                = $this->get_hidden_columns();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$per_page              = $this->get_items_per_page( 'utmm_leads_per_page', 20 );
		$paged                 = $this->get_pagenum();
		$order_by              = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';
		$order                 = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';
		$search                = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

		$args = array(
			'post_type'      => 'utmm_lead',
			'posts_per_page' => $per_page,
			'paged'          => $paged,
			's'              => $search,
			'orderby'        => $order_by,
			'order'          => $order,
			'post_status'    => 'any',
		);

		/**
		 * Filter the query arguments for the list table.
		 *
		 * @param array $args An associative array of arguments.
		 *
		 * @since 1.0.0
		 */
		$args = apply_filters( 'utmm_leads_table_query_args', $args );

		$this->items = utmm_get_leads( $args );
		$total       = utmm_get_leads( $args, true );

		$this->set_pagination_args(
			array(
				'total_items' => $total,
				'per_page'    => $per_page,
			)
		);
	}

	/**
	 * No items found text.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'No leads found.', 'utm-manager' );
	}


	/**
	 * Get the table columns
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_columns() {
		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'name'         => __( 'IP', 'utm-manager' ),
			'utm_id'       => __( 'UTM ID', 'utm-manager' ),
			'utm_source'   => __( 'UTM Source', 'utm-manager' ),
			'utm_medium'   => __( 'UTM Medium', 'utm-manager' ),
			'utm_campaign' => __( 'UTM Campaign', 'utm-manager' ),
			'utm_term'     => __( 'UTM Term', 'utm-manager' ),
			'content'      => __( 'UTM Content', 'utm-manager' ),
			'date'         => __( 'Date', 'utm-manager' ),
		);

		return $columns;
	}

	/**
	 * Get hidden columns.
	 */
	public function get_hidden_columns() {
		return get_hidden_columns( get_current_screen() );
	}

	/**
	 * Get sortable columns.
	 */
	public function get_sortable_columns() {
		return array( 'name' => array( 'post_title', true ) );
	}

	/**
	 * Get primary columns name. or define the primary column name.
	 */
	public function get_primary_column_name() {
		return 'name';
	}

	/**
	 * Renders the checkbox column in the items list table.
	 *
	 * @param Object $item The current master key object.
	 *
	 * @return string Displays a checkbox.
	 * @since  1.0.0
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="ids[]" value="%d"/>', esc_attr( $item->ID ) );
	}

	/**
	 * Renders the master_key column in the items list table.
	 *
	 * @param Object $item The current master key object.
	 *
	 * @return string Displays the Master key.
	 * @since  1.0.0
	 */
	public function column_name( $item ) {
		$view_url   = add_query_arg( array( 'view' => $item->ID ), admin_url( 'admin.php?page=utm-manager' ) );
		$delete_url = add_query_arg(
			array(
				'ids'    => $item->ID,
				'action' => 'delete',
			),
			admin_url( 'admin.php?page=utm-manager' )
		);
		$item_title = sprintf( '<a href="%1$s">%2$s</a>', $view_url, esc_html( $item->post_title ) );
		// translators: %d: key id.
		$actions['ids']    = sprintf( __( 'ID: %d', 'utm-manager' ), esc_html( $item->ID ) );
		$actions['view']   = sprintf( '<a href="%1$s">%2$s</a>', $view_url, __( 'View', 'utm-manager' ) );
		$actions['delete'] = sprintf( '<a href="%1$s">%2$s</a>', wp_nonce_url( $delete_url, 'bulk-' . $this->_args['plural'] ), __( 'Delete', 'utm-manager' ) );

		return sprintf( '%1$s %2$s', $item_title, $this->row_actions( $actions ) );
	}

	/**
	 * Get bulk actions.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'delete' => __( 'Delete', 'utm-manager' ),
		);
	}

	/**
	 * Display column utm_id.
	 *
	 * @param Object $item Item.
	 *
	 * @since 1.0.0
	 */
	protected function column_utm_id( $item ) {
		$value  = '&mdash;';
		$utm_id = get_post_meta( $item->ID, '_utmm_utm_id', true );
		if ( $utm_id ) {
			$value = sprintf( '<span>%s</span>', esc_html( $utm_id ) );
		}

		return $value;
	}

	/**
	 * Display column utm_source.
	 *
	 * @param Object $item Item.
	 *
	 * @since 1.0.0
	 */
	protected function column_utm_source( $item ) {
		$value      = '&mdash;';
		$utm_source = get_post_meta( $item->ID, '_utmm_utm_source', true );
		if ( $utm_source ) {
			$value = sprintf( '<span>%s</span>', esc_html( $utm_source ) );
		}

		return $value;
	}

	/**
	 * Display column utm_medium.
	 *
	 * @param Object $item Item.
	 *
	 * @since 1.0.0
	 */
	protected function column_utm_medium( $item ) {
		$value      = '&mdash;';
		$utm_medium = get_post_meta( $item->ID, '_utmm_utm_medium', true );
		if ( $utm_medium ) {
			$value = sprintf( '<span>%s</span>', esc_html( $utm_medium ) );
		}

		return $value;
	}

	/**
	 * Display column utm_campaign.
	 *
	 * @param Object $item Item.
	 *
	 * @since 1.0.0
	 */
	protected function column_utm_campaign( $item ) {
		$value        = '&mdash;';
		$utm_campaign = get_post_meta( $item->ID, '_utmm_utm_campaign', true );
		if ( $utm_campaign ) {
			$value = sprintf( '<span>%s</span>', esc_html( $utm_campaign ) );
		}

		return $value;
	}

	/**
	 * Display column utm_term.
	 *
	 * @param Object $item Item.
	 *
	 * @since 1.0.0
	 */
	protected function column_utm_term( $item ) {
		$value    = '&mdash;';
		$utm_term = get_post_meta( $item->ID, '_utmm_utm_term', true );
		if ( $utm_term ) {
			$value = sprintf( '<span>%s</span>', esc_html( $utm_term ) );
		}

		return $value;
	}

	/**
	 * Display column content.
	 *
	 * @param Object $item Item.
	 *
	 * @since 1.0.0
	 */
	protected function column_content( $item ) {
		$value       = '&mdash;';
		$utm_content = $item->post_content;
		if ( $utm_content ) {
			$value = wp_kses_post( $utm_content );
		}

		return $value;
	}

	/**
	 * Display column date.
	 *
	 * @param Object $item Item.
	 *
	 * @since 1.0.0
	 */
	protected function column_date( $item ) {
		$value = '&mdash;';
		$date  = $item->post_date;
		if ( $date ) {
			$value = sprintf( '<time datetime="%s">%s</time>', esc_attr( $date ), esc_html( date_i18n( get_option( 'date_format' ) . ' | ' . get_option( 'time_format' ), strtotime( $date ) ) ) );
		}

		return $value;
	}
}
