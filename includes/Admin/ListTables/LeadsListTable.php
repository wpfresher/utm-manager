<?php

namespace WpFreshers\UTMManager\Admin\ListTables;

defined( 'ABSPATH' ) || exit;

// WP_List_Table is not loaded automatically, so we need to load it in our application.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class Leads List Table.
 *
 * @since 1.0.0
 * @package WpFreshers\UTMManager\Admin\ListTables
 */
class LeadsListTable extends \WP_List_Table {

	/**
	 * The total items count.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $total_count;

	/**
	 * Leads list table constructor.
	 *
	 * @param array $args An associative array of arguments.
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 * @since 1.0.0
	 */
	public function __construct( $args = array() ) {
		parent::__construct(
			wp_parse_args(
				$args,
				array(
					'singular' => 'lead',
					'plural'   => 'leads',
					'screen'   => get_current_screen(),
					'args'     => array(),
				),
			),
		);
	}

	/**
	 * Paper items.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {
		wp_verify_nonce( '_wpnonce' );
		$per_page              = $this->get_items_per_page( 'utmm_leads_per_page', 20 );
		$columns               = $this->get_columns();
		$hidden                = $this->get_hidden_columns();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$order_by              = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : '';
		$order                 = isset( $_GET['order'] ) ? sanitize_key( wp_unslash( $_GET['order'] ) ) : '';
		$search                = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

		/**
		 * Processing bulk action.
		 */
		$this->process_bulk_action();

		$args = array(
			'post_type'      => 'utmm_lead',
			'posts_per_page' => $per_page,
			'paged'          => $this->get_pagenum(),
			's'              => $search,
			'orderby'        => $order_by,
			'order'          => $order,
			'post_status'    => 'any',
		);

		$this->items       = utmm_get_leads( $args );
		$this->total_count = utmm_get_leads( $args, true );

		$this->set_pagination_args(
			array(
				'total_items' => $this->total_count,
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
		esc_html_e( 'No items found.', 'utm-manager' );
	}

	/**
	 * Define columns.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function define_columns() {
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
	 * Get the table columns.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_columns() {
		return get_column_headers( get_current_screen() );
	}

	/**
	 * Get hidden columns.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_hidden_columns() {
		return get_hidden_columns( get_current_screen() );
	}

	/**
	 * Get sortable columns.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_sortable_columns() {
		return array( 'name' => array( 'post_title', true ) );
	}

	/**
	 * Get primary columns name. or define the primary column name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_primary_column_name() {
		return 'name';
	}

	/**
	 * Renders the checkbox column in the items list table.
	 *
	 * @param Object $item The current master key object.
	 *
	 * @since  1.0.0
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="ids[]" value="%d"/>', esc_attr( $item->ID ) );
	}


	/**
	 * Renders the master_key column in the items list table.
	 *
	 * @param Object $item The current master key object.
	 *
	 * @since  1.0.0
	 * @return string Displays the Master key.
	 */
	public function column_name( $item ) {
		$view_url   = add_query_arg( array( 'view' => $item->ID ), admin_url( 'admin.php?page=utm-manager' ) );
		$delete_url = add_query_arg(
			array(
				'id'     => $item->ID,
				'action' => 'delete',
			),
			admin_url( 'admin.php?page=utm-manager' )
		);
		$item_title = sprintf( '<a href="%1$s">%2$s</a>', $view_url, esc_html( $item->post_title ) );
		// translators: %d: key id.
		$actions['id']     = sprintf( __( 'ID: %d', 'utm-manager' ), esc_html( $item->ID ) );
		$actions['view']   = sprintf( '<a href="%1$s">%2$s</a>', $view_url, __( 'View', 'utm-manager' ) );
		$actions['delete'] = sprintf( '<a href="%1$s">%2$s</a>', wp_nonce_url( $delete_url, 'bulk-utmm_lead' ), __( 'Delete', 'utm-manager' ) );

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
	 * Handle bulk actions.
	 *
	 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	 * For this example package, we will handle it in the class to keep leads
	 * clean and organized.
	 *
	 * @see $this->prepare_items()
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function process_bulk_action() {

		$referer = wp_get_referer();

		// Detect when a bulk action is being triggered.
		if ( 'delete' === $this->current_action() ) {

			$id  = filter_input( INPUT_GET, 'id' );
			$ids = filter_input( INPUT_GET, 'ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

			if ( ! empty( $id ) ) {
				$ids = wp_parse_id_list( $id );
			} elseif ( ! empty( $ids ) ) {
				$ids = array_map( 'absint', $ids );
			} elseif ( wp_get_referer() ) {
				wp_safe_redirect( $referer );
				exit;
			}

			$deleted = 0;
			foreach ( $ids as $id ) {
				$lead = utmm_get_lead( $id );
				if ( $lead && wp_delete_post( $lead->ID, true ) ) {
					++$deleted;
				}
			}
		}
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param Object $item The current master key object.
	 * @param string $column_name The name of the column.
	 *
	 * @since 1.0.0
	 * @return mixed | string
	 */
	public function column_default( $item, $column_name ) {

		$value = '&mdash;';

		switch ( $column_name ) {

			case 'utm_id':
				$utm_id = get_post_meta( $item->ID, '_utmm_utm_id', true );
				if ( $utm_id ) {
					$value = sprintf( '<span>%s</span>', esc_html( $utm_id ) );
				}
				break;

			case 'utm_source':
				$utm_source = get_post_meta( $item->ID, '_utmm_utm_source', true );
				if ( $utm_source ) {
					$value = sprintf( '<span>%s</span>', esc_html( $utm_source ) );
				}
				break;

			case 'utm_medium':
				$utm_medium = get_post_meta( $item->ID, '_utmm_utm_medium', true );
				if ( $utm_medium ) {
					$value = sprintf( '<span>%s</span>', esc_html( $utm_medium ) );
				}
				break;

			case 'utm_campaign':
				$utm_campaign = get_post_meta( $item->ID, '_utmm_utm_campaign', true );
				if ( $utm_campaign ) {
					$value = sprintf( '<span>%s</span>', esc_html( $utm_campaign ) );
				}
				break;

			case 'utm_term':
				$utm_term = get_post_meta( $item->ID, '_utmm_utm_term', true );
				if ( $utm_term ) {
					$value = sprintf( '<span>%s</span>', esc_html( $utm_term ) );
				}
				break;

			case 'content':
				$utm_content = $item->post_content;
				if ( $utm_content ) {
					$value = wp_kses_post( $utm_content );
				}
				break;

			case 'date':
				$date = $item->post_date;
				if ( $date ) {
					$value = sprintf( '<time datetime="%s">%s</time>', esc_attr( $date ), esc_html( date_i18n( get_option( 'date_format' ) . ' | ' . get_option( 'time_format' ), strtotime( $date ) ) ) );
				}
				break;
		}

		return $value;
	}
}
