<?php
// Announcement bar + navigation (identical markup to the original design) + account menu.
// Needs: $user (array|null), $isHome (bool).
$B = BASE_URL;
$circle = 'flex items-center justify-center w-8 h-8 rounded-full border border-[#e5e1d8] dark:border-[#2a2620] bg-white dark:bg-[#1a1a1d] text-[#6b6558] dark:text-[#9c9891] transition-all hover:opacity-80 cursor-pointer';
$badge = 'absolute -top-1.5 -right-1.5 items-center justify-center w-4 h-4 rounded-full bg-[#cba135] text-[#16140f] text-[9px] font-bold';
$menuItem = 'block w-full text-left px-3 py-2 rounded-lg hover:bg-[#f5f2ea] dark:hover:bg-[#1a1a1d] transition';
$home = $isHome ? '' : $B . '/shop.php';   // shop sections live on shop.php
$activePage = $activePage ?? '';
$lk = 'nav-link hover:text-[#16140f] dark:hover:text-white transition';
$on = fn($p) => $activePage === $p ? ' is-active' : '';
?>
  <!-- Top Announcement Bar -->
  <div class="bg-[#16140f] text-[#e5e1d8] text-xs py-2.5 px-4 text-center tracking-tight font-normal">
    <span>Flexible 2 to 6 Months Lay-by at 0% Interest across all SA branches.</span>
    <a href="<?= $home ?>#layby" class="text-[#cba135] hover:text-[#e0bc5c] underline ml-1 font-medium">Learn more &rarr;</a>
  </div>

  <!-- Navigation Bar -->
  <div id="scrollBar" class="fixed top-0 left-0 h-[3px] w-0 z-[60] bg-gradient-to-r from-[#a87c1f] via-[#e8c766] to-[#a87c1f]"></div>
  <nav id="mainNav" class="sticky top-0 z-40 bg-white/85 dark:bg-[#131316]/90 backdrop-blur-md border-b border-[#e5e1d8]/80 dark:border-[#232019]">
    <div class="max-w-7xl mx-auto px-6 h-14 flex items-center justify-between">
      <a href="<?= $B ?>/index.php" class="flex items-center space-x-2.5">
        <?= logo_mark(28) ?>
        <span class="font-display font-semibold text-base tracking-tight text-[#16140f] dark:text-white">Latest<span class="text-[#a87c1f] dark:text-[#cba135]">iPhone</span></span>
      </a>

      <div class="hidden md:flex space-x-7 text-xs font-medium text-[#6b6558] dark:text-[#9c9891] tracking-tight">
        <a href="<?= $B ?>/index.php" class="<?= $lk . $on('home') ?>">Home</a>
        <a href="<?= $B ?>/shop.php#catalog" class="<?= $lk . $on('shop') ?>">Shop iPhones</a>
        <a href="<?= $home ?>#smart-match" class="<?= $lk ?>">Smart Match</a>
        <a href="<?= $home ?>#calculator" class="<?= $lk ?>">Calculator</a>
        <a href="<?= $home ?>#trade-in" class="<?= $lk ?>">Trade-In</a>
        <a href="<?= $home ?>#branches" class="<?= $lk ?>">Stores</a>
      </div>

      <div class="flex items-center space-x-2">
        <button type="button" onclick="document.getElementById('mobileNav').classList.toggle('hidden')" class="md:hidden <?= $circle ?>" aria-label="Menu"><i data-lucide="menu" class="w-4 h-4"></i></button>
        <!-- Dark Mode Toggle -->
        <button id="themeToggleBtn" onclick="toggleDarkMode()" class="<?= $circle ?>" title="Toggle theme">
          <i id="themeIcon" data-lucide="moon" class="w-4 h-4"></i>
        </button>

<?php if ($isHome): ?>
        <!-- Wishlist -->
        <button id="wishlistNavBtn" onclick="toggleWishlistOnly()" title="View wishlist" class="relative <?= $circle ?>">
          <i id="wishlistNavIcon" data-lucide="heart" class="w-4 h-4"></i>
          <span id="wishlistCount" class="hidden absolute <?= $badge ?>"></span>
        </button>
<?php endif; ?>

        <!-- Orders (real, from the database) -->
