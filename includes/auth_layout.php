<?php
/** Split-screen layout for login/register. Usage: auth_open($title); ...form...; auth_close(); */
require_once __DIR__ . '/functions.php';
function auth_open(string $title, string $eyebrow, string $sub): void {
    $pageTitle = $title; $extraHead = '';
    require __DIR__ . '/head.php';
    $imgs = ['iphone-17-pro-max.jpeg', 'iphone-16-pro.jpeg', 'iphone-17.jpeg'];
?>
<body class="bg-[#f5f2ea] dark:bg-[#0b0b0d] text-[#16140f] dark:text-[#f5f1e8] font-sans antialiased">
<div class="auth-wrap">
  <aside class="auth-brand" data-parallax>
    <div class="hero-bg"><span class="blob b1"></span><span class="blob b2"></span></div>
    <div class="relative z-10"><?= logo_full(44, true) ?></div>
    <div class="relative z-10 my-10">
      <div class="relative h-[340px] max-w-[420px]">
        <div class="ring" style="width:260px;height:260px;left:80px;top:40px"></div><div class="ring r2" style="width:260px;height:260px;left:80px;top:40px"></div>
        <div class="phone-card float absolute left-0 top-10 w-36 h-48" data-depth="0.6" style="--r:-8deg"><img src="<?= BASE_URL ?>/images/<?= $imgs[1] ?>" alt="" class="w-full h-full object-contain p-2"></div>
        <div class="phone-card float d1 absolute left-[110px] top-0 w-44 h-60 z-10" data-depth="1" style="--r:3deg"><img src="<?= BASE_URL ?>/images/<?= $imgs[0] ?>" alt="" class="w-full h-full object-contain p-2"></div>
        <div class="phone-card float d2 absolute left-[250px] top-16 w-36 h-48" data-depth="0.8" style="--r:9deg"><img src="<?= BASE_URL ?>/images/<?= $imgs[2] ?>" alt="" class="w-full h-full object-contain p-2"></div>
      </div>
      <h2 class="font-display text-3xl md:text-4xl font-semibold leading-tight mt-2" style="color:#f5f1e8">Every iPhone.<br><span class="grad-text">One trusted home.</span></h2>
      <p class="mt-3 text-sm text-[#a09a8c] max-w-sm">Genuine devices, six branches across South Africa and lay-by from 2 to 6 months at 0% interest.</p>
    </div>
    <div class="relative z-10 text-xs text-[#7d786c]">&copy; <?= date('Y') ?> LatestiPhone</div>
  </aside>
  <section class="auth-form">
    <div class="auth-card">
      <div class="lg:hidden mb-6 md:hidden"><?= logo_full(36) ?></div>
      <span class="eyebrow"><?= e($eyebrow) ?></span>
      <h1 class="font-display text-3xl font-semibold tracking-tight"><?= e($title) ?></h1>
      <p class="muted text-sm mt-1 mb-2"><?= e($sub) ?></p>
<?php }
function auth_close(): void { ?>
    </div>
  </section>
</div>
<script src="<?= asset('/assets/js/theme.js') ?>"></script>
<script src="<?= asset('/assets/js/anim.js') ?>"></script>
</body></html>
<?php } ?>
