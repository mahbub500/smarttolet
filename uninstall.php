<?php
/**
 * Plugin uninstall – runs when the plugin is deleted via WP admin.
 *
 * @package SmartToLet
 */

// Prevent direct access.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// ── Remove DB tables ─────────────────────────────────────────────────────
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}smarttolet_enquiries" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}smarttolet_favourites" );

// ── Remove options ────────────────────────────────────────────────────────
delete_option( 'smarttolet_options' );
delete_option( 'smarttolet_db_version' );

// ── Remove post meta ──────────────────────────────────────────────────────
$meta_keys = [
	'_stl_price', '_stl_bedrooms', '_stl_bathrooms', '_stl_area',
	'_stl_area_unit', '_stl_status', '_stl_furnished', '_stl_available',
	'_stl_address', '_stl_latitude', '_stl_longitude', '_stl_video_url',
	'_stl_gallery_ids', '_stl_featured',
];

foreach ( $meta_keys as $key ) {
	$wpdb->delete( $wpdb->postmeta, [ 'meta_key' => $key ], [ '%s' ] ); // phpcs:ignore
}
