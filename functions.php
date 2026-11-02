<?php

$theme_dir 			= get_stylesheet_directory_uri();

/* ---------- Plan C: ACF Blocks homepage (see inc/blocks.php) ---------- */
require_once get_stylesheet_directory() . '/inc/blocks.php';

/* ---------- صفحه محصول: بلوک‌های گوتنبرگ (جایگزین اسنیپت‌های ۱، ۲، ۳، نمایش) ---------- */
require_once get_stylesheet_directory() . '/inc/product-blocks.php';
require_once get_stylesheet_directory() . '/inc/product-page.php';
require_once get_stylesheet_directory() . '/inc/apron-landing.php';


//-------------- RB Custom Code ----------------------
function termeh_theme_setup() {
	register_nav_menus(array(
		'with_takdooz_footer_menu' => esc_html__('منوی همراه با تکدوز - فوتر', 'takdooz-theme'),
		'customers_footer_menu' => esc_html__('منوی خدمات مشتریان - فوتر', 'takdooz-theme'),
		'shopping_help_footer_menu' => esc_html__('منوی راهنمای خرید - فوتر', 'takdooz-theme'),
	));
}
add_action('after_setup_theme', 'termeh_theme_setup');

/* ------------ چک کردن محصول در سبد خرید --------------------- */
function rb_is_in_cart( $search_products ) {
	$count = 0;
	// WC()->cart is null in admin / REST contexts (e.g. when the block editor
	// server-renders the ACF product blocks), so guard before using it.
	$cart = function_exists( 'WC' ) ? WC()->cart : null;
	if ( $cart && ! $cart->is_empty() ) {
		foreach($cart->get_cart() as $cart_item ) {
			$cart_item_ids = array($cart_item['product_id'], $cart_item['variation_id']);
			if( ( is_array($search_products) && array_intersect($search_products, $cart_item_ids) ) 
			   || ( !is_array($search_products) && in_array($search_products, $cart_item_ids) ) )
				$count++;
		}
	}
	return $count;
}

/**
 * Load WooCommerce's native AJAX cart scripts on the block homepage.
 */
function takdooz_enqueue_home_product_cart_scripts() {
	if ( is_front_page() ) {
		wp_enqueue_script( 'wc-add-to-cart' );
		wp_enqueue_script( 'wc-cart-fragments' );
		wp_enqueue_script( 'wc-add-to-cart-variation' );
	}
}
add_action( 'wp_enqueue_scripts', 'takdooz_enqueue_home_product_cart_scripts', 30 );

/**
 * Keep both custom header cart badges in sync after an AJAX add-to-cart.
 *
 * @param array $fragments WooCommerce cart fragments.
 * @return array
 */
function takdooz_header_cart_count_fragment( $fragments ) {
	$cart  = function_exists( 'WC' ) ? WC()->cart : null;
	$count = $cart ? $cart->get_cart_contents_count() : 0;

	ob_start();
	?>
	<span class="cart_icon_badge"><?php echo esc_html( $count ); ?></span>
	<?php
	$fragments['span.cart_icon_badge'] = ob_get_clean();

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'takdooz_header_cart_count_fragment' );

/**
 * Return WooCommerce's variation form for the shared homepage quick-cart modal.
 */
