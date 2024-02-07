<?php

namespace UTMSourceTracker\Lib;

defined( 'ABSPATH' ) || exit;

/**
 * Describes a plugin instance.
 *
 * @since   1.0.0
 * @version 1.0.1
 * @author  Kawsar Ahmed <kawsarahmed@urldev.com>
 * @package WpStarterPlugin\Lib
 * @subpackage Lib/Plugin
 */
interface PluginInterface {
	/**
	 * Check if the plugin is active.
	 *
	 * @param string $plugin The plugin slug or basename.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_plugin_active( $plugin );

	/**
	 * Get meta links.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_meta_links();

	/**
	 * Get action links.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_action_links();

	/**
	 * Get plugin basename.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_basename();

	/**
	 * Get premium plugin basename.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_premium_basename();

	/**
	 * Has premium plugin.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function has_premium();

	/**
	 * Is premium plugin active.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_premium_active();
}
