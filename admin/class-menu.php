<?php
/**
 * Admin menu registration.
 *
 * @package SmartToLet\Admin
 */

namespace SmartToLet\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Menu
 */
class Menu {

	/** @var Menu|null */
	private static ?Menu $instance = null;

	private function __construct() {
		add_action( 'admin_menu', [ $this, 'register' ] );
	}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register admin menu pages.
	 */
	public function register(): void {
		// Top-level "SmartToLet" menu.
		add_menu_page(
			__( 'SmartToLet', 'smarttolet' ),
			__( 'SmartToLet', 'smarttolet' ),
			'manage_options',
			'smarttolet',
			[ $this, 'render_dashboard' ],
			'dashicons-building',
			25
		);

		// Dashboard sub-page (duplicate of top level).
		add_submenu_page(
			'smarttolet',
			__( 'Dashboard', 'smarttolet' ),
			__( 'Dashboard', 'smarttolet' ),
			'manage_options',
			'smarttolet',
			[ $this, 'render_dashboard' ]
		);

		// Properties sub-page (links to CPT list).
		add_submenu_page(
			'smarttolet',
			__( 'Properties', 'smarttolet' ),
			__( 'Properties', 'smarttolet' ),
			'manage_options',
			'edit.php?post_type=stl_property'
		);

		// Enquiries sub-page.
		add_submenu_page(
			'smarttolet',
			__( 'Enquiries', 'smarttolet' ),
			__( 'Enquiries', 'smarttolet' ),
			'manage_options',
			'smarttolet-enquiries',
			[ $this, 'render_enquiries' ]
		);

		// Settings sub-page.
		add_submenu_page(
			'smarttolet',
			__( 'Settings', 'smarttolet' ),
			__( 'Settings', 'smarttolet' ),
			'manage_options',
			'smarttolet-settings',
			[ $this, 'render_settings' ]
		);
	}

	public function render_dashboard(): void {
		include SMARTTOLET_PATH . 'admin/views/dashboard.php';
	}

	public function render_enquiries(): void {
		include SMARTTOLET_PATH . 'admin/views/enquiries.php';
	}

	public function render_settings(): void {
		include SMARTTOLET_PATH . 'admin/views/settings.php';
	}
}
