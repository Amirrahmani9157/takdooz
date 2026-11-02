<?php
/**
 * Block: لندینگ پیش‌بند (apron-landing)
 * ------------------------------------------------------------------
 * یک بلوک گوتنبرگ (ACF Block) برای صفحه‌ی فرود پیش‌بند:
 *   - هیروی کوتاه
 *   - نوار فیلتر/تب زیردسته‌ها (همان الگوی tab_title/tab_content تم)
 *   - با کلیک روی هر تب، محصولات همان زیردسته نمایش داده می‌شود
 *
 * سئو-امن:
 *   - محصولات سمت سرور رندر می‌شوند (قابل ایندکس).
 *   - فیلترهای «باریستا/کمری/ضدآب/دوطرفه» بر اساس اسم محصول‌اند و هیچ
 *     دسته/تگی ساخته یا تغییر داده نمی‌شود.
 *   - «گل‌فروشی» و «آرایشگری» دسته‌ی مستقل و رتبه‌دارند؛ هر تب یک دکمه‌ی
 *     «مشاهده همه» به صفحه‌ی اصلی همان دسته دارد (انتقال اعتبار لینک).
 *
 * فیلدهای ACF اختیاری‌اند؛ بدون تنظیم هم با مقادیر پیش‌فرض کار می‌کند.
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$al_heading = get_field( 'heading' ) ?: 'پیش‌بندهای حرفه‌ای تکدوز';
$al_sub     = get_field( 'subheading' ) ?: 'از باریستا و کمری تا ضدآب و گل‌فروشی؛ با بهترین پارچه، دوخت و امکان چاپ لوگو.';
$al_count   = (int) ( get_field( 'products_count' ) ?: 8 );

// شناسه‌ی دسته‌های واقعی (در صورت تغییر، فقط این‌جا اصلاح شود)
$cat_apron   = 2424; // پیش بند
$cat_florist = 2621; // پیشبند گلفروشی
$cat_barber  = 2545; // پیش بند آرایشگری

/**
 * تعریف تب‌ها.
 * type=term : کوئری بر اساس دسته‌ی واقعی (term_id) + لینک «مشاهده همه» به آرشیو آن دسته
 * type=name : کوئری بر اساس کلمه در اسم محصول، محدود به دسته‌ی «پیش بند»
 */
$tabs = array(
	array( 'label' => 'همه مدل‌ها', 'type' => 'term', 'term' => $cat_apron ),
	array( 'label' => 'باریستا',    'type' => 'name', 'kw' => 'باریستا' ),
	array( 'label' => 'کمری',       'type' => 'name', 'kw' => 'کمری' ),
	array( 'label' => 'ضدآب',       'type' => 'name', 'kw' => 'ضد' ),
	array( 'label' => 'دوطرفه',     'type' => 'name', 'kw' => 'طرفه' ),
	array( 'label' => 'گل‌فروشی',   'type' => 'term', 'term' => $cat_florist ),
	array( 'label' => 'آرایشگری',   'type' => 'term', 'term' => $cat_barber ),
);

// پیش‌نمایش سبک در ویرایشگر (مثل بقیه‌ی بلوک‌ها، برای سرعت)
if ( $is_preview ) {
	takdooz_block_preview_card( $al_heading, count( $tabs ) . ' فیلتر زیردسته، ' . $al_count . ' محصول در هر تب' );
	return;
}

/**
 * ساخت کوئری محصولات یک تب.
 */
function tkd_apron_tab_query( $tab, $apron_id, $per_page ) {
	$args = array(
		'post_type'           => 'product',
		'posts_per_page'      => $per_page,
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'post_status'         => 'publish',
	);

	if ( 'term' === $tab['type'] ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => (int) $tab['term'],
			),
		);
	} else {
		// فیلتر بر اساس اسم محصول، محدود به دسته‌ی «پیش بند»
		$args['s']         = $tab['kw'];
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => (int) $apron_id,
			),
		);
	}

	return new WP_Query( $args );
}

/**
 * لینک «مشاهده همه» هر تب.
 */
function tkd_apron_tab_link( $tab ) {
	if ( 'term' === $tab['type'] ) {
		$url = get_term_link( (int) $tab['term'], 'product_cat' );
		return is_wp_error( $url ) ? '' : $url;
	}
	// تب‌های اسمی: به آرشیو دسته‌ی پیش بند لینک می‌دهیم
	$url = get_term_link( 2424, 'product_cat' );
	return is_wp_error( $url ) ? '' : $url;
}
?>

