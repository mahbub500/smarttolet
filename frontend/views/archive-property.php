<?php
/**
 * Archive / taxonomy template for properties.
 *
 * @package SmartToLet
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="stl-archive">
	<div class="stl-archive__header">
		<h1 class="stl-archive__title">
			<?php
			if ( is_post_type_archive( 'stl_property' ) ) {
				esc_html_e( 'All Properties', 'smarttolet' );
			} else {
				single_term_title();
			}
			?>
		</h1>
		<?php
		$description = get_the_archive_description();
		if ( $description ) :
			?>
			<div class="stl-archive__description"><?php echo wp_kses_post( $description ); ?></div>
		<?php endif; ?>
	</div>

	<?php echo do_shortcode( '[smarttolet_search]' ); ?>

	<?php if ( have_posts() ) : ?>
		<div class="stl-listings__grid">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php include SMARTTOLET_PATH . 'frontend/views/property-card.php'; ?>
			<?php endwhile; ?>
		</div>

		<div class="stl-pagination">
			<?php
			the_posts_pagination( [
				'mid_size'  => 2,
				'prev_text' => __( '&larr; Previous', 'smarttolet' ),
				'next_text' => __( 'Next &rarr;', 'smarttolet' ),
			] );
			?>
		</div>
	<?php else : ?>
		<p class="stl-listings__empty"><?php esc_html_e( 'No properties found.', 'smarttolet' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
