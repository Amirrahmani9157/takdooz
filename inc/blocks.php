<?php
/**
 * Plan C — ACF Blocks homepage migration.
 *
 * Each homepage section from custom-template.php is migrated to a standalone
 * ACF block (ACF Pro 5.8+). The original custom-template.php is left untouched
 * and used only as the reference for section markup and ordering.
 *
 * Adding a block = create blocks/<slug>/render.php and add an entry to
 * takdooz_home_blocks(). Blocks whose render.php is missing are skipped, so
 * the editor never fatals while the migration is in progress.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block definitions, in homepage display order (mirrors custom-template.php).
 *
 * @return array<string,array{title:string,description:string,icon:string,keywords:string[]}>
 */
function takdooz_home_blocks() {
	return array(
		'hero-slider'       => array(
			'title'       => 'اسلایدر اصلی',
			'description' => 'اسلایدر بالای صفحه اصلی (custom-template.php خطوط ۳۵–۶۸)',
			'icon'        => 'images-alt2',
			'keywords'    => array( 'slider', 'hero', 'اسلایدر' ),
		),
		'bestseller-tabs'   => array(
			'title'       => 'پرفروش‌ترین محصولات (تب‌دار)',
			'description' => 'تب دسته‌بندی + اسلایدر پرفروش‌ها (خطوط ۷۰–۱۵۳)',
			'icon'        => 'star-filled',
			'keywords'    => array( 'bestseller', 'tabs', 'پرفروش' ),
		),
		'category-banner'   => array(
			'title'       => 'بنر دسته‌بندی',
			'description' => 'بنر تبلیغاتی میانی (خطوط ۱۵۶–۱۶۴)',
			'icon'        => 'format-image',
			'keywords'    => array( 'banner', 'بنر' ),
		),
		'offer-products'    => array(
			'title'       => 'محصولات پیشنهاد ویژه + تایمر',
			'description' => 'کمپین با شمارش معکوس (خطوط ۱۶۶–۲۵۸)',
			'icon'        => 'clock',
			'keywords'    => array( 'offer', 'campaign', 'پیشنهاد' ),
		),
		'newest-products'   => array(
			'title'       => 'جدیدترین محصولات',
			'description' => 'گرید/اسلایدر جدیدترین محصولات (خطوط ۲۶۰–۳۱۲)',
			'icon'        => 'products',
			'keywords'    => array( 'newest', 'جدید' ),
		),
		'why-takdooz'       => array(
			'title'       => 'چرا تکدوز',
			'description' => 'ویدیو + متن چرا تکدوز (خطوط ۳۱۵–۳۲۶)',
			'icon'        => 'video-alt3',
			'keywords'    => array( 'video', 'why', 'چرا' ),
		),
		'featured-category' => array(
			'title'       => 'دسته‌بندی منتخب',
			'description' => 'کارت دسته منتخب + محصولاتش (خطوط ۳۲۸–۳۸۷)',
			'icon'        => 'category',
			'keywords'    => array( 'category', 'featured', 'دسته' ),
		),
		'testimonials'      => array(
			'title'       => 'نظرات مشتریان',
			'description' => 'اسلایدر نظرات مشتریان (خطوط ۳۸۹–۴۸۶)',
			'icon'        => 'format-quote',
			'keywords'    => array( 'testimonials', 'نظرات' ),
		),
		'blog-posts'        => array(
			'title'       => 'مقالات مجله',
			'description' => 'اسلایدر آخرین مقالات (خطوط ۴۸۸–۵۳۶)',
			'icon'        => 'admin-post',
			'keywords'    => array( 'blog', 'posts', 'مقالات' ),
		),
	);
}

/**
 * Register all available homepage blocks.
 */
add_action( 'acf/init', 'takdooz_register_home_blocks' );
function takdooz_register_home_blocks() {

	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return; // ACF Pro inactive.
	}

	$blocks_dir = get_stylesheet_directory() . '/blocks';

	foreach ( takdooz_home_blocks() as $slug => $args ) {

		$render = $blocks_dir . '/' . $slug . '/render.php';

		// Skip blocks whose render template is not built yet.
		if ( ! file_exists( $render ) ) {
			continue;
		}

		acf_register_block_type( array(
			'name'            => 'takdooz-' . $slug,
			'title'           => $args['title'],
			'description'     => $args['description'],
			'category'        => 'takdooz-home',
			'icon'            => $args['icon'],
			'keywords'        => $args['keywords'],
			'mode'            => 'preview',
			'supports'        => array(
				'align'  => false,
				'anchor' => true,
				'jsx'    => true,
			),
			'render_template' => 'blocks/' . $slug . '/render.php',
		) );
	}
}