function takdooz_load_quick_cart_options() {
	check_ajax_referer( 'takdooz_quick_cart', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$product    = $product_id ? wc_get_product( $product_id ) : false;

	if ( ! $product || ! $product->is_type( 'variable' ) || 'publish' !== get_post_status( $product_id ) ) {
		wp_send_json_error( array( 'message' => 'محصول یا گزینه‌های آن در دسترس نیست.' ), 404 );
	}

	$previous_product   = isset( $GLOBALS['product'] ) ? $GLOBALS['product'] : null;
	$GLOBALS['product'] = $product;

	// Product-page-only size-guide buttons have separate modals and should not
	// be injected into this compact quick-cart dialog.
	remove_action( 'woocommerce_before_add_to_cart_form', 'takdooz_size_button' );
	remove_action( 'woocommerce_before_add_to_cart_form', 'takdooz_kids_size_button' );

	ob_start();
	woocommerce_variable_add_to_cart();
	$form_html = ob_get_clean();

	add_action( 'woocommerce_before_add_to_cart_form', 'takdooz_size_button' );
	add_action( 'woocommerce_before_add_to_cart_form', 'takdooz_kids_size_button' );
	$GLOBALS['product'] = $previous_product;

	wp_send_json_success(
		array(
			'title'           => $product->get_name(),
			'html'            => $form_html,
			'size_guide_html' => takdooz_get_quick_cart_size_guide( $product_id ),
		)
	);
}
add_action( 'wp_ajax_takdooz_quick_cart', 'takdooz_load_quick_cart_options' );
add_action( 'wp_ajax_nopriv_takdooz_quick_cart', 'takdooz_load_quick_cart_options' );

/**
 * Return the relevant compact size guide for quick cart.
 */
function takdooz_get_quick_cart_size_guide( $product_id ) {
	$adult_categories = array( 'chef-women-clothes', 'chef-men-clothes' );
	$is_adult         = has_term( $adult_categories, 'product_cat', $product_id );
	$is_kids          = has_term( 'chef-kids-clothes', 'product_cat', $product_id );

	if ( ! $is_adult && ! $is_kids ) {
		return '';
	}

	ob_start();
	?>
	<div class="rb_quick_size_guide" hidden>
		<button type="button" class="rb_quick_size_back" data-rb-size-guide-back>
			<span aria-hidden="true">→</span> بازگشت به انتخاب محصول
		</button>
		<?php if ( $is_adult ) : ?>
			<div class="rb_quick_size_layout">
				<div class="rb_quick_size_image">
					<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/05/Screenshot-1405-02-16-at-18.34.42.png' ) ); ?>" alt="روش اندازه‌گیری عرض و قد روپوش">
				</div>
				<div class="rb_quick_size_table">
					<table>
						<thead><tr><th>سایز</th><th>عرض</th><th>قد</th></tr></thead>
						<tbody>
							<tr><td>S</td><td>۴۲</td><td>۵۴</td></tr>
							<tr><td>M</td><td>۴۵</td><td>۶۰</td></tr>
							<tr><td>L</td><td>۴۹</td><td>۶۳</td></tr>
							<tr><td>XL</td><td>۵۴</td><td>۶۹</td></tr>
							<tr><td>XXL</td><td>۵۸</td><td>۷۲</td></tr>
						</tbody>
					</table>
				</div>
			</div>
		<?php else : ?>
			<div class="rb_quick_size_table is-kids">
				<table>
					<thead><tr><th>سایز</th><th>دور سر کلاه</th><th>قد پیشبند</th><th>قد جلیقه</th></tr></thead>
					<tbody>
						<tr><td>۶ ماه تا ۱.۵ سال</td><td>۴۶ تا ۵۱</td><td>۳۸</td><td>—</td></tr>
						<tr><td>۲ تا ۴ سال</td><td>۵۰ تا ۵۴</td><td>۴۲</td><td>۳۸</td></tr>
						<tr><td>۵ تا ۸ سال</td><td>۵۳ تا ۵۷</td><td>۵۰</td><td>۴۸</td></tr>
						<tr><td>۹ تا ۱۲ سال</td><td>۵۶ تا ۵۹</td><td>۵۸</td><td>۵۵</td></tr>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Add a selected variation and return standard WooCommerce cart fragments.
 */
function takdooz_add_quick_cart_variation() {
	check_ajax_referer( 'takdooz_quick_cart', 'nonce' );

	$product_id  = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$items        = isset( $_POST['items'] ) && is_array( $_POST['items'] ) ? wp_unslash( $_POST['items'] ) : array();
	$variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
	$quantity     = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 1;
	$product      = $product_id ? wc_get_product( $product_id ) : false;

	if ( ! $product || ! $product->is_type( 'variable' ) ) {
		wp_send_json_error( array( 'message' => 'گزینه انتخاب‌شده معتبر نیست.' ), 400 );
	}

	if ( empty( $items ) ) {
		$items[] = array(
			'variation_id' => $variation_id,
			'quantity'     => $quantity,
			'attributes'   => isset( $_POST['attributes'] ) && is_array( $_POST['attributes'] ) ? wp_unslash( $_POST['attributes'] ) : array(),
		);
	}

	$validated_items = array();
	foreach ( $items as $item ) {
		$item_variation_id = isset( $item['variation_id'] ) ? absint( $item['variation_id'] ) : 0;
		$item_quantity     = isset( $item['quantity'] ) ? wc_stock_amount( $item['quantity'] ) : 0;
		$variation         = $item_variation_id ? wc_get_product( $item_variation_id ) : false;
		$item_attributes   = isset( $item['attributes'] ) && is_array( $item['attributes'] ) ? $item['attributes'] : array();

		if (
			$item_quantity < 1 ||
			! $variation ||
			! $variation->is_type( 'variation' ) ||
			$variation->get_parent_id() !== $product_id ||
			! $variation->is_purchasable() ||
			! $variation->is_in_stock() ||
			( $variation->managing_stock() && $item_quantity > $variation->get_stock_quantity() )
		) {
			wp_send_json_error( array( 'message' => 'یکی از گزینه‌ها یا تعداد انتخاب‌شده در دسترس نیست.' ), 400 );
		}

		$variation_attributes = $variation->get_variation_attributes();
		$product_attributes   = $product->get_variation_attributes();
		foreach ( $item_attributes as $attribute_name => $attribute_value ) {
			$attribute_name  = sanitize_key( $attribute_name );
			$attribute_value = wc_clean( $attribute_value );
			$product_key     = 0 === strpos( $attribute_name, 'attribute_' ) ? substr( $attribute_name, 10 ) : '';
			$variation_value = isset( $variation_attributes[ $attribute_name ] ) ? $variation_attributes[ $attribute_name ] : null;

			if (
				! $product_key ||
				! isset( $product_attributes[ $product_key ] ) ||
				! in_array( $attribute_value, $product_attributes[ $product_key ], true ) ||
				( $variation_value && $variation_value !== $attribute_value )
			) {
				wp_send_json_error( array( 'message' => 'یکی از گزینه‌های انتخاب‌شده معتبر نیست.' ), 400 );
			}

			$variation_attributes[ $attribute_name ] = $attribute_value;
		}

		$validated_items[] = array(
			'variation_id' => $item_variation_id,
			'quantity'     => $item_quantity,
			'attributes'   => $variation_attributes,
		);
	}

	foreach ( $validated_items as $item ) {
		$passed_validation = apply_filters(
			'woocommerce_add_to_cart_validation',
			true,
			$product_id,
			$item['quantity'],
			$item['variation_id'],
			$item['attributes']
		);

		if ( ! $passed_validation || false === WC()->cart->add_to_cart( $product_id, $item['quantity'], $item['variation_id'], $item['attributes'] ) ) {
			$message = wc_get_notices( 'error' );
			wc_clear_notices();
			wp_send_json_error(
				array( 'message' => ! empty( $message[0]['notice'] ) ? wp_strip_all_tags( $message[0]['notice'] ) : 'افزودن محصول ناموفق بود.' ),
				400
			);
		}
	}

	do_action( 'woocommerce_ajax_added_to_cart', $product_id );
	WC_AJAX::get_refreshed_fragments();
}
add_action( 'wp_ajax_takdooz_add_quick_cart_variation', 'takdooz_add_quick_cart_variation' );
add_action( 'wp_ajax_nopriv_takdooz_add_quick_cart_variation', 'takdooz_add_quick_cart_variation' );

/********** گرفتن درصد تخفیف محصول ****************/
function get_product_percent( $product ){
	if( !$product )
		global $product;
	$product_sale_percent = 0;
	if( $product->is_on_sale() ) {
		if( $product->is_type('variable') ) {
			$product_vari_percents = array();
			$available_variations 	= $product->get_available_variations();
			foreach( $available_variations as $available_variation ){
				$product_vari_id = $available_variation['variation_id'];
				$product_vari = wc_get_product( $product_vari_id );
				if( $product_vari->is_on_sale() ) {
					$product_vari_regular_price =  $product_vari->get_regular_price();
					$product_vari_sale_price = $product_vari->get_sale_price();
					$product_vari_percents[] = ($product_vari_regular_price - $product_vari_sale_price) / $product_vari_regular_price * 100;
				}
			}
			$product_sale_percent = round( max( $product_vari_percents ) );
		}else{
			$product_regular_price =  $product->get_regular_price();
			$product_sale_price = $product->get_sale_price();
			$product_sale_percent = round( ($product_regular_price - $product_sale_price) / $product_regular_price * 100 );
		}
		return $product_sale_percent;
	}
}


//--------------  ایجکس نتایج جستجو -----------------------
add_action('wp_ajax_rb_header_search', 'rb_header_search_function');
add_action('wp_ajax_nopriv_rb_header_search', 'rb_header_search_function');
function rb_header_search_function(){
	$search_key 			= $_POST['search_key']; 
	$user_id 				= get_current_user_id();
	$user_search_history 	= get_user_meta( $user_id , 'search_history' , true ) ? get_user_meta( $user_id , 'search_history' , true ) : array();
	$product_arg = array(
		'post_type' 		=> 'product',
		'post_status' 		=> 'publish',
		'posts_per_page' 	=> -1,
		's' 				=> $search_key,
	);

	$product_query = new WP_Query( $product_arg );

	if( $product_query->found_posts > 0  and !in_array( $search_key , $user_search_history ) ){
		$user_search_history[] = $search_key;
		update_user_meta( $user_id , 'search_history' , $user_search_history );
	} 
	$user_search_history 	= get_user_meta( $user_id , 'search_history' , true ) ? get_user_meta( $user_id , 'search_history' , true ) : array();
	if( !empty( $user_search_history ) ){
		$user_search_history = array_reverse( $user_search_history );
	} ?>
<div class="header_search_box">
	<div id="header_search_box_close_btn">
		<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>
	</div>
	<?php if( $product_query->have_posts() ) { ?>
	<div class="header_search_title mt-3">
		<h4>محصولات کلمه جستجو شده</h4>
	</div>
	<div class="header_last_searched">
		<div class="row" style="overflow-x: scroll;flex-wrap: nowrap;padding: 10px 0;gap: 5px;">
			<?php $count = 1; while( $product_query->have_posts() ){
		if( $count == 6 ) break;
		$product_query->the_post();
		$product_id = get_the_id(); 
		$product = wc_get_product( $product_id );
		if( $product instanceof WC_Product ){
			$product_link 		= get_permalink( $product_id );
			$product_name 		= $product->get_name();
			$product_price		= $product->get_price_html();
			$product_image 		= get_the_post_thumbnail_url($product_id);?> 
			<div class="rb_flex_start_wrap">
				<a href="<?php echo $product_link; ?>">
					<div class="searched_item">
						<img src="<?php echo $product_image; ?>" width="60">
						<div>
							<span ><?php echo $product_name; ?></span>
							<span ><?php echo $product_price; ?></span>
						</div>
					</div>
				</a>	
			</div>
			<?php } $count++; 
	} wp_reset_query(); ?>
		</div>
	</div>
	<?php }else{ ?>
	<div class="header_search_title mt-3">
		<h4>محصولی برای ""<?php echo $search_key; ?>"" یافت نشد</h4>
	</div>
	<?php } ?>
	<?php if( $user_search_history ){ ?>
	<div class="header_search_title">
		<span><h4>جستجو های اخیر</h4></span>
		<span class="clear_history"><p><svg xmlns="http://www.w3.org/2000/svg" width="14px" height="14px" viewBox="0 0 51 53" version="1.1"><path fill="red" opacity="1.00" d=" M 14.09 10.24 C 13.89 6.78 13.94 2.01 18.03 0.83 C 22.96 0.37 28.00 0.39 32.94 0.81 C 37.05 2.00 37.14 6.76 36.91 10.24 C 41.48 10.25 46.04 10.26 50.61 10.24 C 50.61 11.89 50.61 13.53 50.63 15.18 C 49.65 15.18 47.68 15.19 46.70 15.19 C 45.66 25.76 44.69 36.34 43.64 46.91 C 43.58 49.88 40.97 52.56 37.94 52.41 C 29.63 52.46 21.33 52.46 13.02 52.41 C 10.04 52.56 7.42 49.95 7.34 47.01 C 6.30 36.40 5.34 25.80 4.32 15.19 C 3.33 15.19 1.36 15.18 0.37 15.18 C 0.39 13.53 0.39 11.89 0.39 10.24 C 4.95 10.25 9.52 10.25 14.09 10.24 M 19.29 5.44 C 19.12 7.04 19.04 8.64 19.02 10.25 C 23.34 10.25 27.66 10.25 31.98 10.24 C 31.92 8.68 31.87 7.13 31.82 5.56 C 27.65 5.38 23.47 5.45 19.29 5.44 M 9.28 15.20 C 10.42 25.96 11.15 36.76 12.50 47.50 C 21.18 47.44 29.87 47.61 38.55 47.48 C 39.83 36.74 40.65 25.95 41.71 15.18 C 30.90 15.20 20.09 15.17 9.28 15.20 Z"/><path fill="red" opacity="1.00" d=" M 17.37 41.12 C 17.38 34.62 17.37 28.12 17.38 21.62 C 19.02 21.63 20.67 21.63 22.31 21.63 C 22.31 28.13 22.32 34.63 22.31 41.13 C 20.66 41.11 19.02 41.11 17.37 41.12 Z"/><path fill="red" opacity="1.00" d=" M 28.69 21.63 C 30.33 21.63 31.98 21.63 33.62 21.62 C 33.63 28.12 33.62 34.62 33.63 41.12 C 31.98 41.11 30.34 41.11 28.69 41.13 C 28.68 34.63 28.69 28.13 28.69 21.63 Z"/></svg></p></span>
	</div>
	<div class="search_box_cat">
		<?php $count = 1; foreach( $user_search_history as $history ) { 
		if( $count == 6 ) break;
		if( $history == '' ) continue; ?> 
		<a href="/shop?s=<?php echo $history; ?>"><span class="cat_item"><?php echo $history; ?></span></a>
		<?php $count++; } ?>
	</div>
	<?php } ?>
</div>

<?php
	wp_die();
}

//----------- Load product Category in ACF ------------------
function acf_load_product_cat_names( $field ) {
	$field['choices'] = array();
	$field['choices'][0] = 'انتخاب دسته محصول';

	$terms = get_terms( 'product_cat' );
	if( is_array($terms) ) {
		foreach( $terms as $terms ) {
			$field['choices'][ $terms->term_id ] = $terms->name;
		}
	}
	return $field;
}
add_filter('acf/load_field/name=product_cat_in_home', 'acf_load_product_cat_names');
add_filter('acf/load_field/name=campain_category', 'acf_load_product_cat_names');


add_filter( 'plupload_init', 'custom_file_input_text' );



// Add custom Theme Functions here
function wpdocs_override_stylesheets()
{
	wp_enqueue_style('theme-override', get_template_directory_uri() . '-child/css/rtl-font.css', array(), '0.1.0', 'all');
}
add_action('wp_enqueue_scripts', 'wpdocs_override_stylesheets', PHP_INT_MAX);

remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
add_action( 'woocommerce_after_shop_loop', 'woocommerce_taxonomy_archive_description', 100 );

// Pages Titles
function tmt_wp_admin_title($admin_title, $title) {
	return $title . ' - ' . get_bloginfo('name');
}
add_filter('admin_title', 'tmt_wp_admin_title', 10, 2);
add_filter('login_title', 'tmt_wp_admin_title', 10, 2);


/**
*      Rename popularity Sorting Option @ WooCommerce Shop
*/
add_filter( 'woocommerce_catalog_orderby', 'HM_rename_sorting_option_woocommerce_shop' );
function HM_rename_sorting_option_woocommerce_shop( $options ) {
	$options['popularity'] = 'مرتب سازی بر اساس پیشنهاد تکدوز';
	return $options;
}
function trim_number($num)
{
	$eng = array('0','1','2','3','4','5','6','7','8','9');
	$per = array('۰','۱','۲','۳','۴','۵','۶','۷','۸','۹');
	return str_replace($eng,$per,$num);
}
//add noindex to some pages
function no_index_query_string_pages() {
	if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
		echo '<meta name="robots" content="noindex, follow">';
	}
}
add_action('wp_head', 'no_index_query_string_pages');





// چاپ برگه پستی
add_shortcode('takdooz_orders_print', function() {
    $args = array(
        'status' => 'processing',
        'limit' => -1,
    );
    $orders = wc_get_orders($args);

    $logo_url = 'https://takdooz.ir/wp-content/uploads/2023/07/takdooz.png';
    $sender_address = "تهران، شهرک غرب، ایوانک غربی، گل افشان شمالی، خیابان یکم، مجتمع ایرانیان، پلاک 1، واحد 3 همراه: 09128052860 جمالی";

    $html = '<div id="orders-print" class="orders-print" style="direction: rtl;">';

    // نقشه استان‌ها به فارسی
    $states = array(
        'ADL' => 'اردبیل',
        'THR' => 'تهران',
        'KRN' => 'کرمان',
        'ESF' => 'اصفهان',
        'FAR' => 'فارس',
        'KHZ' => 'خوزستان',
        'GIL' => 'گیلان',
        'KBL' => 'کرمانشاه',
        'YZD' => 'یزد',
        'MBR' => 'مازندران',
        'HRM' => 'هرمزگان',
        'MKZ' => 'مرکزی',
        'ZJN' => 'زنجان',
        'QHR' => 'قزوین',
        'SBR' => 'سیستان و بلوچستان',
        'GLS' => 'گلستان',
        'QMK' => 'قم',
        'KHY' => 'کهگیلویه و بویراحمد',
        'ALB' => 'البرز',
        'KRG' => 'کردستان',
        'KRM' => 'کرمان',
		'KBD' => 'یاسوج',
		'MZN' => 'مازندران',
		'WAZ' => 'ارومیه',
		'ABZ' =>'محمد شهر',
		'HRZ' => 'هرمزگان',
		'KRH' => 'کرمانشاه',
		'QHM' => 'قم',
		'EAZ' => 'تبریز',
		'SBN' => 'جیرفت',
		'SBN' => 'زاهدان',
		'SKH' => 'بیرجند',
		'FRS' => 'زرین دشت',
		'KRD' => 'سنندج',
		'RKH' => 'مشهد',
		'CHB' => 'سامان',
		'GZN' => 'قزوین',
		'HDN' => 'همدان',
		'BHR' => 'بوشهر',
		'LRS' => 'لرستان',
		'SMN' => 'سمنان',
		'ILM' => 'ایلام'
        // اضافه کردن سایر استان‌ها به همین شکل
    );

    foreach ($orders as $order) {
        $billing_first_name = $order->get_billing_first_name();
        $billing_last_name = $order->get_billing_last_name();
        $billing_phone = $order->get_billing_phone();
        $billing_postcode = $order->get_billing_postcode();
        $billing_address = $order->get_billing_address_1();
        $billing_city = $order->get_billing_city(); // شهر
        $billing_state = $order->get_billing_state(); // استان
        $order_items = $order->get_items();
		$order_id = $order->get_id();

        $billing_phone = preg_replace('/\+98/', '', $billing_phone);

        $html .= '<div class="order-box">';
        
        // لوگو و آدرس فرستنده
        $html .= '<div class="top-header">';
        $html .= '<div class="sender-box">' . $sender_address . '</div>';
        $html .= '<div class="logo-container"><img src="' . $logo_url . '" alt=""></div>';
        $html .= '</div>';
        $html .= '<div class="order-id" style="font-size: 10px; text-align: left; margin-top: 2px;">#' . $order_id . '</div>';

		// اطلاعات گیرنده
$html .= '<div class="receiver-info">';

// ساخت متن گیرنده: اول استان و شهر (بر اساس شرط)، بعد آدرس
$receiver_address = '';

// اگر هم شهر و هم استان موجود باشن
if (!empty($billing_city) && !empty($billing_state)) {
    $state_name = isset($states[$billing_state]) ? $states[$billing_state] : $billing_state;
    if ($billing_city === $state_name) {
        // اگر شهر و استان یکی بودن، فقط استان بیاد
        $receiver_address .= $state_name;
    } else {
        // اگر شهر و استان فرق داشتن، اول استان بعد شهر بیاد
        $receiver_address .= $state_name . '، ' . $billing_city;
    }
}
// اگر فقط شهر موجود بود
elseif (!empty($billing_city) && empty($billing_state)) {
    $receiver_address .= $billing_city;
}
// اگر فقط استان موجود بود
elseif (empty($billing_city) && !empty($billing_state)) {
    $state_name = isset($states[$billing_state]) ? $states[$billing_state] : $billing_state;
    $receiver_address .= $state_name;
}

// اضافه کردن آدرس خیابان
if (!empty($billing_address)) {
    if (!empty($receiver_address)) {
        $receiver_address .= '، ' . $billing_address;
    } else {
        $receiver_address .= $billing_address;
    }
}

// چاپ اطلاعات گیرنده
$html .= '<p class="big-print"><strong>گیرنده:</strong> ' . $receiver_address . '</p>';



// بقیه اطلاعات
$html .= '<p class="big-print"><strong>نام کامل:</strong> ' . $billing_first_name . ' ' . $billing_last_name . '</p>';
$html .= '<p class="big-print"><strong>کدپستی:</strong> ' . $billing_postcode . '</p>';
$html .= '<p class="big-print"><strong>تلفن:</strong> ' . $billing_phone . '</p>';

$html .= '</div>';


        // محصولات
        $html .= '<div class="product-box">';
        foreach ($order_items as $item) {
            $product_name = $item->get_name();
            $product_quantity = $item->get_quantity();
            $html .= '<span class="product-item">' . $product_name . ' × ' . $product_quantity . '</span>';
        }
        $html .= '</div>';

        // چک‌باکس انتخاب
        $html .= '<div class="order-select-box"><label><input type="checkbox" class="order-select"> انتخاب سفارش</label></div>';

        $html .= '</div>'; // order-box
    }

    $html .= '</div>'; // orders-print

    $html .= '<button id="print-selected" class="print-btn">چاپ سفارشات انتخاب شده</button>';

    // استایل کامل
$html .= '
<style>
@font-face {
    font-family: "IRANSans";
    src: url("https://your-site.com/path-to-font/IRANSans.woff2") format("woff2"),
         url("https://your-site.com/path-to-font/IRANSans.woff") format("woff");
    font-weight: normal;
    font-style: normal;
}
.orders-print {
    display: flex;
    flex-wrap: wrap; 
    justify-content: space-between;
    gap: 10px;
    direction: rtl;
    font-family: "IRANSans", Tahoma, sans-serif;
    
	justify-content: center;
}
.order-box {
    width: 30%;
    min-width: 300px;
    height: auto;
    border: 1px solid #333;
    padding: 8px;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    box-sizing: border-box;
    overflow: hidden;
    transition: all 0.3s ease;
}
.order-box:hover {
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    border-color: #555;
}
.top-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}
.sender-box {
    font-size: 11px;
    font-weight: bold;
    line-height: 1.4;
    background: #f9f9f9;
    padding: 5px 8px;
    border: 1px solid #aaa;
    border-radius: 5px;
    flex: 1;
    margin-left: 8px;
    white-space: normal;
}
.logo-container {
    width: 50px;
}
.logo-container img {
    width: 100%;
    height: auto;
}
.receiver-info {
    margin: 5px 0;
    font-size: 13px;
}
.receiver-info p {
    margin: 2px 0;
}
.product-box {
    border: 1px dashed #aaa;
    border-radius: 5px;
    padding: 5px;
    margin-top: auto;
    font-size: 8px;
    text-align: right;
}
.product-item {
    margin-left: 5px;
    display: inline-block;
}
.print-btn {
    margin-top: 20px;
    padding: 10px 20px;
    font-size: 16px;
    background: #333;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: block;
    margin: 20px auto;
}
.order-select-box {
    margin-top: 5px;
    font-size: 12px;
}



@media print {
    @page {
        size: A4 portrait;
        margin: 1cm;
    }

    header, footer, .header, .footer, #header, #footer, #print-selected,
    .arcontactus-message-button {
        display: none !important;
    }

    body {
        margin: 0;
        padding: 0;
        direction: rtl;
    }

    .orders-print {
        display: grid;
        grid-template-columns: 1fr; /* دو ستون */
        gap: 0.5cm; /* فاصله بین کارت‌ها */
        width: 150%;
        box-sizing: border-box;
    }

    .order-box {
        width: 17.0cm;
        min-height: 4.2cm; /* ارتفاع مناسب */
        border: 1px solid #333;
//         padding: 2px;
        border-radius: 6px;
//         overflow: hidden;
//         page-break-inside: avoid;
        font-size: 10px;
//      box-sizing: border-box;
         display: flex;
		 flex-direction: column;
         justify-content: center;
    }

    .order-select-box {
        display: none !important;
    }
}

/* مخصوص A5 (مثل قبل) */
@media print and (min-width: 138mm) and (max-width: 148mm) {
    .orders-print {
        grid-template-columns: 1fr;
        grid-gap: 0.5cm;
    }
    .order-box {
        width: 20.0cm;
        min-height: 5.2cm;
        font-size: 10px;
        padding: 3px;
        overflow: visible;
    }
}
.big-print {
    font-size: 14px !important;
    font-weight: bold;
}



</style>';

    // جاوااسکریپت انتخاب و چاپ
    $html .= '
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("print-selected").addEventListener("click", function() {
            let checkboxes = document.querySelectorAll(".order-select");
            checkboxes.forEach(function(checkbox) {
                if (!checkbox.checked) {
                    checkbox.closest(".order-box").style.display = "none";
                }
            });
            window.print();
            setTimeout(function() {
                location.reload();
            }, 1000);
        });
    });
    </script>';

    return $html;
});

