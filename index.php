<?php
require_once __DIR__ . '/includes/functions.php';
$user = require_member();          // the login screen comes first; everything else is for members
$isHome = false;
$activePage = 'home';
$pageTitle = 'Home';
$pdo = db();
$nModels   = (int)$pdo->query('SELECT COUNT(*) FROM product_models WHERE is_active=1')->fetchColumn();
$branches  = $pdo->query('SELECT branch_name, province FROM branches ORDER BY branch_id')->fetchAll();
$featured  = $pdo->query("SELECT m.slug, m.model_name, m.chip, m.display_spec,
        (SELECT MIN(p.price) FROM products p WHERE p.model_id=m.model_id AND p.is_active=1) AS from_price,
        (SELECT c.image_path FROM model_colours c WHERE c.model_id=m.model_id ORDER BY c.colour_id LIMIT 1) AS img
    FROM product_models m WHERE m.is_active=1 ORDER BY m.sort_order, m.model_id LIMIT 4")->fetchAll();
$heroImgs = array_column($featured, 'img');
$first = explode(' ', trim($user['full_name']))[0];
require __DIR__ . '/includes/head.php';
?>
<body class="bg-[#f5f2ea] dark:bg-[#0b0b0d] text-[#16140f] dark:text-[#f5f1e8] font-sans antialiased selection:bg-[#cba135] selection:text-[#16140f] min-h-screen">
<?php require __DIR__ . '/includes/nav.php'; ?>

<!-- ============ HERO ============ -->
<section class="relative overflow-hidden" data-parallax>
  <div class="hero-bg"><span class="blob b1"></span><span class="blob b2"></span><span class="blob b3"></span></div>
  <div class="relative max-w-7xl mx-auto px-6 pt-16 pb-20 md:pt-24 md:pb-28 grid md:grid-cols-2 gap-12 items-center">
    <div>
      <span data-anim="up" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-[#a87c1f] dark:text-[#cba135] bg-white/70 dark:bg-white/5 border border-[#e5e1d8] dark:border-white/10 rounded-full px-3 py-1.5">
        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Welcome back, <?= e($first) ?>
      </span>
      <h1 data-anim="up" style="--d:.1s" class="font-display text-5xl md:text-7xl font-semibold tracking-tight leading-[1.02] mt-5">
        Every iPhone.<br><span class="grad-text">One trusted home.</span>
      </h1>
      <p data-anim="up" style="--d:.2s" class="mt-6 text-lg text-[#6b6558] dark:text-[#9c9891] max-w-lg leading-relaxed">
        LatestiPhone brings the full iPhone range, from the newest Series 17 to proven older models, to six branches across South Africa. Pay in full, or spread it over 2&ndash;6 months with <strong class="text-[#16140f] dark:text-white">0% interest</strong> lay-by.
      </p>
      <div data-anim="up" style="--d:.3s" class="mt-8 flex flex-wrap gap-3">
        <a href="<?= BASE_URL ?>/shop.php#catalog" class="btn-gold shine">Shop iPhones <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
        <a href="#about" class="btn-ghost">Our story</a>
      </div>
      <div data-anim="fade" style="--d:.5s" class="mt-10 flex flex-wrap gap-x-7 gap-y-2 text-xs text-[#6b6558] dark:text-[#9c9891]">
        <span class="flex items-center gap-1.5"><i data-lucide="shield-check" class="w-4 h-4 text-[#a87c1f]"></i>Genuine Apple devices</span>
        <span class="flex items-center gap-1.5"><i data-lucide="truck" class="w-4 h-4 text-[#a87c1f]"></i>PEP Paxi &amp; The Courier Guy</span>
        <span class="flex items-center gap-1.5"><i data-lucide="lock" class="w-4 h-4 text-[#a87c1f]"></i>Secure checkout</span>
      </div>
    </div>

    <div class="relative h-[420px] md:h-[520px]" data-anim="zoom" style="--d:.2s">
      <div class="ring" style="width:300px;height:300px;left:calc(50% - 150px);top:calc(50% - 150px)"></div>
      <div class="ring r2" style="width:300px;height:300px;left:calc(50% - 150px);top:calc(50% - 150px)"></div>
      <div class="ring r3" style="width:300px;height:300px;left:calc(50% - 150px);top:calc(50% - 150px)"></div>
<?php
$pos = [['left:2%;top:14%;width:190px;height:250px;--r:-9deg', 'float d1', 0.7], ['left:28%;top:0;width:230px;height:300px;z-index:2;--r:2deg', 'float', 1.2], ['right:0;top:22%;width:190px;height:250px;--r:9deg', 'float d2', 0.9], ['left:22%;bottom:0;width:170px;height:220px;z-index:3;--r:-4deg', 'float d3', 1.4]];
foreach ($heroImgs as $i => $img): if (!isset($pos[$i])) break; ?>
      <div class="absolute" style="<?= $pos[$i][0] ?>" data-depth="<?= $pos[$i][2] ?>">
        <div class="phone-card <?= $pos[$i][1] ?> w-full h-full" style="--r:<?= preg_match('/--r:([^;]+)/', $pos[$i][0], $m) ? $m[1] : '0deg' ?>"><img src="<?= BASE_URL . '/' . e($img) ?>" alt="" class="w-full h-full object-contain p-3"></div>
      </div>
<?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ MARQUEE ============ -->
<section class="bg-[#16140f] text-[#cfc9ba] py-4 border-y border-[#2a2620]">
  <div class="marquee"><div class="marquee-track text-sm tracking-wide">
<?php for ($k = 0; $k < 2; $k++) foreach (['Genuine Apple devices', '0% interest lay-by', 'Six branches across SA', 'PEP Paxi & The Courier Guy', 'PayFast · Ozow · Yoco', 'Order on WhatsApp', 'New & pre-owned'] as $t): ?>
    <span class="inline-flex items-center gap-3"><span class="text-[#cba135]">&#9670;</span><?= e($t) ?></span>
<?php endforeach; ?>
  </div></div>
</section>

<!-- ============ STATS ============ -->
<section class="max-w-6xl mx-auto px-6 -mt-0 py-16 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
<?php foreach ([[count($branches), 'Branches across SA', ''], [$nModels, 'iPhone models', '+'], [6, 'Months max lay-by', ''], [0, '% interest on lay-by', '%']] as $i => [$n, $l, $suf]): ?>
  <div data-anim="up" style="--d:<?= $i * .1 ?>s" class="lift bg-white dark:bg-[#131316] border border-[#e5e1d8] dark:border-[#232019] rounded-2xl py-7">
    <div class="font-display text-5xl font-semibold text-[#a87c1f] dark:text-[#cba135]"><span data-count="<?= $n ?>" data-suffix="<?= $suf === '%' ? '' : $suf ?>">0</span></div>
    <div class="text-xs uppercase tracking-widest text-[#a09a8c] mt-2"><?= e($l) ?></div>
  </div>
<?php endforeach; ?>
</section>

<!-- ============ ABOUT ============ -->
<section id="about" class="max-w-6xl mx-auto px-6 py-16 grid md:grid-cols-2 gap-14 items-center">
  <div data-anim="left">
    <span class="eyebrow">Who we are</span>
    <h2 class="font-display text-4xl font-semibold tracking-tight leading-tight">A South African iPhone specialist, close to you.</h2>
    <p class="mt-5 text-[#6b6558] dark:text-[#9c9891] leading-relaxed">LatestiPhone sells new and pre-owned Apple iPhones through six branches: Pretoria, Durban, Pinetown, Richards Bay, Nelspruit and Manguzi. We focus on one thing, iPhones, so we can stock the whole range and help you choose the right one.</p>
    <p class="mt-4 text-[#6b6558] dark:text-[#9c9891] leading-relaxed">Not everyone can pay for a flagship in one go. That is why every phone can be bought on lay-by over 2 to 6 months with no interest, and why you can see exactly which of our branches has your model in stock before you order.</p>
    <a href="<?= BASE_URL ?>/shop.php#catalog" class="btn-gold shine mt-7">Browse the lineup <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
  </div>
  <div data-anim="right" class="grid grid-cols-2 gap-4">
<?php foreach ([['smartphone', 'The full range', 'Series 17 down to iPhone X, in every colour.'], ['wallet', 'Lay-by, 0% interest', 'Choose 2 to 6 months and pay it off monthly.'], ['map-pin', 'Branch stock', 'Live stock for each of our six branches.'], ['message-circle', 'Order on WhatsApp', 'Ask a question or confirm your order in chat.']] as $i => [$ic, $t, $d]): ?>
    <div class="lift bg-white dark:bg-[#131316] border border-[#e5e1d8] dark:border-[#232019] rounded-2xl p-5 <?= $i % 2 ? 'mt-6' : '' ?>">
      <div class="w-10 h-10 rounded-xl bg-[#16140f] text-[#cba135] flex items-center justify-center mb-3"><i data-lucide="<?= $ic ?>" class="w-5 h-5"></i></div>
      <div class="font-semibold text-sm"><?= e($t) ?></div><div class="text-xs text-[#6b6558] dark:text-[#9c9891] mt-1 leading-relaxed"><?= e($d) ?></div>
    </div>
<?php endforeach; ?>
  </div>
</section>

<!-- ============ FEATURED ============ -->
<section class="bg-white/60 dark:bg-[#0f0f12] border-y border-[#e5e1d8] dark:border-[#232019] py-16">
  <div class="max-w-7xl mx-auto px-6">
    <div class="flex items-end justify-between flex-wrap gap-4 mb-10" data-anim="up">
      <div><span class="eyebrow">Latest arrivals</span><h2 class="font-display text-4xl font-semibold tracking-tight">The newest iPhones</h2></div>
      <a href="<?= BASE_URL ?>/shop.php#catalog" class="btn-ghost">View all <?= $nModels ?> models <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
<?php foreach ($featured as $i => $f): ?>
      <a href="<?= BASE_URL ?>/shop.php?q=<?= urlencode($f['model_name']) ?>#catalog" data-anim="up" style="--d:<?= $i * .1 ?>s" class="lift group block bg-white dark:bg-[#131316] border border-[#e5e1d8] dark:border-[#232019] rounded-3xl overflow-hidden">
        <div class="bg-[#faf8f3] dark:bg-[#1a1a1d] h-60 flex items-center justify-center overflow-hidden"><img src="<?= BASE_URL . '/' . e($f['img']) ?>" alt="<?= e($f['model_name']) ?>" class="h-full w-full object-contain p-4 transition-transform duration-700 group-hover:scale-110"></div>
        <div class="p-5"><div class="font-display text-lg font-semibold"><?= e($f['model_name']) ?></div>
          <div class="text-xs text-[#a09a8c] mt-0.5"><?= e($f['chip']) ?></div>
          <div class="mt-3 text-sm">From <span class="font-semibold text-[#a87c1f] dark:text-[#cba135]"><?= money($f['from_price']) ?></span></div></div>
      </a>
<?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ HOW IT WORKS ============ -->
<section class="max-w-4xl mx-auto px-6 py-20">
  <div class="text-center mb-14" data-anim="up"><span class="eyebrow">How it works</span><h2 class="font-display text-4xl font-semibold tracking-tight">From browsing to your hands in three steps</h2></div>
  <div class="relative pl-12 md:pl-0">
    <div class="absolute left-[19px] md:left-1/2 top-2 bottom-2 w-px bg-[#cba135]/60 tl-line"></div>
<?php foreach ([['Choose your iPhone', 'Compare models, colours and storage, and check which branch has it in stock.', 'search'], ['Pay in full or lay-by', 'Pay online with PayFast, Ozow or Yoco, or pick a 2 to 6 month plan with a small deposit.', 'credit-card'], ['Collect or have it delivered', 'Choose PEP Paxi pick-up or The Courier Guy door delivery, and confirm on WhatsApp.', 'package-check']] as $i => [$t, $d, $ic]): ?>
    <div data-anim="<?= $i % 2 ? 'right' : 'left' ?>" class="relative mb-10 md:w-1/2 <?= $i % 2 ? 'md:ml-auto md:pl-12' : 'md:pr-12 md:text-right' ?>">
      <div class="absolute top-0 <?= $i % 2 ? '-left-12 md:-left-5' : '-left-12 md:left-auto md:-right-5' ?> w-10 h-10 rounded-full bg-[#16140f] text-[#cba135] flex items-center justify-center ring-4 ring-[#f5f2ea] dark:ring-[#0b0b0d]"><i data-lucide="<?= $ic ?>" class="w-4 h-4"></i></div>
      <div class="text-xs text-[#a87c1f] dark:text-[#cba135] font-semibold tracking-widest">STEP <?= $i + 1 ?></div>
      <div class="font-display text-xl font-semibold mt-1"><?= e($t) ?></div>
      <p class="text-sm text-[#6b6558] dark:text-[#9c9891] mt-1.5 leading-relaxed"><?= e($d) ?></p>
    </div>
<?php endforeach; ?>
  </div>
</section>

<!-- ============ BRANCHES ============ -->
<section class="bg-[#16140f] text-[#f5f1e8] py-16">
  <div class="max-w-6xl mx-auto px-6">
    <div class="text-center mb-10" data-anim="up"><span class="eyebrow" style="color:#cba135">Find us</span><h2 class="font-display text-4xl font-semibold tracking-tight">Six branches, one standard</h2></div>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
<?php foreach ($branches as $i => $b): ?>
      <div data-anim="zoom" style="--d:<?= $i * .08 ?>s" class="lift bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
        <div class="w-11 h-11 mx-auto rounded-full bg-[#cba135]/15 text-[#cba135] flex items-center justify-center mb-3"><i data-lucide="map-pin" class="w-5 h-5"></i></div>
        <div class="font-display text-lg font-semibold"><?= e($b['branch_name']) ?></div><div class="text-xs text-[#a09a8c]"><?= e($b['province']) ?></div>
      </div>
<?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ CTA ============ -->
<section class="relative overflow-hidden py-24 text-center">
  <div class="hero-bg"><span class="blob b1"></span><span class="blob b2"></span></div>
  <div class="relative max-w-2xl mx-auto px-6" data-anim="up">
    <h2 class="font-display text-4xl md:text-5xl font-semibold tracking-tight">Ready to find <span class="grad-text">your iPhone?</span></h2>
    <p class="mt-4 text-[#6b6558] dark:text-[#9c9891]">See every model, compare them side by side and start a lay-by plan in minutes.</p>
    <div class="mt-8 flex justify-center gap-3 flex-wrap">
      <a href="<?= BASE_URL ?>/shop.php#catalog" class="btn-gold shine">Start shopping <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
      <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hi%20LatestiPhone" target="_blank" rel="noopener" class="btn-ghost"><i data-lucide="message-circle" class="w-4 h-4"></i> Chat on WhatsApp</a>
    </div>
  </div>
</section>

<?php
// footer.php closes <main>, so open a harmless wrapper first
echo '<main>';
require __DIR__ . '/includes/footer.php';
