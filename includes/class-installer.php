<?php
/**
 * Database installer.
 *
 * @package SmartToLet
 */

namespace SmartToLet;

defined( 'ABSPATH' ) || exit;

/**
 * Class Installer
 *
 * Creates / upgrades plugin DB tables on activation.
 */
class Installer {

	/** DB schema version option key. */
	const DB_VERSION_OPTION = 'smarttolet_db_version';

	/** Current schema version. */
	const DB_VERSION = '1.0.0';

	/**
	 * Run the installer.
	 */
	public static function run(): void {
		$installed = get_option( self::DB_VERSION_OPTION, '0.0.0' );

		// if ( version_compare( $installed, self::DB_VERSION, '<' ) ) {
		// 	self::create_tables();
		// 	update_option( self::DB_VERSION_OPTION, self::DB_VERSION );
		// }
	}

	/**
	 * Create plugin tables.
	 */
	private static function create_tables(): void {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$prefix          = $wpdb->prefix . 'smarttolet_';

		$sql = [];

		// Enquiries table.
		$sql[] = "CREATE TABLE {$prefix}enquiries (
			id          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			property_id BIGINT(20) UNSIGNED NOT NULL,
			name        VARCHAR(100)        NOT NULL DEFAULT '',
			email       VARCHAR(200)        NOT NULL DEFAULT '',
			phone       VARCHAR(30)         NOT NULL DEFAULT '',
			message     TEXT,
			status      VARCHAR(20)         NOT NULL DEFAULT 'new',
			created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY property_id (property_id)
		) $charset_collate;";

		// Favourites table.
		$sql[] = "CREATE TABLE {$prefix}favourites (
			id          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id     BIGINT(20) UNSIGNED NOT NULL,
			property_id BIGINT(20) UNSIGNED NOT NULL,
			created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY user_property (user_id, property_id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		foreach ( $sql as $query ) {
			dbDelta( $query );
		}
	}
}
