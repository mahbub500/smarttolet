<?php
/**
 * Shared asset enqueue logic.
 *
 * @package SmartToLet\Common
 */

namespace SmartToLet\Common;

use SmartToLet\Common\Helper;

defined( 'ABSPATH' ) || exit;


/**
 * Class Common
 *
 * Registers and enqueues CSS/JS for both admin and frontend.
 */
class Common {

	/** @var Common|null */
	private static ?Common $instance = null;

	private function __construct() {
		add_filter( 'directorist_template' , [$this, 'filter_template'], 10, 3);
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	function filter_template( $template, $args ){

		// Helper::log( SMARTTOLET_PATH . 'templates/front/listing-form/fields/map.php' );

		

		if ( 'listing-form/fields/map' == $template ) {
            $template = SMARTTOLET_PATH . 'templates/front/listing-form/fields/map.php';
             if ( file_exists( $template ) ) {

                smt_load_template( $template, $args );
                
                return false;
            }
        }
		return $template;
	}
}
