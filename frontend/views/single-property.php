<?php
/**
 * Single property template.
 *
 * @package SmartToLet
 */

defined( 'ABSPATH' ) || exit;

use SmartToLet\Admin\Meta_Boxes;
use SmartToLet\Admin\Settings;

get_header();

if ( have_posts() ) : the_post();
	$pid        = get_the_ID();
	$price      = Meta_Boxes::get( $pid, 'price' );
	$bedrooms   = Meta_Boxes::get( $pid, 'bedrooms' );
	$bathrooms  = Meta_Boxes::get( $pid, 'bathrooms' );
	$area       = Meta_Boxes::get( $pid, 'area' );
	$area_unit  = Meta_Boxes::get( $pid, 'area_unit', 'sqft' );
	$status     = Meta_Boxes::get( $pid, 'status', 'available' );
	$furnished  = Meta_Boxes::get( $pid, 'furnished' );
	$available  = Meta_Boxes::get( $pid, 'available' );
	$address    = Meta_Boxes::get( $pid, 'address' );
	$video_url  = Meta_Boxes::get( $pid, 'video_url' );
	$gallery    = array_filter( array_map( 'absint', explode( ',', Meta_Boxes::get( $pid, 'gallery_ids', '' ) ) ) );
	$sym        = Settings::get( 'currency_symbol', '£' );
	$suf        = Settings::get( 'price_suffix', '/mo' );
?>
<div class="stl-single">
	<div class="stl-single__header">
		<div class="stl-single__header-content">
			<h1 class="stl-single__title"><?php the_title(); ?></h1>
			<?php if ( $address ) : ?>
				<p class="stl-single__address"><span class="stl-icon stl-icon--pin"></span><?php echo esc_html( $address ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( $price ) : ?>
			<div class="stl-single__price"><?php echo esc_html( $sym . number_format( $price ) . $suf ); ?></div>
		<?php endif; ?>
	</div>

	<!-- Gallery -->
	<div class="stl-gallery" id="stl-single-gallery">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="stl-gallery__main">
				<?php the_post_thumbnail( 'large', [ 'class' => 'stl-gallery__main-img' ] ); ?>
			</div>
		<?php endif; ?>
		<?php if ( $gallery ) : ?>
			<div class="stl-gallery__thumbs">
				<?php foreach ( $gallery as $img_id ) : ?>
					<div class="stl-gallery__thumb">
						<?php echo wp_get_attachment_image( $img_id, 'thumbnail', false, [ 'class' => 'stl-gallery__thumb-img' ] ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="stl-single__body">
		<!-- Details sidebar -->
		<aside class="stl-single__sidebar">
			<div class="stl-detail-card">
				<h3><?php esc_html_e( 'Property Details', 'smarttolet' ); ?></h3>
				<ul class="stl-detail-list">
					<?php if ( $status )    : ?><li><strong><?php esc_html_e( 'Status', 'smarttolet' ); ?></strong><span><?php echo esc_html( ucfirst( str_replace( '_', ' ', $status ) ) ); ?></span></li><?php endif; ?>
					<?php if ( $bedrooms )  : ?><li><strong><?php esc_html_e( 'Bedrooms', 'smarttolet' ); ?></strong><span><?php echo esc_html( $bedrooms ); ?></span></li><?php endif; ?>
					<?php if ( $bathrooms ) : ?><li><strong><?php esc_html_e( 'Bathrooms', 'smarttolet' ); ?></strong><span><?php echo esc_html( $bathrooms ); ?></span></li><?php endif; ?>
					<?php if ( $area )      : ?><li><strong><?php esc_html_e( 'Area', 'smarttolet' ); ?></strong><span><?php echo esc_html( $area . ' ' . $area_unit ); ?></span></li><?php endif; ?>
					<?php if ( $furnished ) : ?><li><strong><?php esc_html_e( 'Furnished', 'smarttolet' ); ?></strong><span><?php echo esc_html( ucfirst( $furnished ) ); ?></span></li><?php endif; ?>
					<?php if ( $available ) : ?><li><strong><?php esc_html_e( 'Available', 'smarttolet' ); ?></strong><span><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $available ) ) ); ?></span></li><?php endif; ?>
				</ul>
			</div>

			<!-- Enquiry Form -->
			<?php echo do_shortcode( '[smarttolet_enquiry property_id="' . $pid . '"]' ); ?>
		</aside>

		<!-- Main content -->
		<div class="stl-single__main">
			<div class="stl-single__description">
				<h2><?php esc_html_e( 'Description', 'smarttolet' ); ?></h2>
				<?php the_content(); ?>
			</div>

			<!-- Amenities -->
			<?php $amenities = get_the_terms( $pid, 'stl_amenity' ); ?>
			<?php if ( $amenities && ! is_wp_error( $amenities ) ) : ?>
				<div class="stl-single__amenities">
					<h2><?php esc_html_e( 'Amenities', 'smarttolet' ); ?></h2>
					<ul class="stl-amenity-list">
						<?php foreach ( $amenities as $amenity ) : ?>
							<li><?php echo esc_html( $amenity->name ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<!-- Video Tour -->
			<?php if ( $video_url ) : ?>
				<div class="stl-single__video">
					<h2><?php esc_html_e( 'Video Tour', 'smarttolet' ); ?></h2>
					<?php echo wp_oembed_get( $video_url ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php endif; ?>

<?php get_footer(); ?>