// حذف (...) پیش‌فرض
remove_filter('excerpt_more', 'flatsome_excerpt_more');

// فیلتر جدید برای افزودن دکمه ادامه مطلب
function custom_excerpt_more($excerpt) {
    global $post;

    if (is_home() || is_archive() || is_category() || is_tag()) {
        $read_more = '<div><a class="read-more-button" href="' . get_permalink($post->ID) . '">ادامه مطلب> </a></div>';
        return $excerpt . $read_more;
    }

    return $excerpt;
}
add_filter('get_the_excerpt', 'custom_excerpt_more');
// فیلتر جدید برای افزودن دکمه ادامه مطلب


// 1. حذف سه نقطه پیش‌فرض excerpt و اضافه کردن دکمه ادامه مطلب
remove_filter('excerpt_more', 'flatsome_excerpt_more'); //
if (!function_exists('custom_excerpt_more')) {
    function custom_excerpt_more($excerpt) {
        global $post;
        if (is_home() || is_archive() || is_category() || is_tag()) {
            $read_more = '<div><a class="read-more-button" href="' . get_permalink($post->ID) . '">ادامه مطلب &gt;</a></div>';
            return $excerpt . $read_more;
        }
        return $excerpt;
    }
    add_filter('get_the_excerpt', 'custom_excerpt_more');
}

