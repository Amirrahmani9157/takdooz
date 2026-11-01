<?php
/**
 * Block: بنر دسته‌بندی (category-banner)
 * Faithful port of custom-template.php lines 24–27 (data) + 156–164 (markup).
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Prefer per-block fields (editor-configurable); fall back to the global
// ACF Options values so existing content keeps rendering until an editor
// fills the block fields.
$banner_image     = get_field( 'banner_image' ) ?: get_field( 'banner_image', 'options' );
$banner_title     = get_field( 'banner_title' ) ?: get_field( 'banner_title', 'options' );
$banner_sub_title = get_field( 'banner_sub_title' ) ?: get_field( 'banner_sub_title', 'options' );
$banner_link      = get_field( 'banner_link' ) ?: get_field( 'banner_link', 'options' );

if ( empty( $banner_image ) && empty( $banner_title ) ) {
	if ( $is_preview ) {
		echo '<p style="padding:24px;text-align:center;color:#888;">بنر دسته‌بندی: تصویر و عنوان را از تنظیمات این بلوک وارد کنید.</p>';
	}
	return;
}

// Resolve the image (id | url | array) to a URL plus real dimensions so we
// can reserve layout space (better CLS) regardless of where it came from.
$banner_url = '';
$banner_w   = 0;
$banner_h   = 0;
if ( is_array( $banner_image ) ) {
	$banner_url = $banner_image['url'] ?? '';
	$banner_w   = (int) ( $banner_image['width'] ?? 0 );
	$banner_h   = (int) ( $banner_image['height'] ?? 0 );
} elseif ( is_numeric( $banner_image ) ) {
	$src = wp_get_attachment_image_src( (int) $banner_image, 'full' );
	if ( $src ) {
		list( $banner_url, $banner_w, $banner_h ) = $src;
	}
} elseif ( is_string( $banner_image ) ) {
	$banner_url = $banner_image;
	$att_id     = attachment_url_to_postid( $banner_image );
	if ( $att_id ) {
		$src = wp_get_attachment_image_src( $att_id, 'full' );
		if ( $src ) {
			$banner_w = (int) $src[1];
			$banner_h = (int) $src[2];
		}
	}
}

$banner_dim = ( $banner_w && $banner_h ) ? sprintf( ' width="%d" height="%d"', $banner_w, $banner_h ) : '';
?>

<div class="cat_banner_box my-4 my-lg-5">
	<img src="<?php echo esc_url( $banner_url ); ?>"<?php echo $banner_dim; // phpcs:ignore ?> loading="lazy" decoding="async" alt="<?php echo esc_attr( $banner_title ); ?>">
	<div class="cat_banner_box_title">
		<span><?php echo esc_html( $banner_title ); ?></span>
		<span><?php echo esc_html( $banner_sub_title ); ?></span>
		<?php if ( $banner_link ) : ?>
		<a href="<?php echo esc_url( $banner_link ); ?>">مشاهده و خرید <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#fff" class="bi bi-arrow-left-short" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/></svg></a>
		<?php endif; ?>
	</div>
</div>
