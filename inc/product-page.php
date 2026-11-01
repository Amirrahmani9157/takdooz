<?php
/**
 * صفحه محصول تکدوز — منطق و داده‌ها (منتقل‌شده از اسنیپت ۹ «۱» و ۱۰ «۲»).
 *
 * - ابزارها، تنظیمات و منطق چاپ/گلدوزی در سبد خرید: عیناً از اسنیپت ۹ (خطوط ۷ تا ۲۸۱).
 * - tkd_pdp_data(): همان محاسبات شورت‌کد [tkd_product] (خطوط ۲۸۴ تا ۳۷۵)
 *   که یک بار اجرا می‌شود و بین بلوک‌های صفحه محصول مشترک است.
 * - JS صفحه از اسنیپت ۱۰ به فایل assets/js/tkd-pdp.js منتقل شده است.
 *
 * تا وقتی اسنیپت‌های قدیمی فعال‌اند این فایل هیچ کاری نمی‌کند تا تداخلی پیش نیاید.
 *
 * @package flatsome-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// تا وقتی اسنیپت قدیمی «۱» فعال است، این کد تعریف نمی‌شود (تعریف شرطی برای جلوگیری از Cannot redeclare).
if ( ! function_exists( 'tkd_cfg' ) ) :

/* ---------- تنظیمات: فقط همین بخش را ویرایش کن ---------- */
function tkd_cfg() {
	return array(
		'warranty'   => '۶ ماه ضمانت کیفیت',
		'phone'      => '02100000000',      // شماره تماس
		'whatsapp'   => '989000000000',     // شماره واتس‌اپ با ۹۸ و بدون صفر
		'trust'      => array( '۶ ماه ضمانت کیفیت', 'ارسال به سراسر کشور', 'سفارش با لوگوی شما' ),
		'svc'        => array(              // خدمات و قیمت هر طرف (تومان)
			'print' => array( 'label' => 'چاپ', 'price' => 150000 ),
			'emb'   => array( 'label' => 'گلدوزی', 'price' => 250000 ),
		),
		'sides'      => array( 'front' => 'جلو', 'back' => 'پشت' ),
		'addon_cats' => array( 'chef-hat', 'apron' ), // اگر Cross-sells خالی بود، از این دسته‌ها پیشنهاد می‌دهد
		'max_mb'     => 10,
	);
}

function tkd_size_guides() {
	return array(
		'chef-men-clothes' =>
			'<table class="tkd-size-table"><thead><tr><th>سایز</th><th>عرض</th><th>قد</th></tr></thead><tbody>'
			. '<tr><th>M</th><td>۴۵</td><td>۶۰</td></tr>'
			. '<tr><th>L</th><td>۴۹</td><td>۶۳</td></tr>'
			. '<tr><th>XL</th><td>۵۴</td><td>۶۹</td></tr>'
			. '<tr><th>2XL</th><td>۵۸</td><td>۷۲</td></tr>'
			. '</tbody></table><p class="tkd-note">اندازه‌ها بر حسب سانتی‌متر است.</p>',
	);
}

/* ---------- ابزارها ---------- */
function tkd_size_guide_html( $product_id ) {
	foreach ( tkd_size_guides() as $slug => $html ) {
		if ( has_term( $slug, 'product_cat', $product_id ) ) {
			return $html;
		}
	}
	return '';
}

function tkd_fa_digits( $s ) {
	return strtr( (string) $s, array(
		'0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
		'5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹', ',' => '٬', '.' => '٫',
	) );
}

function tkd_money( $n ) {
	return tkd_fa_digits( number_format( (float) $n ) );
}

function tkd_icon( $key ) {
	$paths = array(
		'list'   => '<path d="M5 6h14M5 11h14M5 16h9"/>',
		'doc'    => '<path d="M7 3h8l4 4v14H7z"/><path d="M10 12h6M10 16h6"/>',
		'gear'   => '<circle cx="12" cy="12" r="3"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3"/>',
		'ruler'  => '<path d="M3 15L15 3l6 6L9 21zM7 11l2 2M10 8l2 2M13 5l2 2"/>',
		'chat'   => '<path d="M4 5h16v11H9l-5 4z"/>',
		'shield' => '<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/><path d="M9 12l2 2 4-4"/>',
		'truck'  => '<path d="M3 6h11v10H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
		'needle' => '<path d="M5 19L19 5"/><circle cx="17.5" cy="6.5" r="1.3"/><path d="M4 14c3 0 3 6 6 6"/>',
		'check'  => '<path d="M5 12l5 5 9-10"/>',
	);
	$p = isset( $paths[ $key ] ) ? $paths[ $key ] : $paths['doc'];
	return '<svg viewBox="0 0 24 24" aria-hidden="true">' . $p . '</svg>';
}

