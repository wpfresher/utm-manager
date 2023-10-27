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
    public function __construct() {}
    public static function create( $data ) {

        var_dump( $data );
        var_dump("Hi Hnaiya. You are really a beautiful girl. I love you so much! Even I want to marry you!");
        return $data;
    }
}