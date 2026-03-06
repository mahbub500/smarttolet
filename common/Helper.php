<?php
/**
 * Helper Class for SmartToLet Plugin
 *
 * @package SmartToLet\Common
 */

namespace SmartToLet\Common;

defined( 'ABSPATH' ) || exit;

class Helper {

    // ── Singleton ────────────────────────────────────────────────────────
    private static ?Helper $instance = null;

    private function __construct() {}

    public static function get_instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    // =========================================================================
    // ARRAY DEBUGGING – print in wp_head
    // =========================================================================

    /**
     * Dump any variable inside <script> in wp_head (visible in page source).
     *
     * Usage: Helper::dump_in_head( $myArray, 'my_label' );
     */
    public static function dump_in_head( mixed $data, string $label = 'STL_DEBUG' ): void {
        add_action( 'wp_head', function () use ( $data, $label ) {
            echo "\n<!-- STL DEBUG: {$label} -->\n";
            echo "<script>\nconsole.group('" . esc_js( $label ) . "');\n";
            echo "console.log(" . wp_json_encode( $data, JSON_PRETTY_PRINT ) . ");\n";
            echo "console.groupEnd();\n</script>\n";
        } );
    }

    /**
     * Dump inside an HTML comment in wp_head (hidden from normal view).
     *
     * Usage: Helper::dump_in_head_comment( $myArray, 'my_label' );
     */
    public static function dump_in_head_comment( mixed $data, string $label = 'STL_DEBUG' ): void {
        add_action( 'wp_head', function () use ( $data, $label ) {
            echo "\n<!-- STL DEBUG [{$label}]:\n";
            echo esc_html( print_r( $data, true ) );
            echo "\n-->\n";
        } );
    }

