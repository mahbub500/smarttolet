<?php
/**
 * Plugin Name:       SmartToLet
 * Plugin URI:        https://yoursite.com/smarttolet
 * Description:       A smart property rental & to-let listing plugin with OOP architecture.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Mahbub
 * Author URI:        https://yoursite.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smarttolet
 * Domain Path:       /languages
 *
 * @package SmartToLet
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Plugin Constants ────────────────────────────────────────────────────────
define( 'SMARTTOLET_VERSION',     '1.0.0' );
define( 'SMARTTOLET_FILE',        __FILE__ );
define( 'SMARTTOLET_PATH',        plugin_dir_path( __FILE__ ) );
define( 'SMARTTOLET_URL',         plugin_dir_url( __FILE__ ) );
define( 'SMARTTOLET_BASENAME',    plugin_basename( __FILE__ ) );
define( 'SMARTTOLET_ASSETS_URL',  SMARTTOLET_URL  . 'assets/' );
define( 'SMARTTOLET_ASSETS_PATH', SMARTTOLET_PATH . 'assets/' );

// ── Composer Autoloader ─────────────────────────────────────────────────────
if ( file_exists( SMARTTOLET_PATH . 'vendor/autoload.php' ) ) {
	require_once SMARTTOLET_PATH . 'vendor/autoload.php';
}

// ── Bootstrap ───────────────────────────────────────────────────────────────
require_once SMARTTOLET_PATH . 'includes/class-smarttolet.php';

/**
 * Returns the main plugin instance.
 *
 * @return SmartToLet\SmartToLet
 */
function smarttolet(): SmartToLet\SmartToLet {
	return SmartToLet\SmartToLet::get_instance();
}

// Kick off!
smarttolet();
