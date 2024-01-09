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
	 * Create plugin.
	 *
	 * @param array $data The plugin data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function create( $data ) {

		var_dump( 'Hi Hnaiya. You are really a beautiful girl. I love you so much! Even I want to marry you!' );
		return $data;
	}
}