// 2. فیلتر مرتب‌سازی و دسته بندی محصولات 
add_action('woocommerce_before_main_content', 'takdooz_custom_sort_and_category_ui', 5);
function takdooz_custom_sort_and_category_ui() {
    if (!is_shop() && !is_product_category() && !is_product_tag()) return;

    $current_orderby = $_GET['orderby'] ?? '';
    $base_url = strtok($_SERVER["REQUEST_URI"], '?');
    $query_args = $_GET;
    unset($query_args['orderby']);

    // گرفتن دسته‌بندی‌ها
    $categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
    ]);

    // ساخت URL با پارامترها
    function build_url($base, $params = []) {
        if (empty($params)) return $base;
        return $base . '?' . http_build_query($params);
    }
    ?>

    <div class="takdooz-filter-bar">
        <!-- دکمه مرتب‌سازی -->
        <div class="takdooz-sort-wrapper">
            <?php
            $label = 'مرتب‌سازی';
            switch ($current_orderby) {
                case 'popularity':
                    $label = 'پرفروش‌ترین';
                    break;
                case 'date':
                    $label = 'جدیدترین';
                    break;
                default:
                    $label = 'مرتب‌سازی';
            }
            ?>
            <button id="takdooz-sort-toggle" aria-expanded="false" aria-haspopup="true" aria-controls="takdooz-sort-menu">
                <?php echo esc_html($label); ?> &#9662;
            </button>
            <div id="takdooz-sort-menu" class="takdooz-dropdown" hidden>
                <a href="<?php echo esc_url(build_url($base_url, $query_args)); ?>" class="<?php echo ($current_orderby === '') ? 'active' : ''; ?>">پیش‌فرض</a>
                <a href="<?php echo esc_url(build_url($base_url, array_merge($query_args, ['orderby' => 'popularity']))); ?>" class="<?php echo ($current_orderby === 'popularity') ? 'active' : ''; ?>">پرفروش‌ترین</a>
                <a href="<?php echo esc_url(build_url($base_url, array_merge($query_args, ['orderby' => 'date']))); ?>" class="<?php echo ($current_orderby === 'date') ? 'active' : ''; ?>">جدیدترین</a>
            </div>
        </div>

        <!-- دکمه دسته‌بندی -->
        <div class="takdooz-cat-wrapper">
            <button id="takdooz-cat-toggle" aria-expanded="false" aria-haspopup="true" aria-controls="takdooz-cat-menu">
                دسته های محصولات &#9662;
            </button>
            <div id="takdooz-cat-menu" class="takdooz-dropdown" hidden>
                <ul>
                    <?php foreach ($categories as $cat): ?>
                        <li><a href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

     <style>
         .takdooz-filter-bar {
             display: flex;
             gap: 12px;
             align-items: center;
             flex-wrap: wrap;
            margin: 20px 0;
             justify-content: flex-start;
             font-family: Tahoma, sans-serif;
             user-select: none;
             direction: rtl;
         }
         .takdooz-sort-wrapper,
         .takdooz-cat-wrapper {
             position: relative;
         }
         button#takdooz-sort-toggle,
         button#takdooz-cat-toggle {
             background-color: #0073aa;
             color: white;
             border: none;
             border-radius: 4px;
             padding: 8px 14px;
             font-size: 14px;
             cursor: pointer;
             user-select: none;
             transition: background-color 0.3s ease;
         }
         button:hover,
         button[aria-expanded="true"] {
             background-color: #005177;
         }
         .takdooz-dropdown {
             position: absolute;
             top: 110%;
             right: 0;
             background: #fff;
             border: 1px solid #ddd;
             box-shadow: 0 4px 12px rgba(0,0,0,0.1);
             border-radius: 6px;
             padding: 8px 0;
             min-width: 180px;
             z-index: 9999;
             max-height: 280px;
             overflow-y: auto;
         }
         .takdooz-dropdown a {
             display: block;
             padding: 8px 15px;
             text-decoration: none;
             color: #333;
             font-size: 14px;
             transition: background-color 0.2s ease;
         }
         .takdooz-dropdown a:hover,
         .takdooz-dropdown a.active {
             background-color: #f0f0f0;
             font-weight: bold;
         }
         .takdooz-dropdown ul {
             list-style: none;
             margin: 0;
             padding: 0;
         }
         .takdooz-dropdown ul li {
             border-bottom: 1px solid #eee;
         }
         .takdooz-dropdown ul li:last-child {
             border-bottom: none;
         }
         .takdooz-dropdown ul li a {
             padding: 8px 15px;
             display: block;
         }

         /* موبایل */
         @media (max-width: 600px) {
             .takdooz-filter-bar {
                 flex-direction: row;
                 justify-content: flex-start;
                 gap: 10px;
             }
             button#takdooz-sort-toggle,
             button#takdooz-cat-toggle {
                 padding: 7px 12px;
                 font-size: 13px;
             }
             .takdooz-dropdown {
                 min-width: 140px;
                 max-height: 200px;
             }
         }
     </style>

     <script>
         document.addEventListener('DOMContentLoaded', function() {
             const sortToggle = document.getElementById('takdooz-sort-toggle');
             const sortMenu = document.getElementById('takdooz-sort-menu');
             const catToggle = document.getElementById('takdooz-cat-toggle');
             const catMenu = document.getElementById('takdooz-cat-menu');
             function closeAllMenus(except = null) {

                     sortMenu.hidden = true;
                 if (except !== sortMenu) {
                     sortToggle.setAttribute('aria-expanded', 'false');
                 }
                 if (except !== catMenu) {
                     catMenu.hidden = true;
                     catToggle.setAttribute('aria-expanded', 'false');
                 }
             }

             sortToggle.addEventListener('click', function(e) {
                 e.stopPropagation();
                 const isOpen = sortToggle.getAttribute('aria-expanded') === 'true';
                 closeAllMenus(isOpen ? null : sortMenu);
                 sortMenu.hidden = isOpen;
                sortToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
           });

            catToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = catToggle.getAttribute('aria-expanded') === 'true';
                closeAllMenus(isOpen ? null : catMenu);
                catMenu.hidden = isOpen;
                catToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            });

            document.addEventListener('click', function() {
                closeAllMenus();
            });
        });
    </script>

