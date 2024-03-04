<?php

namespace UTMManager;

defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin.
 *
 * @since 1.0.0
 *
 * @package UTMManager
 */
class Plugin extends Lib\Plugin {
	/**
	 * Plugin constructor.
	 *
	 * @param array $data The plugin data.
	 *
	 * @since 1.0.0
	 */
	protected function __construct( $data ) {
		parent::__construct( $data );
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}
	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function define_constants() {
		$this->define( 'WCSP_VERSION', $this->get_data( 'version' ) );
		$this->define( 'WCSP_FILE', $this->get_data( 'file' ) );
		$this->define( 'WCSP_PATH', $this->get_data( 'dir_path' ) );
		$this->define( 'WCSP_URL', $this->get_data( 'dir_url' ) );
		$this->define( 'WCSP_ASSETS_URL', $this->get_data( 'assets_url' ) );
		$this->define( 'WCSP_ASSETS_PATH', $this->get_data( 'assets_path' ) );
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
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init_hooks() {
		add_action( 'admin_notices', array( $this, 'dependencies_notices' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Missing dependencies notice.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function dependencies_notices() {
		if ( ! $this->is_plugin_active( 'woocommerce' ) ) {
			return;
		}
		$notice = sprintf(
		/* translators: 1: plugin name 2: WooCommerce */
			__( '%1$s requires %2$s to be installed and active.', 'utm-manager' ),
			'<strong>' . esc_html( $this->data['name'] ) . '</strong>',
			'<strong>' . esc_html__( 'WooCommerce', 'utm-manager' ) . '</strong>'
		);

		echo '<div class="notice notice-error"><p>' . wp_kses_post( $notice ) . '</p></div>';
	}

	/**
	 * Init hook.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {

		$this->services->add( PostTypes::class );
		$this->services->add( Models\Lead::class );

		if ( self::is_request( 'admin' ) ) {
			$this->services->add( Admin\Admin::class );
		}

		// Init action.
		do_action( 'utm_manager_init' );
	}
}
