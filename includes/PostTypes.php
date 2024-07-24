<?php

namespace UTMManager;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Class PostTypes.
 *
 * Responsible for registering custom post types.
 *
 * @since 1.0.0
 * @package UTMManager
 */
class PostTypes {

	/**
	 * CPT constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_cpt' ) );
	}

	/**
	 * Register custom post types.
	 *
	 * @since 1.0.0
	 */
	public function register_cpt() {
		$labels = array(
			'name'               => _x( 'Leads', 'post type general name', 'utm-manager' ),
			'singular_name'      => _x( 'Lead', 'post type singular name', 'utm-manager' ),
			'menu_name'          => _x( 'Leads', 'admin menu', 'utm-manager' ),
			'name_admin_bar'     => _x( 'Lead', 'add new on admin bar', 'utm-manager' ),
			'add_new'            => _x( 'Add New', 'ticket', 'utm-manager' ),
			'add_new_item'       => __( 'Add New Lead', 'utm-manager' ),
			'new_item'           => __( 'New Lead', 'utm-manager' ),
			'edit_item'          => __( 'Edit Lead', 'utm-manager' ),
			'view_item'          => __( 'View Lead', 'utm-manager' ),
			'all_items'          => __( 'All Leads', 'utm-manager' ),
			'search_items'       => __( 'Search Leads', 'utm-manager' ),
			'parent_item_colon'  => __( 'Parent Leads:', 'utm-manager' ),
			'not_found'          => __( 'No leads found.', 'utm-manager' ),
			'not_found_in_trash' => __( 'No leads found in Trash.', 'utm-manager' ),
		);

		$args = array(
			'labels'              => apply_filters( 'utmm_lead_post_type_labels', $labels ),
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
			'supports'            => array(),
		);

		register_post_type( 'utmm_lead', apply_filters( 'utmm_lead_post_type_args', $args ) );
	}
}
