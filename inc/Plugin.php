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
class Plugin {
    /**
     * Plugin constructor.
     *
     * @param array $data The plugin data.
     *
     * @since 1.0.0
     */
    protected function __construct( $data ) {}

    /**
     * Plugin create method.
     *
     * @param array $data The plugin data.
     *
     * @since 1.0.0
     * @return null
     */
    public static function create( $data ): null {
        return null;
    }
}