/**
 * Lightweight editor preview card.
 *
 * The product/post-query blocks are expensive to render (loops + template
 * parts) and the block editor re-renders them server-side via REST on every
 * load. In the editor we instead show a compact labeled card describing the
 * block + its current settings, which keeps editing fast. The real, full
 * output is always rendered on the front-end.
 *
 * @param string $title Block title.
 * @param string $note  Optional one-line summary of the block's settings.
 */
function takdooz_block_preview_card( $title, $note = '' ) {
	echo '<div style="border:1px dashed #c3c4c7;border-radius:8px;padding:26px 20px;text-align:center;background:#f6f7f7;color:#3c434a;font-family:Tahoma,Vazirmatn,sans-serif;direction:rtl;">';
	echo '<strong style="display:block;font-size:15px;margin-bottom:6px;">' . esc_html( $title ) . '</strong>';
	if ( $note ) {
		echo '<span style="font-size:12px;color:#787c82;">' . esc_html( $note ) . '</span>';
	}
	echo '<div style="font-size:11px;color:#a7aaad;margin-top:10px;">پیش‌نمایش سبک برای سرعت ویرایشگر — خروجی کامل در سایت نمایش داده می‌شود.</div>';
	echo '</div>';
}

/**
 * Convert a date string to a Unix timestamp, supporting Jalali (Persian) dates.
 *
 * ACF's Persian date picker stores values like "1404-08-29 20:00:00", which
 * strtotime() mis-reads as the medieval Gregorian year 1404. When the year
 * looks Jalali (1300–1699) and the intl extension is available, parse it with
 * the Persian calendar in the site timezone; otherwise fall back to strtotime.
 *
 * @param string $raw Raw date string ("Y-m-d" or "Y-m-d H:i:s", - or /).
 * @return int Unix timestamp, or 0 on failure.
 */
function takdooz_to_timestamp( $raw ) {
	$raw = trim( (string) $raw );
	if ( '' === $raw ) {
		return 0;
	}

	$year = (int) substr( $raw, 0, 4 );

	if ( $year >= 1300 && $year < 1700 && class_exists( 'IntlDateFormatter' ) ) {
		$norm = str_replace( '/', '-', $raw );
		if ( strlen( $norm ) <= 10 ) {
			$norm .= ' 00:00:00';
		}
		$tz  = wp_timezone()->getName();
		$fmt = new IntlDateFormatter(
			'en_US@calendar=persian',
			IntlDateFormatter::FULL,
			IntlDateFormatter::FULL,
			$tz,
			IntlDateFormatter::TRADITIONAL,
			'yyyy-MM-dd HH:mm:ss'
		);
		$ts = $fmt->parse( $norm );
		if ( $ts ) {
			return (int) $ts;
		}
	}

	$ts = strtotime( $raw );
	return $ts ? (int) $ts : 0;
}

/**
 * Custom block-editor category so all homepage blocks group together.
 */
add_filter( 'block_categories_all', 'takdooz_block_category', 10, 1 );
function takdooz_block_category( $categories ) {
	return array_merge(
		array(
			array(
				'slug'  => 'takdooz-home',
				'title' => 'بلوک‌های صفحه اصلی تکدوز',
				'icon'  => null,
			),
		),
		$categories
	);
}

/**
 * Version-control ACF field groups as JSON in the theme (git-trackable).
 */
add_filter( 'acf/settings/save_json', 'takdooz_acf_json_save_point' );
function takdooz_acf_json_save_point( $path ) {
	return get_stylesheet_directory() . '/acf-json';
}

add_filter( 'acf/settings/load_json', 'takdooz_acf_json_load_point' );
function takdooz_acf_json_load_point( $paths ) {
	$paths[] = get_stylesheet_directory() . '/acf-json';
	return $paths;
}

