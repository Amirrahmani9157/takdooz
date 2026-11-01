<?php // template name: قالب صفحه اصلی
global $theme_dir;
$product_cats 		= get_terms(array( 'taxonomy' => 'product_cat' , 'parent' => 0, ));
$custom_cat 		= get_field( 'product_cat_in_home' , 'options' ) ? get_term_by( 'term_id' , get_field( 'product_cat_in_home' , 'options' ) , 'product_cat' ) : null;
if( $custom_cat ){
	$custom_cat_image 				= get_term_meta($custom_cat->term_id , 'cat_img' , 1) ? wp_get_attachment_url( get_term_meta($custom_cat->term_id , 'cat_img' , 1) ) : '';
	$custom_cat_title 				= $custom_cat->name;
	$custom_cat_link				= get_term_link($custom_cat->term_id , 'product_cat');
	$custom_cat_product_count 		= $custom_cat->count;
}
$campain_category	= get_field('campain_category' , 'options') ? get_term_by( 'term_id' , get_field( 'campain_category' , 'options' ) , 'product_cat' ) : null;
$campain_date 		= get_field('campain_date' , 'options') ? get_field('campain_date' , 'options') : null;
if( $campain_date ){
	$campain_date		= strtotime( $campain_date ) - time();
	$campain_day 		= date( 'd' , $campain_date );
	$campain_hour 		= date( 'H' , $campain_date );
	$campain_minute 	= date( 'i' , $campain_date );
	$campain_second 	= date( 's' , $campain_date );
	$campain_text 		= get_field('campain_text' , 'options');
	$campain_title 		= get_field('campain_title' , 'options');
	$campain_link 		= get_field('campain_link' , 'options');
}

$banner_image 		= get_field('banner_image' , 'options');
$banner_title 		= get_field('banner_title' , 'options');
$banner_sub_title 	= get_field('banner_sub_title' , 'options');
$banner_link 		= get_field('banner_link' , 'options');

$sliders = get_posts(array(
	'post_type' 		=> 'slider',
	'posts_per_page' 	=> -1,
	'post_status' 		=> 'publish',
));
get_header();
if (!empty($sliders)){ ?>
<div class="main_slider_wrap">
	<div class="swiper rb_slider_swiper">
		<div class="swiper-wrapper">
			<?php 
	foreach($sliders as $slider){
		$slider_id 				= $slider->ID;
		$slider_img 			= get_the_post_thumbnail_url($slider_id);
		$slider_mobile_img 		= get_field('mobile_slide' , $slider_id);
		$slider_link 			= get_field('slider_link' , $slider_id);
		$content_post 			= get_post($slider_id);
		$slider_content 		= $content_post->post_content;
			?>
			<div class="swiper-slide">
				<div class="container">
					<a aria-label="مشاهده اسلایدر" href="<?php echo $slider_link; ?>" class="rb_box_link"></a>
					<div class="row align-items-center">
						<div class="col-12 col-lg-6">
							<img src="<?php echo wp_is_mobile() ? $slider_mobile_img : $slider_img; ?>" alt="تکدوز" class="slider_image">
						</div>
						<div class="col-12 col-lg-6">
							<div class="slider_content">
								<?php echo $slider_content; ?>
							</div>
						</div>
					</div>
				</div>
			</div>	
			<?php } ?>
		</div>
		<div class="slider-swiper-pagination"></div>
	</div>
</div>
<?php } ?>

<?php
// فقط این ۳ دسته رو بگیر
$product_cats = get_terms(array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
    'include'    => array(2649, 2424, 1040) 
));
?>



