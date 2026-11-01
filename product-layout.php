<?php
/**
 * Template Name: قالب صفحه محصول (بلوک‌ها)
 *
 * چیدمان بلوک‌های صفحه محصول در این برگه ساخته می‌شود.
 * این برگه فقط برای ویرایش است؛ روی سایت هر محصول با همین چیدمان نمایش داده می‌شود.
 *
 * @package flatsome-child
 */

get_header();
?>
<div class="container" style="padding:30px 15px;">
	<?php
	while ( have_posts() ) {
		the_post();
		the_content();
	}
	?>
</div>
<?php
get_footer();
