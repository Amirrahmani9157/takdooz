<?php
/**
 * Block: پرفروش‌ترین محصولات (bestseller-tabs)
 * Faithful port of custom-template.php lines 70–153.
 * Known quirks (hardcoded category IDs, `'stock' => 1`, meta_value_num orderby,
 * wp_reset_query) are preserved here on purpose; fixed in the hardening commit.
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Editor-configurable fields with fallback to the original hardcoded values.
$bt_heading  = get_field( 'heading' ) ?: 'پرفروش ترین محصولات';
$bt_cat_ids  = get_field( 'categories' );
if ( empty( $bt_cat_ids ) ) {
	$bt_cat_ids = array( 2649, 2424, 1040 );
}
$bt_count    = (int) ( get_field( 'products_count' ) ?: 8 );
$bt_more_url = get_field( 'show_more_link' ) ?: '/shop/?orderby=popularity';
$bt_autoplay = (bool) get_field( 'autoplay' );
$bt_delay    = min( 15, max( 1, (int) ( get_field( 'autoplay_delay' ) ?: 3 ) ) );

if ( $is_preview ) {
	$preview_details = sprintf( '%d دسته‌بندی، %d محصول در هر تب', count( (array) $bt_cat_ids ), $bt_count );
	if ( $bt_autoplay ) {
		$preview_details .= sprintf( '، حرکت خودکار هر %d ثانیه', $bt_delay );
	}
	takdooz_block_preview_card( $bt_heading ?: 'پرفروش‌ترین محصولات', $preview_details );
	return;
}

$product_cats = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => false,
	'include'    => $bt_cat_ids,
	'orderby'    => 'include',
) );

if ( empty( $product_cats ) || is_wp_error( $product_cats ) ) {
	if ( $is_preview ) {
		echo '<p style="padding:24px;text-align:center;color:#888;">پرفروش‌ترین محصولات: دسته‌بندی‌ها را از تنظیمات این بلوک انتخاب کنید.</p>';
	}
	return;
}
?>

<div class="most_sale_products">
	<div class="container">
		<div class="row">
			<div class="title_wrap">
				<div class="title_wrap_text">
					<h2><?php echo esc_html( $bt_heading ); ?></h2>
					<ul class="tab_title">
						<?php
						$count = 1;
						foreach ( $product_cats as $cat ) { ?>
						<li class="<?php echo $count == 1 ? 'active' : ''; ?>" data-id="<?php echo $count; ?>"><?php echo esc_html( $cat->name ); ?></li>
						<?php $count++; } ?>
					</ul>
				</div>
				<a href="<?php echo esc_url( $bt_more_url ); ?>" class="show_more_btn show_more_btn--top">نمایش بیشتر <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="var(--main-color)" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/></svg>
				</a>
			</div>
		</div>
		<div class="row">
			<div class="tabs_content_wrap">
				<?php
				$count = 1;
				foreach ( $product_cats as $cat ) {
					// The bestseller sort (meta_value_num on total_sales) is expensive,
					// so cache the resulting product IDs per category for an hour. The
					// per-request query then fetches those IDs cheaply (no meta sort).
					$cache_key   = 'takdooz_bt_' . $cat->term_id . '_' . $bt_count;
					$product_ids = get_transient( $cache_key );
					if ( false === $product_ids ) {
						$id_query = new WP_Query( array(
							'post_type'      => 'product',
							'fields'         => 'ids',
							'no_found_rows'  => true,
							'posts_per_page' => $bt_count,
							'orderby'        => 'meta_value_num',
							'meta_key'       => 'total_sales',
							'order'          => 'DESC',
							'tax_query'      => array(
								array(
									'taxonomy' => 'product_cat',
									'field'    => 'term_id',
									'terms'    => $cat->term_id,
								),
							),
						) );
						$product_ids = $id_query->posts;
						set_transient( $cache_key, $product_ids, HOUR_IN_SECONDS );
					}

					$most_sale_prodcuts = ! empty( $product_ids ) ? new WP_Query( array(
						'post_type'           => 'product',
						'post__in'            => $product_ids,
						'orderby'             => 'post__in',
						'posts_per_page'      => $bt_count,
						'no_found_rows'       => true,
						'ignore_sticky_posts' => true,
					) ) : new WP_Query( array( 'post__in' => array( 0 ) ) );
					if ( $most_sale_prodcuts->have_posts() ) { ?>
				<div class="tab_content" data-id="<?php echo $count; ?>" style="<?php echo $count != 1 ? 'display:none' : ''; ?>">
					<div class="swiper rb_product_swiper" data-autoplay="<?php echo $bt_autoplay ? '1' : '0'; ?>" data-autoplay-delay="<?php echo esc_attr( $bt_delay * 1000 ); ?>">
						<div class="swiper-wrapper">
							<?php
							while ( $most_sale_prodcuts->have_posts() ) {
								$most_sale_prodcuts->the_post(); ?>
							<div class="swiper-slide">
								<?php get_template_part( 'template-parts/content', 'product' ); ?>
							</div>
							<?php } ?>
						</div>
						<div class="rb_swiper_btns">
							<span class="slideNext-btn">
								<svg width="18" height="24" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L6 6L1 11" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
							<span class="slidePrev-btn">
								<svg width="18" height="24" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 11L1 6L6 1" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
						</div>
					</div>
				</div>
				<?php } wp_reset_postdata(); ?>
				<?php $count++; } ?>
			</div>
			<a href="<?php echo esc_url( $bt_more_url ); ?>" class="show_more_btn show_more_btn--bottom">نمایش بیشتر <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="var(--main-color)" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/></svg>
			</a>
		</div>
	</div>
</div>
