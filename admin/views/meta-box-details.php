<?php
/**
 * Property details meta box template.
 *
 * @var WP_Post $post
 * @package SmartToLet
 */

defined( 'ABSPATH' ) || exit;

use SmartToLet\Admin\Meta_Boxes;

$pid       = $post->ID;
$price     = Meta_Boxes::get( $pid, 'price' );
$bedrooms  = Meta_Boxes::get( $pid, 'bedrooms' );
$bathrooms = Meta_Boxes::get( $pid, 'bathrooms' );
$area      = Meta_Boxes::get( $pid, 'area' );
$area_unit = Meta_Boxes::get( $pid, 'area_unit', 'sqft' );
$status    = Meta_Boxes::get( $pid, 'status', 'available' );
$furnished = Meta_Boxes::get( $pid, 'furnished', 'unfurnished' );
$available = Meta_Boxes::get( $pid, 'available' );
$featured  = Meta_Boxes::get( $pid, 'featured', 0 );
$video_url = Meta_Boxes::get( $pid, 'video_url' );
?>
<div class="stl-meta-grid">
	<div class="stl-meta-row">
		<label for="stl_price"><?php esc_html_e( 'Price', 'smarttolet' ); ?></label>
		<input type="number" id="stl_price" name="_stl_price" value="<?php echo esc_attr( $price ); ?>" min="0" step="1" class="regular-text">
	</div>
	<div class="stl-meta-row">
		<label for="stl_bedrooms"><?php esc_html_e( 'Bedrooms', 'smarttolet' ); ?></label>
		<input type="number" id="stl_bedrooms" name="_stl_bedrooms" value="<?php echo esc_attr( $bedrooms ); ?>" min="0" class="small-text">
	</div>
	<div class="stl-meta-row">
		<label for="stl_bathrooms"><?php esc_html_e( 'Bathrooms', 'smarttolet' ); ?></label>
		<input type="number" id="stl_bathrooms" name="_stl_bathrooms" value="<?php echo esc_attr( $bathrooms ); ?>" min="0" class="small-text">
	</div>
	<div class="stl-meta-row">
		<label for="stl_area"><?php esc_html_e( 'Area', 'smarttolet' ); ?></label>
		<input type="number" id="stl_area" name="_stl_area" value="<?php echo esc_attr( $area ); ?>" min="0" step="0.01" class="small-text">
		<select name="_stl_area_unit">
			<option value="sqft" <?php selected( $area_unit, 'sqft' ); ?>><?php esc_html_e( 'sq ft', 'smarttolet' ); ?></option>
			<option value="sqm"  <?php selected( $area_unit, 'sqm' ); ?>><?php esc_html_e( 'sq m', 'smarttolet' ); ?></option>
		</select>
	</div>
	<div class="stl-meta-row">
		<label for="stl_status"><?php esc_html_e( 'Listing Status', 'smarttolet' ); ?></label>
		<select id="stl_status" name="_stl_status">
			<option value="available"   <?php selected( $status, 'available' ); ?>><?php esc_html_e( 'Available',   'smarttolet' ); ?></option>
			<option value="let_agreed"  <?php selected( $status, 'let_agreed' ); ?>><?php esc_html_e( 'Let Agreed',  'smarttolet' ); ?></option>
			<option value="unavailable" <?php selected( $status, 'unavailable' ); ?>><?php esc_html_e( 'Unavailable', 'smarttolet' ); ?></option>
		</select>
	</div>
	<div class="stl-meta-row">
		<label for="stl_furnished"><?php esc_html_e( 'Furnished', 'smarttolet' ); ?></label>
		<select id="stl_furnished" name="_stl_furnished">
			<option value="furnished"   <?php selected( $furnished, 'furnished' ); ?>><?php esc_html_e( 'Furnished',   'smarttolet' ); ?></option>
			<option value="unfurnished" <?php selected( $furnished, 'unfurnished' ); ?>><?php esc_html_e( 'Unfurnished', 'smarttolet' ); ?></option>
			<option value="part"        <?php selected( $furnished, 'part' ); ?>><?php esc_html_e( 'Part Furnished', 'smarttolet' ); ?></option>
		</select>
	</div>
	<div class="stl-meta-row">
		<label for="stl_available"><?php esc_html_e( 'Available From', 'smarttolet' ); ?></label>
		<input type="date" id="stl_available" name="_stl_available" value="<?php echo esc_attr( $available ); ?>">
	</div>
	<div class="stl-meta-row">
		<label for="stl_video_url"><?php esc_html_e( 'Video Tour URL', 'smarttolet' ); ?></label>
		<input type="url" id="stl_video_url" name="_stl_video_url" value="<?php echo esc_attr( $video_url ); ?>" class="large-text" placeholder="https://youtube.com/...">
	</div>
	<div class="stl-meta-row">
		<label>
			<input type="checkbox" name="_stl_featured" value="1" <?php checked( $featured, 1 ); ?>>
			<?php esc_html_e( 'Mark as Featured', 'smarttolet' ); ?>
		</label>
	</div>
</div>
