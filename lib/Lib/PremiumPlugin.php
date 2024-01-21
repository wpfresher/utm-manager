<?php

namespace EssentialElementsPro\Lib;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Basic implementation of the Plugin interface which stores core data about a
 * WordPress plugin (Prefix, version number, etc.). Data is passed as an array on construction.
 *
 * @since 1.0.0
 * @author Kawsar Ahmed <kawsar@urldev.com>
 * @license   GPL-3.0
 *
 * @version   1.0.0
 * @package  EssentialElementsPro\Lib
 */
abstract class PremiumPlugin extends Plugin {
	/**
	 * PremiumPlugin constructor.
	 *
	 * @param array $data The plugin data.
	 *
	 * @since 1.0.0
	 */
	protected function __construct( $data ) {
		parent::__construct( $data );

		if ( empty( $this->get_item_id() ) ) {
			// translators: %s is the plugin name.
			wp_die( esc_html( sprintf( __( 'The item_id is missing for %s', 'essential-elements-pro' ), $this->data['name'] ) ) );
		}

		add_action( 'admin_footer', array( $this, 'license_notices' ), PHP_INT_MAX );
		add_action( 'plugin_action_links_' . $this->get_basename(), array( $this, 'add_license_link' ), 5 );
		add_action( 'after_plugin_row_' . $this->get_basename(), array( $this, 'add_license_row' ), PHP_INT_MAX );
		add_action( 'wp_ajax_' . $this->get_basename() . '_license_action', array( $this, 'license_ajax_handler' ) );
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
		add_action( 'wp_version_check', array( $this, 'refresh_license_status' ) );
	}

	/**
	 * Get the item ID.
	 *
	 * @since 1.0.0
	 * @return int
	 */
	public function get_item_id() {
		return (int) $this->data['item_id'];
	}

