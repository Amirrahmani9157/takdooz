<?php
/**
 * Template Name: قالب صفحه اصلی (بلوک‌ها)
 *
 * Plan C homepage shell. The original custom-template.php is left untouched
 * and serves only as the reference for section markup and order.
 *
 * The page assigned to this template composes the homepage from the
 * "بلوک‌های صفحه اصلی تکدوز" ACF blocks (see inc/blocks.php) in this order
 * (mirrors custom-template.php):
 *
 *   1. اسلایدر اصلی            (hero-slider)        ref. lines 35–68
 *   2. پرفروش‌ترین محصولات      (bestseller-tabs)    ref. lines 70–153
 *   3. بنر دسته‌بندی            (category-banner)    ref. lines 156–164
 *   4. پیشنهاد ویژه + تایمر     (offer-products)     ref. lines 166–258
 *   5. جدیدترین محصولات         (newest-products)    ref. lines 260–312
 *   6. چرا تکدوز               (why-takdooz)        ref. lines 315–326
 *   7. دسته‌بندی منتخب          (featured-category)  ref. lines 328–387
 *   8. نظرات مشتریان            (testimonials)       ref. lines 389–486
 *   9. مقالات مجله             (blog-posts)         ref. lines 488–536
 *
 * @package flatsome-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	the_content();
endwhile;

get_footer();
