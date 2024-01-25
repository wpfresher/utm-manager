<?php

namespace WpStarterPlugin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin.
 *
 * @since 1.0.0
 *
 * @package WpStarterPlugin
 */
class Plugin extends Lib\PremiumPlugin {
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
	public function define_constants() {}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function includes() {}

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
		if ( $this->is_plugin_active( 'woocommerce' ) ) {
			return;
		}
		$notice = sprintf(
		/* translators: 1: plugin name 2: WooCommerce */
			__( '%1$s requires %2$s to be installed and active.', 'wp-starter-plugin' ),
			'<strong>' . esc_html( $this->data['name'] ) . '</strong>',
			'<strong>' . esc_html__( 'WooCommerce', 'wp-starter-plugin' ) . '</strong>'
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

		if ( self::is_request( 'admin' ) ) {
			$this->services->add( Admin\Admin::class );
		}

		var_dump($this->services);
		wp_die();

		// Init action.
		do_action( 'wp_starter_plugin_init' );
	}
}
