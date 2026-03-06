<?php
/**
 * Property gallery meta box template.
 *
 * @var WP_Post $post
 * @package SmartToLet
 */

defined( 'ABSPATH' ) || exit;

use SmartToLet\Admin\Meta_Boxes;

$gallery_ids = Meta_Boxes::get( $post->ID, 'gallery_ids', '' );
$ids_array   = array_filter( array_map( 'absint', explode( ',', $gallery_ids ) ) );
?>
<div class="stl-gallery-wrap">
	<input type="hidden" id="stl_gallery_ids" name="_stl_gallery_ids" value="<?php echo esc_attr( $gallery_ids ); ?>">

	<div class="stl-gallery-preview" id="stl-gallery-preview">
		<?php foreach ( $ids_array as $id ) : ?>
			<div class="stl-gallery-item" data-id="<?php echo esc_attr( $id ); ?>">
				<?php echo wp_get_attachment_image( $id, [ 80, 80 ] ); ?>
				<button type="button" class="stl-remove-image" title="<?php esc_attr_e( 'Remove', 'smarttolet' ); ?>">✕</button>
			</div>
		<?php endforeach; ?>
	</div>

	<button type="button" id="stl-add-gallery" class="button">
		<?php esc_html_e( '+ Add Images', 'smarttolet' ); ?>
	</button>
</div>
