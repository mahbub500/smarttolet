<?php
/**
 * Property card partial – used in listings & search results.
 *
 * @package SmartToLet
 */

defined( 'ABSPATH' ) || exit;

use SmartToLet\Admin\Meta_Boxes;
use SmartToLet\Admin\Settings;

$pid       = get_the_ID();
$price     = Meta_Boxes::get( $pid, 'price' );
$bedrooms  = Meta_Boxes::get( $pid, 'bedrooms' );
$bathrooms = Meta_Boxes::get( $pid, 'bathrooms' );
$area      = Meta_Boxes::get( $pid, 'area' );
$area_unit = Meta_Boxes::get( $pid, 'area_unit', 'sqft' );
$status    = Meta_Boxes::get( $pid, 'status', 'available' );
$featured  = Meta_Boxes::get( $pid, 'featured', 0 );
$sym       = Settings::get( 'currency_symbol', '£' );
$suf       = Settings::get( 'price_suffix', '/mo' );
?>
<article class="stl-card <?php echo $featured ? 'stl-card--featured' : ''; ?>">
	<a class="stl-card__image-link" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'medium', [ 'class' => 'stl-card__image' ] ); ?>
		<?php else : ?>
			<div class="stl-card__image stl-card__image--placeholder"></div>
		<?php endif; ?>

		<?php if ( $featured ) : ?>
			<span class="stl-card__badge stl-card__badge--featured"><?php esc_html_e( 'Featured', 'smarttolet' ); ?></span>
		<?php endif; ?>
		<span class="stl-card__badge stl-card__badge--status stl-card__badge--<?php echo esc_attr( $status ); ?>">
			<?php echo esc_html( ucfirst( str_replace( '_', ' ', $status ) ) ); ?>
		</span>
	</a>

	<div class="stl-card__body">
		<h3 class="stl-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<?php $types = get_the_terms( $pid, 'stl_property_type' ); ?>
		<?php if ( $types && ! is_wp_error( $types ) ) : ?>
			<p class="stl-card__type"><?php echo esc_html( $types[0]->name ); ?></p>
		<?php endif; ?>

		<?php if ( $price ) : ?>
			<p class="stl-card__price"><?php echo esc_html( $sym . number_format( $price ) . $suf ); ?></p>
		<?php endif; ?>

		<ul class="stl-card__features">
			<?php if ( $bedrooms ) : ?>
				<li><span class="stl-icon stl-icon--bed"></span><?php echo esc_html( $bedrooms ); ?> <?php esc_html_e( 'Bed', 'smarttolet' ); ?></li>
			<?php endif; ?>
			<?php if ( $bathrooms ) : ?>
				<li><span class="stl-icon stl-icon--bath"></span><?php echo esc_html( $bathrooms ); ?> <?php esc_html_e( 'Bath', 'smarttolet' ); ?></li>
			<?php endif; ?>
			<?php if ( $area ) : ?>
				<li><span class="stl-icon stl-icon--area"></span><?php echo esc_html( $area . ' ' . $area_unit ); ?></li>
			<?php endif; ?>
		</ul>
	</div>

	<div class="stl-card__footer">
		<a class="stl-btn stl-btn--sm" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View Details', 'smarttolet' ); ?></a>
		<?php if ( is_user_logged_in() ) : ?>
			<button class="stl-btn stl-btn--sm stl-btn--outline stl-favourite" data-id="<?php echo esc_attr( $pid ); ?>" title="<?php esc_attr_e( 'Save', 'smarttolet' ); ?>">♡</button>
		<?php endif; ?>
	</div>
</article>
