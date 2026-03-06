<?php
/**
 * Admin-area bootstrap.
 *
 * @package SmartToLet\Admin
 */

namespace SmartToLet\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Admin
 *
 * Loads all admin-specific sub-components.
 */
class Admin {

	/** @var Admin|null */
	private static ?Admin $instance = null;

	private function __construct() {
		
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
