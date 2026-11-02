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
		'apron-landing'     => array(
			'title'       => 'لندینگ پیش‌بند',
			'description' => 'هیرو + فیلتر زیردسته‌های پیش‌بند (باریستا، کمری، ضدآب، گل‌فروشی، آرایشگری)',
			'icon'        => 'grid-view',
			'keywords'    => array( 'apron', 'landing', 'پیش بند', 'فیلتر' ),
		),
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
