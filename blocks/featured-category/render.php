<?php
/**
 * Block: دسته‌بندی منتخب (featured-category)
 * Faithful port of custom-template.php lines 4–10 (data) + 328–387 (markup).
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Editor-configurable category + count, falling back to the global Options value.
$fc_cat_id = get_field( 'category' ) ?: get_field( 'product_cat_in_home', 'options' );
$fc_count  = (int) ( get_field( 'products_count' ) ?: 6 );
$custom_cat = $fc_cat_id ? get_term_by( 'term_id', $fc_cat_id, 'product_cat' ) : null;
if ( $custom_cat && ! is_wp_error( $custom_cat ) ) {
	$cat_img_id               = (int) get_term_meta( $custom_cat->term_id, 'cat_img', true );
	$cat_img_src              = $cat_img_id ? wp_get_attachment_image_src( $cat_img_id, 'full' ) : false;
	$custom_cat_image         = $cat_img_src ? $cat_img_src[0] : '';
	$custom_cat_image_dim     = $cat_img_src ? sprintf( ' width="%d" height="%d"', (int) $cat_img_src[1], (int) $cat_img_src[2] ) : '';
	$custom_cat_title         = $custom_cat->name;
	$custom_cat_link          = get_term_link( $custom_cat->term_id, 'product_cat' );
	$custom_cat_product_count = $custom_cat->count;
} else {
	$custom_cat = null;
}

if ( $is_preview ) {
	if ( $custom_cat ) {
		takdooz_block_preview_card( 'دسته‌بندی منتخب: ' . $custom_cat->name, sprintf( '%d محصول', $fc_count ) );
	} else {
		takdooz_block_preview_card( 'دسته‌بندی منتخب', 'یک دسته را از تنظیمات این بلوک انتخاب کنید.' );
	}
	return;
}

if ( $custom_cat ) {
	$custom_posts_arg = array(
		'post_type'      => 'product',
		'no_found_rows'  => true,
		'posts_per_page' => $fc_count,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'tax_query'      => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => array( $custom_cat->term_id ),
			),
		),
	);
	$posts = new WP_Query( $custom_posts_arg );
	if ( $posts->have_posts() ) { ?>
<div class="products_cats_wrap">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-12 col-lg-6 order-2">
				<div class="products_wrap">
					<div class="swiper rb_offer_product_swiper">
						<div class="swiper-wrapper">
							<?php
							while ( $posts->have_posts() ) {
								$posts->the_post(); ?>
							<div class="swiper-slide">
								<?php get_template_part( 'template-parts/content', 'product' ); ?>
							</div>
							<?php } ?>
						</div>
						<div class="rb_swiper_btns">
							<span class="slideNext-btn">
								<svg width="15" height="20" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L6 6L1 11" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
							<span class="slidePrev-btn">
								<svg width="15" height="20" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 11L1 6L6 1" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-6 order-1">

				<div class="cat_box">
					<a href="<?php echo esc_url( $custom_cat_link ); ?>" class="box_link" aria-label="<?php echo esc_attr( $custom_cat_title ); ?>"></a>
					<?php if ( $custom_cat_image ) : ?>
					<img src="<?php echo esc_url( $custom_cat_image ); ?>"<?php echo $custom_cat_image_dim; // phpcs:ignore ?> loading="lazy" decoding="async" alt="<?php echo esc_attr( $custom_cat_title ); ?>">
					<?php endif; ?>
					<div class="cat_box_title">
						<h3><?php echo esc_html( $custom_cat_title ); ?></h3>
						<span class="cat_box_product_count"><?php echo esc_html( $custom_cat_product_count . ' محصول ' ); ?></span>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
<?php } wp_reset_postdata(); ?>
<?php } ?>
