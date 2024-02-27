<?php

namespace UTMSourceTracker;

defined( 'ABSPATH' ) || exit;

/**
 * Class PostTypes.
 *
 * Responsible for registering custom post types.
 *
 * @package UTMSourceTracker
 * @since 1.0.0
 */
class PostTypes {

	/**
	 * CPT constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_cpt' ] );
	}

	/**
	 * Register custom post types.
	 *
	 * @since 1.0.0
	 */
	public function register_cpt() {
		$labels = array(
			'name'               => _x( 'Leads', 'post type general name', 'utm-source-tracker' ),
			'singular_name'      => _x( 'Lead', 'post type singular name', 'utm-source-tracker' ),
			'menu_name'          => _x( 'Leads', 'admin menu', 'utm-source-tracker' ),
			'name_admin_bar'     => _x( 'Lead', 'add new on admin bar', 'utm-source-tracker' ),
			'add_new'            => _x( 'Add New', 'ticket', 'utm-source-tracker' ),
			'add_new_item'       => __( 'Add New Lead', 'utm-source-tracker' ),
			'new_item'           => __( 'New Lead', 'utm-source-tracker' ),
			'edit_item'          => __( 'Edit Lead', 'utm-source-tracker' ),
			'view_item'          => __( 'View Lead', 'utm-source-tracker' ),
			'all_items'          => __( 'All Leads', 'utm-source-tracker' ),
			'search_items'       => __( 'Search Leads', 'utm-source-tracker' ),
			'parent_item_colon'  => __( 'Parent Leads:', 'utm-source-tracker' ),
			'not_found'          => __( 'No tickets found.', 'utm-source-tracker' ),
			'not_found_in_trash' => __( 'No tickets found in Trash.', 'utm-source-tracker' ),
		);

		$args = array(
			'labels'              => apply_filters( 'wc_start_plugin_lead_post_type_labels', $labels ),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'query_var'           => false,
			'can_export'          => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => null,
			'supports'            => [],
		);

		register_post_type( 'utmst_lead', apply_filters( 'utm_source_tracker_lead_post_type_args', $args ) );
	}

}
