<?php 

use SmartToLet\Common\Helper;
/* ============================================================
 * ✅ Template Loader
 * ============================================================ */

if ( ! function_exists( 'smt_load_template' ) ) {
    /**
     * Load a PHP template file and pass data to it.
     */
    function smt_load_template( $file, $args = [], $echo = true ) {
        if ( ! file_exists( $file ) ) return;

        

        if ( is_array( $args ) && ! empty( $args ) ) {
            extract( $args, EXTR_SKIP );
        }

        ob_start();
        include $file;
        $output = ob_get_clean();

        if ( $echo ) {
            echo $output;
            return null;
        }

        return $output;
    }
}