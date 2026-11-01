<?php
defined( 'ABSPATH' ) || exit;
global $product;
$product_id 			= $product->get_id();
$product_title 			= $product->get_name();
$product_image 			= get_the_post_thumbnail_url($product_id) ? get_the_post_thumbnail_url($product_id) : '/wp-content/uploads/woocommerce-placeholder.png';
$product_html_price 	= $product->get_price_html();
$product_sale_percent 	= get_product_percent( $product ) ? '%' . get_product_percent( $product ) : null;
$product_link 			= get_permalink( $product_id ); 
$newness_days 			= 3;
$average_rating 		= $product->get_average_rating();
$new_flag 				= false;
$created 				= strtotime( $product->get_date_created() );
if ( ( time() - ( 60 * 60 * 24 * $newness_days ) ) < $created ) {
	$new_flag = true;
}
$is_in_cart = rb_is_in_cart( $product_id ) > 0;
?>
<div class="rb_product_box <?php echo $product->is_in_stock() ? '' : 'not_in_stock'; ?>">
	<?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?>
	<?php if($new_flag){ ?>
	<span class="rb_product_box_new_tag">جدید</span>
	<?php } ?>
	<div class="rb_product_box_image">
		<a href="<?php echo esc_url( $product_link ); ?>">
			<img src="<?php echo esc_url( $product_image ); ?>" alt="<?php echo esc_attr( $product_title ); ?>">
		</a>
		<div class="rb_product_actions">
			<?php if ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) : ?>
				<a
					href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
					class="rb_product_cart_icon rb_product_add_to_cart product_type_simple add_to_cart_button ajax_add_to_cart<?php echo $is_in_cart ? ' is-in-cart' : ''; ?>"
					data-quantity="1"
					data-product_id="<?php echo esc_attr( $product_id ); ?>"
					data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
					data-success_message="<?php echo esc_attr( $product->add_to_cart_success_message() ); ?>"
					aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>"
					role="button"
					rel="nofollow"
				>
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 3h2l1.6 10.1a2 2 0 0 0 2 1.7h7.9a2 2 0 0 0 1.9-1.4L21 6H6.1M9 20a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
					<span class="rb_cart_status_badge" aria-hidden="true">✓</span>
					<span class="rb_cart_tooltip"><?php echo $is_in_cart ? 'در سبد خرید — افزودن مجدد' : 'افزودن به سبد خرید'; ?></span>
				</a>
			<?php elseif ( $product->is_type( 'variable' ) && $product->is_purchasable() && $product->is_in_stock() ) : ?>
				<button
					type="button"
					class="rb_product_cart_icon rb_variable_cart_trigger<?php echo $is_in_cart ? ' is-in-cart' : ''; ?>"
					data-product-id="<?php echo esc_attr( $product_id ); ?>"
					data-product-title="<?php echo esc_attr( $product_title ); ?>"
					aria-label="<?php echo esc_attr( sprintf( 'انتخاب گزینه‌های %s', $product_title ) ); ?>"
				>
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 3h2l1.6 10.1a2 2 0 0 0 2 1.7h7.9a2 2 0 0 0 1.9-1.4L21 6H6.1M9 20a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
					<span class="rb_cart_status_badge" aria-hidden="true">✓</span>
					<span class="rb_cart_tooltip"><?php echo $is_in_cart ? 'در سبد خرید — انتخاب گزینه دیگر' : 'انتخاب گزینه‌ها'; ?></span>
				</button>
			<?php else : ?>
				<a class="rb_product_cart_icon" href="<?php echo esc_url( $product_link ); ?>" aria-label="<?php echo esc_attr( $product_title ); ?>">
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 3h2l1.6 10.1a2 2 0 0 0 2 1.7h7.9a2 2 0 0 0 1.9-1.4L21 6H6.1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
					<span class="rb_cart_tooltip">مشاهده محصول</span>
				</a>
			<?php endif; ?>
		</div>
	</div>
	<div class="rb_product_box_content">
		<a href="<?php echo $product_link; ?>" class="rb_product_title_link"><div class="rb_product_title"><?php echo $product_title; ?></div></a>
		<?php if( $average_rating != 0 ){ ?>
		<span class="product_box_rate">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gold" class="bi bi-star" viewBox="0 0 16 16"><path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"/></svg>
			<span><?php echo $average_rating; ?></span>
		</span>
		<?php } ?>
		<span class="product_price">
			<?php if($product_sale_percent){ ?>
			<span class="rb_product_percent"><?php echo $product_sale_percent . '-'; ?></span>
			<?php } ?>
			<?php echo $product_html_price; ?>
		</span>
	</div>
</div>
