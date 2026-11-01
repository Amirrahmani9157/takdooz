<?php
/**
 * صفحه محصول تکدوز — چیدمان گوتنبرگ (ACF Blocks).
 *
 * جایگزین template_redirect اسنیپت‌های «۱» و «نمایش» و فیلتر اسنیپت «۳».
 * - همه محصولات از یک چیدمان مشترک استفاده می‌کنند (takdooz_product_layout_content).
 * - اطلاعات هر محصول در بلوک‌ها از WooCommerce خوانده می‌شود.
 * - با ?tkd_old=1 یا وقتی کد جدید فعال نیست، قالب اصلی Flatsome نمایش داده می‌شود.
 *
 * @package flatsome-child
 */

defined( 'ABSPATH' ) || exit;

$tkd_use_blocks = function_exists( 'tkd_pdp_data' )
	&& function_exists( 'takdooz_product_layout_content' )
	&& ! isset( $_GET['tkd_old'] ); // phpcs:ignore WordPress.Security.NonceVerification

if ( ! $tkd_use_blocks ) {
	$tkd_parent = get_template_directory() . '/woocommerce/single-product.php';
	include file_exists( $tkd_parent ) ? $tkd_parent : WC()->plugin_path() . '/templates/single-product.php';
	return;
}

get_header();

while ( have_posts() ) {
	the_post();
	wc_print_notices();
	echo '<div class="tkd-pdp">';
	echo do_blocks( takdooz_product_layout_content() ); // phpcs:ignore WordPress.Security.EscapeOutput
	echo '</div>';
}

get_footer();
