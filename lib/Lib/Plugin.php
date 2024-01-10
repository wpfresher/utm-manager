<?php

namespace EssentialElements\Lib;

defined( 'ABSPATH' ) || exit;

/**
 * Template for encapsulating some of the most often required abilities of a plugin instance.
 *
 * @since   1.0.0
 * @version 1.0.1
 * @author  Kawsar Ahmed <kawsarahmed@wpfresher.com>
 * @package EssentialElements\Lib
 * @subpackage Lib/Plugin
 */
abstract class Plugin implements PluginInterface {

	/**
	 * The plugin data store.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'api_url'   => 'https://wpfresher.com',
		'store_url' => 'https://wpfresher.com',
		'notices'   => array(),
	);

	/**
	 * The single instance of the class.
	 *
	 * @since 1.0.0
	 * @var self
	 */
	public static $instance;

	/**
	 * Gets the single instance of the class.
	 * This method is used to create a new instance of the class.
	 *
	 * @param string|array $data The plugin data.
	 *
	 * @since 1.0.0
	 * @return static
	 */
	final public static function create( $data = null ) {
		if ( is_null( static::$instance ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			$called_class = get_called_class();
			if ( ! is_array( $data ) ) {
				$file         = $data;
				$data         = array();
				$data['file'] = $file;
			}
			$file             = $data['file'];
			$plugin_data      = get_plugin_data( $file, false, false );
			$plugin_data      = array_change_key_case( $plugin_data, CASE_LOWER );
			$plugin_data      = array_merge( $plugin_data, $data );
			static::$instance = new $called_class( $plugin_data );
		}

		return static::$instance;
	}

	/**
	 * Gets the instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	final public static function get_instance() {
		_doing_it_wrong( __FUNCTION__, 'Use static::create() instead.', '1.0.5' );
		return static::instance();
	}

	/**
	 * Gets the instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	final public static function instance() {
		if ( null === static::$instance ) {
			_doing_it_wrong( __FUNCTION__, 'Plugin instance called before initiating the instance.', '1.0.0' );
		}

		return static::$instance;
	}

	/**
	 * Plugin constructor.
	 *
	 * @param array $data The plugin data.
	 *
	 * @since 1.0.0
	 */
	protected function __construct( $data ) {
		// Only set the data keys that are not already set.
		$this->data = array_merge( $this->data, $data );
		// If the slug is not set, then set it.
		if ( ! isset( $this->data['slug'] ) ) {
			$this->data['slug'] = basename( $this->data['file'], '.php' );
		}
		// If the version is not set, then set it.
		if ( ! isset( $this->data['version'] ) ) {
			$this->data['version'] = '1.0.0';
		}
		// If the prefix is not set, then set it.
		if ( ! isset( $this->data['prefix'] ) ) {
			$this->data['prefix'] = str_replace( '-', '_', $this->data['slug'] );
		}
	}
}