/* کارت کوچک محصول (برای محصولات مرتبط و پاپ‌آپ) */
function tkd_mini_card( $p, $quick = false ) {
	$link = get_permalink( $p->get_id() );
	$out  = '<div class="tkd-pc">'
		. '<a class="tkd-pc-img" href="' . esc_url( $link ) . '">' . $p->get_image( 'woocommerce_thumbnail' ) . '</a>'
		. '<a class="tkd-pc-name" href="' . esc_url( $link ) . '">' . esc_html( $p->get_name() ) . '</a>'
		. '<div class="tkd-pc-price">' . $p->get_price_html() . '</div>';
	if ( $quick && $p->is_type( 'simple' ) && $p->is_purchasable() && $p->is_in_stock() ) {
		$out .= '<a href="' . esc_url( $p->add_to_cart_url() ) . '" data-quantity="1" data-product_id="' . (int) $p->get_id()
			. '" class="tkd-pc-btn add_to_cart_button ajax_add_to_cart" rel="nofollow">افزودن به سبد</a>';
	} else {
		$out .= '<a class="tkd-pc-btn is-ghost" href="' . esc_url( $link ) . '">' . ( $quick ? 'انتخاب سایز' : 'مشاهده محصول' ) . '</a>';
	}
	return $out . '</div>';
}

/* محصولات مکمل: اول Cross-sells، بعد دسته‌های اکسسوری */
function tkd_addon_ids( $pid ) {
	$p   = wc_get_product( $pid );
	$ids = $p ? $p->get_cross_sell_ids() : array();
	if ( count( $ids ) < 4 ) {
		$more = wc_get_products( array(
			'status'       => 'publish',
			'limit'        => 8,
			'category'     => tkd_cfg()['addon_cats'],
			'exclude'      => array_merge( $ids, array( $pid ) ),
			'stock_status' => 'instock',
			'orderby'      => 'rand',
			'return'       => 'ids',
		) );
		$ids = array_merge( $ids, $more );
	}
	return array_slice( array_unique( array_map( 'intval', $ids ) ), 0, 4 );
}

/* خلاصه‌ی امتیاز دیدگاه‌ها */
function tkd_reviews_summary( $product ) {
	$count = (int) $product->get_review_count();
	if ( ! $count ) {
		return '<div class="tkd-rs-empty"><b>هنوز دیدگاهی ثبت نشده</b><span>اگر این محصول را خریده‌اید، تجربه‌تان را بنویسید تا به خریداران دیگر کمک کند.</span></div>';
	}
	$avg = (float) $product->get_average_rating();
	$rc  = $product->get_rating_counts();
	$sum = array_sum( $rc );
	$h   = '<div class="tkd-rs"><div class="tkd-rs-score"><b>' . tkd_fa_digits( number_format( $avg, 1 ) ) . '</b>'
		. '<span class="tkd-stars" style="--r:' . round( $avg / 5 * 100 ) . '%" aria-label="امتیاز ' . esc_attr( $avg ) . ' از ۵"></span>'
		. '<small>از ' . tkd_fa_digits( $count ) . ' دیدگاه</small></div><div class="tkd-rs-bars">';
	for ( $i = 5; $i >= 1; $i-- ) {
		$n  = isset( $rc[ $i ] ) ? (int) $rc[ $i ] : 0;
		$pc = $sum ? round( $n / $sum * 100 ) : 0;
		$h .= '<div class="tkd-rs-row"><span>' . tkd_fa_digits( $i ) . ' ستاره</span><i><em style="width:' . $pc . '%"></em></i><span>' . tkd_fa_digits( $n ) . '</span></div>';
	}
	return $h . '</div></div>';
}

