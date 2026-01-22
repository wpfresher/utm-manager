<?php

namespace UTMManager;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * The main plugin class.
 *
 * @since 1.0.0
 * @package UTMManager
 */
class Plugin {

	/**
	 * Plugin file path.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $file;

	/**
	 * Plugin version.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $version = '1.0.0';

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since 1.0.0
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
		$constants = array(
			'UTMM_VERSION'     => $this->version,
			'UTMM_FILE'        => $this->file,
			'UTMM_PATH'        => plugin_dir_path( $this->file ),
			'UTMM_URL'         => plugin_dir_url( $this->file ),
			'UTMM_ASSETS_PATH' => plugin_dir_path( $this->file ) . 'assets/',
			'UTMM_ASSETS_URL'  => plugin_dir_url( $this->file ) . 'assets/',
		);

		foreach ( $constants as $name => $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}
	}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function includes() {
		require_once __DIR__ . '/functions.php';
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function init_hooks() {
		register_activation_hook( $this->file, array( Installer::class, 'install' ) );
		register_deactivation_hook( $this->file, array( Installer::class, 'deactivate' ) );
		add_action( 'admin_notices', array( $this, 'display_flash_notices' ), 12 );
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Get the plugin version.
	 *
	 * @since 1.0.0
	 * @return string The plugin version.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Get the plugin file path.
	 *
	 * @since 1.0.0
	 * @return string The plugin file path.
	 */
	public function get_file() {
		return $this->file;
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
				'notice'      => wp_kses_post( $notice ),
				'type'        => sanitize_key( $type ),
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
			echo wp_kses_post(
				sprintf(
					'<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
					esc_attr( $notice['type'] ),
					esc_attr( $notice['dismissible'] ),
					esc_html( $notice['notice'] ),
				)
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
		new Installer();
		new PostTypes();
		new Leads();

		if ( is_admin() ) {
			new Admin\Admin();
			new Controllers\Actions();
		}
	}
}