<div class="most_sale_products">
	<div class="container">
		<div class="row">
			<div class="title_wrap">
				<div class="title_wrap_text">
					<h2>پرفروش ترین محصولات</h2>
					<ul class="tab_title">
						<?php 
						$count = 1;
						foreach($product_cats as $cat){ ?>
						<li class="<?php echo $count == 1 ? 'active' : ''; ?>" data-id="<?php echo $count; ?>"><?php echo $cat->name; ?></li>
						<?php $count++; } ?>
					</ul>
				</div>
				<?php if(!wp_is_mobile()){ ?>
				<a href="/shop/?orderby=popularity" class="show_more_btn">نمایش بیشتر <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="var(--main-color)" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/></svg>
				</a>
				<?php } ?>
			</div>
		</div>
		<div class="row">
			<div class="tabs_content_wrap">
				<?php 
				$count = 1;
				foreach($product_cats as $cat){
					$most_sale_prodcuts_arg = array(
						'post_type' 		=> 'product', 
						'stock' 			=> 1, 
						'posts_per_page' 	=> 8, 
						'orderby' 			=> 'meta_value_num',
						'meta_key'			=> 'total_sales',
						'order' 			=> 'DESC',
						'tax_query' 		=> array(
							'relation' 		=> 'AND',
							array(
								'taxonomy' 	=> 'product_cat',
								'field' 	=> 'term_id',
								'terms' 	=> $cat->term_id,
							) ),
					);
					$most_sale_prodcuts = new WP_Query( $most_sale_prodcuts_arg );
					if ( $most_sale_prodcuts->have_posts() ){ ?>
				<div class="tab_content" data-id="<?php echo $count; ?>" style="<?php echo $count != 1 ? 'display:none' : ''; ?>">
					<div class="swiper rb_product_swiper">
						<div class="swiper-wrapper">
							<?php  
															 while ( $most_sale_prodcuts->have_posts() ){
																 $most_sale_prodcuts->the_post(); ?>
							<div class="swiper-slide">
								<?php get_template_part( 'template-parts/content' , 'product' ); ?>
							</div>
							<?php } ?>
						</div>
						<div class="rb_swiper_btns">
							<span class="slideNext-btn">
								<svg width="18" height="24" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L6 6L1 11" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
							<span class="slidePrev-btn">
								<svg width="18" height="24" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 11L1 6L6 1" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
						</div>
					</div>
				</div>
				<?php } wp_reset_query(); ?>
				<?php $count++; } ?>
			</div>
			<?php if(wp_is_mobile()){ ?>
			<a href="#" class="show_more_btn">نمایش بیشتر <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="var(--main-color)" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/></svg>
			</a>
			<?php } ?>
		</div>
	</div>
</div>


<div class="cat_banner_box my-4 my-lg-5">
	<img src="<?php echo $banner_image; ?>">
	<div class="cat_banner_box_title">
		<span><?php echo $banner_title; ?></span>
		<span><?php echo $banner_sub_title; ?></span>
		<a href="<?php echo $banner_link; ?>">مشاهده و خرید <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/></svg></a>
	</div>

</div>