<?php
}
// تغیر متن
	add_filter( 'gettext', 'takdooz_translate_woocommerce_strings', 999, 3 );
function takdooz_translate_woocommerce_strings( $translated_text, $text, $domain ) {
    if ( $translated_text === 'Select options' ) {
        $translated_text = 'افزودن به سبد خرید';
    }
    return $translated_text;
}
// ابعاد تصاویر
add_filter( 'wp_get_attachment_image_attributes', 'takdooz_add_image_dimensions', 10, 3 );
function takdooz_add_image_dimensions( $attr, $attachment, $size ) {
    if ( isset($attr['width']) && isset($attr['height']) ) {
        return $attr;
    }
    // گرفتن اطلاعات تصویر
    $img = wp_get_attachment_image_src( $attachment->ID, $size );
    if ( $img ) {
        $attr['width'] = $img[1];
        $attr['height'] = $img[2];
    }
    return $attr;
}
add_action('send_headers', function () {
    header("X-Frame-Options: SAMEORIGIN");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
});






// دکمه راهنمای سایز برای روپوش ها
add_action('woocommerce_before_add_to_cart_form', 'takdooz_size_button');

function takdooz_size_button() {
    global $product;

    // اسلاگ دسته‌بندی‌ها 
    $categories = ['chef-women-clothes', 'chef-men-clothes'];

    if ( has_term( $categories, 'product_cat', $product->get_id() ) ) {
        echo '<button type="button" class="size-guide-btn">سایز من چنده؟</button>';
    }
}
add_action('wp_footer', 'takdooz_size_modal');

