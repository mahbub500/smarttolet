<?php
/**
 * Shared asset enqueue logic.
 *
 * @package SmartToLet\Common
 */

namespace SmartToLet\Common;

defined( 'ABSPATH' ) || exit;


/**
 * Class Assets
 *
 * Registers and enqueues CSS/JS for both admin and frontend.
 */
class Common {

	/** @var Assets|null */
	private static ?Assets $instance = null;

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
		
		return $template;
	}
}