	/**
	 * get license key.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_license_key() {
		return get_option( $this->data['prefix'] . '_license_key', '' );
	}

	/**
	 * get license key status.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_license_status() {
		return get_option( $this->data['prefix'] . '_license_status', '' );
	}

	/**
	 * Is license valid.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_license_valid() {
		return 'valid' === $this->get_license_status();
	}

	/**
	 * License notices.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function license_notices() {
		if ( ! current_user_can( 'manage_options' ) || ! empty( $this->get_license_status() ) ) {
			return;
		}
		$license = $this->get_license_key();
		?>
		<div id="<?php echo esc_attr( $this->data['slug'] ); ?>-license-notice" class="notice notice-info license-notice is-dismissible" style="background-color: #f0f6fc;">
			<div class="license-notice__content" style="display:flex;align-items: center;margin:22px 0 23px;">
				<div class="license-notice__icon" style="width:60px;height: 60px;margin-right:20px;">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 720.53 720.56">
						<defs>
							<linearGradient id="a" x1="292.26" y1="744.06" x2="442.1" y2="39.12" gradientUnits="userSpaceOnUse">
								<stop offset="0" stop-color="#00aeef"/>
								<stop offset=".06" stop-color="#01a8eb"/>
								<stop offset=".45" stop-color="#098ad9"/>
								<stop offset=".77" stop-color="#0d77cd"/>
								<stop offset="1" stop-color="#0f70c9"/>
							</linearGradient>
							<linearGradient id="b" x1="150.12" y1="369.23" x2="680.76" y2="165.53" gradientUnits="userSpaceOnUse">
								<stop offset="0" stop-color="#00aeef"/>
								<stop offset=".36" stop-color="#01acef"/>
								<stop offset=".57" stop-color="#03a3ee"/>
								<stop offset=".74" stop-color="#0695ed"/>
								<stop offset=".89" stop-color="#0a82ec"/>
								<stop offset="1" stop-color="#0f6eeb"/>
							</linearGradient>
							<linearGradient id="c" x1="182.78" y1="569.81" x2="910.94" y2="544.38" gradientUnits="userSpaceOnUse">
								<stop offset="0" stop-color="#00c4ef"/>
								<stop offset=".17" stop-color="#03beeb"/>
								<stop offset=".4" stop-color="#0cace1"/>
								<stop offset=".67" stop-color="#1a8ecf"/>
								<stop offset=".97" stop-color="#2e65b7"/>
								<stop offset="1" stop-color="#3061b5"/>
							</linearGradient>
							<linearGradient id="d" x1="762.26" y1="505.41" x2="144.89" y2="505.41" gradientUnits="userSpaceOnUse">
								<stop offset="0" stop-color="#00aeef"/>
								<stop offset=".22" stop-color="#02acec"/>
								<stop offset=".41" stop-color="#07a5e1"/>
								<stop offset=".58" stop-color="#0f99d0"/>
								<stop offset=".75" stop-color="#1c89b8"/>
								<stop offset=".81" stop-color="#2182ad"/>
							</linearGradient>
							<linearGradient id="e" x1="853.19" y1="218.49" x2="232.76" y2="739.09" gradientUnits="userSpaceOnUse">
								<stop offset="0" stop-color="#00aeef"/>
								<stop offset=".5" stop-color="#02adee"/>
								<stop offset=".68" stop-color="#09a8eb"/>
								<stop offset=".8" stop-color="#14a0e6"/>
								<stop offset=".91" stop-color="#2595df"/>
								<stop offset=".99" stop-color="#3b86d5"/>
								<stop offset="1" stop-color="#3d85d4"/>
							</linearGradient>
							<linearGradient id="f" x1="456.13" y1="809.13" x2="98.8" y2="439.1" gradientUnits="userSpaceOnUse">
								<stop offset="0" stop-color="#00aeef"/>
								<stop offset=".18" stop-color="#05a8eb"/>
								<stop offset=".42" stop-color="#1297e0"/>
								<stop offset=".69" stop-color="#277ace"/>
								<stop offset=".99" stop-color="#4453b6"/>
								<stop offset="1" stop-color="#4552b5"/>
							</linearGradient>
							<linearGradient id="g" x1="583.22" y1="436.26" x2="196.91" y2="88.42" gradientUnits="userSpaceOnUse">
								<stop offset="0" stop-color="#00aeef"/>
								<stop offset=".17" stop-color="#02a8ee"/>
								<stop offset=".41" stop-color="#0696e9"/>
								<stop offset=".68" stop-color="#0d78e3"/>
								<stop offset=".99" stop-color="#164fd9"/>
								<stop offset="1" stop-color="#174dd9"/>
							</linearGradient>
							<linearGradient id="h" x1="482.73" y1="492.51" x2="146.81" y2="144.66" gradientUnits="userSpaceOnUse">
								<stop offset="0" stop-color="#00aeef"/>
								<stop offset=".3" stop-color="#0592e2"/>
								<stop offset=".76" stop-color="#0c6cd0"/>
								<stop offset="1" stop-color="#0f5ec9"/>
							</linearGradient>
							<linearGradient id="i" x1="456.07" y1="537.09" x2="529.81" y2="190.21" gradientUnits="userSpaceOnUse">
								<stop offset="0" stop-color="#00aeef"/>
								<stop offset=".29" stop-color="#01abee"/>
								<stop offset=".5" stop-color="#02a2e9"/>
								<stop offset=".68" stop-color="#0593e2"/>
								<stop offset=".84" stop-color="#097dd8"/>
								<stop offset=".99" stop-color="#0e61ca"/>
								<stop offset="1" stop-color="#0f5ec9"/>
							</linearGradient>
							<linearGradient id="j" x1="547.32" y1="56.82" x2="63.4" y2="575.76" gradientUnits="userSpaceOnUse">
								<stop offset="0" stop-color="#00aeef"/>
								<stop offset=".21" stop-color="#0496e4"/>
								<stop offset=".53" stop-color="#0a78d5"/>
								<stop offset=".81" stop-color="#0e65cc"/>
								<stop offset="1" stop-color="#0f5ec9"/>
							</linearGradient>
						</defs>
						<g style="isolation:isolate;">
							<path d="M647.62 135.52a215.65 215.65 0 0 0-26-22.06A358 358 0 0 0 402 38.72C203.08 38.72 41.73 200 41.73 399c0 8.82.43 17.56 1 26.23 17.16-38 54.35-65 99.06-65 39.53 0 76.3 22.83 95.47 56.06a275.39 275.39 0 0 1 10.27-32.07c16.63-42.93 40.34-75.95 71.05-100.55a227.15 227.15 0 0 1 50.56-32c78.65-36.63 162.14-23.92 186.47 28.33s-15.6 135-98.45 160.87c-88.83 27.73-150.92 10.52-173.48 12.95-30 11.3 4.92 31.48 4.92 31.48 58.82 27.47 133 42.92 201 35.37C611.37 507 711.58 422.48 714.61 301.2c.06-1.85.06-3.66.06-5.55a223.87 223.87 0 0 0-67.05-160.13Z" transform="translate(-41.73 -38.72)" style="fill:url(#a)"/>
							<path
								d="M621.57 113.46a357.94 357.94 0 0 0-202.91-74.34 6.05 6.05 0 0 1-.88 0c-3.46.2-7 .41-10.48.71-2.72.19-5.37.41-8.09.7-8.16.75-16.18 1.89-24.11 3.18-1.42.27-2.82.49-4.2.78-4.77.82-9.56 1.81-14.29 2.89-1.46.3-2.94.63-4.36 1-4.14 1-8.21 2-12.29 3.15-2 .52-4 1.11-6 1.7l-6 1.78c-2.65.89-5.31 1.7-8 2.63l-8.35 2.91c-2.74 1-5.47 2.1-8.19 3.12-5.33 2.15-10.57 4.3-15.77 6.67-.31.15-.53.26-.81.37-5.21 2.37-10.29 4.82-15.39 7.44-2.17 1.12-4.37 2.26-6.54 3.44-5.52 2.94-10.9 6.08-16.25 9.28-2.47 1.5-4.89 3.06-7.31 4.61s-4.9 3.12-7.29 4.75c-2 1.36-4 2.73-6 4.15-.58.41-1.2.81-1.77 1.25-1.83 1.3-3.61 2.6-5.36 4-2.67 1.93-5.34 3.93-7.95 5.93-3 2.43-6.05 4.93-9.09 7.45-2.6 2.21-5.13 4.4-7.67 6.63-3.18 2.85-6.27 5.66-9.31 8.62-1.43 1.3-2.79 2.67-4.15 4s-2.72 2.67-4.09 4.08q-5.62 5.7-11 11.73-3.64 4.06-7.12 8.11c-2.65 3.11-5.24 6.34-7.77 9.52s-5 6.45-7.45 9.74-4.84 6.71-7.14 10.07c-4.61 6.71-9 13.66-13.24 20.78s-8.07 14.29-11.79 21.66c-.08 44.18 15.79 86.58 43.56 123.35 33.68 4.88 63.81 26 80.55 55a275.39 275.39 0 0 1 10.27-32.07c16.63-42.93 40.34-75.95 71.05-100.55a227.15 227.15 0 0 1 50.56-32c36.38-16.92 73.79-23.28 105.89-20.09 52.2-4.91 97.27 12.31 114.08 48.46 24.25 52.17-15.67 135-98.47 160.87a350.16 350.16 0 0 1-121.75 15.62 430.07 430.07 0 0 1-54.9-1.44c-5.79 2.77-8.35 5.85-8.84 9.07a379 379 0 0 0 80 17.59C548.79 499.65 692.8 410.7 706.75 283a184 184 0 0 0-1.61-51.36 1.48 1.48 0 0 0 0-.45c-.13-.71-.33-1.41-.47-2.11a223.86 223.86 0 0 0-57-93.58 215.65 215.65 0 0 0-26.1-22.04Z"
								transform="translate(-41.73 -38.72)" style="fill:url(#b)"/>
							<path d="M759.14 352.34C732.55 478 621.11 572.23 487.63 572.23A266.37 266.37 0 0 1 453 570a272.54 272.54 0 0 1-65.78-16.58 276.57 276.57 0 0 1-117.52-87.21 1.68 1.68 0 0 0 .06.37A303.23 303.23 0 0 0 243 643c-4.48 1-8.91 2.11-13.29 3.48-12.59 3.92-24.85 9.44-36.72 15.33-2-57.25-4.37-112.54-3.31-169.78-.95 3.51-2 7-2.87 10.55-.38 14.4-.69 28.8-1.61 43.21-1.23 18.4-2.5 36.63-1.14 55 .6 7.91 1.32 15.85 2.19 23.74.74 4.44 2.7 17.77 2.91 19.14 2.68 17 .87 30 3.18 47.18 58.24 40.47 133.39 68.4 209.69 68.4 199 0 360.27-161.39 360.27-360.29a357.16 357.16 0 0 0-3.16-46.62Z" transform="translate(-41.73 -38.72)" style="fill:url(#c)"/>
							<path d="M759.14 352.34C732.55 478 621.11 572.23 487.63 572.23A266.37 266.37 0 0 1 453 570a272.54 272.54 0 0 1-65.78-16.58 276.8 276.8 0 0 1-117.52-87.17 1.45 1.45 0 0 0 .06.33c-1.17 2.45-2.34 4.85-3.45 7.37C314.17 581.12 449.88 658.48 610 658.48c14.87 0 29.53-.79 43.93-2.08a358.66 358.66 0 0 0 105.21-304.06Z" transform="translate(-41.73 -38.72)" style="opacity:.5;fill:url(#d);mix-blend-mode:screen"/>
							<path d="M759.38 354.52c-22 175.72-147.25 322.4-336.26 308.36-79.49-5.89-139.52-32.07-183.19-77-.18 4.32-.28 8.58-.28 12.92A305.07 305.07 0 0 0 243 643c-4.48 1-8.91 2.11-13.29 3.48-12.59 3.92-24.85 9.44-36.72 15.33-1.76-50.77-3.85-100-3.57-150.46-.91-1.92-1.81-3.85-2.67-5.82-.37 13.42-.69 26.86-1.55 40.26-1.23 18.4-2.5 36.63-1.14 55 .6 7.91 1.32 15.85 2.19 23.74.74 4.44 2.7 17.77 2.91 19.14 2.68 17 .87 30 3.18 47.18 58.24 40.47 133.39 68.4 209.69 68.4 199 0 360.27-161.39 360.27-360.29a352.61 352.61 0 0 0-2.92-44.44Z" transform="translate(-41.73 -38.72)" style="fill:url(#e);mix-blend-mode:screen"/>
							<path d="M250.84 516.79A302.8 302.8 0 0 0 243 643c-4.48 1-8.91 2.11-13.29 3.48-12.59 3.92-24.85 9.44-36.72 15.33-2-57.25-4.37-112.54-3.31-169.78-.95 3.51-2 7-2.87 10.55-.38 14.4-.69 28.8-1.61 43.21-1.23 18.4-2.5 36.63-1.14 55 .6 7.91 1.32 15.85 2.19 23.74.74 4.44 2.7 17.77 2.91 19.14 2.68 17 .87 30 3.18 47.18 58.24 40.47 133.39 68.4 209.69 68.4a358.56 358.56 0 0 0 131.57-24.93C391.28 712.18 279.29 626 250.84 516.79Z" transform="translate(-41.73 -38.72)" style="fill:url(#f);mix-blend-mode:screen"/>
							<path
								d="M673.26 283c-13.94 127.7-157.93 216.65-321.52 198.76a380.08 380.08 0 0 1-80.06-17.59c.63-3.74 4-7.33 12-10.41 22.53-2.4 84.68 14.78 173.48-12.91C540 415 580 332.15 555.67 280s-107.85-65-186.51-28.37a228.92 228.92 0 0 0-50.54 32c-3.45 2.74-6.75 5.6-10 8.59-18.58 16.86-34.36 37.26-47.22 61.59a6.66 6.66 0 0 0-.37.67 288.89 288.89 0 0 0-13.45 29.7 277.11 277.11 0 0 0-16.32 63.43c-.81-.41-1.64-.85-2.49-1.23C138.7 401.89 79.6 323.23 79.73 238c3.72-7.37 7.59-14.63 11.79-21.66s8.61-14.07 13.22-20.78c2.28-3.36 4.72-6.78 7.15-10.07s4.91-6.52 7.44-9.74 5.14-6.41 7.82-9.52c2.29-2.7 4.66-5.4 7-8.11q5.43-6 11.05-11.73c1.37-1.41 2.75-2.78 4.08-4.08s2.73-2.7 4.14-4c3.07-3 6.15-5.77 9.32-8.62 2.55-2.23 5.08-4.42 7.7-6.63 3-2.52 6-5 9.09-7.45 2.59-2 5.24-4 7.91-5.93 1.77-1.36 3.54-2.66 5.37-4 .61-.44 1.21-.84 1.78-1.25 1.94-1.42 4-2.79 6-4.15 2.36-1.63 4.77-3.22 7.25-4.75s4.86-3.11 7.34-4.61c5.29-3.2 10.76-6.34 16.25-9.28 2.2-1.18 4.36-2.32 6.54-3.44 5.07-2.62 10.17-5.07 15.37-7.44.28-.11.52-.22.84-.37 5.18-2.37 10.43-4.52 15.75-6.67 2.72-1 5.43-2.1 8.21-3.12s5.57-2 8.3-2.91 5.34-1.74 8-2.63c2-.6 4-1.19 6.05-1.78s4-1.18 6-1.7c4.08-1.15 8.17-2.19 12.29-3.15 1.42-.37 2.9-.7 4.4-1 4.7-1.08 9.48-2.07 14.27-2.89 1.36-.29 2.78-.51 4.21-.78 7.9-1.29 16-2.43 24.09-3.18 2.7-.29 5.37-.51 8.09-.7 3.48-.3 7-.51 10.52-.71 2.4-.1 4.83-.18 7.23-.25 3.5-.08 6.91-.16 10.41-.16s7.28.08 10.82.16 7.2.25 10.81.47c3 .19 6 .42 9 .68 1.42.1 2.77.22 4.12.41 2.51.17 4.93.41 7.3.74a8.49 8.49 0 0 1 1.22.11c6.46.77 12.76 1.73 19 2.81 1.74.26 3.55.63 5.24 1 2.77.48 5.49 1 8.16 1.66s5.68 1.22 8.48 1.85 5.28 1.34 7.89 2c1.64.41 3.25.82 4.81 1.29 3.09.82 6.11 1.72 9.06 2.61l7.2 2.29c6.83 2.3 13.59 4.74 20.17 7.4 2.76 1 5.46 2.19 8.19 3.37q5.37 2.27 10.64 4.78c3.07 1.41 6.1 2.88 9.14 4.41 58.16 39.2 97.93 94.2 108.37 154.41a2.43 2.43 0 0 1 0 .45 183.21 183.21 0 0 1 1.64 51.34Z"
								transform="translate(-41.73 -38.72)" style="fill:url(#g)"/>
							<path
								d="M671.65 231.66c-18.18-55.33-91.13-76.15-176.79-45.14-67 24.29-92.87 40.58-164.4 86.35a283.43 283.43 0 0 0-21.85 19.36 268.75 268.75 0 0 0-47.22 61.59 6.66 6.66 0 0 0-.37.67c-15.23 27.44-23.53 59.28-29.77 93.13a390.12 390.12 0 0 0-6 99.72c8.52 128.84 87 217-40.55 138.95-86.82-65.73-143-170-143-287.3a357.58 357.58 0 0 1 38-161c3.64-7.37 7.59-14.63 11.79-21.66s8.55-14.07 13.22-20.78c2.28-3.36 4.72-6.78 7.15-10.07s4.91-6.52 7.44-9.74 5.14-6.41 7.82-9.52c2.29-2.7 4.66-5.4 7-8.11q5.43-6 11.05-11.73c1.37-1.41 2.75-2.78 4.08-4.08s2.73-2.7 4.14-4c3.07-3 6.15-5.77 9.32-8.62 2.55-2.23 5.08-4.42 7.7-6.63 3-2.52 6-5 9.09-7.45 2.59-2 5.24-4 7.91-5.93 1.77-1.36 3.54-2.66 5.37-4 .61-.44 1.21-.84 1.78-1.25 1.94-1.42 4-2.79 6-4.15 2.36-1.63 4.77-3.22 7.25-4.75s4.86-3.11 7.34-4.61c5.29-3.2 10.76-6.34 16.25-9.28 2.2-1.18 4.36-2.32 6.54-3.44 5.07-2.59 10.17-5.07 15.37-7.44.28-.11.52-.22.84-.37 5.18-2.37 10.43-4.52 15.75-6.67 2.72-1 5.43-2.1 8.21-3.12s5.57-2 8.3-2.91 5.34-1.74 8-2.63c2-.6 4-1.19 6.05-1.78s4-1.18 6-1.7c4.08-1.15 8.17-2.19 12.29-3.15 1.42-.37 2.9-.7 4.4-1 4.7-1.08 9.48-2.07 14.27-2.89 1.36-.29 2.78-.51 4.21-.78 7.9-1.29 16-2.43 24.09-3.18 2.7-.29 5.37-.51 8.09-.7 3.48-.3 7-.51 10.52-.71 2.4-.1 4.83-.18 7.23-.25 3.5-.08 6.91-.16 10.41-.16s7.28.08 10.82.16 7.2.25 10.81.47c3 .19 6 .42 9 .68 1.42.1 2.77.22 4.12.41 2.51.17 4.93.41 7.3.74a8.49 8.49 0 0 1 1.22.11c6.39.77 12.76 1.67 19 2.81 1.74.26 3.55.63 5.24 1 2.77.48 5.49 1 8.16 1.66s5.68 1.22 8.48 1.85 5.28 1.34 7.89 2c1.64.41 3.25.82 4.81 1.29 3.09.82 6.11 1.72 9.06 2.61l7.2 2.29c6.83 2.3 13.59 4.74 20.17 7.4 2.76 1 5.46 2.19 8.19 3.37q5.37 2.27 10.64 4.78c3.07 1.41 6.1 2.88 9.14 4.41C621.41 116 661.18 171 671.62 231.21a2.43 2.43 0 0 1 .03.45Z"
								transform="translate(-41.73 -38.72)" style="fill:url(#h)"/>
							<path d="M627.42 235.36c-27-50.78-123.47-52.21-215.29-3.29-5.31 2.8-10.48 5.76-15.47 8.76 69.75-22.32 137.52-7.07 159 39.15 20.22 43.43-4.15 108.13-60.65 143.49 5.05-2.37 10.06-4.88 15.11-7.54C602 367 654.46 286.13 627.42 235.36Z" transform="translate(-41.73 -38.72)" style="fill:url(#i)"/>
							<path
								d="M212.3 681.73C83 553.16 83.13 343.49 212.52 213.36 335.26 89.93 594.8 52.49 660.3 191.92c-17.81-44.62-51.84-84.62-97-115.09-3-1.53-6.07-3-9.14-4.41q-5.28-2.51-10.64-4.78c-2.73-1.18-5.43-2.33-8.19-3.37-6.58-2.66-13.34-5.1-20.17-7.4L508 54.58c-2.95-.89-6-1.79-9.06-2.61-1.56-.47-3.17-.88-4.81-1.29-2.61-.7-5.21-1.37-7.89-2s-5.67-1.29-8.48-1.85-5.39-1.18-8.16-1.66c-1.69-.37-3.5-.74-5.24-1-6.28-1.14-12.65-2-19-2.81a8.49 8.49 0 0 0-1.22-.11c-2.37-.33-4.79-.57-7.3-.74-1.35-.19-2.7-.31-4.12-.41-3-.26-6-.49-9-.68-3.61-.22-7.23-.32-10.81-.47s-7.2-.16-10.82-.16-6.91.08-10.41.16c-2.4.07-4.83.15-7.23.25-3.5.2-7 .41-10.52.71-2.72.19-5.39.41-8.09.7-8.14.75-16.19 1.89-24.09 3.18-1.43.27-2.85.49-4.21.78-4.79.82-9.57 1.81-14.27 2.89-1.5.3-3 .63-4.4 1-4.12 1-8.21 2-12.29 3.15-2 .52-4 1.11-6 1.7s-4 1.18-6.05 1.78c-2.66.89-5.32 1.7-8 2.63s-5.54 1.93-8.3 2.91-5.49 2.1-8.21 3.12c-5.32 2.15-10.57 4.3-15.75 6.67-.32.15-.56.26-.84.37-5.2 2.37-10.3 4.85-15.37 7.44-2.18 1.12-4.34 2.26-6.54 3.44a338.6 338.6 0 0 0-16.25 9.28c-2.48 1.5-4.91 3.06-7.34 4.61s-4.89 3.12-7.25 4.75c-2 1.36-4 2.73-6 4.15-.57.41-1.17.81-1.78 1.25-1.83 1.3-3.6 2.6-5.37 4-2.67 1.93-5.32 3.93-7.91 5.93-3.07 2.43-6.09 4.93-9.09 7.45-2.62 2.21-5.15 4.4-7.7 6.63-3.17 2.85-6.25 5.66-9.32 8.62-1.41 1.3-2.8 2.67-4.14 4s-2.71 2.67-4.08 4.08q-5.62 5.7-11.05 11.73c-2.39 2.71-4.76 5.41-7 8.11-2.68 3.11-5.28 6.34-7.82 9.52s-5 6.45-7.44 9.74-4.87 6.71-7.15 10.07c-4.67 6.71-9.07 13.66-13.22 20.78S83.37 230.58 79.73 238a357.58 357.58 0 0 0-38 161c0 117.32 56.16 221.57 143 287.3 38 23.29 60 33.18 68.64 27.62-12.63-10.68-29.13-20.3-41.07-32.19Z"
								transform="translate(-41.73 -38.72)" style="fill:url(#j)"/>
						</g>
					</svg>
				</div>
				<div class="license-notice__text">
					<h2 style="margin:0 0 10px;padding: 0;">
						<?php // translators: %s: plugin name. ?>
						<?php echo sprintf( esc_html__( 'Thanks for installing "%s"!', 'essential-elements-pro' ), esc_html( $this->data['name'] ) ); ?>
					</h2>
					<?php
					echo sprintf(
						'<p style="margin:0 0 10px;font-size: 14px;">%s</p>',
						wp_kses_post(
							sprintf(
							// translators: %1$s is the plugin name, %2$s is the license key, %3$s is the license status.
								__( 'Please activate your license to get updates and support. You can find your license key in your account on %1$surldev.com%2$s and in the email you received after purchase.', 'essential-elements-pro' ),
								'<a href="https://urldev.com/my-account/" target="_blank">',
								'</a>'
							)
						)
					);
					?>
					<form method="post" style="display:flex;flex-direction:row;align-items:center;flex-wrap:wrap;gap: 10px;">
						<?php wp_nonce_field( $this->get_basename() . '_license_action', 'nonce' ); ?>
						<input type="hidden" name="operation" value="activate_license">
						<input type="hidden" name="action" value="<?php echo esc_attr( $this->get_basename() ); ?>_license_action">
						<input class="regular-text" type="text" name="key" placeholder="<?php echo esc_attr__( 'Enter your license key', 'essential-elements-pro' ); ?>" required value="<?php echo esc_attr( $license ); ?>" style="margin-right:-10px; border-top-right-radius:0; border-bottom-right-radius:0; border-right:0;">
						<button type="submit" class="button button-secondary" style="border-top-left-radius:0; border-bottom-left-radius:0;line-height: 20px;"><span class="dashicons dashicons-admin-network"></span>&nbsp;<?php echo esc_html__( 'Activate', 'essential-elements-pro' ); ?></button>
						<?php echo sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $this->data['pluginuri'] ), esc_html__( 'Buy License', 'essential-elements-pro' ) ); ?>
						<?php echo sprintf( '<a href="https://urldev.com/support/" target="_blank">%s</a>', esc_html__( 'Contact Support', 'essential-elements-pro' ) ); ?>
						<span class="spinner"></span>
					</form>
				</div>
			</div>
		</div>
		<script type="application/javascript">
            addEventListener('DOMContentLoaded', () => {
                if (typeof jQuery !== 'undefined') {
                    jQuery(function ($) {
                        // When document is ready, and form is submitted with license key make an ajax request to activate the license.
                        $(document).on('submit', '#<?php echo esc_attr( $this->data['slug'] ); ?>-license-notice form', function (e) {
                            e.preventDefault();
                            var $form = $(this);
                            var $spinner = $form.find('span.spinner');
                            var $notice = $form.closest('.notice.license-notice');
                            $.ajax({
                                url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
                                method: 'POST',
                                data: $form.serialize(),
                                beforeSend: function () {
                                    $form.find('button').attr('disabled', 'disabled');
                                    $spinner.addClass('is-active');
                                },
                                success: function (response) {
                                    if (response.data && response.data.message) {
                                        alert(response.data.message);
                                    }
                                    if (response.data.reload) {
                                        $notice.remove()
                                        location.reload();
                                    }
                                },
                                error: function (response) {
                                    if (response && response.data && response.data.message) {
                                        alert(response.data.message);
                                    }
                                },
                                complete: function () {
                                    $form.find('button').removeAttr('disabled');
                                    $spinner.removeClass('is-active');
                                }
                            });
                        });
                    });
                }
            });
		</script>
		<?php
	}

	/**
	 * Add license link to plugin action links.
	 *
	 * @param array $links The plugin action links.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function add_license_link( $links ) {
		if ( ! $this->is_license_valid() ) {
			return $links;
		}
		$action_links = array(
			'license' => sprintf(
				'<a href="javascript:void(0);" class="license-manage-link" aria-label="%s">%s</a>',
				esc_attr__( 'license', 'essential-elements-pro' ),
				esc_html__( 'License', 'essential-elements-pro' )
			),
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Add license row to plugin row.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_license_row() {
		$screen  = get_current_screen();
		$columns = get_column_headers( $screen );
		$colspan = ! is_countable( $columns ) ? 3 : count( $columns );
		$action  = $this->get_basename() . '_license_action';
		$nonce   = wp_create_nonce( $this->get_basename() . '_license_action' );
		$visible = $this->is_license_valid() ? 'hidden' : 'visible';
		$button  = '<button class="button license-button" data-action="%1$s" data-operation="%2$s" data-nonce="%3$s" style="line-height: 20px;%4$s"><span class="dashicons %5$s"></span>&nbsp;%6$s</button>';
		?>
		<tr class="license-row notice-warning notice-alt plugin-update-tr <?php echo esc_attr( $visible ); ?>" data-plugin="<?php echo esc_attr( $this->get_basename() ); ?>">
			<td colspan="<?php echo esc_attr( $colspan ); ?>" class="plugin-update colspanchange">
				<div class="update-message" style="margin-top: 15px;display: flex;flex-direction: row;align-items: center;flex-wrap: wrap;gap: 10px;">
					<?php if ( 'valid' === $this->get_license_status() ) : ?>
						<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
						<span><?php esc_html_e( 'License is valid.', 'essential-elements-pro' ); ?></span>
					<?php elseif ( 'expired' === $this->get_license_status() ) : ?>
						<span class="dashicons dashicons-warning" style="color: #dc3232;"></span>
						<span><?php esc_html_e( 'License is expired.', 'essential-elements-pro' ); ?></span>
					<?php elseif ( '' === $this->get_license_status() ) : ?>
						<span class="dashicons dashicons-warning" style="color: #dc3232;"></span>
						<span><?php esc_html_e( 'Please activate your license.', 'essential-elements-pro' ); ?></span>
					<?php else : ?>
						<span class="dashicons dashicons-warning" style="color: #dc3232;"></span>
						<?php /* translators: %s: license status */ ?>
						<span><?php echo sprintf( esc_html__( 'License is %s.', 'essential-elements-pro' ), esc_html( $this->get_license_status() ) ); ?></span>
					<?php endif; ?>
					<?php
					echo sprintf(
						'<input class="regular-text license-key" type="text" placeholder="%s" value="%s" style="margin-right:-10px; border-top-right-radius:0; border-bottom-right-radius:0; border-right:0;" />',
						esc_attr__( 'Enter your license key', 'essential-elements-pro' ),
						esc_attr( $this->get_license_key() )
					);
					echo sprintf(
						wp_kses_post( $button ),
						esc_attr( $action ),
						esc_attr( 'activate_license' ),
						esc_attr( $nonce ),
						esc_attr( 'border-top-left-radius:0; border-bottom-left-radius:0;' ),
						esc_attr( 'dashicons-admin-network' ),
						esc_html__( 'Activate License', 'essential-elements-pro' )
					);
					if ( 'valid' === $this->get_license_status() ) {
						echo sprintf(
							wp_kses_post( $button ),
							esc_attr( $action ),
							esc_attr( 'deactivate_license' ),
							esc_attr( $nonce ),
							esc_attr( '' ),
							esc_attr( 'dashicons-no-alt' ),
							esc_html__( 'Deactivate License', 'essential-elements-pro' )
						);

						echo sprintf(
							wp_kses_post( $button ),
							esc_attr( $action ),
							esc_attr( 'check_license' ),
							esc_attr( $nonce ),
							esc_attr( '' ),
							esc_attr( 'dashicons-update' ),
							esc_html__( 'Check License', 'essential-elements-pro' )
						);
					} elseif ( 'expired' === $this->get_license_status() ) {
						echo sprintf(
							'<a href="%s" target="_blank">%s</a>',
							esc_url( 'https://urldev.com/checkouts/' . $this->get_item_id() . '?edd_license_key=' . $this->get_license_key() ),
							esc_html__( 'Renew License', 'essential-elements-pro' )
						);
					} elseif ( in_array( $this->get_license_status(), array( 'revoked', 'disabled' ), true ) ) {
						echo sprintf(
							'<a href="%s" target="_blank">%s</a>',
							esc_url( 'https://urldev.com/my-account/' ),
							esc_html__( 'Contact Support', 'essential-elements-pro' )
						);
					} else {
						echo sprintf(
							'<a href="%s" target="_blank">%s</a>',
							esc_url( 'https://urldev.com/checkouts?edd_action=add_to_cart&download_id=' . $this->get_item_id() ),
							esc_html__( 'Buy License', 'essential-elements-pro' )
						);
					}
					?>
					<span class="spinner"></span>
					<script type="application/javascript">
                        addEventListener('DOMContentLoaded', () => {
                            // check if Jquery is loaded. If not load return.
                            if (typeof jQuery !== 'undefined') {
                                jQuery(function ($) {
                                    $('body')
                                        .on('click', '[data-plugin="<?php echo esc_attr( $this->get_basename() ); ?>"] .license-manage-link', function (e) {
                                            e.preventDefault();
                                            const plugin = $(this).closest('tr').data('plugin');
                                            $(this).closest('tr').siblings('.license-row[data-plugin="' + plugin + '"]').toggle();
                                        })
                                        .on('click', '[data-plugin="<?php echo esc_attr( $this->get_basename() ); ?>"] .license-button', function (e) {
                                            e.preventDefault();
                                            var $this = $(this);
                                            var $row = $this.closest('tr');
                                            var $spinner = $row.find('.spinner');
                                            var $buttons = $row.find('.license-button');
                                            var $key = $row.find('.license-key');
                                            var action = $this.data('action');
                                            var operation = $this.data('operation');
                                            var nonce = $this.data('nonce');
                                            var key = $key.val();
                                            $.ajax({
                                                url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
                                                method: 'POST',
                                                data: {
                                                    action: action,
                                                    operation: operation,
                                                    nonce: nonce,
                                                    key: key,
                                                },
                                                beforeSend: function () {
                                                    $spinner.addClass('is-active');
                                                    $buttons.prop('disabled', true);
                                                },
                                                success: function (response) {
                                                    if (response.data && response.data.message) {
                                                        alert(response.data.message);
                                                    }
                                                    if (response.data.reload) {
                                                        $row.fadeOut('fast');
                                                        location.reload();
                                                    }
                                                },
                                                error: function (response) {
                                                    if (response.data && response.data.message) {
                                                        alert(response.data.message);
                                                    }
                                                },
                                                complete: function () {
                                                    $spinner.removeClass('is-active');
                                                    $buttons.prop('disabled', false);
                                                }
                                            });
                                        });

                                });
                            }
                        });
					</script>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * License AJAX handler.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function license_ajax_handler() {
		if ( ! isset( $_POST['action'] ) || $this->get_basename() . '_license_action' !== $_POST['action'] ) {
			return;
		}

		if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), $this->get_basename() . '_license_action' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error( __( 'Invalid nonce', 'essential-elements-pro' ) );
		}

		if ( ! isset( $_POST['operation'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid operation', 'essential-elements-pro' ) ) );
		}

		$operation = sanitize_text_field( wp_unslash( $_POST['operation'] ) );
		$license   = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
		if ( empty( $license ) ) {
			wp_send_json_error( array( 'message' => __( 'License key is required', 'essential-elements-pro' ) ) );
		}

		switch ( $operation ) {
			case 'activate_license':
				if ( $this->is_license_valid() && $license === $this->get_license_key() ) {
					wp_send_json_error( array( 'message' => __( 'License key is already activated', 'essential-elements-pro' ) ) );
				}

				if ( ! empty( $this->get_license_key() ) && $this->get_license_key() !== $license && $this->get_license_status() ) {
					$this->get_license_api_data( $this->get_license_key(), 'deactivate_license' );
				}

				$api_data = $this->get_license_api_data( $license, 'activate_license' );
				if ( is_wp_error( $api_data ) || ! $api_data->success ) {
					wp_send_json_error(
						array(
							'message' => sprintf(
							/* translators: %s: error message */
								__( 'License activation failed. %s', 'essential-elements-pro' ),
								esc_html( is_wp_error( $api_data ) ? $api_data->get_error_message() : $api_data->error_message )
							),
							'data'    => $api_data,
						)
					);
				}
				if ( isset( $api_data->license ) && 'valid' === $api_data->license ) {
					update_option( $this->data['prefix'] . '_license_key', $license );
					update_option( $this->data['prefix'] . '_license_status', $api_data->license );
					delete_transient( $this->data['prefix'] . '_latest_version' );
					delete_site_transient( 'update_plugins' );
					wp_send_json_success(
						array(
							'message' => __( 'License activated successfully.', 'essential-elements-pro' ),
							'code'    => $api_data->license,
							'reload'  => true,
						)
					);
				} else {
					wp_send_json_error(
						array(
							'message' => __( 'License activation failed.', 'essential-elements-pro' ),
							'data'    => $api_data,
						)
					);
				}
				break;
			case 'deactivate_license':
				$api_data = $this->get_license_api_data( $license, 'deactivate_license' );
				if ( is_wp_error( $api_data ) || ! $api_data->success ) {
					wp_send_json_error(
						array(
							'message' => sprintf(
							/* translators: %s: error message */
								__( 'License deactivation failed. %s', 'essential-elements-pro' ),
								esc_html( is_wp_error( $api_data ) ? $api_data->get_error_message() : $api_data->error_message )
							),
							'data'    => $api_data,
						)
					);
				}
				if ( isset( $api_data->license ) && 'deactivated' === $api_data->license ) {
					delete_option( $this->data['prefix'] . '_license_status' );
					delete_transient( $this->data['prefix'] . '_latest_version' );
					delete_site_transient( 'update_plugins' );
					wp_send_json_success(
						array(
							'message' => __( 'License deactivated successfully', 'essential-elements-pro' ),
							'code'    => $api_data->license,
							'reload'  => true,
						)
					);
				} else {
					wp_send_json_error(
						array(
							'message' => __( 'License deactivation failed', 'essential-elements-pro' ),
							'code'    => $api_data->license,
						)
					);
				}
				break;
			case 'check_license':
				$api_data = $this->get_license_api_data( $license, 'check_license' );
				if ( is_wp_error( $api_data ) || ! $api_data->success ) {
					wp_send_json_error(
						array(
							'message' => sprintf(
							/* translators: %s: error message */
								__( 'License check was failed. %s', 'essential-elements-pro' ),
								esc_html( is_wp_error( $api_data ) ? $api_data->get_error_message() : $api_data->error_message )
							),
							'data'    => $api_data,
						)
					);
				}
				if ( isset( $api_data->license ) && 'valid' === $api_data->license ) {
					update_option( $this->data['prefix'] . '_license_status', $api_data->license );
					delete_transient( $this->data['prefix'] . '_latest_version' );
					delete_site_transient( 'update_plugins' );
					$message = __( 'Your license key is valid.', 'essential-elements-pro' );
					// if set activation limit.
					if ( isset( $api_data->activations_left ) && $api_data->activations_left > 0 ) {
						/* translators: %s: number of activations left */
						$message .= ' ' . sprintf( __( 'You have %s activations left.', 'essential-elements-pro' ), number_format( $api_data->activations_left ) );
					}
					// if set expiration date.
					if ( isset( $api_data->expires ) && 'lifetime' !== $api_data->expires ) {
						/* translators: %s: expiration date */
						$message .= ' ' . sprintf( __( 'Your license key expires on %s.', 'essential-elements-pro' ), date_i18n( get_option( 'date_format' ), strtotime( $api_data->expires ) ) );
					}
					wp_send_json_success(
						array(
							'message' => $message,
							'code'    => $api_data->license,
						)
					);
				} else {
					wp_send_json_error(
						array(
							'message' => __( 'License is invalid', 'essential-elements-pro' ),
							'code'    => $api_data->license,
						)
					);
				}
				break;
			default:
				wp_send_json_error(
					array(
						'message' => __( 'Invalid action', 'essential-elements-pro' ),
						'code'    => 'invalid_action',
					)
				);
				break;
		}
	}

	/**
	 * Check for plugin update.
	 *
	 * @param object $transient_data The update plugins transient.
	 *
	 * @since 1.0.0
	 * @return object
	 */
	public function check_for_update( $transient_data ) {
		global $pagenow;

		if ( ! is_object( $transient_data ) ) {
			$transient_data = new \stdClass();
		}

		if ( 'plugins.php' === $pagenow && is_multisite() ) {
			return $transient_data;
		}

		$basename = $this->get_basename();

		// First check if plugin info already exists in the WP transient.
		if ( ! empty( $transient_data->response ) && ! empty( $transient_data->response[ $basename ] ) ) {
			return $transient_data;
		}

		$latest_version = $this->get_latest_version();

		if ( is_object( $latest_version ) && isset( $latest_version->new_version ) ) {
			if ( version_compare( $this->data['version'], $latest_version->new_version, '<' ) ) {
				$transient_data->response[ $basename ]         = $latest_version;
				$transient_data->response[ $basename ]->plugin = $basename;
				$transient_data->response[ $basename ]->id     = $basename;
				if ( ! $this->is_license_valid() ) {
					$transient_data->package = '';
				}
			} else {
				$transient_data->no_update[ $basename ] = (object) array(
					'id'            => $basename,
					'slug'          => $this->data['slug'],
					'plugin'        => $basename,
					'new_version'   => $this->data['version'],
					'url'           => '',
					'package'       => '',
					'icons'         => array(),
					'banners'       => array(),
					'banners_rtl'   => array(),
					'tested'        => '',
					'requires_php'  => '',
					'compatibility' => new \stdClass(),
				);
			}

			$transient_data->last_checked         = time();
			$transient_data->checked[ $basename ] = $this->data['version'];
		}

		return $transient_data;
	}

	/**
	 * Plugin API calls to get plugin information.
	 *
	 * @param mixed  $result Result.
	 * @param string $action The type of information being requested from the Plugin Installation API.
	 * @param object $args Plugin API arguments.
	 *
	 * @since 1.0.0
	 */
	public function plugins_api_filter( $result, $action, $args ) {
		if ( 'plugin_information' !== $action || ! isset( $args->slug ) || $args->slug !== $this->data['slug'] ) {
			return $result;
		}

		$request = $this->get_latest_version();

		if ( ! is_object( $request ) || ! isset( $request->sections ) ) {
			return $result;
		}

		if ( ! $this->is_license_valid() ) {
			$request->package               = '';
			$request->sections['changelog'] = sprintf( esc_html__( 'Please activate your license key to get the latest updates and changelog.', 'essential-elements-pro' ), $this->data['name'] );
		}

		return $request;
	}

	/**
	 * Check license status.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function refresh_license_status() {
		$license_key = $this->get_license_key();
		if ( empty( $license_key ) ) {
			return;
		}
		$license_data = $this->get_license_api_data( $license_key, 'check_license' );
		if ( ! is_wp_error( $license_data ) && isset( $license_data->license ) ) {
			update_option( $this->data['prefix'] . '_license_status', $license_data->license );
		}
	}

	/**
	 * Get latest version.
	 *
	 * @param bool $force Force update.
	 *
	 * @since 1.0.0
	 * @return \stdClass|false The latest version or false.
	 */
	public function get_latest_version( $force = false ) {
		$cache_key = $this->data['prefix'] . '_latest_version';
		$api_data  = get_transient( $cache_key );

		if ( $force || false === $api_data ) {
			$api_data = $this->get_license_api_data( $this->get_license_key(), 'get_version' );
			if ( ! is_wp_error( $api_data ) && is_object( $api_data ) && isset( $api_data->new_version ) ) {
				foreach ( get_object_vars( $api_data ) as $prop => $data ) {
					$api_data->$prop = maybe_unserialize( $data );
				}
				$api_data->name = $this->data['name'];
				$api_data->slug = $this->data['slug'];
				// set_transient( $cache_key, $api_data, 3 * HOUR_IN_SECONDS );
				set_transient( $cache_key, $api_data, 1 );
			}
		}

		if ( ! $this->is_license_valid() && isset( $api_data->package ) ) {
			$api_data->package = '';
		}

		return $api_data;
	}

	/**
	 * Get license API data.
	 *
	 * @param string $license License key.
	 * @param string $action Action to perform.
	 * @param array  $args Additional arguments.
	 *
	 * @since 1.0.0
	 * @return object|\WP_Error Response object or WP_Error on failure.
	 */
	protected function get_license_api_data( $license, $action, $args = array() ) {
		$api_params = array(
			'edd_action'        => $action,
			'license'           => $license,
			'item_id'           => $this->get_item_id(),
			'url'               => home_url(),
			'version'           => $this->data['version'],
			'wp_version'        => get_bloginfo( 'version' ),
			'php_version'       => PHP_VERSION,
			'mysql_version'     => $GLOBALS['wpdb']->db_version(),
			'framework_version' => $this->data['framework_version'],
		);

		$api_params = array_merge( $api_params, $args );
		$response   = wp_remote_post(
			$this->data['api_url'],
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new \WP_Error( 'error', is_wp_error( $response ) ? $response->get_error_message() : wp_remote_retrieve_response_message( $response ) );
		}

		$api_data = json_decode( wp_remote_retrieve_body( $response ) );
		if ( ! is_object( $api_data ) ) {
			return new \WP_Error( 'error', __( 'Something went wrong. Please try again.', 'essential-elements-pro' ) );
		}

		if ( isset( $api_data->success ) && false === $api_data->success ) {
			switch ( $api_data->error ) {
				case 'expired':
					$message = sprintf(
					/* translators: %s: license key */
						__( 'Your license key expired on %s.', 'essential-elements-pro' ),
						date_i18n( get_option( 'date_format' ), strtotime( $api_data->expires ) )
					);
					break;
				case 'revoked':
				case 'disabled':
					$message = __( 'Your license key has been disabled. Please contact support.', 'essential-elements-pro' );
					break;
				case 'missing':
					$message = __( 'Invalid license.', 'essential-elements-pro' );
					break;
				case 'invalid':
				case 'site_inactive':
					$message = __( 'Your license is not active for this URL.', 'essential-elements-pro' );
					break;
				case 'item_name_mismatch':
				case 'invalid_item_id':
					/* translators: %s: plugin name */
					$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'essential-elements-pro' ), esc_html( $this->data['name'] ) );
					break;
				case 'no_activations_left':
					$message = __( 'Your license key has reached its activation limit.', 'essential-elements-pro' );
					break;
				default:
					$message = __( 'An error occurred, please try again.', 'essential-elements-pro' );
					break;
			}

			$api_data->error_message = $message;
		}

		return $api_data;
	}
}
