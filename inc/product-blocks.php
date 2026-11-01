<?php
/**
 * بلوک‌های گوتنبرگ صفحه محصول (ACF Blocks) — همان الگوی inc/blocks.php.
 *
 * چیدمان بلوک‌ها در برگه‌ای با قالب «قالب صفحه محصول (بلوک‌ها)» ذخیره می‌شود
 * و woocommerce/single-product.php همان چیدمان را برای هر محصول رندر می‌کند.
 *
 * @package flatsome-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * تعریف بلوک‌ها به ترتیب نمایش.
 */
function takdooz_product_blocks() {
	return array(
		'product-hero'       => array(
			'title'       => 'محصول: بخش بالا (گالری + کارت خرید)',
			'description' => 'نگهدارنده‌ی گالری و کارت خرید',
			'icon'        => 'columns',
		),
		'product-gallery'    => array(
			'title'       => 'محصول: گالری تصاویر',
			'description' => 'گالری، کشیدن با انگشت و بزرگ‌نمایی',
			'icon'        => 'format-gallery',
		),
		'product-buy-card'   => array(
			'title'       => 'محصول: کارت خرید',
			'description' => 'عنوان، قیمت، سایز، چاپ و گلدوزی، افزودن به سبد و پاپ‌آپ بعد از افزودن',
			'icon'        => 'cart',
		),
		'product-tabs'       => array(
			'title'       => 'محصول: تب‌ها',
			'description' => 'معرفی کوتاه، توضیحات، مشخصات، راهنمای سایز، دیدگاه‌ها',
			'icon'        => 'index-card',
		),
		'product-related'    => array(
			'title'       => 'محصول: محصولات مرتبط',
			'description' => '۴ محصول مرتبط',
			'icon'        => 'grid-view',
		),
		'product-sticky-bar' => array(
			'title'       => 'محصول: نوار چسبان خرید',
			'description' => 'نوار پایین صفحه با قیمت و دکمه افزودن به سبد',
			'icon'        => 'align-full-width',
		),
	);
}

add_action( 'acf/init', 'takdooz_register_product_blocks' );
function takdooz_register_product_blocks() {

	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	foreach ( takdooz_product_blocks() as $slug => $args ) {

		if ( ! file_exists( get_stylesheet_directory() . '/blocks/' . $slug . '/render.php' ) ) {
			continue;
		}

		acf_register_block_type( array(
			'name'            => 'takdooz-' . $slug,
			'title'           => $args['title'],
			'description'     => $args['description'],
			'category'        => 'takdooz-product',
			'icon'            => $args['icon'],
			'keywords'        => array( 'محصول', 'تکدوز', 'product' ),
			'mode'            => 'preview',
			'supports'        => array(
				'align'    => false,
				'anchor'   => false,
				'mode'     => false,
				'multiple' => false,
				'jsx'      => true,
			),
			'render_template' => 'blocks/' . $slug . '/render.php',
		) );
	}
}

/* دسته‌ی جدا برای بلوک‌های محصول در گوتنبرگ */
add_filter( 'block_categories_all', function ( $categories ) {
	$categories[] = array(
		'slug'  => 'takdooz-product',
		'title' => 'بلوک‌های صفحه محصول تکدوز',
		'icon'  => null,
	);
	return $categories;
} );

/**
 * چیدمان پیش‌فرض (اگر برگه‌ی چیدمان هنوز ساخته نشده باشد).
 */
function takdooz_product_default_layout() {
	return '<!-- wp:acf/takdooz-product-hero {"name":"acf/takdooz-product-hero","mode":"preview"} -->'
		. '<!-- wp:acf/takdooz-product-gallery {"name":"acf/takdooz-product-gallery","mode":"preview"} /-->'
		. '<!-- wp:acf/takdooz-product-buy-card {"name":"acf/takdooz-product-buy-card","mode":"preview"} /-->'
		. '<!-- /wp:acf/takdooz-product-hero -->'
		. '<!-- wp:acf/takdooz-product-tabs {"name":"acf/takdooz-product-tabs","mode":"preview"} /-->'
		. '<!-- wp:acf/takdooz-product-related {"name":"acf/takdooz-product-related","mode":"preview"} /-->'
		. '<!-- wp:acf/takdooz-product-sticky-bar {"name":"acf/takdooz-product-sticky-bar","mode":"preview"} /-->';
}

/**
 * محتوای چیدمان: از اولین برگه‌ای که قالب product-layout.php دارد.
 */
function takdooz_product_layout_content() {
	$pages = get_posts( array(
		'post_type'   => 'page',
		'post_status' => array( 'publish', 'private', 'draft' ),
		'meta_key'    => '_wp_page_template',
		'meta_value'  => 'product-layout.php',
		'numberposts' => 1,
		'orderby'     => 'ID',
		'order'       => 'ASC',
	) );

	if ( $pages && has_blocks( $pages[0]->post_content ) ) {
		return $pages[0]->post_content;
	}

	return takdooz_product_default_layout();
}

/* برگه‌ی چیدمان محصول همیشه با گوتنبرگ باز شود (مثل صفحه اصلی) */
add_filter( 'use_block_editor_for_post', function ( $use, $post ) {
	if ( $post instanceof WP_Post
		&& 'page' === $post->post_type
		&& 'product-layout.php' === get_page_template_slug( $post ) ) {
		return true;
	}
	return $use;
}, 9999, 2 );

add_action( 'load-post.php', function () {
	$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
	if ( ! $post_id ) {
		return;
	}
	$post = get_post( $post_id );
	if ( $post instanceof WP_Post
		&& 'page' === $post->post_type
		&& 'product-layout.php' === get_page_template_slug( $post ) ) {
		remove_filter( 'use_block_editor_for_post_type', '__return_false', 100 );
	}
} );

/**
 * آماده‌سازی داده برای بلوک‌ها.
 * در ویرایشگر یا بیرون از صفحه محصول کارت پیش‌نمایش نشان می‌دهد و false برمی‌گرداند.
 *
 * @return array|false
 */
function takdooz_product_block_data( $title, $is_preview ) {
	if ( $is_preview || ! is_singular( 'product' ) || ! function_exists( 'tkd_pdp_data' ) ) {
		if ( function_exists( 'takdooz_block_preview_card' ) ) {
			takdooz_block_preview_card( $title, 'روی صفحه‌ی هر محصول با اطلاعات همان محصول نمایش داده می‌شود.' );
		}
		return false;
	}
	$data = tkd_pdp_data();
	return $data ? $data : false;
}
