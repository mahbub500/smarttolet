<?php
/**
 * Helper Class for SmartToLet Plugin
 *
 * @package SmartToLet\Common
 */

namespace SmartToLet\Common;

defined( 'ABSPATH' ) || exit;

class Helper {

    /**
     * Write to WordPress/PHP error log.
     *
     * Helper::write_log( $data );
     * Helper::write_log( $data, 'My Label' );
     */
    public static function log( mixed $data, string $label = '' ): void {
        if ( ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) return;

        $prefix  = '[SmartToLet]';
        $prefix .= $label ? " [{$label}]" : '';
        $prefix .= ' ' . date( 'Y-m-d H:i:s' ) . ' → ';

        if ( is_array( $data ) || is_object( $data ) ) {
            error_log( $prefix . print_r( $data, true ) );
        } else {
            error_log( $prefix . $data );
        }
    }
}