function takdooz_size_modal() {
    if ( !is_product() ) return;

    global $product;
    $categories = ['chef-women-clothes', 'chef-men-clothes'];

    if ( !has_term( $categories, 'product_cat', $product->get_id() ) ) return;
?>
<div id="sizeModal" class="size-modal">
  <div class="size-modal-content">
    <span class="close-size">&times;</span>
    <h3>جدول سایزبندی روپوش</h3>

    <div class="size-wrapper">
      
      <!-- تصویر -->
      <div class="size-image">
        <img src="https://takdooz.ir/wp-content/uploads/2026/05/Screenshot-1405-02-16-at-18.34.42.png" alt="راهنمای سایز">
      </div>

      <!-- جدول -->
      <div class="size-table">
        <table>
          <tr>
            <th>سایز</th>
            <th>عرض</th>
            <th>قد</th>
          </tr>
          <tr>
            <td>S</td><td>42</td><td>54</td>
          </tr>
          <tr>
            <td>M</td><td>45</td><td>60</td>
          </tr>
          <tr>
            <td>L</td><td>49</td><td>63</td>
          </tr>
          <tr>
            <td>XL</td><td>54</td><td>69</td>
          </tr>
          <tr>
            <td>XXL</td><td>58</td><td>72</td>
          </tr>
        </table>
      </div>

    </div>

  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){

  const btn = document.querySelector('.size-guide-btn');
  const modal = document.getElementById('sizeModal');
  const closeBtn = document.querySelector('.close-size');

  let scrollPosition = 0;

  // باز شدن مودال
  if(btn){

    btn.onclick = () => {

      // ذخیره موقعیت اسکرول
      scrollPosition = window.pageYOffset;

      // نمایش مودال
      modal.style.display = 'block';

      // قفل کامل صفحه
      document.body.style.position = 'fixed';
      document.body.style.top = `-${scrollPosition}px`;
      document.body.style.left = '0';
      document.body.style.right = '0';
      document.body.style.width = '100%';
    };
  }

  // بستن مودال
  function closeModal(){

    modal.style.display = 'none';

    // بازگردانی صفحه
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.left = '';
    document.body.style.right = '';
    document.body.style.width = '';

    // برگشت به همون جای صفحه
    window.scrollTo(0, scrollPosition);
  }

  if(closeBtn){
    closeBtn.onclick = closeModal;
  }

  // بستن با کلیک بیرون
  window.onclick = function(e){
    if(e.target == modal){
      closeModal();
    }
  }

});
</script>
<?php
}

