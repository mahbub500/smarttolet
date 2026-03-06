<?php
/**
 * Main plugin bootstrap class.
 *
 * @package SmartToLet
 */

namespace SmartToLet;

use SmartToLet\Admin\Admin;
use SmartToLet\Frontend\Frontend;
use SmartToLet\Common\Assets;
use SmartToLet\Common\Common;

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Class SmartToLet
 *
 * Singleton that wires up all plugin components.
 */
final class SmartToLet {

	/** @var SmartToLet|null Singleton instance. */
	private static ?SmartToLet $instance = null;

	/** @var string Plugin version. */
	public string $version = SMARTTOLET_VERSION;

	/**
	 * Private constructor – use get_instance().
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Returns (and creates, if needed) the singleton instance.
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register all WordPress hooks.
	 */
	private function init_hooks(): void {
		register_activation_hook( SMARTTOLET_FILE,   [ $this, 'activate' ] );
		register_deactivation_hook( SMARTTOLET_FILE, [ $this, 'deactivate' ] );

		add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
		add_action( 'init',           [ $this, 'init' ], 0 );
	}

	/**
	 * Load plugin text domain.
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'smarttolet',
			false,
			dirname( SMARTTOLET_BASENAME ) . '/languages'
		);
	}

	/**
	 * Initialise all components.
	 */
	public function init(): void {
		// Common (both admin + front).
		
		Assets::get_instance();
		Common::get_instance();
		

		// Context-specific.
		if ( is_admin() ) {
			Admin::get_instance();
		} else {
			Frontend::get_instance();
		}
	}

	/**
	 * Plugin activation callback.
	 */
	public function activate(): void {
		Post_Types::get_instance()->register();
		flush_rewrite_rules();

		// Create default DB tables if needed.
		Installer::run();
	}

	/**
	 * Plugin deactivation callback.
	 */
	public function deactivate(): void {
		flush_rewrite_rules();
	}
}
