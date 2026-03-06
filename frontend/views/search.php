<?php
/**
 * Search form shortcode view.
 *
 * @package SmartToLet
 */

defined( 'ABSPATH' ) || exit;

$types     = get_terms( [ 'taxonomy' => 'stl_property_type', 'hide_empty' => false ] );
$locations = get_terms( [ 'taxonomy' => 'stl_location',      'hide_empty' => false ] );
?>
<div class="stl-search" id="stl-search">
	<form class="stl-search__form" id="stl-search-form">
		<div class="stl-search__row">
			<div class="stl-search__field">
				<label for="stl-keyword"><?php esc_html_e( 'Keyword', 'smarttolet' ); ?></label>
				<input type="text" id="stl-keyword" name="keyword" placeholder="<?php esc_attr_e( 'e.g. city centre', 'smarttolet' ); ?>">
			</div>

			<div class="stl-search__field">
				<label for="stl-type"><?php esc_html_e( 'Property Type', 'smarttolet' ); ?></label>
				<select id="stl-type" name="type">
					<option value=""><?php esc_html_e( 'Any Type', 'smarttolet' ); ?></option>
					<?php if ( $types && ! is_wp_error( $types ) ) : foreach ( $types as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
					<?php endforeach; endif; ?>
				</select>
			</div>

			<div class="stl-search__field">
				<label for="stl-location"><?php esc_html_e( 'Location', 'smarttolet' ); ?></label>
				<select id="stl-location" name="location">
					<option value=""><?php esc_html_e( 'Any Location', 'smarttolet' ); ?></option>
					<?php if ( $locations && ! is_wp_error( $locations ) ) : foreach ( $locations as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
					<?php endforeach; endif; ?>
				</select>
			</div>

			<div class="stl-search__field">
				<label for="stl-min-price"><?php esc_html_e( 'Min Price', 'smarttolet' ); ?></label>
				<input type="number" id="stl-min-price" name="min_price" placeholder="0" min="0">
			</div>

			<div class="stl-search__field">
				<label for="stl-max-price"><?php esc_html_e( 'Max Price', 'smarttolet' ); ?></label>
				<input type="number" id="stl-max-price" name="max_price" placeholder="<?php esc_attr_e( 'Any', 'smarttolet' ); ?>" min="0">
			</div>

			<div class="stl-search__field stl-search__field--submit">
				<button type="submit" class="stl-btn stl-btn--primary"><?php esc_html_e( 'Search', 'smarttolet' ); ?></button>
			</div>
		</div>
	</form>

	<div class="stl-search__results" id="stl-search-results"></div>
</div>
