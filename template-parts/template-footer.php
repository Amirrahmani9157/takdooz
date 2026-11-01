<?php 
$cart_count 	= WC()->cart->get_cart_contents_count(); 
$copyright		= get_field('copyright' , 'options');
?>
<footer>
	<div class="container">
		<div class="footer_top_box">
			<div class="row">
				<div class="col-12 col-lg-6">
					<span class="rss_form_title">برای دریافت آخرین اخبار و تخفیف های جدید, شماره تلفن خود را وارد کنید</span>
					<form method="POST" action="" id="rss_form">
						<div class="form_group">
							<input type="text" id="email" name="phone" placeholder="شماره تلفن شما">
							<input type="hidden" name="action" value="rss_form_ajax">
							<button class="rss_form_btn">ثبت</button>
						</div>
					</form>
				</div>
				<div class="col-12 col-lg-6 align_items_center">
					<div class="footer_social_wrap">
						<?php if (have_rows('social_media' , 'options')){
	while(have_rows('social_media' , 'options')){
		the_row();
		$social_icon = get_sub_field('social_media_icon');
		$social_title = get_sub_field('social_media_title');
		$social_link = get_sub_field('social_media_link'); ?>
						<a href="<?php echo $social_link; ?>" class="social_item" title="<?php echo $social_title; ?>" target="_blank"><?php echo $social_icon; ?></a>
						<?php } } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="rb_footer_wrap">
		<div class="container">
			<div class="row">
				<div class="col-12 col-lg-3">
					<div class="footer_box_title">همراه با تکدوز
						<?php if(wp_is_mobile()){ ?>
						<span class="footer_box_toggle_btn"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000" class="bi bi-chevron-up" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg></span>
						<?php } ?>
					</div>
					<div class="footer_menu">
						<?php wp_nav_menu( array('theme_location' => 'with_takdooz_footer_menu', 'container_class' => 'footer_menu') ); ?>
					</div>
				</div>
				<div class="col-12 col-lg-3">
					<div class="footer_box_title">خدمات مشتریان
						<?php if(wp_is_mobile()){ ?>
						<span class="footer_box_toggle_btn"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000" class="bi bi-chevron-up" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg></span>
						<?php } ?>
					</div>
					<div class="footer_menu">
						<?php wp_nav_menu( array('theme_location' => 'customers_footer_menu', 'container_class' => 'footer_menu') ); ?>
					</div>
				</div>
				<div class="col-12 col-lg-3">
					<div class="footer_box_title">راهنمای خرید
						<?php if(wp_is_mobile()){ ?>
						<span class="footer_box_toggle_btn"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#000" class="bi bi-chevron-up" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg></span>
						<?php } ?>
					</div>
					<div class="footer_menu">
						<?php wp_nav_menu( array('theme_location' => 'shopping_help_footer_menu', 'container_class' => 'footer_menu') ); ?>
					</div>
				</div>
				<div class="col-12 col-lg-3">
					<div class="footer_support_text rb_flex_wrap">
						<h5 style="white-space:nowrap;">پشتیبانی ازساعت ۹الی۱۴،روزهای شنبه تا پنجشنبه
						</h5>
						<h5 style="direction:ltr; text-align:left;"><?php if(!wp_is_mobile()){ ?> شماره تماس: <?php } ?><a href="tel:+989128052860" style="color: var(--main-color);"><strong>09128052860</strong><br>  09019209600</a></h5>
					</div>
					<div class="footer_enamad_box rb_flex_center_wrap">
						<?php if (have_rows('site_namads' , 'options')){
	while(have_rows('site_namads' , 'options')){
		the_row();
		$social_image = get_sub_field('namad_image');
		$social_title = get_sub_field('namad_title');
		$social_link = get_sub_field('namad_link'); ?>
						<a href="<?php echo $social_link; ?>" title="<?php echo $social_title; ?>"><img src="<?php echo $social_image; ?>"></a>
						<?php } } ?>
					</div>
				</div>
			</div>
			<?php if(!wp_is_mobile()){ ?>
			<div class="row">
				<div class="copyright">
					<p><?php echo $copyright; ?></p>
					<span class="back_to_top">
						<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#fff" class="bi bi-chevron-up" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/>
						</svg>
					</span>
				</div>
			</div>
			<?php } ?>
			<div class="row">
				<div class="d-flex align-items-center gap-1 mb-2">
					<div class="footer_about_desc_title">صنایع پوشاک تکدوز</div>
				</div>
				<p class="footer_about_desc"><?php echo get_field('footer_text' , 'options'); ?></p>
			</div>
			<?php if(wp_is_mobile()){ ?>
			<div class="row">
				<div class="copyright">
					<p>تمامی حقوق مادی و معنوی این سایت متعلق به شرکت صنایع پوشاک تکدوز می باشد.</p>
					<span class="back_to_top">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff" class="bi bi-chevron-up" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/>
						</svg>
					</span>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</footer>