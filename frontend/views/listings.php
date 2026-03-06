<?php
/**
 * Listings shortcode view.
 *
 * @var array $atts Shortcode attributes.
 * @package SmartToLet
 */

defined( 'ABSPATH' ) || exit;

use SmartToLet\Frontend\Property_Query;

$query_args = [
	'posts_per_page' => absint( $atts['per_page'] ),
	'orderby'        => sanitize_key( $atts['orderby'] ),
	'order'          => in_array( strtoupper( $atts['order'] ), [ 'ASC', 'DESC' ], true ) ? $atts['order'] : 'DESC',
];

if ( $atts['type'] ) {
	$query_args['tax_query'][] = [
		'taxonomy' => 'stl_property_type',
		'field'    => 'slug',
		'terms'    => sanitize_key( $atts['type'] ),
	];
}
if ( $atts['location'] ) {
	$query_args['tax_query'][] = [
		'taxonomy' => 'stl_location',
		'field'    => 'slug',
		'terms'    => sanitize_key( $atts['location'] ),
	];
}

$query = Property_Query::query( $query_args );
?>
<div class="stl-listings" id="stl-listings">
	<?php if ( $query->have_posts() ) : ?>
		<div class="stl-listings__grid">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<?php include __DIR__ . '/property-card.php'; ?>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	<?php else : ?>
		<p class="stl-listings__empty"><?php esc_html_e( 'No properties found.', 'smarttolet' ); ?></p>
	<?php endif; ?>
</div>
