<?php
/**
 * Featured properties shortcode view.
 *
 * @var array $atts Shortcode attributes.
 * @package SmartToLet
 */

defined( 'ABSPATH' ) || exit;

use SmartToLet\Frontend\Property_Query;

$query = Property_Query::featured( absint( $atts['limit'] ) );
?>
<?php if ( $query->have_posts() ) : ?>
<div class="stl-featured">
	<div class="stl-listings__grid">
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>
			<?php include __DIR__ . '/property-card.php'; ?>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	</div>
</div>
<?php endif; ?>