<div class="tkd-apron-landing most_sale_products">
	<div class="container">

		<!-- هیرو -->
		<div class="row">
			<div class="tkd-al-hero">
				<h2 class="tkd-al-title"><?php echo esc_html( $al_heading ); ?></h2>
				<p class="tkd-al-sub"><?php echo esc_html( $al_sub ); ?></p>
				<div class="tkd-al-trust">
					<span>🚚 ارسال سریع</span>
					<span>🧵 دوخت صنعتی</span>
					<span>🖊️ چاپ لوگو</span>
					<span>✅ ضمانت کیفیت</span>
				</div>
			</div>
		</div>

		<!-- تب‌ها / فیلتر زیردسته‌ها (همان الگوی tab_title تم) -->
		<div class="row">
			<div class="title_wrap">
				<div class="title_wrap_text">
					<ul class="tab_title">
						<?php $i = 1; foreach ( $tabs as $tab ) : ?>
							<li class="<?php echo 1 === $i ? 'active' : ''; ?>" data-id="<?php echo $i; ?>"><?php echo esc_html( $tab['label'] ); ?></li>
						<?php $i++; endforeach; ?>
					</ul>
				</div>
			</div>
		</div>

		<!-- محتوای هر تب -->
		<div class="row">
			<div class="tabs_content_wrap">
				<?php $i = 1; foreach ( $tabs as $tab ) :
					$q    = tkd_apron_tab_query( $tab, $cat_apron, $al_count );
					$link = tkd_apron_tab_link( $tab );
					?>
					<div class="tab_content" data-id="<?php echo $i; ?>" style="<?php echo 1 !== $i ? 'display:none' : ''; ?>">
						<?php if ( $q->have_posts() ) : ?>
							<div class="swiper rb_product_swiper">
								<div class="swiper-wrapper">
									<?php while ( $q->have_posts() ) : $q->the_post(); ?>
										<div class="swiper-slide">
											<?php get_template_part( 'template-parts/content', 'product' ); ?>
										</div>
									<?php endwhile; ?>
								</div>
								<div class="rb_swiper_btns">
									<span class="slideNext-btn"><svg width="18" height="24" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L6 6L1 11" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
									<span class="slidePrev-btn"><svg width="18" height="24" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 11L1 6L6 1" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
								</div>
							</div>
							<?php if ( $link ) : ?>
								<a href="<?php echo esc_url( $link ); ?>" class="show_more_btn show_more_btn--bottom">مشاهده همه‌ی <?php echo esc_html( $tab['label'] ); ?> ←</a>
							<?php endif; ?>
						<?php else : ?>
							<p class="tkd-al-empty">به‌زودی محصول این بخش اضافه می‌شود.</p>
						<?php endif; ?>
					</div>
					<?php wp_reset_postdata(); $i++; endforeach; ?>
			</div>
		</div>

	</div>
</div>

<style>
/* استایل‌ها زیر .tkd-apron-landing محدود شده تا با قالب تداخل نکند */
.tkd-apron-landing .tkd-al-hero{text-align:center;background:linear-gradient(180deg,#faf7f1,#f3ecde);border:1px solid #e9e2d4;border-radius:20px;padding:30px 20px;margin:18px 0 22px}
.tkd-apron-landing .tkd-al-title{color:#26324f;font-weight:900;font-size:28px;margin:0}
.tkd-apron-landing .tkd-al-sub{color:#7b8192;font-size:15px;max-width:560px;margin:10px auto 0;line-height:1.9}
.tkd-apron-landing .tkd-al-trust{display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin-top:16px}
.tkd-apron-landing .tkd-al-trust span{background:#fff;border:1px solid #e9e2d4;border-radius:999px;padding:7px 14px;font-size:12.5px;color:#26324f;font-weight:600}
.tkd-apron-landing .tab_title{display:flex;flex-wrap:wrap;gap:9px;list-style:none;padding:0;margin:0 0 18px}
.tkd-apron-landing .tab_title li{background:#fff;border:1.5px solid #e9e2d4;color:#26324f;font-weight:700;font-size:13.5px;padding:9px 18px;border-radius:999px;cursor:pointer;transition:.15s}
.tkd-apron-landing .tab_title li:hover{border-color:#26324f}
.tkd-apron-landing .tab_title li.active{background:#26324f;color:#fff;border-color:#26324f}
.tkd-apron-landing .tkd-al-empty{padding:26px;text-align:center;color:#888}
@media (max-width:600px){
	.tkd-apron-landing .tkd-al-title{font-size:23px}
	.tkd-apron-landing .tab_title{flex-wrap:nowrap;overflow-x:auto;-webkit-overflow-scrolling:touch}
	.tkd-apron-landing .tab_title li{white-space:nowrap}
}
</style>
