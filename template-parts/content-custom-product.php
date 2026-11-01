<?php
defined( 'ABSPATH' ) || exit;
global $product;
$product_id 			= $product->get_id();
$product_title 			= $product->get_name();
$product_image 			= get_the_post_thumbnail_url($product_id) ? get_the_post_thumbnail_url($product_id) : '/wp-content/uploads/woocommerce-placeholder.png';
$product_link 			= get_permalink( $product_id ); 
if( $product->is_type( 'simple' ) ){
	$is_in_cart 		= rb_is_in_cart( $product_id ) > 0 ? true : false;
} ?>
<div class="rb_custom_product_box <?php echo $product->is_in_stock() ? '' : 'not_in_stock'; ?>">
	<a href="<?php echo $product_link; ?>">
		<div class="rb_product_box_image">
			<img src="<?php echo $product_image; ?>" alt="<?php echo $product_title; ?>">
		</div>
	</a>
	<div class="rb_product_box_content">
		<a href="<?php echo $product_link; ?>" class="rb_product_title_link"><div class="rb_product_title"><?php echo $product_title; ?></div></a>
		
	</div>
</div>