<?php

namespace WpFreshers\UTMManager;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * The main plugin class.
 *
 * @since 1.0.0
 * @package WpFreshers\UTMManager
 */
class Plugin {

	/**
	 * Plugin file path.
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	protected $version = '1.0.0';

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
	 * @param string $file The plugin file path.
	 * @param string $version The plugin version.
	 *
	 * @since 1.0.0
	 * @return static
	 */
	final public static function create( $file, $version = '1.0.0' ) {
		if ( null === self::$instance ) {
			self::$instance = new static( $file, $version );
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @param string $file The plugin file path.
	 * @param string $version The plugin version.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $file, $version ) {
		$this->file    = $file;
		$this->version = $version;
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define plugin constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function define_constants() {
		define( 'UTMM_VERSION', $this->version );
		define( 'UTMM_FILE', $this->file );
		define( 'UTMM_PATH', plugin_dir_path( $this->file ) );
		define( 'UTMM_URL', plugin_dir_url( $this->file ) );
		define( 'UTMM_ASSETS_URL', UTMM_URL . 'assets/' );
	}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function includes() {
		require_once __DIR__ . '/Functions.php';
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function init_hooks() {
		register_activation_hook( UTMM_FILE, array( $this, 'activate' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'admin_notices', array( $this, 'dependencies_notices' ) );
		add_action( 'admin_notices', array( $this, 'display_flash_notices' ), 12 );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Plugin activation hook.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function activate() {
		update_option( 'utmm_version', UTMM_VERSION );
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'utm-manager', false, dirname( plugin_basename( UTMM_FILE ) ) . '/languages/' );
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
	 * Missing dependencies notice.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function dependencies_notices() {
		if ( self::is_plugin_active( 'woocommerce' ) || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$plugin            = 'woocommerce/woocommerce.php';
		$installed_plugins = get_plugins();
		if ( isset( $installed_plugins[ $plugin ] ) ) {
			$notice = sprintf(
			/* translators: 1: plugin name 2: WooCommerce */
				__( '%1$s requires %2$s to be activated. %3$s', 'utm-manager' ),
				'<strong>' . esc_html__( 'UTM Manager', 'utm-manager' ) . '</strong>',
				'<strong>' . esc_html__( 'WooCommerce', 'utm-manager' ) . '</strong>',
				sprintf(
					'<a href="%s">%s</a>',
					esc_url( wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $plugin ), 'activate-plugin_' . $plugin ) ),
					esc_html__( 'Activate WooCommerce', 'utm-manager' )
				)
			);
		} else {
			$notice = sprintf(
			/* translators: 1: plugin name 2: WooCommerce */
				__( '%1$s requires %2$s to be installed and activated. %3$s', 'utm-manager' ),
				'<strong>' . esc_html__( 'UTM Manager', 'utm-manager' ) . '</strong>',
				'<strong>' . esc_html__( 'WooCommerce', 'utm-manager' ) . '</strong>',
				sprintf(
					'<a href="%s">%s</a>',
					esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' ) ),
					esc_html__( 'Install WooCommerce', 'utm-manager' )
				)
			);
		}
		echo '<div class="error"><p>' . wp_kses_post( $notice ) . '</p></div>';
	}

	/**
	 * Add a flash notice.
	 *
	 * @param string  $notice Notice message.
	 * @param string  $type This can be "info", "warning", "error" or "success", "success" as default.
	 * @param boolean $dismissible Whether the notice is-dismissible or not.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_flash_notice( $notice = '', $type = 'success', $dismissible = true ) {
		$notices          = get_option( 'utmm_flash_notices', array() );
		$dismissible_text = ( $dismissible ) ? 'is-dismissible' : '';

		// Add new notice.
		array_push(
			$notices,
			array(
				'notice'      => $notice,
				'type'        => $type,
				'dismissible' => $dismissible_text,
			)
		);

		// Update the notices array.
		update_option( 'utmm_flash_notices', $notices );
	}

	/**
	 * Display flash notices after that, remove the option to prevent notices being displayed forever.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function display_flash_notices() {
		$notices = get_option( 'utmm_flash_notices', array() );

		foreach ( $notices as $notice ) {
			printf(
				'<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
				esc_attr( $notice['type'] ),
				esc_attr( $notice['dismissible'] ),
				esc_html( $notice['notice'] ),
			);
		}

		// Reset options to prevent notices being displayed forever.
		if ( ! empty( $notices ) ) {
			delete_option( 'utmm_flash_notices', array() );
		}
	}

	/**
	 * Initialize the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		new PostTypes();
		new Admin\Admin();
		new Controllers\Actions();
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @param string $hook Hook name.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts( $hook ) {
		$screens = array(
			'toplevel_page_utm-manager',
			'utm-manager_page_utmm-settings',
		);

		wp_register_style( 'utmm-admin', UTMM_URL . 'assets/dist/css/utmm-admin.css', array(), '1.0.0' );
		wp_register_script( 'utmm-admin', UTMM_URL . 'assets/dist/js/utmm-admin.js', array( 'jquery' ), '1.0.0', true );

		if ( in_array( $hook, $screens, true ) ) {
			wp_enqueue_style( 'utmm-admin' );
			wp_enqueue_script( 'utmm-admin' );
		}
	}
}
