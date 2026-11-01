<?php
/**
 * Block: محصولات پیشنهاد ویژه + تایمر (offer-products)
 * Faithful port of custom-template.php lines 11–22 (campaign data) + 166–258.
 * The duplicated $offer_prodcuts_arg (the first, by category, is overwritten by
 * the takdooz-offer tag query) is preserved as-is; resolved in the hardening commit.
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Editor-configurable fields, falling back to the global ACF Options values.
$campain_date  = get_field( 'campain_date' ) ?: get_field( 'campain_date', 'options' );
$campain_date  = $campain_date ? $campain_date : null;
$offer_tag     = get_field( 'product_tag_slug' ) ?: 'takdooz-offer';
$offer_count   = (int) ( get_field( 'products_count' ) ?: 10 );

if ( $is_preview ) {
	$op_title = get_field( 'campain_title' ) ?: get_field( 'campain_title', 'options' );
	if ( $campain_date ) {
		takdooz_block_preview_card( $op_title ?: 'پیشنهاد ویژه + تایمر', sprintf( 'شمارش معکوس تا %s', $campain_date ) );
	} else {
		takdooz_block_preview_card( 'پیشنهاد ویژه + تایمر', 'برای نمایش این بخش، «تاریخ پایان» را تعیین کنید.' );
	}
	return;
}

// Remaining time until the campaign ends. The date is stored in Jalali
// ("1404-08-29 ...") which strtotime() mis-parses, and date() was previously
// used on the resulting duration (off-by-one + breaks past a month). Parse the
// Jalali date properly and compute the breakdown with plain integer math.
$end_ts = $campain_date ? takdooz_to_timestamp( $campain_date ) : 0;
$diff   = $end_ts ? ( $end_ts - time() ) : 0;

// Campaign over (or no date): nothing to show.
if ( $diff <= 0 ) {
	return;
}

$campain_day    = (int) floor( $diff / DAY_IN_SECONDS );
$campain_hour   = (int) floor( ( $diff % DAY_IN_SECONDS ) / HOUR_IN_SECONDS );
$campain_minute = (int) floor( ( $diff % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS );
$campain_second = (int) ( $diff % MINUTE_IN_SECONDS );
$campain_text   = get_field( 'campain_text' ) ?: get_field( 'campain_text', 'options' );
$campain_title  = get_field( 'campain_title' ) ?: get_field( 'campain_title', 'options' );
$campain_link   = get_field( 'campain_link' ) ?: get_field( 'campain_link', 'options' );
$campain_link   = $campain_link ?: '/shop';

// The original built $offer_prodcuts_arg twice: the first (by
// $campain_category) was ALWAYS overwritten by the second and, when no
// campaign category was set, dereferenced a null $campain_category —
// emitting a live PHP warning. Removed; the takdooz-offer tag query
// below is the only one that ever actually ran (behavior unchanged).
$offer_prodcuts = new WP_Query( array(
	'post_type'      => 'product',
	'no_found_rows'  => true,
	'posts_per_page' => $offer_count,
	'tax_query'      => array(
		array(
			'taxonomy' => 'product_tag',
			'field'    => 'slug',
			'terms'    => $offer_tag,
		),
	),
) );

if ( $offer_prodcuts->have_posts() ) { ?>
<div id="offers" class="offer_products">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-12 col-lg-5">
				<h2><?php echo esc_html( $campain_title ); ?></h2>
				<p style="font-size: 15px;color: #666;margin: 20px 0 35px;"><?php echo wp_kses_post( $campain_text ); ?></p>
				<div class="timer_wrap counter">
					<div class="timer_item">
						<span class="timer_item_box seconds"><?php echo esc_html( sprintf( '%02d', $campain_second ) ); ?></span>
						<span>ثانیه</span>
					</div>
					<div class="timer_item">
						<span class="timer_item_box minutes"><?php echo esc_html( sprintf( '%02d', $campain_minute ) ); ?></span>
						<span>دقیقه</span>
					</div>
					<div class="timer_item">
						<span class="timer_item_box hours"><?php echo esc_html( sprintf( '%02d', $campain_hour ) ); ?></span>
						<span>ساعت</span>
					</div>
					<div class="timer_item">
						<span class="timer_item_box days"><?php echo esc_html( sprintf( '%02d', $campain_day ) ); ?></span>
						<span>روز</span>
					</div>
				</div>
				<a href="<?php echo esc_url( $campain_link ); ?>" class="show_product_btn show_product_btn--top">مشاهده همه محصولات</a>
			</div>
			<div class="col-12 col-lg-7">
				<div class="products_wrap">
					<div class="swiper rb_offer_product_swiper">
						<div class="swiper-wrapper">
							<?php
							while ( $offer_prodcuts->have_posts() ) {
								$offer_prodcuts->the_post(); ?>
							<div class="swiper-slide">
								<?php get_template_part( 'template-parts/content', 'custom-product' ); ?>
							</div>
							<?php } ?>
						</div>
						<div class="rb_swiper_btns">
							<span class="slideNext-btn" style="left: 0;right: auto;">
								<svg width="15" height="20" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L6 6L1 11" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
							<span class="slidePrev-btn" style="right: 0;left: auto;">
								<svg width="15" height="20" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 11L1 6L6 1" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
						</div>
					</div>
				</div>
				<a href="<?php echo esc_url( $campain_link ); ?>" class="show_product_btn show_product_btn--bottom">مشاهده همه محصولات</a>
			</div>
		</div>
	</div>
</div>
<?php }
wp_reset_postdata();
