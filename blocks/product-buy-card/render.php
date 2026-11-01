<?php
/**
 * Block: محصول: کارت خرید (product-buy-card)
 * منتقل‌شده از اسنیپت ۹ (شورت‌کد [tkd_product])، خطوط ۴۰۲–۴۳۰ (پاپ‌آپ بعد از افزودن در بلوک نوار چسبان است)
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tkd = takdooz_product_block_data( 'محصول: کارت خرید', $is_preview );
if ( ! $tkd ) {
	return;
}
extract( $tkd, EXTR_SKIP );
?>
<div class="tkd-card" data-price="<?php echo esc_attr( $price ); ?>" data-variable="<?php echo $is_var ? '1' : '0'; ?>">
	<div class="tkd-chips">
		<?php if ( $cat ) : ?>
		<a class="tkd-pill" href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
		<?php endif; ?>
		<?php if ( $product->get_review_count() ) : ?>
		<a class="tkd-pill tkd-pill-rate" href="#tkd-tab-reviews" data-open="tkd-tab-reviews">★ <?php echo esc_html( tkd_fa_digits( number_format( (float) $product->get_average_rating(), 1 ) ) ); ?> <span>(<?php echo esc_html( tkd_fa_digits( $product->get_review_count() ) ); ?> دیدگاه)</span></a>
		<?php endif; ?>
		<?php if ( $product->is_in_stock() ) : ?><span class="tkd-pill tkd-pill-ok">موجود</span><?php endif; ?>
	</div>

	<h1 class="tkd-title"><?php echo esc_html( $name ); ?></h1>

	<?php woocommerce_template_single_add_to_cart(); ?>

	<ul class="tkd-trust">
	<?php foreach ( $c['trust'] as $i => $t ) : ?>
		<li><?php echo tkd_icon( isset( $trust_icons[ $i ] ) ? $trust_icons[ $i ] : 'check' ); ?><span><?php echo esc_html( $t ); ?></span></li>
	<?php endforeach; ?>
	</ul>

	<div class="tkd-consult">
		<p>برای سفارش عمده یا مشاوره‌ی رنگ و لوگو</p>
		<div class="tkd-btns">
			<a class="tkd-wa" href="https://wa.me/<?php echo esc_attr( $c['whatsapp'] ); ?>" target="_blank" rel="noopener">پیام در واتس‌اپ</a>
			<a class="tkd-call" href="tel:<?php echo esc_attr( $c['phone'] ); ?>">تماس تلفنی</a>
		</div>
	</div>
</div>
