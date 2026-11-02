<?php
/**
 * Takdooz — لندینگ صفحه‌ی دسته‌بندی «پیش بند» (apron)
 * ---------------------------------------------------------------
 * نسخه: v1 (برای تست روی stage.takdooz.ir)
 *
 * این فایل چه می‌کند؟
 *  - فقط روی آرشیو دسته‌ی «پیش بند» (slug: apron) اجرا می‌شود.
 *  - یک هیروی تمیز + نوار فیلتر (آکاردئونی) بالای گرید محصولات اضافه می‌کند.
 *  - متن سئوی فعلی دسته را دست نمی‌زند (رتبه حفظ می‌شود).
 *  - فیلترها بر اساس «اسم محصول» و سمت کلاینت کار می‌کنند؛ هیچ دسته/تگی
 *    ساخته یا تغییر داده نمی‌شود، پس تکسونومی و سئو کاملاً دست‌نخورده می‌ماند.
 *  - گل‌فروشی و آرایشگری چون دسته‌ی مستقل و سئوشده هستند، به‌جای فیلتر،
 *    لینک مستقیم به همان صفحه دارند (انتقال اعتبار لینک، بدون محتوای تکراری).
 *
 * نصب:
 *  1) این فایل را در پوشه‌ی تم فرزند بگذارید:  flatsome-child/inc/apron-landing.php
 *  2) در انتهای flatsome-child/functions.php این خط را اضافه کنید:
 *       require get_stylesheet_directory() . '/inc/apron-landing.php';
 *  (اگر با Code Snippets کار می‌کنید، کل محتوای داخل تگ php را به‌جز خط require
 *   می‌توانید مستقیم به‌عنوان یک اسنیپت PHP وارد کنید.)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // جلوگیری از دسترسی مستقیم
}

/**
 * slug دسته‌ای که لندینگ باید روی آن نمایش داده شود.
 */
function tkd_apron_slug() {
	return 'apron';
}

/**
 * آیا الان روی صفحه‌ی آرشیو دسته‌ی «پیش بند» هستیم؟
 */
function tkd_is_apron_archive() {
	return ( function_exists( 'is_product_category' ) && is_product_category( tkd_apron_slug() ) );
}

/**
 * خروجی لندینگ را بعد از توضیحات دسته (متن سئو) و قبل از گرید محصولات چاپ می‌کند.
 * اولویت 20 یعنی بعد از متن سئوی ووکامرس اجرا می‌شود؛ پس آن را حذف نمی‌کند.
 */