/* ---------- چاپ و گلدوزی ---------- */
add_action( 'woocommerce_before_add_to_cart_quantity', function () {
	$c = tkd_cfg();
	echo '<div class="tkd-svc-wrap"><div class="tkd2-head"><b>چاپ یا گلدوزی لوگو</b><span>اختیاری</span></div>';
	foreach ( $c['sides'] as $sk => $sl ) {
		echo '<div class="tkd2-row"><span class="tkd2-side">' . esc_html( $sl ) . '</span><div class="tkd2-opts">';
		$opts = array( 'none' => array( 'label' => 'ندارد', 'price' => 0 ) ) + $c['svc'];
		foreach ( $opts as $k => $o ) {
			echo '<label class="tkd2-opt"><input type="radio" name="tkd_svc[' . esc_attr( $sk ) . ']" value="' . esc_attr( $k ) . '" data-price="' . (int) $o['price'] . '"' . ( 'none' === $k ? ' checked' : '' ) . '>'
				. '<span class="tkd2-dot"></span><span class="tkd2-txt"><b>' . esc_html( $o['label'] ) . '</b>'
				. ( $o['price'] ? '<em>+' . esc_html( tkd_money( $o['price'] ) ) . ' تومان</em>' : '' ) . '</span></label>';
		}
		echo '</div></div>';
	}
	echo '<div class="tkd-up" hidden><label class="tkd-up-box"><input type="file" name="tkd_logo" accept=".jpg,.jpeg,.png,.pdf">'
		. '<span class="tkd-up-ic">' . tkd_icon( 'doc' ) . '</span><span class="tkd-up-t"><b>فایل لوگو را انتخاب کنید</b>'
		. '<small>JPG، PNG یا PDF، حداکثر ' . tkd_fa_digits( $c['max_mb'] ) . ' مگابایت</small></span></label></div>';
	echo '</div>';
}, 5 );

function tkd_posted_svc() {
	$c   = tkd_cfg();
	$sel = array();
	if ( ! empty( $_POST['tkd_svc'] ) && is_array( $_POST['tkd_svc'] ) ) {
		foreach ( wp_unslash( $_POST['tkd_svc'] ) as $s => $v ) {
			$s = sanitize_key( $s );
			$v = sanitize_key( $v );
			if ( isset( $c['sides'][ $s ], $c['svc'][ $v ] ) ) {
				$sel[ $s ] = $v;
			}
		}
	}
	return $sel;
}

function tkd_logo_mimes() {
	return array( 'jpg|jpeg' => 'image/jpeg', 'png' => 'image/png', 'pdf' => 'application/pdf' );
}

add_filter( 'woocommerce_add_to_cart_validation', function ( $ok ) {
	if ( ! tkd_posted_svc() ) {
		return $ok;
	}
	$f = isset( $_FILES['tkd_logo'] ) ? $_FILES['tkd_logo'] : null;
	if ( ! $f || empty( $f['name'] ) || UPLOAD_ERR_OK !== (int) $f['error'] ) {
		wc_add_notice( 'برای چاپ یا گلدوزی، فایل لوگو را بارگذاری کنید.', 'error' );
		return false;
	}
	if ( $f['size'] > tkd_cfg()['max_mb'] * MB_IN_BYTES ) {
		wc_add_notice( 'حجم فایل لوگو بیشتر از حد مجاز است.', 'error' );
		return false;
	}
	$ft = wp_check_filetype( $f['name'], tkd_logo_mimes() );
	if ( ! $ft['ext'] ) {
		wc_add_notice( 'فرمت فایل لوگو باید JPG، PNG یا PDF باشد.', 'error' );
		return false;
	}
	return $ok;
}, 10 );