/**
 * Force the Block (Gutenberg) editor ONLY for pages using the
 * home-blocks.php template, so the homepage can be edited block-by-block
 * with ACF fields. Every other page/post keeps the site-wide Classic
 * Editor (this is a Flatsome / UX Builder site).
 *
 * Priority 9999 so this runs after the Classic Editor plugin's own
 * use_block_editor_for_post filter (priority 100) and wins for this template.
 */
add_filter( 'use_block_editor_for_post', 'takdooz_force_block_editor_for_home', 9999, 2 );
function takdooz_force_block_editor_for_home( $use_block_editor, $post ) {

	if ( $post instanceof WP_Post
		&& 'page' === $post->post_type
		&& 'home-blocks.php' === get_page_template_slug( $post ) ) {
		return true;
	}

	return $use_block_editor;
}

/**
 * The Classic Editor plugin runs on its defaults here ("Classic editor",
 * switching disallowed), which disables the Block editor site-wide via
 * use_block_editor_for_post_type => __return_false (priority 100). That
 * post-type-level kill switch is what actually keeps the homepage page in
 * the Classic editor despite the filter above.
 *
 * On the edit screen of a page using home-blocks.php ONLY, and for that
 * single request, remove Classic Editor's kill switch so the Block editor
 * (and its REST/ACF-block UI) loads. Every other page stays Classic.
 *
 * load-post.php fires before post.php decides which editor to include,
 * so this is correctly timed.
 */
add_action( 'load-post.php', 'takdooz_block_editor_for_home_screen' );
function takdooz_block_editor_for_home_screen() {

	$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;

	if ( ! $post_id ) {
		return;
	}

	$post = get_post( $post_id );

	if ( $post instanceof WP_Post
		&& 'page' === $post->post_type
		&& 'home-blocks.php' === get_page_template_slug( $post ) ) {
		remove_filter( 'use_block_editor_for_post_type', '__return_false', 100 );
	}
}


/**
 * Apron category page — landing section + storefront product-card enhancements.
 *
 * Follows the same convention as the homepage blocks above (takdooz_* naming,
 * ACF block registration, markup kept with its block):
 *
 *   - takdooz_register_apron_landing / takdooz_render_apron_landing
 *       ACF block "takdooz-apron-landing" (hero + «خرید پیش‌بند بر اساس دسته‌بندی»),
 *       injected above the apron category archive by takdooz_apron_category_landing().
 *   - takdooz_apron_loop_per_page / takdooz_apron_pre_get_posts
 *       24 products per page on the apron archive (term 2424).
 *   - takdooz_landing_noindex / takdooz_landing_wp_robots
 *       noindex for the home-blocks.php landing-preview pages.
 *   - takdooz_enqueue_child_style
 *       loads the child style.css site-wide (product-card styling).
 *   - takdooz_card_interactions / takdooz_cart_icon
 *       wishlist feedback, whole-card click-through, and the SVG cart-bag icon.
 */

add_action( 'acf/init', 'takdooz_register_apron_landing' );
function takdooz_register_apron_landing() {
	if ( ! function_exists( 'acf_register_block_type' ) ) { return; }
	acf_register_block_type( array(
		'name' => 'takdooz-apron-landing', 'title' => 'لندینگ پیش‌بند',
		'description' => 'لندینگ پیش‌بند', 'category' => 'takdooz-home',
		'icon' => 'grid-view', 'keywords' => array( 'apron', 'پیش بند' ),
		'mode' => 'preview', 'supports' => array( 'align' => array( 'full' ), 'anchor' => true, 'jsx' => true ),
		'render_callback' => 'takdooz_render_apron_landing',
	) );
}

