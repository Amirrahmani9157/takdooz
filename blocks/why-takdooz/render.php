<?php
/**
 * Block: چرا تکدوز (why-takdooz)
 * Faithful port of custom-template.php lines 315–326.
 *
 * @package flatsome-child
 * @var array $block
 * @var bool  $is_preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wt_video  = get_field( 'why_takdooz_video' ) ?: get_field( 'why_takdooz_video', 'options' );
$wt_poster = get_field( 'why_takdooz_image' ) ?: get_field( 'why_takdooz_image', 'options' );
$wt_text   = get_field( 'why_takdooz_text' ) ?: get_field( 'why_takdooz_text', 'options' );

if ( empty( $wt_video ) && empty( $wt_text ) ) {
	if ( $is_preview ) {
		echo '<p style="padding:24px;text-align:center;color:#888;">چرا تکدوز: ویدیو و متن را از تنظیمات این بلوک وارد کنید.</p>';
	}
	return;
}

// Resolve poster dimensions so the <video> reserves space (better CLS).
$poster_dim = '';
if ( $wt_poster && is_string( $wt_poster ) ) {
	$poster_id = attachment_url_to_postid( $wt_poster );
	if ( $poster_id ) {
		$src = wp_get_attachment_image_src( $poster_id, 'full' );
		if ( $src ) {
			$poster_dim = sprintf( ' width="%d" height="%d"', (int) $src[1], (int) $src[2] );
		}
	}
}
?>

<div class="why_takdooz_wrap">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-12 col-lg-6">
				<?php if ( $wt_video ) : ?>
				<video poster="<?php echo esc_url( $wt_poster ); ?>" src="<?php echo esc_url( $wt_video ); ?>"<?php echo $poster_dim; // phpcs:ignore ?> preload="none" playsinline controls aria-label="ویدیو معرفی تکدوز"></video>
				<?php elseif ( $wt_poster ) : ?>
				<img src="<?php echo esc_url( $wt_poster ); ?>"<?php echo $poster_dim; // phpcs:ignore ?> loading="lazy" decoding="async" alt="چرا تکدوز">
				<?php endif; ?>
			</div>
			<div class="col-12 col-lg-6">
				<?php echo wp_kses_post( $wt_text ); ?>
			</div>
		</div>
	</div>
</div>
