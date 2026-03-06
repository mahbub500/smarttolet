<?php
/**
 * Frontend bootstrap.
 *
 * @package SmartToLet\Frontend
 */

namespace SmartToLet\Frontend;

defined( 'ABSPATH' ) || exit;

/**
 * Class Frontend
 *
 * Loads all front-end sub-components.
 */
class Frontend {

	/** @var Frontend|null */
	private static ?Frontend $instance = null;

	private function __construct() {
		Template_Loader::get_instance();
		Property_Query::get_instance();
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
