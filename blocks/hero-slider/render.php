<?php
/**
 * Block: اسلایدر اصلی (hero-slider)
 *
 * Slides are managed inline in the block via the ACF "slides" repeater.
 * If the repeater is empty, the block falls back to the legacy `slider` CPT
 * so existing content keeps rendering until it is migrated.
 *
 * Images use a <picture> (mobile <source> + desktop <img>) so the markup is
 * identical for every device and safe for full-page caching. The first slide
 * is eager + high priority (it is the LCP); the rest lazy-load.
 *
 * @package flatsome-child
 *
 * @var array $block      ACF block settings.
 * @var bool  $is_preview True when rendered in the editor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalize an ACF image value (id | url | array) to a URL string.
 */
$takdooz_img_url = static function ( $value, $size = 'full' ) {
	if ( is_array( $value ) ) {
		return $value['url'] ?? '';
	}
	if ( is_numeric( $value ) ) {
		return (string) wp_get_attachment_image_url( (int) $value, $size );
	}
	return is_string( $value ) ? $value : '';
};

/**
 * Render one slide.
 *
 * @param array $slide {
 *     @type string $desktop_url Desktop image URL.
 *     @type array  $desktop_dim [width,height] or [].
 *     @type string $mobile_url  Mobile image URL (optional).
 *     @type string $link        Slide link.
 *     @type string $content     Slide HTML content.
 *     @type string $alt         Image alt text.
 *     @type bool   $first       Whether this is the first slide.
 * }
 */
$takdooz_render_slide = static function ( $slide ) {
	$loading = $slide['first'] ? ' loading="eager" fetchpriority="high"' : ' loading="lazy"';
	$dim     = '';
	if ( ! empty( $slide['desktop_dim'] ) ) {
		$dim = sprintf( ' width="%d" height="%d"', (int) $slide['desktop_dim'][0], (int) $slide['desktop_dim'][1] );
	}
	?>
	<div class="swiper-slide">
		<div class="container">
			<a aria-label="<?php echo esc_attr( $slide['alt'] ); ?>" href="<?php echo esc_url( $slide['link'] ); ?>" class="rb_box_link"></a>
			<div class="row align-items-center">
				<div class="col-12 col-lg-6">
					<picture>
						<?php if ( ! empty( $slide['mobile_url'] ) ) : ?>
						<source media="(max-width: 991px)" srcset="<?php echo esc_url( $slide['mobile_url'] ); ?>">
						<?php endif; ?>
						<img src="<?php echo esc_url( $slide['desktop_url'] ); ?>"<?php echo $dim . $loading; // phpcs:ignore ?> alt="<?php echo esc_attr( $slide['alt'] ); ?>" class="slider_image">
					</picture>
				</div>
				<div class="col-12 col-lg-6">
					<div class="slider_content">
						<?php echo wp_kses_post( $slide['content'] ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
};

// Build the list of slides from the repeater, falling back to the Slider CPT.
$slides = array();

if ( have_rows( 'slides' ) ) {
	$i = 0;
	while ( have_rows( 'slides' ) ) {
		the_row();
		$desktop_id  = get_sub_field( 'desktop_image' );
		$desktop     = $desktop_id ? wp_get_attachment_image_src( (int) $desktop_id, 'full' ) : false;
		$content     = (string) get_sub_field( 'content' );
		// Prefer the first real heading (h1-h3) for the alt; fall back to an excerpt.
		$alt = '';
		if ( preg_match( '/<h[1-3][^>]*>(.*?)<\/h[1-3]>/is', $content, $heading ) ) {
			$alt = trim( wp_strip_all_tags( $heading[1] ) );
		}
		if ( '' === $alt ) {
			$alt = trim( wp_html_excerpt( wp_strip_all_tags( $content ), 80 ) );
		}
		$slides[]    = array(
			'desktop_url' => $desktop ? $desktop[0] : $takdooz_img_url( $desktop_id ),
			'desktop_dim' => $desktop ? array( $desktop[1], $desktop[2] ) : array(),
			'mobile_url'  => $takdooz_img_url( get_sub_field( 'mobile_image' ) ),
			'link'        => (string) get_sub_field( 'link' ),
			'content'     => $content,
			'alt'         => $alt ? $alt : 'تکدوز',
			'first'       => ( 0 === $i ),
		);
		$i++;
	}
} else {
	$cpt_slides = get_posts( array(
		'post_type'      => 'slider',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	) );
	foreach ( $cpt_slides as $index => $slider ) {
		$slider_id = $slider->ID;
		$thumb_id  = get_post_thumbnail_id( $slider_id );
		$desktop   = $thumb_id ? wp_get_attachment_image_src( $thumb_id, 'full' ) : false;
		$slides[]  = array(
			'desktop_url' => $desktop ? $desktop[0] : get_the_post_thumbnail_url( $slider_id ),
			'desktop_dim' => $desktop ? array( $desktop[1], $desktop[2] ) : array(),
			'mobile_url'  => $takdooz_img_url( get_field( 'mobile_slide', $slider_id ) ),
			'link'        => (string) get_field( 'slider_link', $slider_id ),
			'content'     => get_post( $slider_id )->post_content,
			'alt'         => get_the_title( $slider_id ),
			'first'       => ( 0 === $index ),
		);
	}
}

if ( empty( $slides ) ) {
	if ( $is_preview ) {
		echo '<p style="padding:24px;text-align:center;color:#888;">هیچ اسلایدی وجود ندارد. از دکمه «افزودن اسلاید» در تنظیمات این بلوک استفاده کنید.</p>';
	}
	return;
}
?>
<div class="main_slider_wrap">
	<div class="swiper rb_slider_swiper">
		<div class="swiper-wrapper">
			<?php
			foreach ( $slides as $slide ) {
				$takdooz_render_slide( $slide );
			}
			?>
		</div>
		<div class="slider-swiper-pagination"></div>
	</div>
</div>