add_action( 'woocommerce_archive_description', 'tkd_apron_landing_output', 20 );
function tkd_apron_landing_output() {

	if ( ! tkd_is_apron_archive() ) {
		return;
	}

	// آدرس دسته‌های سئوشده‌ی مستقل (برای لینک «مشاهده همه»)
	$florist_url = home_url( '/product-category/florist-apron/' );
	$barber_url  = home_url( '/product-category/barber-apron/' );
	$barista_url = home_url( '/product-tag/barista-apron/' );

	/**
	 * فیلترها
	 * key    : شناسه‌ی داخلی
	 * label  : متن دکمه
	 * type   : filter = فیلتر سمت کلاینت بر اساس اسم | link = لینک به صفحه‌ی دیگر
	 * match  : کلمه‌هایی که باید در اسم محصول باشد (برای type=filter)
	 * url    : آدرس مقصد (برای type=link)
	 */
	$filters = array(
		array( 'key' => 'all',      'label' => 'همه مدل‌ها', 'type' => 'filter', 'match' => array() ),
		array( 'key' => 'barista',  'label' => 'باریستا',    'type' => 'filter', 'match' => array( 'باریستا' ) ),
		array( 'key' => 'kamari',   'label' => 'کمری',       'type' => 'filter', 'match' => array( 'کمری' ) ),
		array( 'key' => 'waterproof','label'=> 'ضدآب',       'type' => 'filter', 'match' => array( 'ضدآب', 'ضد آب', 'ضدّاب' ) ),
		array( 'key' => 'double',   'label' => 'دوطرفه',     'type' => 'filter', 'match' => array( 'دوطرفه', 'دو طرفه', 'کوبلر' ) ),
		array( 'key' => 'four',     'label' => '۴ حالته',    'type' => 'filter', 'match' => array( 'حالته' ) ),
		array( 'key' => 'florist',  'label' => 'گل‌فروشی',   'type' => 'link',   'url' => $florist_url ),
		array( 'key' => 'barber',   'label' => 'آرایشگری',   'type' => 'link',   'url' => $barber_url ),
	);

	?>
	<div class="tkl">

		<!-- هیرو -->
		<section class="tkl-hero">
			<div class="tkl-hero-in">
				<span class="tkl-eye">کیفیت در جزئیات، حرفه‌ای در عملکرد</span>
				<h2 class="tkl-h1">پیش‌بندهای حرفه‌ای <em>تکدوز</em></h2>
				<p class="tkl-sub">مناسب آشپزی، باریستا، گل‌فروشی و تمام مشاغل؛ با بهترین پارچه، دوخت و طراحی کاربردی و امکان چاپ و گلدوزی لوگو.</p>
				<div class="tkl-trust">
					<span>🚚 ارسال سریع</span>
					<span>🧵 دوخت صنعتی</span>
					<span>🖊️ چاپ لوگو</span>
					<span>✅ ضمانت کیفیت</span>
				</div>
			</div>
		</section>

		<!-- نوار فیلتر -->
		<nav class="tkl-filters" aria-label="فیلتر مدل پیش‌بند">
			<?php foreach ( $filters as $f ) : ?>
				<?php if ( 'link' === $f['type'] ) : ?>
					<a class="tkl-chip tkl-chip-link" href="<?php echo esc_url( $f['url'] ); ?>">
						<?php echo esc_html( $f['label'] ); ?> ↗
					</a>
				<?php else : ?>
					<button type="button"
						class="tkl-chip<?php echo ( 'all' === $f['key'] ) ? ' is-active' : ''; ?>"
						data-match="<?php echo esc_attr( implode( '|', $f['match'] ) ); ?>">
						<?php echo esc_html( $f['label'] ); ?>
					</button>
				<?php endif; ?>
			<?php endforeach; ?>
		</nav>

		<p class="tkl-note" data-tkl-count></p>
	</div>

	<style>
	/* همه‌ی استایل‌ها زیر .tkl محدود شده تا با قالب تداخل نکند */
	.tkl{--navy:#26324f;--gold:#e0a63a;--muted:#7b8192;--line:#e9e2d4;--soft:#f5f1e9;font-family:inherit;direction:rtl;margin:18px 0 26px}
	.tkl-hero{background:linear-gradient(180deg,#faf7f1,#f3ecde);border:1px solid var(--line);border-radius:20px;padding:30px 24px;text-align:center}
	.tkl-eye{color:#9a6c12;font-weight:800;font-size:13px}
	.tkl-h1{color:var(--navy);font-weight:900;font-size:30px;margin:10px 0 0;line-height:1.4}
	.tkl-h1 em{color:var(--gold);font-style:normal}
	.tkl-sub{color:var(--muted);font-size:15px;max-width:560px;margin:12px auto 0;line-height:1.9}
	.tkl-trust{display:flex;flex-wrap:wrap;gap:10px;justify-content:center;margin-top:18px}
	.tkl-trust span{background:#fff;border:1px solid var(--line);border-radius:999px;padding:7px 14px;font-size:12.5px;color:var(--navy);font-weight:600}
	.tkl-filters{display:flex;flex-wrap:wrap;gap:9px;margin-top:20px}
	.tkl-chip{background:#fff;border:1.5px solid var(--line);color:var(--navy);font-family:inherit;font-weight:700;font-size:13.5px;padding:10px 18px;border-radius:999px;cursor:pointer;transition:.15s}
	.tkl-chip:hover{border-color:var(--navy)}
	.tkl-chip.is-active{background:var(--navy);color:#fff;border-color:var(--navy)}
	.tkl-chip-link{text-decoration:none;background:#fff4e0;border-color:#f0d9a8;color:#8a5b14}
	.tkl-chip-link:hover{background:#ffe9c4}
	.tkl-note{color:var(--muted);font-size:13px;margin:12px 0 0;min-height:1em}
	@media (max-width:600px){.tkl-h1{font-size:24px}.tkl-sub{font-size:14px}}
	</style>

	<script>
	(function(){
		var root = document.querySelector('.tkl');
		if(!root) return;

		// گرید محصولات ووکامرس در فلت‌سام معمولاً این انتخابگرهاست:
		var grid  = document.querySelector('ul.products, .products.row, .shop-container .products');
		var cards = grid ? grid.querySelectorAll('li.product, .product-small.col, .product') : [];
		var chips = root.querySelectorAll('.tkl-chip[data-match]');
		var note  = root.querySelector('[data-tkl-count]');

		function titleOf(card){
			var t = card.querySelector('.product-title, .woocommerce-loop-product__title, h2, h3, h4, a');
			return t ? (t.textContent || '').trim() : '';
		}

		function applyFilter(matchStr){
			var terms = matchStr ? matchStr.split('|').filter(Boolean) : [];
			var shown = 0;
			cards.forEach(function(card){
				var title = titleOf(card);
				var ok = terms.length === 0 || terms.some(function(term){ return title.indexOf(term) !== -1; });
				card.style.display = ok ? '' : 'none';
				if(ok) shown++;
			});
			if(note){
				note.textContent = terms.length
					? ('نمایش ' + shown + ' محصول در این صفحه')
					: '';
			}
			// اگر فیلتر در این صفحه نتیجه‌ای نداشت، به کاربر بگو ممکن است در صفحات بعدی باشد
			if(terms.length && shown === 0 && note){
				note.textContent = 'در این صفحه موردی یافت نشد — ممکن است در صفحات بعدی باشد.';
			}
		}

		chips.forEach(function(chip){
			chip.addEventListener('click', function(){
				chips.forEach(function(c){ c.classList.remove('is-active'); });
				chip.classList.add('is-active');
				applyFilter(chip.getAttribute('data-match'));
				// اسکرول نرم به ابتدای گرید
				if(grid){ grid.scrollIntoView({behavior:'smooth', block:'start'}); }
			});
		});
	})();
	</script>
	<?php
}
