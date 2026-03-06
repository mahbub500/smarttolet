<?php
/**
 * Property location meta box template.
 *
 * @var WP_Post $post
 * @package SmartToLet
 */

defined( 'ABSPATH' ) || exit;

use SmartToLet\Admin\Meta_Boxes;
use SmartToLet\Admin\Settings;

$address   = Meta_Boxes::get( $post->ID, 'address' );
$latitude  = Meta_Boxes::get( $post->ID, 'latitude' );
$longitude = Meta_Boxes::get( $post->ID, 'longitude' );
$maps_key  = Settings::get( 'maps_api_key' );
?>
<div class="stl-location-wrap">
	<div class="stl-meta-row">
		<label for="stl_address"><?php esc_html_e( 'Full Address', 'smarttolet' ); ?></label>
		<input type="text" id="stl_address" name="_stl_address" value="<?php echo esc_attr( $address ); ?>" class="large-text" placeholder="e.g. 123 High St, London, E1 1AA">
		<?php if ( $maps_key ) : ?>
			<button type="button" id="stl-geocode" class="button"><?php esc_html_e( 'Find on Map', 'smarttolet' ); ?></button>
		<?php endif; ?>
	</div>
	<div class="stl-meta-row stl-meta-row--half">
		<div>
			<label for="stl_latitude"><?php esc_html_e( 'Latitude', 'smarttolet' ); ?></label>
			<input type="text" id="stl_latitude" name="_stl_latitude" value="<?php echo esc_attr( $latitude ); ?>" class="regular-text" placeholder="51.5074">
		</div>
		<div>
			<label for="stl_longitude"><?php esc_html_e( 'Longitude', 'smarttolet' ); ?></label>
			<input type="text" id="stl_longitude" name="_stl_longitude" value="<?php echo esc_attr( $longitude ); ?>" class="regular-text" placeholder="-0.1278">
		</div>
	</div>
	<?php if ( $latitude && $longitude && $maps_key ) : ?>
		<div id="stl-map-preview" style="height:300px;margin-top:10px;border-radius:6px;overflow:hidden;" data-lat="<?php echo esc_attr( $latitude ); ?>" data-lng="<?php echo esc_attr( $longitude ); ?>"></div>
	<?php endif; ?>
</div>