add_filter( 'woocommerce_add_cart_item_data', function ( $data ) {
	$sel = tkd_posted_svc();
	if ( ! $sel ) {
		return $data;
	}
	$c     = tkd_cfg();
	$extra = 0;
	foreach ( $sel as $v ) {
		$extra += (int) $c['svc'][ $v ]['price'];
	}
	if ( ! empty( $_FILES['tkd_logo']['name'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		$up = wp_handle_upload( $_FILES['tkd_logo'], array( 'test_form' => false, 'mimes' => tkd_logo_mimes() ) );
		if ( ! empty( $up['url'] ) ) {
			$data['tkd_logo'] = $up['url'];
		}
	}
	$data['tkd_svc']   = $sel;
	$data['tkd_extra'] = $extra;
	$data['tkd_uid']   = md5( microtime() . wp_rand() );
	return $data;
} );

add_action( 'woocommerce_before_calculate_totals', function ( $cart ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}
	foreach ( $cart->get_cart() as $item ) {
		if ( empty( $item['tkd_extra'] ) ) {
			continue;
		}
		$fresh = wc_get_product( $item['data']->get_id() );
		if ( $fresh ) {
			$item['data']->set_price( (float) $fresh->get_price( 'edit' ) + (int) $item['tkd_extra'] );
		}
	}
}, 20 );

add_filter( 'woocommerce_get_item_data', function ( $d, $item ) {
	if ( empty( $item['tkd_svc'] ) ) {
		return $d;
	}
	$c = tkd_cfg();
	foreach ( $item['tkd_svc'] as $s => $v ) {
		if ( isset( $c['svc'][ $v ], $c['sides'][ $s ] ) ) {
			$d[] = array( 'key' => $c['svc'][ $v ]['label'] . ' ' . $c['sides'][ $s ], 'value' => '+' . tkd_money( $c['svc'][ $v ]['price'] ) . ' تومان' );
		}
	}
	if ( ! empty( $item['tkd_logo'] ) ) {
		$d[] = array( 'key' => 'فایل لوگو', 'value' => basename( $item['tkd_logo'] ) );
	}
	return $d;
}, 10, 2 );

add_action( 'woocommerce_checkout_create_order_line_item', function ( $oi, $key, $values ) {
	if ( empty( $values['tkd_svc'] ) ) {
		return;
	}
	$c = tkd_cfg();
	foreach ( $values['tkd_svc'] as $s => $v ) {
		if ( isset( $c['svc'][ $v ], $c['sides'][ $s ] ) ) {
			$oi->add_meta_data( $c['svc'][ $v ]['label'] . ' ' . $c['sides'][ $s ], '+' . tkd_money( $c['svc'][ $v ]['price'] ) . ' تومان' );
		}
	}
	if ( ! empty( $values['tkd_logo'] ) ) {
		$oi->add_meta_data( 'فایل لوگو', esc_url_raw( $values['tkd_logo'] ) );
	}
}, 10, 3 );

/* بعد از افزودن به سبد، پاپ‌آپ پیشنهاد نشان بده */
add_action( 'woocommerce_add_to_cart', function ( $key, $pid ) {
	if ( wp_doing_ajax() || ! WC()->session ) {
		return;
	}
	WC()->session->set( 'tkd_just_added', (int) $pid );
}, 10, 2 );

/* ---------- قیمت نهایی کنار تعداد ---------- */
add_action( 'woocommerce_before_add_to_cart_quantity', function () {
	global $product;
	if ( ! is_a( $product, 'WC_Product' ) ) {
		return;
	}
	if ( $product->is_type( 'variable' ) ) {
		$price = (float) $product->get_variation_price( 'min', true );
		$label = 'قیمت نهایی (از)';
	} else {
		$price = (float) wc_get_price_to_display( $product );
		$label = 'قیمت نهایی';
	}
	echo '<div class="tkd-qlabel">تعداد</div><div class="tkd-final"><small>' . esc_html( $label ) . '</small><b>'
		. esc_html( tkd_money( $price ) ) . '</b><i>تومان</i></div>';
}, 10 );


/**
 * داده‌های صفحه‌ی محصول جاری (همان متغیرهای شورت‌کد قدیمی).
 * نتیجه در یک متغیر static نگه داشته می‌شود تا هر بلوک دوباره حساب نکند.
 *
 * @return array خالی اگر محصولی پیدا نشود.
 */
function tkd_pdp_data() {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}
	$cache = array();

	global $product;
	if ( ! is_a( $product, 'WC_Product' ) ) {
		$product = wc_get_product( get_the_ID() );
	}
	if ( ! is_a( $product, 'WC_Product' ) ) {
		return '';
	}
	$c    = tkd_cfg();
	$id   = $product->get_id();
	$name = $product->get_name();

	// داده‌ی ساختاریافته‌ی محصول برای گوگل (قبلاً چاپ نمی‌شد)
	if ( isset( WC()->structured_data ) ) {
		WC()->structured_data->generate_product_data( $product );
	}

	$ids  = array_values( array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) ) );
	$imgs = array();
	foreach ( $ids as $img_id ) {
		$full  = wp_get_attachment_image_url( $img_id, 'large' );
		$thumb = wp_get_attachment_image_url( $img_id, 'thumbnail' );
		if ( $full ) {
			$alt    = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
			$imgs[] = array( 'full' => $full, 'thumb' => $thumb ? $thumb : $full, 'alt' => $alt ? $alt : $name );
		}
	}
	if ( ! $imgs ) {
		$ph     = wc_placeholder_img_src();
		$imgs[] = array( 'full' => $ph, 'thumb' => $ph, 'alt' => $name );
	}

	$is_var = $product->is_type( 'variable' );
	$price  = $is_var ? (float) $product->get_variation_price( 'min', true ) : (float) wc_get_price_to_display( $product );

	$cat   = null;
	$terms = get_the_terms( $id, 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $t ) {
			if ( 'uncategorized' !== $t->slug ) {
				$cat = $t;
				break;
			}
		}
	}

	/* تب‌ها */
	$items = array();
	$short = trim( $product->get_short_description() );
	if ( $short ) {
		$items['short'] = array( 'title' => 'معرفی کوتاه', 'icon' => 'list', 'html' => do_shortcode( wpautop( $short ) ) );
	}
	$guide      = tkd_size_guide_html( $id );
	$guide_done = false;
	$titles     = array( 'description' => 'توضیحات', 'additional_information' => 'مشخصات فنی' );
	$icons      = array( 'description' => 'doc', 'additional_information' => 'gear', 'reviews' => 'chat' );
	foreach ( (array) apply_filters( 'woocommerce_product_tabs', array() ) as $key => $tab ) {
		if ( empty( $tab['callback'] ) || ! is_callable( $tab['callback'] ) ) {
			continue;
		}
		if ( 'reviews' === $key && $guide && ! $guide_done ) {
			$items['size'] = array( 'title' => 'راهنمای سایز', 'icon' => 'ruler', 'html' => $guide );
			$guide_done    = true;
		}
		ob_start();
		call_user_func( $tab['callback'], $key, $tab );
		$html = ob_get_clean();
		if ( 'reviews' !== $key && ! trim( wp_strip_all_tags( $html ) ) ) {
			continue;
		}
		if ( 'reviews' === $key ) {
			$title = 'دیدگاه‌ها (' . tkd_fa_digits( (int) $product->get_review_count() ) . ')';
			$html  = tkd_reviews_summary( $product ) . $html;
		} else {
			$title = isset( $titles[ $key ] ) ? $titles[ $key ] : wp_strip_all_tags( $tab['title'] );
		}
		$items[ $key ] = array( 'title' => $title, 'icon' => isset( $icons[ $key ] ) ? $icons[ $key ] : 'doc', 'html' => $html );
	}
	if ( $guide && ! $guide_done ) {
		$items['size'] = array( 'title' => 'راهنمای سایز', 'icon' => 'ruler', 'html' => $guide );
	}

	/* محصولات مرتبط: دقیقاً ۴ تا */
	$related = array_filter( array_map( 'wc_get_product', wc_get_related_products( $id, 4 ) ) );

	/* پاپ‌آپ بعد از افزودن */
	$just = WC()->session ? (int) WC()->session->get( 'tkd_just_added' ) : 0;
	if ( $just ) {
		WC()->session->set( 'tkd_just_added', 0 );
	}
	$addons = $just ? array_filter( array_map( 'wc_get_product', tkd_addon_ids( $just ) ) ) : array();

	$trust_icons = array( 'shield', 'truck', 'needle' );

	$cache = get_defined_vars();
	unset( $cache['cache'] );

	return $cache;
}

