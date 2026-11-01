<?php 
$cart_count 	= WC()->cart->get_cart_contents_count(); 
$user_id 		= get_current_user_id();
$user 			= get_user_by( 'id' , $user_id );
$user_name 		= is_user_logged_in() ? $user->display_name : 'کاربر';
$organize_link	= get_field('organize_link' , 'options');
$header_text	= get_field('header_text' , 'options');
$header_text_link = get_field('header_text_link', 'options');
?>

<header id="rb_header">
	<div class="rb_header_banner active">
		<span class="close_btn">
			<svg width="22" height="22" viewBox="0 0 24 24" fill="#fff" xmlns="http://www.w3.org/2000/svg"><path d="M20.7457 3.32851C20.3552 2.93798 19.722 2.93798 19.3315 3.32851L12.0371 10.6229L4.74275 3.32851C4.35223 2.93798 3.71906 2.93798 3.32854 3.32851C2.93801 3.71903 2.93801 4.3522 3.32854 4.74272L10.6229 12.0371L3.32856 19.3314C2.93803 19.722 2.93803 20.3551 3.32856 20.7457C3.71908 21.1362 4.35225 21.1362 4.74277 20.7457L12.0371 13.4513L19.3315 20.7457C19.722 21.1362 20.3552 21.1362 20.7457 20.7457C21.1362 20.3551 21.1362 19.722 20.7457 19.3315L13.4513 12.0371L20.7457 4.74272C21.1362 4.3522 21.1362 3.71903 20.7457 3.32851Z" fill="#fff"/></svg>
		</span>
		<?php
		if($header_text_link){
			?>
		<a href="<?php echo $header_text_link; ?>"><h5><?php echo $header_text; ?></h5></a>
		<?php
		}else{
			?>
		<h5><?php echo $header_text; ?></h5>
		<?php
		}
		?>
		
	</div>

	<div class="rb_header_wrap">
		<div class="container">
			<div class="row">
				<div class="col-4 col-lg-3 order-2">
					<a href="/" class="header_logo" aria-label="Main Logo">
						<img src="/wp-content/uploads/2024/05/takdooz-1.webp" alt="takdooz">
					</a>
				</div>
				<?php if(wp_is_mobile()){ ?>
				<div class="col-4 col-lg-3 order-3">
					<div class="header_icons">
						<a href="/cart">
							<span class="cart_icon position-relative">
								<span class="cart_icon_badge"><?php echo $cart_count; ?></span>
								<svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.5 18C8.32843 18 9 18.6716 9 19.5C9 20.3284 8.32843 21 7.5 21C6.67157 21 6 20.3284 6 19.5C6 18.6716 6.67157 18 7.5 18Z" stroke="var(--main-color)" stroke-width="1.5"/><path d="M16.5 18.0001C17.3284 18.0001 18 18.6716 18 19.5001C18 20.3285 17.3284 21.0001 16.5 21.0001C15.6716 21.0001 15 20.3285 15 19.5001C15 18.6716 15.6716 18.0001 16.5 18.0001Z" stroke="var(--main-color)" stroke-width="1.5"/><path d="M11 9H8" stroke="var(--main-color)" stroke-width="1.5" stroke-linecap="round"/><path d="M2 3L2.26491 3.0883C3.58495 3.52832 4.24497 3.74832 4.62248 4.2721C5 4.79587 5 5.49159 5 6.88304V9.5C5 12.3284 5 13.7426 5.87868 14.6213C6.75736 15.5 8.17157 15.5 11 15.5H13M19 15.5H17" stroke="var(--main-color)" stroke-width="1.5" stroke-linecap="round"/><path d="M5 6H8M5.5 13H16.0218C16.9812 13 17.4609 13 17.8366 12.7523C18.2123 12.5045 18.4013 12.0636 18.7792 11.1818L19.2078 10.1818C20.0173 8.29294 20.4221 7.34853 19.9775 6.67426C19.5328 6 18.5054 6 16.4504 6H12" stroke="var(--main-color)" stroke-width="1.5" stroke-linecap="round"/></svg>
							</span>
						</a>
						<!-- 
						<a href="/loginx" aria-label="user account icon">
							<span class="user_account_icon">
								<svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M2.00098 11.999L16.001 11.999M16.001 11.999L12.501 8.99902M16.001 11.999L12.501 14.999" stroke="var(--main-color)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M9.00195 7C9.01406 4.82497 9.11051 3.64706 9.87889 2.87868C10.7576 2 12.1718 2 15.0002 2L16.0002 2C18.8286 2 20.2429 2 21.1215 2.87868C22.0002 3.75736 22.0002 5.17157 22.0002 8L22.0002 16C22.0002 18.8284 22.0002 20.2426 21.1215 21.1213C20.2429 22 18.8286 22 16.0002 22H15.0002C12.1718 22 10.7576 22 9.87889 21.1213C9.11051 20.3529 9.01406 19.175 9.00195 17" stroke="var(--main-color)" stroke-width="1.5" stroke-linecap="round"/>
								</svg>
							</span>
						</a>
						-->
					</div>
				</div>
				<?php } ?>
				<div class="col-12 col-lg-6 order-3">
					<div class="product_search_box">
						<form id="rb_header_search" method="GET" action="/shop">
							<div class="input-group">
								<input class="form-control" type="text" id="search_box" name="s" placeholder="جستجو بین دسته بندی ها و محصولات" >
								<button class="rb_header_search_btn" type="submit" aria-label="Header Search">
									<svg width="20px" height="20px" viewBox="0 0 71 73" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="#1f0e31ff"><path fill="#1f0e31" opacity="1.00" d=" M 32.51 0.00 L 34.64 0.00 C 49.07 0.13 62.76 10.24 66.78 24.16 C 70.40 35.53 67.29 48.40 59.43 57.26 C 63.26 61.20 67.29 64.94 71.00 68.99 L 71.00 71.09 C 70.50 71.56 69.49 72.51 68.99 72.99 C 63.50 70.31 60.06 64.90 55.55 60.94 C 45.96 68.31 32.55 70.51 21.30 65.77 C 9.08 60.97 0.58 48.62 0.00 35.55 L 0.00 32.55 C 0.61 24.41 3.84 16.41 9.59 10.54 C 15.52 4.24 23.93 0.64 32.51 0.00 M 26.36 6.48 C 21.66 7.82 17.30 10.36 13.85 13.82 C 4.11 23.17 2.60 39.56 10.49 50.52 C 18.16 62.09 34.65 66.41 46.96 59.94 C 59.89 53.87 66.46 37.39 61.13 24.10 C 56.33 10.43 40.26 2.18 26.36 6.48 Z"/></g></svg>
								</button>
							</div>	
							<input type="hidden" name="post_type" value="product">
							<div id="rb_header_search_resault"></div>
						</form>
					</div>
				</div>
				<div class="col-4 col-lg-3 order-1">
					<?php if(wp_is_mobile()){ ?>
					<span class="offcanvass_menu_btn">
						<svg fill="#222" height="24" width="24" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1792 1792" xml:space="preserve"><path d="M1673.9,1363.2L1673.9,1363.2c0,52.3-42.4,94.3-94.3,94.3H212.7c-52.3,0-94.3-42.4-94.3-94.3l0,0  c0-52.3,42.4-94.3,94.3-94.3h1366.8C1631.5,1268.5,1673.9,1310.9,1673.9,1363.2z"/><path d="M1673.9,895.6L1673.9,895.6c0,52.3-42.4,94.3-94.3,94.3H659c-52.3,0-94.3-42.4-94.3-94.3l0,0c0-52.3,42.4-94.3,94.3-94.3  h920.6C1631.5,800.8,1673.9,843.2,1673.9,895.6z"/><path d="M1673.9,427.9L1673.9,427.9c0,52.3-42.4,94.3-94.3,94.3H212.7c-52.3,0-94.3-42.4-94.3-94.3l0,0c0-52.3,42.4-94.3,94.3-94.3  h1366.8C1631.5,333.2,1673.9,375.6,1673.9,427.9z"/></svg>
					</span>
					<div class="rb_overlay"></div>
					<div class="offcanvas_menu_wrap">
						<span class="offcanvas_close_btn">
							<svg width="32" height="32" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.2431 12.7574L12.7578 21.2426" stroke="#AAAAAA" stroke-width="1.5" stroke-linecap="round"/><path d="M21.2431 21.2426L12.7578 12.7574" stroke="#AAAAAA" stroke-width="1.5" stroke-linecap="round"/><path d="M33 17C33 8.16344 25.8366 1 17 1C8.16344 1 1 8.16344 1 17C1 25.8366 8.16344 33 17 33C25.8366 33 33 25.8366 33 17Z" stroke="#AAAAAA" stroke-width="1.5"/></svg>
						</span>
						<div class="user_account_sidemenu_header rb_flex_start_wrap text-center">
							<a href="/cart" class="m-auto">
								<span class="user_account_icon">
									<svg width="70" height="70" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="20" cy="20" r="19.5" stroke="#EEEEEE"/><path d="M23 14C23 15.6569 21.6568 17 20 17C18.3432 17 17 15.6569 17 14C17 12.3431 18.3432 11 20 11C21.6568 11 23 12.3431 23 14Z" stroke="#2C2C2C"/><path d="M26 25C26 26.0123 25.4153 26.9955 24.3332 27.7531C23.2527 28.5099 21.7229 29 20 29C18.2771 29 16.7473 28.5099 15.6667 27.7531C14.5847 26.9955 14 26.0123 14 25C14 23.9877 14.5847 23.0045 15.6667 22.2469C16.7473 21.4901 18.2771 21 20 21C21.7229 21 23.2527 21.4901 24.3332 22.2469C25.4153 23.0045 26 23.9877 26 25Z" stroke="#2C2C2C"/></svg>
								</span>
							</a>
						</div>
						<div class="header_cat_menu">
							<?php wp_nav_menu( array('theme_location' => 'primary')); ?>
						</div>
						<a href="<?php echo $organize_link; ?>" class="rb_btn" style="width: 100%;margin-top: 25px;">
							<svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.5 11V14C3.5 17.7712 3.5 19.6569 4.67157 20.8284C5.84315 22 7.72876 22 11.5 22H12.5C16.2712 22 18.1569 22 19.3284 20.8284M20.5 11V14C20.5 15.1698 20.5 16.1581 20.465 17" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/><path d="M9.50002 2H14.5M9.50002 2L8.84828 8.51737C8.66182 10.382 10.1261 12 12 12C13.874 12 15.3382 10.382 15.1518 8.51737L14.5 2M9.50002 2H7.41771C6.50969 2 6.05567 2 5.66628 2.10675C4.84579 2.33168 4.15938 2.89439 3.77791 3.65484M9.50002 2L8.77549 9.24527C8.61911 10.8091 7.30318 12 5.73155 12C3.8011 12 2.35324 10.2339 2.73183 8.34093L2.80002 8M14.5 2H16.5823C17.4904 2 17.9444 2 18.3338 2.10675C19.1542 2.33168 19.8407 2.89439 20.2221 3.65484C20.4032 4.01573 20.4922 4.46093 20.6703 5.35133L21.2682 8.34093C21.6468 10.2339 20.1989 12 18.2685 12C16.6969 12 15.3809 10.8091 15.2245 9.24527L14.5 2Z" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/><path d="M9.5 21.5V18.5C9.5 17.5654 9.5 17.0981 9.70096 16.75C9.83261 16.522 10.022 16.3326 10.25 16.201C10.5981 16 11.0654 16 12 16C12.9346 16 13.4019 16 13.75 16.201C13.978 16.3326 14.1674 16.522 14.299 16.75C14.5 17.0981 14.5 17.5654 14.5 18.5V21.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg>
							<span>سفارش سازمانی</span>
						</a>
						<div class="header_social_wrap">
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
					<?php } ?>
					<?php if(!wp_is_mobile()){ ?>
					<div class="header_icons">
						<a href="/cart">
							<span class="cart_icon position-relative">
								<span class="cart_icon_badge"><?php echo $cart_count; ?></span>
								<svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.5 18C8.32843 18 9 18.6716 9 19.5C9 20.3284 8.32843 21 7.5 21C6.67157 21 6 20.3284 6 19.5C6 18.6716 6.67157 18 7.5 18Z" stroke="var(--main-color)" stroke-width="1.5"/><path d="M16.5 18.0001C17.3284 18.0001 18 18.6716 18 19.5001C18 20.3285 17.3284 21.0001 16.5 21.0001C15.6716 21.0001 15 20.3285 15 19.5001C15 18.6716 15.6716 18.0001 16.5 18.0001Z" stroke="var(--main-color)" stroke-width="1.5"/><path d="M11 9H8" stroke="var(--main-color)" stroke-width="1.5" stroke-linecap="round"/><path d="M2 3L2.26491 3.0883C3.58495 3.52832 4.24497 3.74832 4.62248 4.2721C5 4.79587 5 5.49159 5 6.88304V9.5C5 12.3284 5 13.7426 5.87868 14.6213C6.75736 15.5 8.17157 15.5 11 15.5H13M19 15.5H17" stroke="var(--main-color)" stroke-width="1.5" stroke-linecap="round"/><path d="M5 6H8M5.5 13H16.0218C16.9812 13 17.4609 13 17.8366 12.7523C18.2123 12.5045 18.4013 12.0636 18.7792 11.1818L19.2078 10.1818C20.0173 8.29294 20.4221 7.34853 19.9775 6.67426C19.5328 6 18.5054 6 16.4504 6H12" stroke="var(--main-color)" stroke-width="1.5" stroke-linecap="round"/></svg>
								<span>سبد خرید</span>
							</span>
						</a>
						<!--
						<a href="/loginx" aria-label="user account icon">
							<span class="user_account_icon">
								<svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M2.00098 11.999L16.001 11.999M16.001 11.999L12.501 8.99902M16.001 11.999L12.501 14.999" stroke="var(--main-color)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M9.00195 7C9.01406 4.82497 9.11051 3.64706 9.87889 2.87868C10.7576 2 12.1718 2 15.0002 2L16.0002 2C18.8286 2 20.2429 2 21.1215 2.87868C22.0002 3.75736 22.0002 5.17157 22.0002 8L22.0002 16C22.0002 18.8284 22.0002 20.2426 21.1215 21.1213C20.2429 22 18.8286 22 16.0002 22H15.0002C12.1718 22 10.7576 22 9.87889 21.1213C9.11051 20.3529 9.01406 19.175 9.00195 17" stroke="var(--main-color)" stroke-width="1.5" stroke-linecap="round"/>
								</svg>
								<?php if( is_user_logged_in() ){ ?>
								<a href="/cart" class="user_logged_name"><?php echo $user_name; ?></a>
								<?php }else{ ?>
								<span><?php // echo do_shortcode('[dm-modal]'); ?></span>
								<?php } ?>
							</span>
						</a> 
						-->
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<?php if(!wp_is_mobile()){ ?>
	<div class="rb_header_menu_wrap">
		<div class="container">
			<div class="row">
				<div class="col-12 col-lg-10">
					<div class="header_cat_menu">
						<?php wp_nav_menu( array('theme_location' => 'primary')); ?>
					</div>
				</div>
				<div class="col-12 col-lg-2">
					<a href="<?php echo $organize_link; ?>" class="rb_btn" style="margin-right: auto;">
						<svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.5 11V14C3.5 17.7712 3.5 19.6569 4.67157 20.8284C5.84315 22 7.72876 22 11.5 22H12.5C16.2712 22 18.1569 22 19.3284 20.8284M20.5 11V14C20.5 15.1698 20.5 16.1581 20.465 17" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/><path d="M9.50002 2H14.5M9.50002 2L8.84828 8.51737C8.66182 10.382 10.1261 12 12 12C13.874 12 15.3382 10.382 15.1518 8.51737L14.5 2M9.50002 2H7.41771C6.50969 2 6.05567 2 5.66628 2.10675C4.84579 2.33168 4.15938 2.89439 3.77791 3.65484M9.50002 2L8.77549 9.24527C8.61911 10.8091 7.30318 12 5.73155 12C3.8011 12 2.35324 10.2339 2.73183 8.34093L2.80002 8M14.5 2H16.5823C17.4904 2 17.9444 2 18.3338 2.10675C19.1542 2.33168 19.8407 2.89439 20.2221 3.65484C20.4032 4.01573 20.4922 4.46093 20.6703 5.35133L21.2682 8.34093C21.6468 10.2339 20.1989 12 18.2685 12C16.6969 12 15.3809 10.8091 15.2245 9.24527L14.5 2Z" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/><path d="M9.5 21.5V18.5C9.5 17.5654 9.5 17.0981 9.70096 16.75C9.83261 16.522 10.022 16.3326 10.25 16.201C10.5981 16 11.0654 16 12 16C12.9346 16 13.4019 16 13.75 16.201C13.978 16.3326 14.1674 16.522 14.299 16.75C14.5 17.0981 14.5 17.5654 14.5 18.5V21.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/></svg>
						<span>سفارش سازمانی</span>
					</a>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
</header>