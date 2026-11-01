<?php
/**
 * Block: نظرات مشتریان (testimonials)
 * Faithful port of custom-template.php lines 389–486.
 * Hardening: mobile author field $commenter_title -> $commenter_semat (FIXED).
 * Still deferred: apply_filters('the_content') in-loop, hardcoded placeholders.
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ts_heading  = get_field( 'heading' ) ?: 'مشتریان ما چه میگویند؟';
$ts_subtitle = get_field( 'subtitle' ) ?: 'مشتریان ما همواره از کیفیت محصولات و خدمات سریع تکدوز رضایت داشته اند. رضایت شما، افتخار ماست.';
$ts_count    = (int) ( get_field( 'count' ) ?: 8 );

if ( $is_preview ) {
	takdooz_block_preview_card( $ts_heading ?: 'نظرات مشتریان', sprintf( 'تا %d نظر', $ts_count ) );
	return;
}

$comments_arg = array(
	'post_type'      => 'custom_comments',
	'posts_per_page' => $ts_count,
	'fields'         => 'ids',
);
$comments = get_posts( $comments_arg );
if ( ! empty( $comments ) ) { ?>
<div class="what_say_customers_wrap">
	<div class="container">
		<div class="row">
			<div class="center_title_wrap_text">
				<h2><?php echo esc_html( $ts_heading ); ?></h2>
				<?php if ( $ts_subtitle ) { ?><p><?php echo esc_html( $ts_subtitle ); ?></p><?php } ?>
			</div>
		</div>
		<div class="row align-items-center">
			<div class="col-12">
				<div class="swiper rb_home_comments_swiper">
					<div class="swiper-wrapper">
						<?php
						foreach ( $comments as $comment_id ) {
							$comment_title         = get_the_title( $comment_id );
							$comment_semat         = get_post_meta( $comment_id, 'customer_semat', 1 );
							$comment_rate          = get_post_meta( $comment_id, 'customer_rate', 1 );
							$content_post          = get_post( $comment_id );
							$content               = $content_post->post_content;
							// Was apply_filters('the_content', ...) — ran the full
							// content filter chain (shortcodes/oEmbed/wpautop/blocks)
							// 8x per page just to trim 40 words. Cheap equivalent:
							$comment_text          = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $content ) ), 40, '' );
							$thumb_id              = get_post_thumbnail_id( $comment_id );
							$img_src               = $thumb_id ? wp_get_attachment_image_src( $thumb_id, 'post-thumbnail' ) : false;
							$commenter_image       = $img_src ? $img_src[0] : ( function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src() : '' );
							$commenter_img_dim     = $img_src ? sprintf( ' width="%d" height="%d"', (int) $img_src[1], (int) $img_src[2] ) : '';
							$commenter_name        = $comment_title ? $comment_title : 'محمدرضا منفرد';
							$commenter_semat       = $comment_semat ? $comment_semat : 'مدیر مجموعه رستوران های منفرد';
							$commenter_rate_count  = $comment_rate ? $comment_rate : '5';
							?>
						<div class="swiper-slide">
							<div class="comment_box">
								<?php if ( ! wp_is_mobile() ) { ?>
								<div class="comment_text_box">
									<div class="comment_text">
										<?php echo esc_html( $comment_text ); ?>
									</div>
									<div class="comment_author_detail">
										<h4 class="m-0"><?php echo esc_html( $commenter_name ); ?></h4>
										<span><?php echo esc_html( $commenter_semat ); ?></span>
										<div class="comment_rating">
											<?php for ( $i = 1; $i <= $commenter_rate_count; $i++ ) { ?>
											<span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gold" class="bi bi-star-fill" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg></span>
											<?php } ?>
										</div>
									</div>
								</div>
								<div class="comment_image_box">
									<img src="<?php echo esc_url( $commenter_image ); ?>"<?php echo $commenter_img_dim; // phpcs:ignore ?> loading="lazy" decoding="async" alt="<?php echo esc_attr( $commenter_name ); ?>">
								</div>

								<?php } else { ?>
								<div class="rb_flex_wrap">
									<div class="comment_image_box">
										<img src="<?php echo esc_url( $commenter_image ); ?>"<?php echo $commenter_img_dim; // phpcs:ignore ?> loading="lazy" decoding="async" alt="<?php echo esc_attr( $commenter_name ); ?>">
									</div>
									<div class="comment_text_box">
										<div class="comment_author_detail">
											<h4 class="m-0"><?php echo esc_html( $commenter_name ); ?></h4>
											<span><?php echo esc_html( $commenter_semat ); ?></span>
											<div class="comment_rating">
												<?php for ( $i = 1; $i <= $commenter_rate_count; $i++ ) { ?>
												<span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gold" class="bi bi-star-fill" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg></span>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
								<div class="comment_text">
									<?php echo esc_html( $comment_text ); ?>
								</div>

								<?php } ?>
							</div>
						</div>
						<?php } ?>
					</div>
					<?php if ( ! wp_is_mobile() ) { ?>
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
<?php } wp_reset_postdata(); ?>
