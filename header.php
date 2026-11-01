<?php
/**
 * Header template.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.16.0
 */
global $theme_dir;
$rb_style_version      = file_exists( get_stylesheet_directory() . '/rb_style.min.css' ) ? filemtime( get_stylesheet_directory() . '/rb_style.min.css' ) : null;
$rb_swiper_css_version = file_exists( get_stylesheet_directory() . '/swiper.min.css' ) ? filemtime( get_stylesheet_directory() . '/swiper.min.css' ) : null;
$rb_swiper_js_version  = file_exists( get_stylesheet_directory() . '/swiper.min.js' ) ? filemtime( get_stylesheet_directory() . '/swiper.min.js' ) : null;
$rb_main_version       = file_exists( get_stylesheet_directory() . '/rb_main.min.js' ) ? filemtime( get_stylesheet_directory() . '/rb_main.min.js' ) : null;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php flatsome_html_classes(); ?>">
	<head>
		<meta name="google-site-verification" content="Q9mUpFlc2vernzqlY19Ca7jHx_mpHxv6SiO-GIwC4NA" />
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<link href="<?php echo esc_url( add_query_arg( 'ver', $rb_style_version, $theme_dir . '/rb_style.min.css' ) ); ?>" rel="stylesheet">
		<link href="<?php echo esc_url( add_query_arg( 'ver', $rb_swiper_css_version, $theme_dir . '/swiper.min.css' ) ); ?>" rel="stylesheet">
		<script src="<?php echo esc_url( add_query_arg( 'ver', $rb_swiper_js_version, $theme_dir . '/swiper.min.js' ) ); ?>"></script>
		<script src="<?php echo esc_url( add_query_arg( 'ver', $rb_main_version, $theme_dir . '/rb_main.min.js' ) ); ?>" defer></script>
		
		<?php wp_head(); ?>

	<body <?php body_class(); ?>>
	<?php get_template_part( 'template-parts/template-header' ); ?>
		<?php 
		/*
		do_action( 'flatsome_after_body_open' ); ?>
		<?php wp_body_open(); ?>

		<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'flatsome' ); ?></a>

		<div id="wrapper">

			<?php do_action( 'flatsome_before_header' ); ?>
			

			<?php do_action( 'flatsome_after_header' ); ?>
			
			
			<script>
window.addEventListener('load', function() {
  const toggleBtn = document.querySelector('.offcanvass_menu_btn');
  const offcanvasMenu = document.querySelector('.offcanvas_menu_wrap');
  const overlay = document.querySelector('.rb_overlay');
  const closeBtn = document.querySelector('.offcanvas_close_btn');

  if (toggleBtn && offcanvasMenu) {
    toggleBtn.addEventListener('click', function(e) {
      e.preventDefault();
      offcanvasMenu.classList.toggle('active');
      overlay.classList.toggle('active');
      document.body.style.overflow = offcanvasMenu.classList.contains('active') ? 'hidden' : '';
    });

    [overlay, closeBtn].forEach(el => {
      el?.addEventListener('click', function() {
        offcanvasMenu.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
      });
    });
  }
});
</script>


			
			
			
			
			
*/ ?>
			<main id="main" class="<?php flatsome_main_classes(); ?>">
				