// دکمه راهنمای سایز پیشبندکودک

add_action('woocommerce_before_add_to_cart_form', 'takdooz_kids_size_button');

function takdooz_kids_size_button() {

    global $product;

    if ( has_term( 'chef-kids-clothes', 'product_cat', $product->get_id() ) ) {

        echo '<button type="button" class="kids-size-guide-btn">سایز کودک</button>';

    }
}
add_action('wp_footer', 'takdooz_kids_size_modal');

function takdooz_kids_size_modal() {

    if ( !is_product() ) return;

    global $product;

    if ( !has_term( 'chef-kids-clothes', 'product_cat', $product->get_id() ) ) return;

?>
<div id="kidsSizeModal" class="kids-size-modal">

  <div class="kids-size-modal-content">

    <span class="kids-close-size">&times;</span>

    <h3>جدول سایزبندی کلاه و پیشبند کودک</h3>

    <table>

      <tr>
        <th>سایز</th>
        <th>دور سر کلاه</th>
        <th>قد پیشبند</th>
		 <th>قد جلیقه</th>
      </tr>

      <tr>
        <td>۶ ماه - ۱.۵ سال</td>
        <td>۴۶ تا ۵۱</td>
        <td>۳۸</td>
		<td>------</td>
      </tr>

      <tr>
        <td>۲ الی ۴ سال</td>
        <td>۵۰ تا ۵۴</td>
        <td>۴۲</td>
		<td>۳۸</td>
      </tr>

      <tr>
        <td>۵ الی ۸ سال</td>
        <td>۵۳ تا ۵۷</td>
        <td>۵۰</td>
		<td>۴۸</td>
      </tr>

      <tr>
        <td>۹ الی ۱۲ سال</td>
        <td>۵۶ تا ۵۹</td>
        <td>۵۸</td>
		<td>۵۵</td>
      </tr>

    </table>

  </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function(){

  const btn = document.querySelector('.kids-size-guide-btn');
  const modal = document.getElementById('kidsSizeModal');
  const closeBtn = document.querySelector('.kids-close-size');

  let scrollPosition = 0;

  if(btn){

    btn.onclick = () => {

      scrollPosition = window.pageYOffset;

      modal.style.display = 'block';

      document.body.style.position = 'fixed';
      document.body.style.top = `-${scrollPosition}px`;
      document.body.style.left = '0';
      document.body.style.right = '0';
      document.body.style.width = '100%';
    };
  }

  function closeModal(){

    modal.style.display = 'none';

    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.left = '';
    document.body.style.right = '';
    document.body.style.width = '';

    window.scrollTo(0, scrollPosition);
  }

  if(closeBtn){
    closeBtn.onclick = closeModal;
  }

  window.onclick = function(e){
    if(e.target == modal){
      closeModal();
    }
  }

});
</script>