    /**
     * Pretty-print an array on screen (only for admins).
     *
     * Usage: Helper::print_array( $myArray, 'my_label' );
     */
    public static function print_array( mixed $data, string $label = '', bool $die = false ): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        echo '<pre style="background:#1e1e1e;color:#9cdcfe;padding:16px;border-radius:6px;font-size:13px;overflow:auto;z-index:99999;position:relative;">';
        if ( $label ) {
            echo '<strong style="color:#dcdcaa;">' . esc_html( $label ) . "</strong>\n";
        }
        echo esc_html( print_r( $data, true ) );
        echo '</pre>';
        if ( $die ) {
            die();
        }
    }

    /**
     * Alias: print_array() and die.
     */
    public static function dd( mixed $data, string $label = '' ): void {
        self::print_array( $data, $label, true );
    }


    // =========================================================================
    // ARRAY OPERATIONS
    // =========================================================================

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * Helper::array_flatten( [[1,2],[3,[4,5]]] ) → [1,2,3,4,5]
     */
    public static function array_flatten( array $array ): array {
        $result = [];
        array_walk_recursive( $array, function ( $value ) use ( &$result ) {
            $result[] = $value;
        } );
        return $result;
    }

    /**
     * Pluck a single column/key from an array of arrays or objects.
     *
     * Helper::array_pluck( $posts, 'ID' ) → [1, 2, 3]
     */
    public static function array_pluck( array $array, string $key ): array {
        return array_map( function ( $item ) use ( $key ) {
            return is_object( $item ) ? ( $item->$key ?? null ) : ( $item[ $key ] ?? null );
        }, $array );
    }

    /**
     * Group an array of arrays/objects by a key value.
     *
     * Helper::array_group_by( $properties, 'status' )
     * → [ 'available' => [...], 'let_agreed' => [...] ]
     */
    public static function array_group_by( array $array, string $key ): array {
        $result = [];
        foreach ( $array as $item ) {
            $group = is_object( $item ) ? ( $item->$key ?? 'undefined' ) : ( $item[ $key ] ?? 'undefined' );
            $result[ $group ][] = $item;
        }
        return $result;
    }

    /**
     * Key an array of arrays/objects by a unique field.
     *
     * Helper::array_key_by( $users, 'ID' ) → [ 1 => [...], 2 => [...] ]
     */
    public static function array_key_by( array $array, string $key ): array {
        $result = [];
        foreach ( $array as $item ) {
            $k = is_object( $item ) ? ( $item->$key ?? null ) : ( $item[ $key ] ?? null );
            if ( $k !== null ) {
                $result[ $k ] = $item;
            }
        }
        return $result;
    }

    /**
     * Filter an array of arrays/objects where key matches value.
     *
     * Helper::array_where( $items, 'status', 'available' )
     */
    public static function array_where( array $array, string $key, mixed $value ): array {
        return array_values( array_filter( $array, function ( $item ) use ( $key, $value ) {
            $v = is_object( $item ) ? ( $item->$key ?? null ) : ( $item[ $key ] ?? null );
            return $v === $value;
        } ) );
    }

    /**
     * Sort an array of arrays/objects by a key (asc/desc).
     *
     * Helper::array_sort_by( $properties, 'price', 'asc' )
     */
    public static function array_sort_by( array $array, string $key, string $direction = 'asc' ): array {
        usort( $array, function ( $a, $b ) use ( $key, $direction ) {
            $va = is_object( $a ) ? ( $a->$key ?? null ) : ( $a[ $key ] ?? null );
            $vb = is_object( $b ) ? ( $b->$key ?? null ) : ( $b[ $key ] ?? null );
            return $direction === 'asc' ? $va <=> $vb : $vb <=> $va;
        } );
        return $array;
    }

    /**
     * Remove duplicate values from an array (deep).
     *
     * Helper::array_unique_deep( $array )
     */
    public static function array_unique_deep( array $array ): array {
        return array_map(
            'unserialize',
            array_unique( array_map( 'serialize', $array ) )
        );
    }

    /**
     * Recursively merge two arrays (unlike array_merge_recursive, scalar values overwrite).
     */
    public static function array_merge_deep( array $base, array $override ): array {
        foreach ( $override as $key => $value ) {
            if ( isset( $base[ $key ] ) && is_array( $base[ $key ] ) && is_array( $value ) ) {
                $base[ $key ] = self::array_merge_deep( $base[ $key ], $value );
            } else {
                $base[ $key ] = $value;
            }
        }
        return $base;
    }

    /**
     * Split an array into chunks and return a specific page (pagination).
     *
     * Helper::array_paginate( $items, page: 2, per_page: 10 )
     * → [ 'items' => [...], 'total' => 50, 'pages' => 5 ]
     */
    public static function array_paginate( array $array, int $page = 1, int $per_page = 10 ): array {
        $total  = count( $array );
        $pages  = (int) ceil( $total / $per_page );
        $offset = ( $page - 1 ) * $per_page;
        return [
            'items'    => array_slice( $array, $offset, $per_page ),
            'total'    => $total,
            'pages'    => $pages,
            'page'     => $page,
            'per_page' => $per_page,
        ];
    }

    /**
     * Check if an array is associative.
     */
    public static function is_assoc( array $array ): bool {
        if ( [] === $array ) return false;
        return array_keys( $array ) !== range( 0, count( $array ) - 1 );
    }

    /**
     * Get a nested array value using dot notation.
     *
     * Helper::array_get( $data, 'user.address.city', 'London' )
     */
    public static function array_get( array $array, string $key, mixed $default = null ): mixed {
        foreach ( explode( '.', $key ) as $segment ) {
            if ( ! is_array( $array ) || ! array_key_exists( $segment, $array ) ) {
                return $default;
            }
            $array = $array[ $segment ];
        }
        return $array;
    }

    /**
     * Set a nested array value using dot notation.
     *
     * Helper::array_set( $data, 'user.address.city', 'London' )
     */
    public static function array_set( array &$array, string $key, mixed $value ): void {
        $keys    = explode( '.', $key );
        $current = &$array;
        foreach ( $keys as $segment ) {
            if ( ! isset( $current[ $segment ] ) || ! is_array( $current[ $segment ] ) ) {
                $current[ $segment ] = [];
            }
            $current = &$current[ $segment ];
        }
        $current = $value;
    }

    /**
     * Summarise an array of numbers: sum, avg, min, max, count.
     *
     * Helper::array_stats( [100, 200, 300] )
     * → [ 'count'=>3, 'sum'=>600, 'avg'=>200, 'min'=>100, 'max'=>300 ]
     */
    public static function array_stats( array $numbers ): array {
        $numbers = array_filter( $numbers, 'is_numeric' );
        if ( empty( $numbers ) ) {
            return [ 'count' => 0, 'sum' => 0, 'avg' => 0, 'min' => 0, 'max' => 0 ];
        }
        return [
            'count' => count( $numbers ),
            'sum'   => array_sum( $numbers ),
            'avg'   => array_sum( $numbers ) / count( $numbers ),
            'min'   => min( $numbers ),
            'max'   => max( $numbers ),
        ];
    }


    // =========================================================================
    // STRING OPERATIONS
    // =========================================================================

    /**
     * Truncate a string to a max length with a suffix.
     */
    public static function truncate( string $str, int $length = 100, string $suffix = '…' ): string {
        return mb_strlen( $str ) <= $length ? $str : mb_substr( $str, 0, $length ) . $suffix;
    }

    /**
     * Convert a string to a URL-safe slug.
     */
    public static function slugify( string $str ): string {
        return sanitize_title( $str );
    }

    /**
     * Format a number as a price string.
     *
     * Helper::format_price( 1500 ) → '£1,500/mo'
     */
    public static function format_price( int|float $amount, string $symbol = '£', string $suffix = '/mo' ): string {
        return $symbol . number_format( $amount ) . $suffix;
    }


    // =========================================================================
    // DATE / TIME
    // =========================================================================

    /**
     * Return a human-readable "time ago" string.
     *
     * Helper::time_ago( '2024-01-01 10:00:00' ) → '3 months ago'
     */
    public static function time_ago( string $datetime ): string {
        return human_time_diff( strtotime( $datetime ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'smarttolet' );
    }

    /**
     * Format a date string using the WP date format setting.
     */
    public static function format_date( string $datetime ): string {
        return date_i18n( get_option( 'date_format' ), strtotime( $datetime ) );
    }


    // =========================================================================
    // MISC UTILITIES
    // =========================================================================

    /**
     * Return true only when WP_DEBUG is enabled (gates debug output).
     */
    public static function is_debug(): bool {
        return defined( 'WP_DEBUG' ) && WP_DEBUG;
    }

    /**
     * Get the current page URL.
     */
    public static function current_url(): string {
        return home_url( add_query_arg( [] ) );
    }

    /**
     * Safely get a $_GET / $_POST value with sanitization.
     *
     * Helper::request( 'search', 'get', 'text' )
     */
    public static function request( string $key, string $method = 'get', string $type = 'text' ): mixed {
        $source = strtolower( $method ) === 'post' ? $_POST : $_GET;
        $raw    = $source[ $key ] ?? null;
        if ( $raw === null ) return null;

        return match ( $type ) {
            'int'   => absint( $raw ),
            'float' => floatval( $raw ),
            'email' => sanitize_email( $raw ),
            'url'   => esc_url_raw( $raw ),
            'html'  => wp_kses_post( $raw ),
            'array' => is_array( $raw ) ? array_map( 'sanitize_text_field', $raw ) : [],
            default => sanitize_text_field( $raw ),
        };
    }
}