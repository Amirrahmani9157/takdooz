/* صفحه محصول تکدوز — منتقل‌شده از اسنیپت ۱۰ «۲» (خطوط ۷ تا ۲۴۲) */
jQuery(function ($) {
  var $root = $('.tkd-pdp');
  if (!$root.length) { return; }
  var $card = $root.find('.tkd-card');
  var fa = function (n) { return Number(n).toLocaleString('fa-IR'); };

  // فرم باید فایل بفرستد
  $root.find('form.cart').attr('enctype', 'multipart/form-data');

  var COLORS = {
    'مشکی': '#111827', 'سفید': '#ffffff', 'آبی': '#3b82f6', 'سرمه‌ای': '#263a65', 'سرمه ای': '#263a65',
    'بنفش': '#a855f7', 'قرمز': '#ef4444', 'سبز': '#22c55e', 'زرد': '#f2c200', 'نارنجی': '#f97316',
    'صورتی': '#ec4899', 'طوسی': '#7d838a', 'خاکستری': '#9ca3af', 'قهوه‌ای': '#7a4a2a', 'قهوه ای': '#7a4a2a',
    'کرم': '#f1e7d0', 'بژ': '#d9c7a0', 'زرشکی': '#7f1d1d', 'سبز زیتونی': '#6b7d4a', 'آبی جین': '#2f5d9a',
    'مشکی و طوسی': 'linear-gradient(90deg,#7d838a 0 28%,#0e1211 28% 72%,#7d838a 72% 100%)'
  };

  /* ---------- قیمت ---------- */
  var base0 = parseFloat($card.attr('data-price')) || 0;
  var base = base0;
  var $final = $root.find('.tkd-final');
  var fromLabel = $final.find('small').text();
  var picked = false;
  function svcSum() {
    var s = 0;
    $root.find('.tkd-svc-wrap input[type=radio]:checked').each(function () { s += parseInt($(this).attr('data-price'), 10) || 0; });
    return s;
  }
  function paint() {
    var q = parseInt($root.find('input.qty').first().val(), 10) || 1;
    var total = (base + svcSum()) * q;
    $final.find('b').text(fa(total));
    $final.find('small').text(picked || $card.attr('data-variable') !== '1' ? 'قیمت نهایی' : fromLabel);
    $root.find('.tkd-sticky-p').text(fa(total));
  }

  /* ---------- دکمه‌های رنگ و سایز ---------- */
  function buildPills($form) {
    $form.find('table.variations tr').each(function () {
      var $td = $(this).find('td.value');
      var $sel = $td.find('select');
      if (!$sel.length || $td.data('tkd')) { return; }
      $td.data('tkd', 1).addClass('tkd-has-pills');
      var label = $(this).find('th.label').text() + ' ' + ($sel.attr('name') || '');
      var isColor = /رنگ|colou?r/i.test(label);
      var $wrap = $('<div class="tkd-opts" role="group"></div>').insertAfter($sel);
      $sel.find('option').each(function () {
        if (!this.value) { return; }
        var t = $.trim($(this).text());
        var $b = $('<button type="button" class="tkd-opt" aria-pressed="false"></button>').attr('data-v', this.value);
        if (isColor && COLORS[t]) { $b.append($('<span class="tkd-dotc"></span>').css('background', COLORS[t])); }
        $b.append(document.createTextNode(t));
        $wrap.append($b);
      });
      function sync() {
        var cur = $sel.val();
        $wrap.find('.tkd-opt').each(function () {
          var v = $(this).attr('data-v');
          var has = $sel.find('option').filter(function () { return this.value === v; }).length > 0;
          $(this).toggleClass('is-off', !has).attr('aria-pressed', cur === v ? 'true' : 'false');
        });
      }
      $wrap.on('click', '.tkd-opt', function () {
        if ($(this).hasClass('is-off')) { return; }
        $sel.val($(this).attr('data-v')).trigger('change');
        $td.closest('tr').removeClass('tkd-need');
        sync();
      });
      $sel.on('change', sync);
      $form.on('woocommerce_update_variation_values reset_data', function () { setTimeout(sync, 0); });
      sync();
    });
  }
  $('form.variations_form').each(function () { buildPills($(this)); });

  // اگر سایز انتخاب نشده، به جای پیام پیش‌فرض، ردیف را هایلایت کن
  $root.on('click', '.single_add_to_cart_button', function (e) {
    if (!$(this).is('.disabled, .wc-variation-selection-needed')) { return; }
    e.preventDefault(); e.stopImmediatePropagation();
    var $tr = $root.find('table.variations tr').filter(function () { return !$(this).find('select').val(); }).first();
    $tr.removeClass('tkd-need'); void $tr[0].offsetWidth; $tr.addClass('tkd-need');
    $('html,body').animate({ scrollTop: $tr.offset().top - 120 }, 250);
  });

  /* ---------- گالری ---------- */
    // فقط خود نوار را اسکرول می‌کند، نه کل صفحه را
  function centerIn(box, el) {
    if (!box || !el) { return; }
    var b = box.getBoundingClientRect(), r = el.getBoundingClientRect();
    box.scrollBy({ left: (r.left + r.width / 2) - (b.left + b.width / 2), behavior: 'smooth' });
  }
  var $main = $('#tkd-main');
  var $ths = $root.find('.tkd-th');
  var cur = 0;
  function showImg(src, i) {
    $main.attr('src', src).removeAttr('srcset');
    $ths.removeClass('on');
    if (i !== null && i >= 0) {
      cur = i;
      var $t = $ths.eq(i).addClass('on');
            centerIn($root.find('.tkd-thumbs')[0], $t[0]);
      $root.find('.tkd-count b').text(fa(i + 1));
    }
  }
  function go(step) {
    if (!$ths.length) { return; }
    var i = (cur + step + $ths.length) % $ths.length;
    showImg($ths.eq(i).attr('data-full'), i);
  }
  $ths.on('click', function () { showImg($(this).attr('data-full'), $ths.index(this)); });
    $root.on('click', '.tkd-nav', function () { go($(this).attr('data-go') === 'next' ? 1 : -1); });

  // کشیدن انگشت روی عکس اصلی
  var sx = null, sy = null;
  $main.on('touchstart', function (e) { var t = e.originalEvent.touches[0]; sx = t.clientX; sy = t.clientY; });
  $main.on('touchend', function (e) {
    if (sx === null) { return; }
    var t = e.originalEvent.changedTouches[0], dx = t.clientX - sx, dy = t.clientY - sy;
    sx = null;
    if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) { go(dx > 0 ? 1 : -1); $main.data('swiped', 1); }
  });

  $('form.variations_form').on('found_variation', function (e, v) {
    picked = true;
    base = parseFloat(v.display_price) || base0;
    if (v.image && (v.image.full_src || v.image.src)) { showImg(v.image.full_src || v.image.src, -1); }
    paint();
  }).on('reset_data', function () {
    picked = false; base = base0;
    if ($ths.length) { showImg($ths.eq(0).attr('data-full'), 0); }
    paint();
  });

  /* ---------- چاپ و گلدوزی + آپلود ---------- */
  var $up = $root.find('.tkd-up');
  var $file = $up.find('input[type=file]');
  function syncSvc() {
    var need = $root.find('.tkd-svc-wrap input[type=radio]:checked').filter(function () { return this.value !== 'none'; }).length > 0;
    $up.prop('hidden', !need);
    $file.prop('required', need);
    paint();
  }
  $root.on('change', '.tkd-svc-wrap input[type=radio]', syncSvc);
  $file.on('change', function () {
    var f = this.files && this.files[0];
    var $t = $up.find('.tkd-up-t b');
    if (!f) { $t.text('فایل لوگو را انتخاب کنید'); $up.removeClass('has-file'); return; }
    if (f.size > 10 * 1024 * 1024) { alert('حجم فایل بیشتر از ۱۰ مگابایت است.'); this.value = ''; $t.text('فایل لوگو را انتخاب کنید'); $up.removeClass('has-file'); return; }
    $t.text(f.name); $up.addClass('has-file').removeClass('tkd-need');
  });
  $root.on('submit', 'form.cart', function (e) {
    if ($file.prop('required') && !($file[0].files && $file[0].files.length)) {
      e.preventDefault();
      $up.removeClass('tkd-need'); void $up[0].offsetWidth; $up.addClass('tkd-need');
      $('html,body').animate({ scrollTop: $up.offset().top - 140 }, 250);
    }
  });
  syncSvc();

  $root.on('change input', 'input.qty', function () { setTimeout(paint, 0); });
  $root.on('click', '.quantity .plus, .quantity .minus, .quantity .ux-quantity__button', function () { setTimeout(paint, 30); });
  paint();

  /* ---------- بزرگ‌نمایی ---------- */
  var lb = document.getElementById('tkd-lb');
  if (lb && lb.showModal) {
    $main.on('click', function () {
      if ($main.data('swiped')) { $main.data('swiped', 0); return; }
      $('#tkd-lb-img').attr('src', $main.attr('src')); lb.showModal();
    });
    $root.find('.tkd-zoom').on('click', function () { $('#tkd-lb-img').attr('src', $main.attr('src')); lb.showModal(); });
    $(lb).on('click', function (e) { if (e.target === lb || $(e.target).is('.tkd-lb-in, .tkd-lb-x')) { lb.close(); } });
  }

  /* ---------- تب‌ها ---------- */
  function reviewForm() {
    var w = document.getElementById('review_form_wrapper');
    if (!w || w.getAttribute('data-tkd')) { return; }
    w.setAttribute('data-tkd', '1');
    w.classList.add('tkd-collapsed');
    var b = document.createElement('button');
    b.type = 'button'; b.className = 'tkd-rev-btn'; b.textContent = 'نوشتن دیدگاه';
    b.addEventListener('click', function () {
      var c = w.classList.toggle('tkd-collapsed');
      b.textContent = c ? 'نوشتن دیدگاه' : 'بستن فرم';
    });
    w.parentNode.insertBefore(b, w);
  }
  var $tabs = $root.find('.tkd-tab');
  function openTab($t) {
    var id = $t.attr('aria-controls');
    $tabs.attr('aria-selected', 'false');
    $t.attr('aria-selected', 'true');
    $root.find('.tkd-panel').attr('hidden', 'hidden');
    var $p = $('#' + id).removeAttr('hidden');
    if ($p.hasClass('woocommerce-Tabs-panel--reviews')) { reviewForm(); }
        centerIn($root.find('.tkd-tabs')[0], $t[0]);
  }
  $root.on('click', '.tkd-tab', function () { openTab($(this)); });
  $root.on('click', '[data-open]', function (e) {
    e.preventDefault();
    var $t = $tabs.filter('[aria-controls="' + $(this).attr('data-open') + '"]');
    openTab($t);
    $('html,body').animate({ scrollTop: $t.offset().top - 90 }, 300);
  });
  var hash = (location.hash || '').replace('#', '');
  if (/^comment-|^reviews$/.test(hash) && $tabs.filter('[aria-controls="tkd-tab-reviews"]').length) {
    openTab($tabs.filter('[aria-controls="tkd-tab-reviews"]'));
  } else {
    openTab($tabs.first());
  }

  /* ---------- نوار خرید پایین موبایل ---------- */
  var $sticky = $root.find('.tkd-sticky');
  var btn = $root.find('.single_add_to_cart_button')[0];
  if (btn && 'IntersectionObserver' in window) {
    new IntersectionObserver(function (en) {
      var past = !en[0].isIntersecting && en[0].boundingClientRect.top < 0;
      $sticky.toggleClass('is-on', past).attr('aria-hidden', past ? 'false' : 'true');
      $sticky.find('button').attr('tabindex', past ? '0' : '-1');
    }).observe(btn);
  }
  $sticky.on('click', 'button', function () {
    $('html,body').animate({ scrollTop: $card.offset().top - 80 }, 350);
  });

  /* ---------- پاپ‌آپ محصولات مکمل ---------- */
  var ad = document.getElementById('tkd-addon');
  if (ad && ad.showModal) {
    setTimeout(function () { ad.showModal(); }, 250);
    $(ad).on('click', function (e) { if (e.target === ad || $(e.target).is('.tkd-addon-x, .tkd-addon-close')) { ad.close(); } });
  }
  $(document.body).on('added_to_cart', function (e, frag, hash, $b) {
    if ($b && $b.hasClass('tkd-pc-btn')) { $b.addClass('is-done').text('اضافه شد ✓'); $b.siblings('.added_to_cart').remove(); }
  });
});
