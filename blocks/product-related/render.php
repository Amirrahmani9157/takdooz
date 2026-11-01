<?php
/**
 * Block: محصول: محصولات مرتبط (product-related)
 * منتقل‌شده از اسنیپت ۹ (شورت‌کد [tkd_product])، خطوط ۴۴۹–۴۵۶
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tkd = takdooz_product_block_data( 'محصول: محصولات مرتبط', $is_preview );
if ( ! $tkd ) {
	return;
}
extract( $tkd, EXTR_SKIP );
?>
<?php if ( $related ) : ?>
<section class="tkd-related tkd-wrap">
	<h2 class="tkd-sec-title">محصولات مرتبط</h2>
	<div class="tkd-grid">
	<?php foreach ( $related as $rp ) { echo tkd_mini_card( $rp ); } // phpcs:ignore ?>
	</div>
</section>
<?php endif; ?>
