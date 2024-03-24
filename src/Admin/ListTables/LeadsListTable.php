<?php

namespace UTMManager\Admin\ListTables;

use WP_Screen;

defined( 'ABSPATH' ) || exit;

/**
 * LeadsListTable class.
 *
 * @since 1.0.0
 * @package WooCommerceStarterPlugin
 */
class LeadsListTable extends AbstractListTable {
	/**
	 * Get Leads started
	 *
	 * @param array $args Optional.
	 *
	 * @see WP_List_Table::__construct()
	 * @since  1.0.0
	 */
	public function __construct( $args = array() ) {
		$args         = (array) wp_parse_args(
			$args,
			array(
				'singular' => 'lead',
				'plural'   => 'leads',
			)
		);
		$this->screen = get_current_screen();
		// Add screen custom pagination option.
		add_screen_option( 'per_page', array(
			'default' => 20,
			'option' => 'utmm_leads_per_page',
		) );
		parent::__construct( $args );
	}

	/**
	 * Retrieve all the data for the table.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$sortable              = $this->get_sortable_columns();
		$hidden                = $this->get_hidden_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$args = array(
			'limit'       => $this->get_per_page(),
			'offset'      => $this->get_offset(),
			'search'      => $this->get_search(),
			'order'       => $this->get_order( 'ASC' ),
			'orderby'     => $this->get_orderby( 'post_status' ),
			'post_status' => 'any',
		);

		$meta_props = array(
			'order_id'      => '_order_id',
			'product_id'    => '_product_id',
			'order_item_id' => '_order_item_id',
			'customer_id'   => '_customer_id',
		);
		// If the orderby param is within $meta_props.
		if ( in_array( $args['orderby'], array_keys( $meta_props ), true ) ) {
			$args['meta_key'] = $meta_props[ $args['orderby'] ]; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = 'meta_value_num';
		}

		$this->items       = utmm_get_leads( $args );
		$this->total_count = utmm_get_leads( $args, true );

		$this->set_pagination_args(
			array(
				'total_items' => $this->total_count,
				'per_page'    => 20, // $this->get_per_page(),
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

	public static function define_columns() {

		// TODO: Need update this method with the screen options.

//		$hidden_columns = get_hidden_columns( get_current_screen() );
//		var_dump(get_hidden_columns( get_current_screen() ));
//		$screen_columns =

		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'name'         => __( 'IP', 'utm-manager' ),
			'utm_id'      => __( 'UTM ID', 'utm-manager' ),
			'utm_source'      => __( 'UTM Source', 'utm-manager' ),
			'utm_medium'      => __( 'UTM Medium', 'utm-manager' ),
			'utm_campaign'      => __( 'UTM Campaign', 'utm-manager' ),
			'utm_term'      => __( 'UTM Term', 'utm-manager' ),
			'content'      => __( 'UTM Content', 'utm-manager' ),
			'date' => __( 'Date', 'utm-manager' ),
			'status' => __( 'Status', 'utm-manager' ),
		);

		return $columns;
	}

	/**
	 * Get the table columns
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_columns() {
		return get_column_headers( get_current_screen() );

//		return array(
//			'cb'           => '<input type="checkbox" />',
//			'name'         => __( 'Name', 'utm-manager' ),
//			'content'      => __( 'Content', 'utm-manager' ),
//			'date' => __( 'Date', 'utm-manager' ),
//			'status' => __( 'Status', 'utm-manager' ),
//		);
	}

	/**
	 * Get the table sortable columns
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'name'         => array( 'post_title', true ),
			'utm_id'      => array( 'utm_id', true ),
			'utm_source'    => array( 'utm_source', true ),
			'utm_medium'    => array( 'utm_medium', true ),
			'utm_campaign'  => array( 'utm_campaign', true ),
			'utm_term'      => array( 'utm_term', true ),
			'content'      => array( 'post_content', true ),
			'date'         => array( 'post_date', true ),
		);
	}

	/**
	 * Get the table hidden columns
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Get bulk actions
	 *
	 * since 1.0.0
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'delete' => __( 'Delete', 'utm-manager' ),
		);
	}

	/**
	 * Process bulk action.
	 *
	 * @param string $doaction Action name.
	 *
	 * @since 1.0.2
	 */
	public function process_bulk_action( $doaction ) {
		if ( ! empty( $doaction ) && check_admin_referer( 'bulk-' . $this->_args['plural'] ) ) {
			$id  = filter_input( INPUT_GET, 'id' );
			$ids = filter_input( INPUT_GET, 'ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			if ( ! empty( $id ) ) {
				$ids      = wp_parse_id_list( $id );
				$doaction = ( - 1 !== $_REQUEST['action'] ) ? $_REQUEST['action'] : $_REQUEST['action2']; // phpcs:ignore
			} elseif ( ! empty( $ids ) ) {
				$ids = array_map( 'absint', $ids );
			} elseif ( wp_get_referer() ) {
				wp_safe_redirect( wp_get_referer() );
				exit;
			}

			switch ( $doaction ) {
				case 'delete':
					$deleted = 0;
					foreach ( $ids as $id ) {
						$lead = utmm_get_lead( $id );
						if ( $lead && $lead->delete() ) {
							$deleted ++;
						}
					}
					// translators: %d: number of leads deleted.
					utm_manager()->add_notice( sprintf( _n( '%d lead deleted.', '%d leads deleted.', $deleted, 'utm-manager' ), $deleted ) );
					break;
			}

			wp_safe_redirect( remove_query_arg( array( 'action', 'action2', 'id', 'ids', 'paged' ) ) );
			exit();
		}

		parent::process_bulk_actions( $doaction );
	}

	/**
	 * Define primary column.
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
	 * @param Lead $item The current lead object.
	 *
	 * @since  1.0.0
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="ids[]" value="%d"/>', esc_attr( $item->ID ) );
	}

	/**
	 * Renders the name column in the items list table.
	 *
	 * @param Lead $item The current lead object.
	 *
	 * @since  1.0.0
	 * @return string Displays the lead name.
	 */
	public function column_name( $item ) {
		$admin_url = admin_url( 'admin.php?page=wc-starter-plugin&tab=lead' );
		$id_url    = add_query_arg( 'id', $item->ID, $admin_url );
		$actions   = array(
			'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'edit_lead', $item->ID, $admin_url ) ), __( 'Edit', 'utm-manager' ) ),
			'delete' => sprintf( '<a href="%s">%s</a>', wp_nonce_url( add_query_arg( 'action', 'delete', $id_url ), 'bulk-leads' ), __( 'Delete', 'utm-manager' ) ),
		);

		return sprintf( '<a href="%s">%s</a> %s', esc_url( add_query_arg( 'edit_lead', $item->ID, $admin_url ) ), esc_html( $item->post_title ), $this->row_actions( $actions ) );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param Object $item The current lead object.
	 * @param string $column_name The name of the column.
	 *
	 * @since 1.0.0
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
				$value = $item->post_content;
				break;

			case 'date':
				$date = $item->post_date;
				if ( $date ) {
					$value = sprintf( '<time datetime="%s">%s</time>', esc_attr( $date ), esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) );
				}
				break;

			default:
				$value = parent::column_default( $item, $column_name );
		}

		return $value;
	}
}