<?php if ($user): ?>
        <a href="<?= $B ?>/orders.php" title="Your orders" class="hidden sm:flex <?= $circle ?>"><i data-lucide="receipt" class="w-4 h-4"></i></a>
<?php elseif ($isHome): ?>
        <button onclick="openAuthModal('login')" title="Log in to see your orders" class="hidden sm:flex <?= $circle ?>"><i data-lucide="receipt" class="w-4 h-4"></i></button>
<?php else: ?>
        <a href="<?= $B ?>/login.php" title="Log in to see your orders" class="hidden sm:flex <?= $circle ?>"><i data-lucide="receipt" class="w-4 h-4"></i></a>
<?php endif; ?>

        <!-- Cart -->
<?php if ($isHome): ?>
        <button onclick="toggleCartDrawer()" title="Cart" class="relative <?= $circle ?>">
<?php else: ?>
        <a href="<?= $B ?>/shop.php?cart=1" title="Cart" class="relative <?= $circle ?>">
<?php endif; ?>
          <i data-lucide="shopping-bag" class="w-4 h-4"></i>
          <span id="cartCount" class="<?= $isHome ? 'hidden' : (cart_count() > 0 ? 'flex' : 'hidden') ?> absolute <?= $badge ?>"><?= $isHome ? '' : cart_count() ?></span>
<?php if ($isHome): ?></button><?php else: ?></a><?php endif; ?>


        <!-- Account -->
        <div class="relative">
          <button id="acctBtn" onclick="toggleAcctMenu(event)" title="Account" class="<?= $circle ?> <?= $user ? 'border-[#cba135] text-[#a87c1f] dark:text-[#cba135]' : '' ?>">
            <i data-lucide="user" class="w-4 h-4"></i>
          </button>
          <div id="acctMenu" class="hidden absolute right-0 mt-2 w-56 rounded-xl bg-white dark:bg-[#131316] border border-[#e5e1d8] dark:border-[#232019] shadow-xl p-1.5 text-xs z-50">
<?php if ($user): ?>
            <div class="px-3 py-2 border-b border-[#e5e1d8] dark:border-[#232019] mb-1">
              <div class="font-semibold truncate"><?= e($user['full_name']) ?></div>
              <div class="text-[10px] text-[#a09a8c] truncate"><?= e($user['email']) ?></div>
            </div>
            <a class="<?= $menuItem ?>" href="<?= $B ?>/orders.php">My orders</a>
            <a class="<?= $menuItem ?>" href="<?= $B ?>/layby.php">My layby accounts</a>
            <a class="<?= $menuItem ?> text-[#b3272d]" href="<?= $B ?>/logout.php">Log out</a>
<?php elseif ($isHome): ?>
            <button class="<?= $menuItem ?>" onclick="openAuthModal('login')">Log in</button>
            <button class="<?= $menuItem ?>" onclick="openAuthModal('register')">Create account</button>
<?php else: ?>
            <a class="<?= $menuItem ?>" href="<?= $B ?>/login.php">Log in</a>
            <a class="<?= $menuItem ?>" href="<?= $B ?>/register.php">Create account</a>
<?php endif; ?>
          </div>
        </div>

        <!-- WhatsApp -->
        <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hi%20Latest-iPhone,%20I%20would%20like%20to%20inquire%20about%20an%20iPhone"
           target="_blank" rel="noopener"
           class="bg-[#25D366] hover:bg-[#1fbd5a] text-white px-4 py-1.5 rounded-full text-xs font-semibold transition shadow-sm flex items-center gap-1.5">
          <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
          <span class="hidden sm:inline">WhatsApp</span>
        </a>
      </div>
    </div>
    <div id="mobileNav" class="hidden md:hidden border-t border-[#e5e1d8] dark:border-[#232019] bg-white dark:bg-[#131316] px-6 py-3 text-sm grid gap-1">
      <a class="py-2" href="<?= $B ?>/index.php">Home</a>
      <a class="py-2" href="<?= $B ?>/shop.php#catalog">Shop iPhones</a>
      <a class="py-2" href="<?= $home ?>#smart-match">Smart Match</a>
      <a class="py-2" href="<?= $home ?>#calculator">Calculator</a>
      <a class="py-2" href="<?= $home ?>#trade-in">Trade-In</a>
      <a class="py-2" href="<?= $home ?>#branches">Stores</a>
    </div>
  </nav>
