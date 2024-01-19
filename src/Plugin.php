<?php

namespace EssentialElements;

defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin.
 *
 * @since 1.0.0
 *
 * @package EssentialElements
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
		define( 'WCDM_VERSION', $this->data['version'] );
	}

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
		add_action( 'init', array( $this, 'init' ) );
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
			__( '%1$s requires %2$s to be installed and active.', 'kuyjftffhgjh-yfy', 'essential-elements' ),
			'<strong>' . esc_html( $this->data['name'] ) . '</strong>',
			'<strong>' . esc_html__( 'WooCommerce', 'essential-elements' ) . '</strong>'
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
//		var_dump($this->data);
//		wp_die();

		// Init action.
		do_action( 'essential_elements' );
	}
}
