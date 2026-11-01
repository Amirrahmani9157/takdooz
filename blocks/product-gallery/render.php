<?php
/**
 * Block: محصول: گالری تصاویر (product-gallery)
 * منتقل‌شده از اسنیپت ۹ (شورت‌کد [tkd_product])، خطوط ۳۸۳–۴۰۰ (لایت‌باکس در بلوک نوار چسبان است تا ترتیب HTML مثل قبل بماند)
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tkd = takdooz_product_block_data( 'محصول: گالری تصاویر', $is_preview );
if ( ! $tkd ) {
	return;
}
extract( $tkd, EXTR_SKIP );
?>
<div class="tkd-gal">
	<div class="tkd-stage">
		<img id="tkd-main" src="<?php echo esc_url( $imgs[0]['full'] ); ?>" alt="<?php echo esc_attr( $imgs[0]['alt'] ); ?>" fetchpriority="high">
		<button type="button" class="tkd-zoom" aria-label="بزرگ‌نمایی"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg></button>
		<?php if ( count( $imgs ) > 1 ) : ?><span class="tkd-count"><b>۱</b> / <?php echo esc_html( tkd_fa_digits( count( $imgs ) ) ); ?></span><?php endif; ?>
	</div>
		<?php if ( count( $imgs ) > 1 ) : ?>
	<div class="tkd-strip">
		<button type="button" class="tkd-nav" data-go="prev" aria-label="تصویر قبلی"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg></button>
		<div class="tkd-thumbs" role="list">
		<?php foreach ( $imgs as $i => $im ) : ?>
			<button type="button" class="tkd-th<?php echo 0 === $i ? ' on' : ''; ?>" data-full="<?php echo esc_url( $im['full'] ); ?>" aria-label="تصویر <?php echo esc_attr( tkd_fa_digits( $i + 1 ) ); ?>"><img src="<?php echo esc_url( $im['thumb'] ); ?>" alt="" loading="lazy"></button>
		<?php endforeach; ?>
		</div>
		<button type="button" class="tkd-nav" data-go="next" aria-label="تصویر بعدی"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg></button>
	</div>
	<?php endif; ?>
</div>
