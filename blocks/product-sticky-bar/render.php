<?php
/**
 * Block: محصول: نوار چسبان خرید (product-sticky-bar)
 * منتقل‌شده از اسنیپت ۹ (شورت‌کد [tkd_product])، خطوط ۴۵۸–۴۸۴ (نوار چسبان + لایت‌باکس گالری + پاپ‌آپ بعد از افزودن)
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tkd = takdooz_product_block_data( 'محصول: نوار چسبان خرید', $is_preview );
if ( ! $tkd ) {
	return;
}
extract( $tkd, EXTR_SKIP );
?>
<div class="tkd-sticky" aria-hidden="true">
	<div class="tkd-sticky-in">
		<div class="tkd-sticky-t"><small><?php echo esc_html( $name ); ?></small><b><span class="tkd-sticky-p"><?php echo esc_html( tkd_money( $price ) ); ?></span> تومان</b></div>
		<button type="button" class="tkd-sticky-btn" tabindex="-1">افزودن به سبد</button>
	</div>
</div>

<dialog class="tkd-lb" id="tkd-lb"><div class="tkd-lb-in"><button type="button" class="tkd-lb-x" aria-label="بستن">×</button><img id="tkd-lb-img" alt=""></div></dialog>

<?php if ( $just ) : ?>
<dialog class="tkd-addon" id="tkd-addon">
	<div class="tkd-addon-in">
		<button type="button" class="tkd-addon-x" aria-label="بستن">×</button>
		<div class="tkd-addon-head"><span class="tkd-addon-ok"><?php echo tkd_icon( 'check' ); ?></span><div><b>به سبد خرید اضافه شد</b><small><?php echo esc_html( get_the_title( $just ) ); ?></small></div></div>
		<?php if ( $addons ) : ?>
		<p class="tkd-addon-sub">ست کامل کنید: این‌ها کنار این محصول زیاد خریده می‌شوند</p>
		<div class="tkd-addon-grid">
			<?php foreach ( $addons as $ap ) { echo tkd_mini_card( $ap, true ); } // phpcs:ignore ?>
		</div>
		<?php endif; ?>
		<div class="tkd-addon-act">
			<button type="button" class="tkd-addon-close">ادامه‌ی خرید</button>
			<a class="tkd-addon-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>">مشاهده‌ی سبد و پرداخت</a>
		</div>
	</div>
</dialog>
<?php endif; ?>
