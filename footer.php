<?php
/**
 * The template for displaying the footer.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */
	
global $flatsome_opt;
?>

</main>
<?php get_template_part( 'template-parts/template-footer' ); ?>


</div>

<?php wp_footer(); ?>

<div id="rb-cart-toast" class="rb_cart_toast" role="status" aria-live="polite" aria-atomic="true" hidden>
	<span>محصول به سبد خرید اضافه شد.</span>
	<a href="<?php echo esc_url( wc_get_cart_url() ); ?>">مشاهده سبد خرید</a>
</div>

<?php
$floating_cart     = function_exists( 'WC' ) ? WC()->cart : null;
$floating_cart_count = $floating_cart ? $floating_cart->get_cart_contents_count() : 0;
?>
<a
	href="<?php echo esc_url( wc_get_cart_url() ); ?>"
	id="rb-floating-cart"
	class="rb_floating_cart<?php echo 0 === $floating_cart_count ? ' is-empty' : ''; ?>"
	aria-label="<?php echo esc_attr( sprintf( 'سبد خرید، %s محصول', number_format_i18n( $floating_cart_count ) ) ); ?>"
>
	<span class="rb_floating_cart_icon" aria-hidden="true">
		<svg width="24" height="24" viewBox="0 0 24 24" fill="none">
			<path d="M3 3h2l1.55 9.7a2 2 0 0 0 1.98 1.68h7.75a2 2 0 0 0 1.92-1.44L21 6H6.08" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
			<path d="M9.2 20a1.2 1.2 0 1 1-2.4 0 1.2 1.2 0 0 1 2.4 0ZM19.2 20a1.2 1.2 0 1 1-2.4 0 1.2 1.2 0 0 1 2.4 0Z" fill="currentColor"/>
		</svg>
		<span class="cart_icon_badge"><?php echo esc_html( $floating_cart_count ); ?></span>
	</span>
	<span class="rb_floating_cart_text">
		<strong>سبد خرید</strong>
		<small><?php echo $floating_cart_count ? 'مشاهده و پرداخت' : 'سبد شما خالی است'; ?></small>
	</span>
	<svg class="rb_floating_cart_arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
		<path d="m9 18 6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
	</svg>
</a>

<div
	id="rb-quick-cart-modal"
	class="rb_quick_cart_modal"
	role="dialog"
	aria-modal="true"
	aria-labelledby="rb-quick-cart-title"
	data-nonce="<?php echo esc_attr( wp_create_nonce( 'takdooz_quick_cart' ) ); ?>"
	data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
	hidden
>
	<div class="rb_quick_cart_backdrop" data-rb-quick-cart-close></div>
	<div class="rb_quick_cart_dialog">
		<button type="button" class="rb_quick_cart_close" data-rb-quick-cart-close aria-label="بستن پنجره">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
				<path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
			</svg>
		</button>
		<h3 id="rb-quick-cart-title"></h3>
		<div class="rb_quick_cart_content"></div>
		<p class="rb_quick_cart_status" role="status" aria-live="polite"></p>
	</div>
</div>

</body>

</html>
