<?php

namespace EssentialElementsPro\Lib;

use function EDD\Blocks\Checkout\cart;

defined( 'ABSPATH' ) || exit;

/**
 * Template for encapsulating some of the most often required abilities of a plugin instance.
 *
 * @since   1.0.0
 * @version 1.0.1
 * @author  Kawsar Ahmed <kawsarahmed@urldev.com>
 * @package EssentialElementsPro\Lib
 * @subpackage Lib/Plugin
 */
abstract class Plugin {

	/**
	 * The plugin data store.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'api_url'   => 'https://urldev.com',
		'store_url' => 'https://urldev.com',
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

		// Register hooks.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Admin hooks.
		if ( is_admin() ) {
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
			add_filter( 'plugin_action_links_' . plugin_basename( $this->data['file'] ), array( $this, 'plugin_action_links' ) );
		}
	}

	/**
	 * Check if the plugin is active.
	 *
	 * @param string $plugin The plugin slug or basename.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_plugin_active( $plugin ) {
		// Check if the $plugin is a basename or a slug. If it's a slug, convert it to a basename.
		if ( false === strpos( $plugin, '/' ) ) {
			$plugin = $plugin . '/' . $plugin . '.php';
		}

		$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		return in_array( $plugin, $active_plugins, true ) || array_key_exists( $plugin, $active_plugins );
	}

	/**
	 * What type of request is this?
	 *
	 * @param string $type admin, ajax, cron or frontend.
	 *
	 * @since  1.1.0
	 * @return bool
	 */
	protected function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin() || ( defined( 'WP_CLI' ) && WP_CLI );
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			case 'rest':
				return defined( 'REST_REQUEST' );
		}

		return false;
	}

	/**
	 * Register plugin textdomain.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( $this->data['textdomain'], false, $this->data['domainpath'] );
	}

	/**
	 * Add plugin meta links.
	 *
	 * @param array  $links Plugin meta links.
	 * @param string $file Plugin file.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( plugin_basename( $this->data['file'] ) !== $file ) {
			return $links;
		}
		foreach ( $this->get_meta_links() as $key => $link ) {
			$links[ $key ] = sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( $link['url'] ), esc_html( $link['label'] ) );
		}

		return $links;
	}

	/**
	 * Add plugin action links.
	 *
	 * @param array $links Plugin action links.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$actions = array();
		foreach ( $this->get_action_links() as $key => $link ) {
			$actions[ $key ] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $link['url'] ), wp_kses_post( $link['label'] ) );
		}

		// Add the actions to beginning of the links.
		$links = array_merge( $actions, $links );
		if ( $this->has_premium() && ! $this->is_premium_active() ) {
			// Add UTM parameters to the URL.
			$pro_link        = add_query_arg(
				array(
					'utm_source'   => 'plugins-page',
					'utm_medium'   => 'plugin-action-link',
					'utm_campaign' => 'plugins-page',
					'utm_term'     => 'go-pro',
					'utm_id'       => $this->data['slug'],
				),
				$this->data['premium_url'],
			);
			$links['go_pro'] = sprintf( '<a href="%1$s" target="_blank" style="color: #39b54a; font-weight: bold;">%2$s</a>', esc_url( $pro_link ), esc_html__( 'Go Pro', 'essential-elements-pro' ) );
		}

		return $links;
	}

	/*
	|----------------------------
	| PLUGIN DATA
	|----------------------------
	|
	| Methods to get plugin data.
	|
	*/

	/**
	 * Get meta links.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_meta_links() {
		$links = array();
		if ( ! empty( $this->data['docs_url'] ) ) {
			$links['docs'] = array(
				'label' => __( 'Documentation', 'essential-elements-pro' ),
				'url'   => $this->data['docs_url'],
			);
		}

		if ( ! empty( $this->data['support_url'] ) ) {
			$links['support'] = array(
				'label' => __( 'Support', 'essential-elements-pro' ),
				'url'   => $this->data['support_url'],
			);
		}

		if ( ! empty( $this->data['review_url'] ) ) {
			$links['review'] = array(
				'label' => __( 'Review', 'essential-elements-pro' ),
				'url'   => $this->data['review_url'],
			);
		}

		$links['plugins'] = array(
			'label' => __( 'More Plugins', 'essential-elements-pro' ),
			'url'   => $this->data['store_url'],
		);

		return $links;
	}

	/**
	 * Get action links.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_action_links() {
		$links = array();
		if ( ! empty( $this->data['settings_url'] ) ) {
			$links['settings'] = array(
				'label' => __( 'Settings', 'essential-elements-pro' ),
				'url'   => $this->data['settings_url'],
			);
		}

		return $links;
	}

	/**
	 * Get premium plugin basename.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_premium_basename() {
		$basename = $this->data['premium_basename'];
		if ( ! empty( $basename ) && false === strpos( $basename, '/' ) ) {
			$basename = $basename . '/' . $basename . '.php';
		}

		return $basename;
	}

	/**
	 * Has premium plugin.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function has_premium() {
		return $this->get_premium_basename() && $this->data['premium_url'];
	}

	/**
	 * Is premium plugin active.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_premium_active() {
		return $this->has_premium() && $this->is_plugin_active( $this->get_premium_basename() );
	}
}
