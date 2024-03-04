<?php

namespace UTMManager\Models;

use UTMManager\Lib\Data;

defined( 'ABSPATH' ) || exit();

/**
 * Class Lead.
 *
 * @since 1.0.0
 * @package UTMManager\Models
 */
class Lead extends Data {
	/**
	 * Post type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $post_type = 'utmm_lead';

	/**
	 * All data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array All data.
	 */
	protected $data = array(
		'name'   => 0,
		'status' => 0,
	);

	/**
	 * Save data.
	 *
	 * @since 1.0.0
	 * @return $this|\WP_Error Post object (or WP_Error on failure).
	 */
	public function save() {
		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'missing_required', __( 'Please enter a name for the lead.', 'utm-manager' ) );
		}

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Getters and Setters.
	|--------------------------------------------------------------------------
	| Getters and setters for the data properties.
	*/
	/**
	 * Get id.
	 *
	 * @since 1.0.0
	 * @return int
	 */
	public function get_id() {
		return $this->get_prop( 'ID' );
	}

	/**
	 * Set id.
	 *
	 * @param int $id The id.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_id( $id ) {
		$this->set_prop( 'ID', absint( $id ) );
	}

	/**
	 * Get name.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_name() {
		return $this->get_prop( 'post_title' );
	}

	/**
	 * Set name.
	 *
	 * @param string $name The name.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_name( $name ) {
		$this->set_prop( 'post_title', sanitize_text_field( $name ) );
	}

	/**
	 * Get status.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_status() {
		return $this->get_prop( 'post_status' );
	}

	/**
	 * Set status.
	 *
	 * @param string $status The status.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_status( $status ) {
		$this->set_prop( 'post_status', sanitize_text_field( $status ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Actions
	|--------------------------------------------------------------------------
	| methods that does actions.
	*/


	/*
	|--------------------------------------------------------------------------
	| Helpers
	|--------------------------------------------------------------------------
	| Methods which do not modify class properties but are used by the class.
	*/

}