function takdooz_apron_svg( $d ) { return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' . $d . '</svg>'; }
function takdooz_apron_icon( $k ) {
	$p = array(
		'water'=>'<path d="M12 3c3.5 4.5 6 7.5 6 10.5a6 6 0 0 1-12 0C6 10.5 8.5 7.5 12 3z"/>',
		'kamari'=>'<path d="M8 3a4 4 0 0 0 8 0"/><path d="M8 3 5 4.5 6 9h1v10a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9h1l1-4.5L16 3"/>',
		'barista'=>'<path d="M10 2v2M14 2v2M6 8h12v6a4 4 0 0 1-4 4h-4a4 4 0 0 1-4-4z"/><path d="M18 9h1.5a2.5 2.5 0 0 1 0 5H18"/>',
		'chef'=>'<path d="M7 20h10M8 20v-5M16 20v-5M6 15a4 4 0 0 1-1-7.9A4 4 0 0 1 12 5a4 4 0 0 1 7 2.1A4 4 0 0 1 18 15z"/>',
		'florist'=>'<circle cx="12" cy="7" r="2.3"/><circle cx="7.6" cy="10.3" r="2.3"/><circle cx="16.4" cy="10.3" r="2.3"/><circle cx="9.4" cy="15" r="2.3"/><circle cx="14.6" cy="15" r="2.3"/><path d="M12 17v5"/>',
		'barber'=>'<circle cx="6" cy="6" r="2.6"/><circle cx="6" cy="18" r="2.6"/><path d="M20 4 8.5 15.5M20 20 8.5 8.5"/>',
	);
	return takdooz_apron_svg( isset( $p[$k] ) ? $p[$k] : $p['water'] );
}

function takdooz_render_apron_landing( $block, $content = '', $is_preview = false ) {
	$apron=2424; $florist=2621; $barber=2545;
	$apron_url = get_term_link($apron,'product_cat'); $apron_url = is_wp_error($apron_url)? home_url('/product-category/restaurant-clothes/apron/'):$apron_url;
	$florist_url = get_term_link($florist,'product_cat'); $florist_url=is_wp_error($florist_url)?$apron_url:$florist_url;
	$barber_url = get_term_link($barber,'product_cat'); $barber_url=is_wp_error($barber_url)?$apron_url:$barber_url;
	$srch=function($k){return home_url('/?post_type=product&s='.rawurlencode($k));};

	$hero = get_field('hero_image'); $hero_url=(is_array($hero)&&!empty($hero['url']))?$hero['url']:(is_string($hero)&&$hero?$hero:'');
	if(!$hero_url){$hero_url=home_url('/wp-content/uploads/2026/11/takdooz-hero-1.png');}

	$ill = home_url('/wp-content/uploads/2026/11/');

	$cats = array(
		array('t'=>'باریستا','s'=>'استایل خاص کافه','ic'=>'barista','img'=>$ill.'apron-barista.png','link'=>home_url('/product-tag/barista-apron/')),
		array('t'=>'آشپزی','s'=>'آشپزخانه و رستوران','ic'=>'chef','img'=>$ill.'apron-cook.png','link'=>$apron_url),
		array('t'=>'ضدآب','s'=>'مقاوم در برابر آب و لکه','ic'=>'water','img'=>$ill.'apron-waterproof.png','link'=>$srch('ضد آب')),
		array('t'=>'کمری / نیم‌تنه','s'=>'سبک، کاربری و راحت','ic'=>'kamari','img'=>$ill.'apron-kamari.png','link'=>$srch('کمری')),
		array('t'=>'گل‌فروشی','s'=>'زیبا، سبک و کاربردی','ic'=>'florist','img'=>$ill.'apron-florist.png','link'=>$florist_url),
		array('t'=>'آرایشگری','s'=>'محافظت از مو و آب','ic'=>'barber','img'=>$ill.'apron-barber.png','link'=>$barber_url),
	);

	if($is_preview){ takdooz_block_preview_card('لندینگ پیش‌بند (v12)','هیرو + خرید بر اساس دسته‌بندی (تصویری)'); return; }
	?>
	<div class="tkd-al">

		<!-- HERO -->
		<section class="tkd-hero" style="background-image:linear-gradient(180deg,rgba(20,28,51,.3) 0%,rgba(20,28,51,.55) 55%,rgba(20,28,51,.92) 100%),url('<?php echo esc_url($hero_url); ?>')">
			<div class="tkd-hero-box">
				<span class="tkd-eye">کیفیت در جزئیات، حرفه‌ای در عملکرد</span>
				<h2 class="tkd-h1">پیش‌بندهای حرفه‌ای تکدوز</h2>
				<p class="tkd-sub">مناسب آشپزی، باریستا، گل‌فروشی و تمام مشاغل؛ با بهترین پارچه، دوخت و طراحی کاربردی.</p>
			</div>
		</section>

		<!-- خرید بر اساس شغل (کارت‌های تصویری، لینک به صفحه‌ی دسته) -->
		<div class="tkd-wrap">
			<h3 class="tkd-h2">خرید پیش‌بند بر اساس دسته‌بندی</h3><span class="tkd-u"></span>
			<div class="tkd-cats"><?php foreach($cats as $c): ?>
				<a class="tkd-cat" href="<?php echo esc_url($c['link']); ?>">
					<span class="ph"<?php if($c['img'])echo ' style="background-image:url(\''.esc_url($c['img']).'\')"'; ?>></span>
					<span class="ic"><?php echo takdooz_apron_icon($c['ic']); ?></span>
					<b><?php echo esc_html($c['t']); ?></b>
					<small><?php echo esc_html($c['s']); ?></small>
				</a>
			<?php endforeach; ?></div>
		</div>

	</div>

	<style>
	.tkd-al{--nv:#26324f;--gd:#e0a63a;--ln:#ece5d7;--mut:#7b8192;--bg:#f6f3ec;font-family:inherit;color:#26324f;padding-bottom:30px}
	.tkd-al .tkd-wrap{max-width:1200px;margin:0 auto;padding:0 15px}
	.tkd-al .tkd-btn{display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;border-radius:12px;padding:13px 24px;text-decoration:none;transition:.15s}
	.tkd-al .tkd-btn.p{background:var(--gd);color:#2a1e05}
	.tkd-al .tkd-btn.o{background:transparent;color:#fff;border:1.5px solid rgba(255,255,255,.6)}
	.tkd-al .tkd-btn.outline{background:#fff;color:var(--nv);border:1.5px solid var(--ln)}
	.tkd-al .tkd-center{text-align:center;margin:22px 0 4px}
	.tkd-al .tkd-h2{text-align:center;font-size:21px;font-weight:900;margin:30px 0 0;color:var(--nv)}
	.tkd-al .tkd-u{display:block;width:46px;height:4px;border-radius:3px;background:var(--gd);margin:9px auto 20px}
	/* hero — متن سفید */
	.tkd-al .tkd-hero{background-size:cover;background-position:center;min-height:360px;display:flex;align-items:flex-end;color:#fff}
	.tkd-al .tkd-hero-box{padding:26px 16px 30px;max-width:640px}
	.tkd-al .tkd-eye{color:#fff;font-weight:800;font-size:12.5px;text-shadow:0 1px 8px rgba(0,0,0,.4)}
	.tkd-al .tkd-h1{font-size:28px;font-weight:900;line-height:1.45;margin:10px 0 0;color:#fff;text-shadow:0 2px 14px rgba(0,0,0,.45)}
	.tkd-al .tkd-sub{font-size:14.5px;color:#fff;margin:10px 0 0;line-height:1.9;text-shadow:0 1px 10px rgba(0,0,0,.45)}
	.tkd-al .tkd-hero-cta{display:flex;gap:11px;flex-wrap:wrap;margin-top:18px}
	/* کارت دسته (تصویرسازی) */
	.tkd-al .tkd-cats{display:grid;grid-template-columns:repeat(3,1fr);gap:9px}
	.tkd-al .tkd-cat{background:transparent;border:none;border-radius:16px;text-align:center;padding:0 0 6px;text-decoration:none;color:var(--nv);box-shadow:none;display:block;transition:transform .15s}
	.tkd-al .tkd-cat:hover{transform:translateY(-3px)}
	.tkd-al .tkd-cat:hover .ph{box-shadow:0 12px 26px -16px rgba(38,50,79,.55)}
	.tkd-al .tkd-cat .ph{display:block;height:98px;background-size:contain;background-repeat:no-repeat;background-position:center;background-color:#f6f3ec;border-radius:16px;transition:box-shadow .15s}
	.tkd-al .tkd-cat .ic{width:34px;height:34px;border-radius:50%;background:#fff;border:1px solid var(--ln);color:var(--nv);display:grid;place-items:center;margin:-17px auto 6px;position:relative}
	.tkd-al .tkd-cat .ic svg{width:17px;height:17px}
	.tkd-al .tkd-cat b{display:block;font-size:12px;font-weight:800;line-height:1.4}
	.tkd-al .tkd-cat small{display:none;font-size:11px;color:var(--mut)}
	@media(min-width:768px){
		.tkd-al .tkd-hero{min-height:460px}
		.tkd-al .tkd-hero-box{padding:48px 40px}
		.tkd-al .tkd-h1{font-size:40px}
		.tkd-al .tkd-sub{font-size:17px}
		.tkd-al .tkd-h2{font-size:27px}
		.tkd-al .tkd-cats{grid-template-columns:repeat(6,1fr);gap:12px}
		.tkd-al .tkd-cat .ph{height:132px}
		.tkd-al .tkd-cat b{font-size:13px}
		.tkd-al .tkd-cat small{display:block}
	}
	</style>
	<?php
}

/* نمایش لندینگ روی صفحه‌ی دسته‌ی اصلی پیش‌بند (بالای محصولات، سئو حفظ می‌شود) */
add_action( 'woocommerce_archive_description', 'takdooz_apron_category_landing', 5 );
function takdooz_apron_category_landing() {
	if ( ! function_exists('is_product_category') || ! is_product_category() ) { return; }
	$obj = get_queried_object();
	if ( $obj && isset($obj->term_id) && 2424 === (int) $obj->term_id && function_exists('takdooz_render_apron_landing') ) {
		echo '<div class="tkd-apron-archive-landing">';
		takdooz_render_apron_landing( array() );
		echo '</div>';
		echo '<script>document.addEventListener(\'DOMContentLoaded\',function(){var b=document.querySelector(\'.takdooz-filter-bar\'),l=document.querySelector(\'.tkd-apron-archive-landing\');if(b&&l&&l.parentNode){l.parentNode.insertBefore(b,l.nextSibling);}});</script>';
	}
}


/* 24 محصول در هر صفحه‌ی دسته‌ی پیش‌بند + صفحه‌بندی */
add_filter( 'loop_shop_per_page', 'takdooz_apron_loop_per_page', 99 );
add_action( 'pre_get_posts', 'takdooz_apron_pre_get_posts' );
function takdooz_apron_pre_get_posts( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) { return; }
	if ( function_exists('is_product_category') && is_product_category() ) {
		$o = get_queried_object();
		if ( $o && isset($o->term_id) && 2424 === (int) $o->term_id ) { $q->set( 'posts_per_page', 24 ); }
	}
}
function takdooz_apron_loop_per_page( $n ) {
	if ( function_exists('is_product_category') && is_product_category() ) {
		$o = get_queried_object();
		if ( $o && isset($o->term_id) && 2424 === (int) $o->term_id ) { return 24; }
	}
	return $n;
}

/* noindex برای صفحات پیش‌نمایش لندینگ (قالب home-blocks.php) */
function takdooz_is_landing_preview() {
	if ( ! function_exists('is_page') || ! is_page() ) { return false; }
	$id = get_queried_object_id();
	return $id && 'home-blocks.php' === get_page_template_slug( $id );
}
add_filter( 'rank_math/frontend/robots', 'takdooz_landing_noindex' );
function takdooz_landing_noindex( $robots ) {
	if ( takdooz_is_landing_preview() ) { $robots['index']='noindex'; $robots['follow']='nofollow'; }
	return $robots;
}
add_filter( 'wp_robots', 'takdooz_landing_wp_robots' );
function takdooz_landing_wp_robots( $r ) {
	if ( takdooz_is_landing_preview() ) { $r['noindex']=true; $r['nofollow']=true; }
	return $r;
}

/* enqueue child style.css (not loaded by theme by default) for site-wide product-card styling */
add_action( 'wp_enqueue_scripts', 'takdooz_enqueue_child_style', 99 );
function takdooz_enqueue_child_style() {
	wp_enqueue_style( 'tkd-child-style', get_stylesheet_directory_uri() . '/style.css', array(), filemtime( get_stylesheet_directory() . '/style.css' ) );
}

/* wishlist feedback + whole-card click to product */
add_action( 'wp_footer', 'takdooz_card_interactions' );
function takdooz_card_interactions() {
	if ( is_admin() ) { return; }
	echo '<script>(function(){document.addEventListener("click",function(e){var h=e.target.closest&&e.target.closest(".product-small .wishlist-button");if(!h){return;}h.classList.add("tkd-pop");h.classList.toggle("tkd-faved");setTimeout(function(){h.classList.remove("tkd-pop");},360);},true);document.addEventListener("click",function(e){if(!e.target.closest){return;}if(e.target.closest("a, button, input, label, .add-to-cart-button, .wishlist-icon, .quantity, .star-rating")){return;}var card=e.target.closest(".products .product-small");if(!card){return;}var link=card.querySelector(".name.product-title a, .box-image a");if(link&&link.href){window.location.href=link.href;}},false);})();</script>';
}

/* ===== آیکون سبد خرید (SVG use) + رفع باگ لودینگ دکمه ===== */
add_action( 'wp_footer', 'takdooz_cart_icon' );
function takdooz_cart_icon() {
	if ( is_admin() ) { return; }
	?>
	<svg xmlns="http://www.w3.org/2000/svg" style="position:absolute;width:0;height:0;overflow:hidden" aria-hidden="true"><symbol id="icon-shopping_bag" viewBox="0 0 24 24" fill="none"><path d="M3 3h2l1.6 10.1a2 2 0 0 0 2 1.7h7.9a2 2 0 0 0 1.9-1.4L21 6H6.1M9 20a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></symbol></svg>
	<script>
	(function(){
	function addIcons(){
		var btns=document.querySelectorAll(".products .product-small .add-to-cart-button a.button, .products .product-small a.add_to_cart_button");
		for(var k=0;k<btns.length;k++){
			var b=btns[k];
			if(b.querySelector("svg.tkd-bag")){continue;}
			var s=document.createElementNS("http://www.w3.org/2000/svg","svg");
			s.setAttribute("class","tkd-bag");s.setAttribute("viewBox","0 0 24 24");s.setAttribute("aria-hidden","true");
			var u=document.createElementNS("http://www.w3.org/2000/svg","use");
			u.setAttribute("href","#icon-shopping_bag");
			u.setAttributeNS("http://www.w3.org/1999/xlink","xlink:href","#icon-shopping_bag");
			s.appendChild(u);
			b.insertBefore(s,b.firstChild);
			var bd=document.createElement("span");bd.className="rb_cart_status_badge";bd.setAttribute("aria-hidden","true");bd.textContent="\u2713";b.appendChild(bd);
		}
	}
	if(document.readyState!="loading"){addIcons();}else{document.addEventListener("DOMContentLoaded",addIcons);}
	if(window.jQuery){jQuery(document.body).on("added_to_cart",function(e,f,h,$b){if($b&&$b.addClass){$b.addClass("is-in-cart");}addIcons();});}window.addEventListener("load",addIcons);var _tkp=false;function _tks(){if(_tkp){return;}_tkp=true;setTimeout(function(){_tkp=false;addIcons();},90);}try{new MutationObserver(function(m){for(var i=0;i<m.length;i++){if(m[i].addedNodes&&m[i].addedNodes.length){_tks();break;}}}).observe(document.body,{childList:true,subtree:true});}catch(e){}
	})();
	</script>
	<?php
}

/* ===== وضعیت «در سبد خرید» برای دکمه کارت دسته‌بندی (مثل صفحه اصلی) ===== */
add_filter( 'woocommerce_loop_add_to_cart_link', 'takdooz_loop_in_cart_class', 20, 2 );
function takdooz_loop_in_cart_class( $html, $product ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart || ! $product ) { return $html; }
	$in_cart = false;
	foreach ( WC()->cart->get_cart() as $item ) {
		if ( (int) $item['product_id'] === (int) $product->get_id() ) { $in_cart = true; break; }
	}
	$tip = $product->is_type( 'variable' ) ? ( $in_cart ? 'در سبد خرید — انتخاب گزینه دیگر' : 'انتخاب گزینه‌ها' ) : ( $in_cart ? 'در سبد خرید — افزودن مجدد' : 'افزودن به سبد خرید' );
	$html = preg_replace( '/<a /', '<a title="' . esc_attr( $tip ) . '" ', $html, 1 );
	if ( $in_cart ) { $html = preg_replace( '/class="/', 'class="is-in-cart ', $html, 1 ); }
	return $html;
}