/* JS صفحه محصول (قبلاً اسنیپت ۱۰) */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	$file = get_stylesheet_directory() . '/assets/js/tkd-pdp.js';
	wp_enqueue_script( 'wc-add-to-cart' );
	wp_enqueue_script(
		'tkd-pdp',
		get_stylesheet_directory_uri() . '/assets/js/tkd-pdp.js',
		array( 'jquery', 'wc-add-to-cart' ),
		file_exists( $file ) ? filemtime( $file ) : null,
		true
	);
}, 99 );

endif; // ! function_exists( 'tkd_cfg' )

/*
 * استایل صفحه محصول — بعد از Additional CSS (اولویت ۱۰۱) چاپ می‌شود، به همان ترتیب قبلی:
 * ۱) assets/css/tkd-pdp.css  : استایل اصلی صفحه محصول (از Additional CSS منتقل شد)
 * ۲) assets/css/tkd-pdp-mobile.css : اصلاحات موبایل
 */
add_action( 'wp_head', function () {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	foreach ( array( 'tkd-pdp', 'tkd-pdp-mobile' ) as $name ) {
		$file = get_stylesheet_directory() . '/assets/css/' . $name . '.css';
		if ( ! file_exists( $file ) ) {
			continue;
		}
		printf(
			'<link rel="stylesheet" id="%s-css" href="%s" media="all">' . "\n",
			esc_attr( $name ),
			esc_url( get_stylesheet_directory_uri() . '/assets/css/' . $name . '.css?ver=' . filemtime( $file ) )
		);
	}
}, 102 );