<?php 
if( $campain_date ){
	$offer_prodcuts_arg = array( 
		'post_type' 		=> 'product', 
		'stock' 			=> 1, 
		'posts_per_page' 	=> 8, 
		'orderby' 			=> 'date',
		'order' 			=> 'DESC',
		'tax_query'			=> array(
			array(
				'taxonomy' => 'product_cat',
				'field' => 'term_id',
				'terms' => array($campain_category->term_id),
			),
		),
	);
	$offer_prodcuts_arg = array(
    'post_type' => 'product',
    'posts_per_page' => 10,
    'tax_query' => array(
        array(
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => 'takdooz-offer', // نامک برچسب دلخواه
        ),
    ),
);

	$offer_prodcuts = new WP_Query( $offer_prodcuts_arg );
	if ( $offer_prodcuts->have_posts() ){ ?>
<div id="offers" class="offer_products">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-12 col-lg-5">
				<h2><?php echo $campain_title; ?></h2>
				<p style="font-size: 15px;color: #666;margin: 20px 0 35px;"><?php echo $campain_text; ?></p>
				<div class="timer_wrap counter">
					<div class="timer_item">
						<span class="timer_item_box seconds"><?php echo $campain_second; ?></span>
						<span>ثانیه</span>
					</div>
					<div class="timer_item">
						<span class="timer_item_box minutes"><?php echo $campain_minute; ?></span>
						<span>دقیقه</span>
					</div>
					<div class="timer_item">
						<span class="timer_item_box hours"><?php echo $campain_hour; ?></span>
						<span>ساعت</span>
					</div>
					<div class="timer_item">
						<span class="timer_item_box days"><?php echo $campain_day; ?></span>
						<span>روز</span>
					</div>
				</div>
				<?php if(!wp_is_mobile()){ ?>
				<a href="<?php echo $campain_link; ?>" class="show_product_btn">مشاهده همه محصولات</a>
				<?php } ?>
			</div>
			<div class="col-12 col-lg-7">
				<div class="products_wrap">
					<div class="swiper rb_offer_product_swiper">
						<div class="swiper-wrapper">
							<?php  
										 while ( $offer_prodcuts->have_posts() ){
											 $offer_prodcuts->the_post(); ?>
							<div class="swiper-slide">
								<?php get_template_part( 'template-parts/content' , 'custom-product' ); ?>
							</div>
							<?php } ?>
						</div>
						<?php if(!wp_is_mobile()){ ?>
						<div class="rb_swiper_btns">
							<span class="slideNext-btn" style="left: 0;right: auto;">
								<svg width="15" height="20" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L6 6L1 11" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
							<span class="slidePrev-btn" style="right: 0;left: auto;">
								<svg width="15" height="20" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 11L1 6L6 1" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
						</div>
						<?php } ?>
					</div>
				</div>
				<?php if(wp_is_mobile()){ ?>
				<a href="/shop" class="show_product_btn">مشاهده همه محصولات</a>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php }
	wp_reset_query();

}

$new_prodcuts_arg = array( 
	'post_type' 		=> 'product', 
	'stock' 			=> 1, 
	'posts_per_page' 	=> 8, 
	'orderby' 			=> 'date',
	'order' 			=> 'DESC' 
);
$new_prodcuts = new WP_Query( $new_prodcuts_arg );
if ( $new_prodcuts->have_posts() ){ ?>
<div class="most_sale_products">
	<div class="container">
		<div class="row">
			<div class="title_wrap">
				<div class="title_wrap_text">
					<h2>جدید ترین محصولات</h2>
					<?php if(!wp_is_mobile()){ ?><p>با جدیدترین محصولات ما، کیفیت و تنوع را تجربه کنید.</p><?php } ?>
				</div>
				<?php if(!wp_is_mobile()){ ?>
				<a href="/shop/?orderby=date" class="show_more_btn">مشاهده همه <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="var(--main-color)" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/></svg>
				</a>
				<?php } ?>
			</div>
		</div>
		<div class="row">
			<?php if(!wp_is_mobile()){ ?>
			<div class="rb_product_grid_wrap">
				<?php  
	while ( $new_prodcuts->have_posts() ){
		$new_prodcuts->the_post(); ?>
				<?php get_template_part( 'template-parts/content' , 'product' ); ?>
				<?php } ?>
			</div>
			<?php }else{ ?>
			<div class="swiper rb_product_swiper">
				<div class="swiper-wrapper">
					<?php  
	while ( $new_prodcuts->have_posts() ){
		$new_prodcuts->the_post(); ?>
					<div class="swiper-slide">
						<?php get_template_part( 'template-parts/content' , 'product' ); ?>
					</div>
					<?php } ?>
				</div>
			</div>
			<a href="#" class="show_more_btn">مشاهده همه <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="var(--main-color)" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/></svg>
			</a>
			<?php } ?>
		</div>
	</div>
</div>
<?php }
wp_reset_query();
?>


<div class="why_takdooz_wrap">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-12 col-lg-6">
				<video poster="<?php echo get_field('why_takdooz_image' , 'options'); ?>" src="<?php echo get_field('why_takdooz_video' , 'options'); ?>" alt="تکدوز"></video>
			</div>
			<div class="col-12 col-lg-6">
				<?php echo get_field('why_takdooz_text' , 'options'); ?>
			</div>
		</div>
	</div>
</div>

<?php if( $custom_cat ){ ?>
<?php
	$custom_posts_arg = array( 
		'post_type' 		=> 'product', 
		'posts_per_page' 	=> 6, 
		'orderby' 			=> 'date',
		'order' 			=> 'DESC',
		'tax_query'			=> array(
			array(
				'taxonomy' => 'product_cat',
				'field' => 'term_id',
				'terms' => array($custom_cat->term_id),
			),
		),
	);
	$posts = new WP_Query( $custom_posts_arg );
	if ( $posts->have_posts() ){ ?>
<div class="products_cats_wrap">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-12 col-lg-6 order-2">
				<div class="products_wrap">
					<div class="swiper rb_offer_product_swiper">
						<div class="swiper-wrapper">
							<?php  
		while ( $posts->have_posts() ){
			$posts->the_post(); ?>
							<div class="swiper-slide">
								<?php get_template_part( 'template-parts/content' , 'product' ); ?>
							</div>
							<?php } ?>
						</div>
						<div class="rb_swiper_btns">
							<span class="slideNext-btn">
								<svg width="15" height="20" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L6 6L1 11" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
							<span class="slidePrev-btn">
								<svg width="15" height="20" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 11L1 6L6 1" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12 col-lg-6 order-1">

				<div class="cat_box">
					<a href="<?php echo $custom_cat_link; ?>" class="box_link"></a>
					<img src="<?php echo $custom_cat_image; ?>" alt="تکدوز">
					<div class="cat_box_title">
						<h3><?php echo $custom_cat_title; ?></h3>
						<span class="cat_box_product_count"><?php echo $custom_cat_product_count . ' محصول '; ?></span>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
<?php } wp_reset_query(); ?>
<?php } ?>

<?php 
$comments_arg = array( 
	'post_type' 		=> 'custom_comments', 
	'posts_per_page' 	=> 8,
	'fields' 			=> 'ids',
);
$comments = get_posts( $comments_arg );
if ( !empty($comments) ){ ?>
<div class="what_say_customers_wrap">
	<div class="container">
		<div class="row">
			<div class="center_title_wrap_text">
				<h2>مشتریان ما چه میگویند؟</h2>
				<p>مشتریان ما همواره از کیفیت محصولات و خدمات سریع تکدوز رضایت داشته اند. رضایت شما، افتخار ماست.</p>
			</div>
		</div>
		<div class="row align-items-center">
			<div class="col-12">
				<div class="swiper rb_home_comments_swiper">
					<div class="swiper-wrapper">
						<?php  
	foreach($comments as $comment_id){
		$comment_title			= get_the_title($comment_id);
		$comment_semat			= get_post_meta($comment_id , 'customer_semat' , 1);
		$comment_rate			= get_post_meta($comment_id , 'customer_rate' , 1);
		$content_post 			= get_post($comment_id);
		$content 				= $content_post->post_content;
		$comment_text 			= wp_trim_words(apply_filters('the_content', $content) , 40 , '');
		$commenter_image		= get_the_post_thumbnail_url($comment_id);
		$commenter_name 		= $comment_title ? $comment_title : 'محمدرضا منفرد';
		$commenter_semat 		= $comment_semat ? $comment_semat : 'مدیر مجموعه رستوران های منفرد';
		$commenter_rate_count 	= $comment_rate ? $comment_rate : '5';
		$comment_text			 = $comment_text ? $comment_text : 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک استلورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است';
		$commenter_image 		= $commenter_image ? $commenter_image : 'https://stage.takdooz.ir/wp-content/uploads/2024/03/دستمال-گردن-کرواتی-تشریفاتی.jpg';
						?>
						<div class="swiper-slide">
							<div class="comment_box">
								<?php if(!wp_is_mobile()){ ?>
								<div class="comment_text_box">
									<div class="comment_text">
										<?php echo $comment_text; ?>
									</div>
									<div class="comment_author_detail">
										<h4 class="m-0"><?php echo $commenter_name; ?></h4>
										<span><?php echo $commenter_semat; ?></span>
										<div class="comment_rating">
											<?php for($i= 1; $i <= $commenter_rate_count; $i++){ ?>
											<span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gold" class="bi bi-star-fill" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg></span>
											<?php } ?>
										</div>
									</div>
								</div>
								<div class="comment_image_box">
									<img src="<?php echo $commenter_image; ?>">
								</div>

								<?php }else{ ?>
								<div class="rb_flex_wrap">
									<div class="comment_image_box">
										<img src="<?php echo $commenter_image; ?>">
									</div>
									<div class="comment_text_box">
										<div class="comment_author_detail">
											<h4 class="m-0"><?php echo $commenter_name; ?></h4>
											<span><?php echo $commenter_title; ?></span>
											<div class="comment_rating">
												<?php for($i= 1; $i <= $commenter_rate_count; $i++){ ?>
												<span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gold" class="bi bi-star-fill" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg></span>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
								<div class="comment_text">
									<?php echo $comment_text; ?>
								</div>

								<?php } ?>
							</div>
						</div>
						<?php } ?>
					</div>
					<?php if(!wp_is_mobile()){ ?>
					<div class="rb_swiper_btns">
						<span class="slideNext-btn">
							<svg width="15" height="21" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 11L1 6L6 1" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</span>
						<span class="slidePrev-btn">
							<svg width="15" height="21" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L6 6L1 11" stroke="#bbb" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</span>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } wp_reset_query(); ?>

<?php 
$posts_arg = array( 
	'post_type' 		=> 'post', 
	'posts_per_page' 	=> 8, 
	'orderby' 			=> 'date',
	'order' 			=> 'DESC' 
);
$posts = new WP_Query( $posts_arg );
if ( $posts->have_posts() ){ ?>
<div class="takdooz_posts_wrap">
	<div class="container">
		<div class="row">
			<div class="title_wrap">
				<div class="title_wrap_text">
					<h2>بیش از ۱۰۰ مقاله در مجله تک دوز</h2>
					<p>با بیش از ۱۰۰ مقاله تخصصی در مجله تکدوز، تازه‌ترین مطالب درباره پوشاک صنعتی، نکات کاربردی و اخبار روز را با شما به اشتراک می‌گذاریم.</p>
				</div>
				<?php if(!wp_is_mobile()){ ?>
				<a href="/blog" class="show_more_btn">مشاهده همه پست ها <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="var(--main-color)" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/></svg>
				</a>
				<?php } ?>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<div class="swiper rb_post_swiper">
					<div class="swiper-wrapper">
						<?php  
							while ( $posts->have_posts() ){
								$posts->the_post(); ?>
						<div class="swiper-slide">
							<?php echo get_template_part( 'template-parts/content' , 'post-box' ); ?>
						</div>
						<?php } ?>
					</div>
					<?php if(wp_is_mobile()){ ?>
					<div class="swiper-pagination"></div>
					<?php } ?>
				</div>
			</div>
			<?php if(wp_is_mobile()){ ?>
			<a href="/blog" class="show_more_btn">مشاهده همه پست ها <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="var(--main-color)" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/></svg>
			</a>
			<?php } ?>
		</div>
	</div>
</div>
<?php } wp_reset_query(); ?>
<?php get_footer(); ?>