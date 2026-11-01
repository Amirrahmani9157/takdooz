<?php
/**
 * Block: جدیدترین محصولات (newest-products)
 * Faithful port of custom-template.php lines 260–312.
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$np_heading  = get_field( 'heading' ) ?: 'جدید ترین محصولات';
$np_subtitle = get_field( 'subtitle' ) ?: 'با جدیدترین محصولات ما، کیفیت و تنوع را تجربه کنید.';
$np_count    = (int) ( get_field( 'products_count' ) ?: 8 );
$np_more_url = get_field( 'show_more_link' ) ?: '/shop/?orderby=date';

if ( $is_preview ) {
	takdooz_block_preview_card( $np_heading ?: 'جدیدترین محصولات', sprintf( '%d محصول', $np_count ) );
	return;
}

// NOTE: the desktop grid vs. mobile swiper split below uses wp_is_mobile(),
// which is safe here because WP Rocket caches mobile devices separately
// (cache_mobile + do_caching_mobile_files are enabled).
$new_prodcuts = new WP_Query( array(
	'post_type'      => 'product',
	'no_found_rows'  => true,
	'posts_per_page' => $np_count,
	'orderby'        => 'date',
	'order'          => 'DESC',
) );
if ( $new_prodcuts->have_posts() ) { ?>
<div class="most_sale_products">
	<div class="container">
		<div class="row">
			<div class="title_wrap">
				<div class="title_wrap_text">
					<h2><?php echo esc_html( $np_heading ); ?></h2>
					<?php if ( ! wp_is_mobile() && $np_subtitle ) { ?><p><?php echo esc_html( $np_subtitle ); ?></p><?php } ?>
				</div>
				<?php if ( ! wp_is_mobile() ) { ?>
				<a href="<?php echo esc_url( $np_more_url ); ?>" class="show_more_btn">مشاهده همه <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="var(--main-color)" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/></svg>
				</a>
				<?php } ?>
			</div>
		</div>
		<div class="row">
			<?php if ( ! wp_is_mobile() ) { ?>
			<div class="rb_product_grid_wrap">
				<?php
				while ( $new_prodcuts->have_posts() ) {
					$new_prodcuts->the_post(); ?>
				<?php get_template_part( 'template-parts/content', 'product' ); ?>
				<?php } ?>
			</div>
			<?php } else { ?>
			<div class="swiper rb_product_swiper">
				<div class="swiper-wrapper">
					<?php
					while ( $new_prodcuts->have_posts() ) {
						$new_prodcuts->the_post(); ?>
					<div class="swiper-slide">
						<?php get_template_part( 'template-parts/content', 'product' ); ?>
					</div>
					<?php } ?>
				</div>
			</div>
			<a href="<?php echo esc_url( $np_more_url ); ?>" class="show_more_btn">مشاهده همه <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="var(--main-color)" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/></svg>
			</a>
			<?php } ?>
		</div>
	</div>
</div>
<?php }
wp_reset_postdata();
