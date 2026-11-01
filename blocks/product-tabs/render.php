<?php
/**
 * Block: محصول: تب‌ها (product-tabs)
 * منتقل‌شده از اسنیپت ۹ (شورت‌کد [tkd_product])، خطوط ۴۳۴–۴۴۷
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tkd = takdooz_product_block_data( 'محصول: تب‌ها', $is_preview );
if ( ! $tkd ) {
	return;
}
extract( $tkd, EXTR_SKIP );
?>
<?php if ( $items ) : ?>
<div class="tkd-tabs-area"><div class="tkd-wrap">
	<div class="tkd-tabs" role="tablist">
	<?php foreach ( $items as $key => $it ) : ?>
		<button type="button" class="tkd-tab" role="tab" aria-selected="false" aria-controls="tkd-tab-<?php echo esc_attr( $key ); ?>"><?php echo tkd_icon( $it['icon'] ); ?><span><?php echo esc_html( $it['title'] ); ?></span></button>
	<?php endforeach; ?>
	</div>
	<?php $n = 0; foreach ( $items as $key => $it ) : ?>
	<section class="tkd-panel woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?>" id="tkd-tab-<?php echo esc_attr( $key ); ?>" role="tabpanel"<?php echo $n ? ' hidden' : ''; ?>>
		<?php echo $it['html']; // phpcs:ignore ?>
	</section>
	<?php $n++; endforeach; ?>
</div></div>
<?php endif; ?>
