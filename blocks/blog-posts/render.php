<?php
/**
 * Block: مقالات مجله (blog-posts)
 * Faithful port of custom-template.php lines 488–536 (get_footer stays in the
 * page template, not the block).
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bp_heading  = get_field( 'heading' ) ?: 'بیش از ۱۰۰ مقاله در مجله تک دوز';
$bp_subtitle = get_field( 'subtitle' ) ?: 'با بیش از ۱۰۰ مقاله تخصصی در مجله تکدوز، تازه‌ترین مطالب درباره پوشاک صنعتی، نکات کاربردی و اخبار روز را با شما به اشتراک می‌گذاریم.';
$bp_count    = (int) ( get_field( 'posts_count' ) ?: 8 );
$bp_more_url = get_field( 'show_more_link' ) ?: '/blog';

if ( $is_preview ) {
	takdooz_block_preview_card( $bp_heading ?: 'مقالات مجله', sprintf( '%d مقاله', $bp_count ) );
	return;
}

$posts_arg = array(
	'post_type'      => 'post',
	'no_found_rows'  => true,
	'posts_per_page' => $bp_count,
	'orderby'        => 'date',
	'order'          => 'DESC',
);
$posts = new WP_Query( $posts_arg );
if ( $posts->have_posts() ) { ?>
<div class="takdooz_posts_wrap">
	<div class="container">
		<div class="row">
			<div class="title_wrap">
				<div class="title_wrap_text">
					<h2><?php echo esc_html( $bp_heading ); ?></h2>
					<?php if ( $bp_subtitle ) { ?><p><?php echo esc_html( $bp_subtitle ); ?></p><?php } ?>
				</div>
				<a href="<?php echo esc_url( $bp_more_url ); ?>" class="show_more_btn show_more_btn--top">مشاهده همه پست ها <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="var(--main-color)" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/></svg>
				</a>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<div class="swiper rb_post_swiper">
					<div class="swiper-wrapper">
						<?php
						while ( $posts->have_posts() ) {
							$posts->the_post(); ?>
						<div class="swiper-slide">
							<?php echo get_template_part( 'template-parts/content', 'post-box' ); ?>
						</div>
						<?php } ?>
					</div>
					<div class="swiper-pagination"></div>
				</div>
			</div>
			<a href="<?php echo esc_url( $bp_more_url ); ?>" class="show_more_btn show_more_btn--bottom">مشاهده همه پست ها <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="var(--main-color)" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/></svg>
			</a>
		</div>
	</div>
</div>
<?php } wp_reset_postdata(); ?>
