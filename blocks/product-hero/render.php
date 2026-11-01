<?php
/**
 * Block: محصول: بخش بالا (product-hero)
 * نگهدارنده‌ی گالری و کارت خرید — همان <div class="tkd-band"> خطوط ۳۸۱ و ۴۳۲ اسنیپت ۹.
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$allowed  = array( 'acf/takdooz-product-gallery', 'acf/takdooz-product-buy-card' );
$template = array(
	array( 'acf/takdooz-product-gallery' ),
	array( 'acf/takdooz-product-buy-card' ),
);
?>
<div class="tkd-band"><div class="tkd-wrap tkd-hero">
	<InnerBlocks allowedBlocks="<?php echo esc_attr( wp_json_encode( $allowed ) ); ?>" template="<?php echo esc_attr( wp_json_encode( $template ) ); ?>" templateLock="all" />
</div></div>
