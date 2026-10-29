<?php
require_once __DIR__ . '/../includes/functions.php';
$admin = require_admin();
$flash = flash();
$cur = basename($_SERVER['SCRIPT_NAME']);
require __DIR__ . '/../includes/head.php';
$link = fn($f, $t) => '<a href="' . BASE_URL . '/admin/' . $f . '" class="px-3 py-1.5 rounded-full text-xs font-semibold transition ' .
    ($cur === $f ? 'bg-[#16140f] text-[#cba135] dark:bg-[#cba135] dark:text-[#16140f]' : 'hover:text-[#a87c1f] dark:hover:text-[#cba135]') . '">' . $t . '</a>';
?>
<body class="bg-[#f5f2ea] dark:bg-[#0b0b0d] text-[#16140f] dark:text-[#f5f1e8] font-sans antialiased transition-colors duration-300 min-h-screen">
<header class="border-b border-[#e5e1d8] dark:border-[#232019] bg-white/80 dark:bg-[#131316]/80 backdrop-blur sticky top-0 z-40">
  <div class="max-w-6xl mx-auto px-4 py-3 flex flex-wrap items-center gap-3 justify-between">
    <a href="<?= BASE_URL ?>/admin/index.php" class="flex items-center gap-3"><?= logo_full(30) ?> <span class="text-xs font-sans text-[#a09a8c] uppercase tracking-widest ml-1">Admin</span></a>
    <nav class="flex flex-wrap items-center gap-1">
      <?= $link('index.php', 'Dashboard') . $link('stock.php', 'Branch Stock') . $link('products.php', 'Products') . $link('orders.php', 'Orders') . $link('laybys.php', 'Laybys') ?>
      <a href="<?= BASE_URL ?>/shop.php" class="px-3 py-1.5 text-xs text-[#a09a8c] hover:underline">View shop</a>
      <a href="<?= BASE_URL ?>/admin/logout.php" class="px-3 py-1.5 text-xs text-[#b3272d] font-semibold">Log out</a>
      <button onclick="toggleDarkMode()" class="p-2 rounded-full border border-[#e5e1d8] dark:border-[#232019]" aria-label="Toggle theme"><i id="themeIcon" data-lucide="moon" class="w-4 h-4"></i></button>
    </nav>
  </div>
</header>
<main class="page">
<?php if ($flash): ?><div class="alert <?= e($flash['t']) ?>"><?= e($flash['m']) ?></div><?php endif; ?>