<?php
}

/* ============================================================
   منوی خودکار دسته‌بندی محصولات (WooCommerce product_cat)
   این بخش تعمداً اینجا، در سطح اصلی فایل (خارج از هر تابع دیگه)
   قرار داده شده تا هر جای سایت (از جمله هدر) بتونه صداش بزنه.
   ============================================================ */

if ( ! function_exists( 'rb_get_cat_menu_tree' ) ) {
	function rb_get_cat_menu_tree( $parent_id = 0 ) {
		$cache_key = 'rb_cat_menu_tree_' . $parent_id;
		$cached    = get_transient( $cache_key );
		if ( false !== $cached ) {
			return $cached;
		}

		$terms = get_terms( array(
			'taxonomy'   => 'product_cat',
			'parent'     => $parent_id,
			'hide_empty' => false,
			'orderby'    => 'menu_order',
			'order'      => 'ASC',
		) );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			$terms = array();
		}

		$tree = array();
		foreach ( $terms as $term ) {
			$thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
			$tree[]   = array(
				'id'       => $term->term_id,
				'name'     => $term->name,
				'link'     => get_term_link( $term ),
				'icon'     => $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '',
				'children' => rb_get_cat_menu_tree( $term->term_id ),
			);
		}

		set_transient( $cache_key, $tree, DAY_IN_SECONDS );

		return $tree;
	}
}

if ( ! function_exists( 'rb_clear_cat_menu_cache' ) ) {
	function rb_clear_cat_menu_cache() {
		global $wpdb;
		$keys = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_rb_cat_menu_tree_%'" );
		foreach ( $keys as $key ) {
			delete_option( $key );
			delete_option( str_replace( '_transient_', '_transient_timeout_', $key ) );
		}
	}
}
add_action( 'created_term', 'rb_clear_cat_menu_cache' );
add_action( 'edited_term', 'rb_clear_cat_menu_cache' );
add_action( 'delete_term', 'rb_clear_cat_menu_cache' );
add_action( 'save_post_product', 'rb_clear_cat_menu_cache' );

if ( ! function_exists( 'rb_render_cat_menu' ) ) {
	function rb_render_cat_menu( $items, $depth = 0 ) {
		if ( empty( $items ) ) {
			return '';
		}

		$html = '<ul class="rb_catnav_list rb_catnav_depth_' . intval( $depth ) . '">';

		foreach ( $items as $item ) {
			$has_children = ! empty( $item['children'] );

			$html .= '<li class="rb_catnav_item' . ( $has_children ? ' has_children' : '' ) . '">';
			$html .= '<a href="' . esc_url( $item['link'] ) . '" class="rb_catnav_link">';
			if ( $item['icon'] ) {
				$html .= '<img src="' . esc_url( $item['icon'] ) . '" alt="" class="rb_catnav_icon" loading="lazy">';
			}
			$html .= '<span>' . esc_html( $item['name'] ) . '</span>';
			$html .= '</a>';

			if ( $has_children ) {
				$html .= '<button type="button" class="rb_catnav_toggle" aria-expanded="false" aria-label="نمایش زیردسته‌ها">';
				$html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
				$html .= '</button>';

				$html .= '<div class="rb_catnav_submenu">';
				$html .= '<div class="rb_catnav_submenu_header">';
				$html .= '<button type="button" class="rb_catnav_back">';
				$html .= '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
				$html .= '<span>بازگشت</span>';
				$html .= '</button>';
				$html .= '<strong>' . esc_html( $item['name'] ) . '</strong>';
				$html .= '</div>';
				$html .= rb_render_cat_menu( $item['children'], $depth + 1 );
				$html .= '</div>';
			}

			$html .= '</li>';
		}

		$html .= '</ul>';

		return $html;
	}
}

if ( ! function_exists( 'rb_print_cat_menu' ) ) {
	function rb_print_cat_menu() {
		$tree = rb_get_cat_menu_tree( 0 );
		echo '<div class="rb_catnav_wrap">';
		echo '<button type="button" class="rb_catnav_trigger" aria-expanded="false">';
		echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.6"/><rect x="13" y="3" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.6"/><rect x="3" y="13" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.6"/><rect x="13" y="13" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.6"/></svg>';
		echo '<span>دسته‌بندی</span>';
		echo '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" class="rb_catnav_trigger_chevron"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
		echo '</button>';
		echo '<nav class="rb_catnav" aria-label="دسته‌بندی محصولات">';
		echo rb_render_cat_menu( $tree );
		echo '</nav>';
		echo '</div>';
	}
}

/* ---------- هدر: اصلاح منوی اصلی در موبایل و تبلت (assets/css/tkd-header-mobile.css) ---------- */
add_action( 'wp_head', function () {
	$file = get_stylesheet_directory() . '/assets/css/tkd-header-mobile.css';
	if ( file_exists( $file ) ) {
		printf(
			'<link rel="stylesheet" id="tkd-header-mobile-css" href="%s" media="all">' . "\n",
			esc_url( get_stylesheet_directory_uri() . '/assets/css/tkd-header-mobile.css?ver=' . filemtime( $file ) )
		);
	}
}, 102 );
