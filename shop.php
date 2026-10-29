<?php
require_once __DIR__ . '/includes/functions.php';
$user = require_member();
$isHome = true;   // 'true' = this is the shop page (cart drawer, wishlist, catalogue scripts)
$activePage = 'shop';
$pageTitle = 'Premium iPhone Lineup & Lay-by';
$extraHead = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" /><script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    :root { --gold: #cba135; --ink: #16140f; --cream: #f5f2ea; }
    .scrollbar-none::-webkit-scrollbar { display: none; } .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
    .tag-new { background: var(--ink); color: var(--gold); }
    .heart-btn.active svg { fill: currentColor; }
    .heart-btn.active { color: var(--gold); border-color: var(--gold) !important; }
    input[type="range"] { accent-color: var(--gold); }
    #storeMap { z-index: 0; }
    .leaflet-popup-content-wrapper { border-radius: 10px; font-family: Inter, sans-serif; font-size: 12px; }
  </style>';
require __DIR__ . '/includes/head.php';
?>
<body class="bg-[#f5f2ea] dark:bg-[#0b0b0d] text-[#16140f] dark:text-[#f5f1e8] font-sans antialiased selection:bg-[#cba135] selection:text-[#16140f] transition-colors duration-300 min-h-screen">

<?php require __DIR__ . '/includes/nav.php'; ?>

  <!-- Hero Section -->
  <section class="pt-16 pb-12 px-6 max-w-7xl mx-auto text-center reveal">
    <span class="text-xs font-semibold uppercase tracking-wider text-[#a87c1f] dark:text-[#cba135] mb-2 block">The Complete Collection</span>
    <h1 class="font-display text-4xl md:text-6xl font-semibold tracking-tight text-[#16140f] dark:text-white max-w-3xl mx-auto leading-tight">
      <span class="grad-text">Buy iPhone.</span>
    </h1>
    <p class="mt-3 text-[#6b6558] dark:text-[#9c9891] max-w-xl mx-auto text-base font-normal">
      Compare models, select storage capacity, and choose between upfront cash or zero-interest lay-by terms.
    </p>

    <!-- About the store -->
    <p class="mt-5 text-[#6b6558] dark:text-[#9c9891] max-w-2xl mx-auto text-sm leading-relaxed">
      LatestiPhone is South Africa's home for the full iPhone lineup — from the newest Series 17 to
      well-priced older generations — all in one place, backed by branches across SA and flexible lay-by so
      you're never locked into paying it all upfront.
    </p>

    <div class="mt-6 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-xs font-medium text-[#6b6558] dark:text-[#9c9891]">
      <span class="flex items-center gap-1.5"><i data-lucide="shield-check" class="w-4 h-4 text-[#a87c1f] dark:text-[#cba135]"></i> Genuine Apple devices</span>
      <span class="flex items-center gap-1.5"><i data-lucide="wallet" class="w-4 h-4 text-[#a87c1f] dark:text-[#cba135]"></i> 0% interest lay-by, 2–6 months</span>
      <span class="flex items-center gap-1.5"><i data-lucide="map-pin" class="w-4 h-4 text-[#a87c1f] dark:text-[#cba135]"></i> Branches across SA</span>
      <span class="flex items-center gap-1.5"><i data-lucide="message-circle" class="w-4 h-4 text-[#a87c1f] dark:text-[#cba135]"></i> Real answers on WhatsApp</span>
    </div>
  </section>

  <!-- Product Catalog Section -->
  <section id="catalog" class="py-8 px-6 max-w-7xl mx-auto reveal">
    <div class="flex flex-col gap-4 mb-6 pb-6 border-b border-[#e5e1d8] dark:border-[#232019]">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Search Bar -->
        <div class="relative w-full md:w-72">
          <i data-lucide="search" class="w-4 h-4 text-[#a09a8c] absolute left-3.5 top-2.5"></i>
          <input type="text" id="searchInput" oninput="filterCatalog()" placeholder="Search model (e.g., 15 Pro, Mini)..."
                 class="w-full bg-white dark:bg-[#1a1a1d] border border-[#e5e1d8] dark:border-[#2a2620] rounded-full pl-9 pr-4 py-2 text-xs text-[#16140f] dark:text-white placeholder-[#a09a8c] focus:outline-none focus:ring-2 focus:ring-[#cba135]/30 focus:border-[#cba135] transition">
        </div>

        <!-- Sort -->
        <select id="sortSelect" onchange="applyFilters()"
                class="bg-white dark:bg-[#1a1a1d] border border-[#e5e1d8] dark:border-[#2a2620] rounded-full px-4 py-2 text-xs text-[#16140f] dark:text-white focus:outline-none focus:ring-2 focus:ring-[#cba135]/30">
          <option value="featured">Sort: Featured</option>
          <option value="newest">Sort: Newest</option>
          <option value="price-low">Sort: Price (Low to High)</option>
          <option value="price-high">Sort: Price (High to Low)</option>
          <option value="name">Sort: Name (A–Z)</option>
        </select>

        <!-- Branch (live stock per branch comes from the database) -->
        <select id="branchSelect" onchange="setBranch(this.value)" title="Show stock for one branch"
                class="bg-white dark:bg-[#1a1a1d] border border-[#e5e1d8] dark:border-[#2a2620] rounded-full px-4 py-2 text-xs text-[#16140f] dark:text-white focus:outline-none focus:ring-2 focus:ring-[#cba135]/30">
          <option value="">Stock: All branches</option>
        </select>

        <!-- Generation Filters -->
        <div class="flex items-center space-x-1.5 overflow-x-auto pb-2 md:pb-0 scrollbar-none" id="genFilters">
          <button onclick="filterGen('all', this)" class="gen-btn bg-[#16140f] text-[#cba135] dark:bg-[#cba135] dark:text-[#16140f] text-xs px-4 py-2 rounded-full font-medium transition whitespace-nowrap">All</button>
          <button onclick="filterGen('17', this)" class="gen-btn bg-white border border-[#e5e1d8] dark:bg-[#1a1a1d] dark:border-[#2a2620] text-[#6b6558] dark:text-[#9c9891] hover:border-[#cba135] text-xs px-4 py-2 rounded-full font-medium transition whitespace-nowrap">iPhone 17</button>
          <button onclick="filterGen('16', this)" class="gen-btn bg-white border border-[#e5e1d8] dark:bg-[#1a1a1d] dark:border-[#2a2620] text-[#6b6558] dark:text-[#9c9891] hover:border-[#cba135] text-xs px-4 py-2 rounded-full font-medium transition whitespace-nowrap">iPhone 16</button>
          <button onclick="filterGen('15', this)" class="gen-btn bg-white border border-[#e5e1d8] dark:bg-[#1a1a1d] dark:border-[#2a2620] text-[#6b6558] dark:text-[#9c9891] hover:border-[#cba135] text-xs px-4 py-2 rounded-full font-medium transition whitespace-nowrap">iPhone 15</button>
          <button onclick="filterGen('14', this)" class="gen-btn bg-white border border-[#e5e1d8] dark:bg-[#1a1a1d] dark:border-[#2a2620] text-[#6b6558] dark:text-[#9c9891] hover:border-[#cba135] text-xs px-4 py-2 rounded-full font-medium transition whitespace-nowrap">iPhone 14</button>
          <button onclick="filterGen('13', this)" class="gen-btn bg-white border border-[#e5e1d8] dark:bg-[#1a1a1d] dark:border-[#2a2620] text-[#6b6558] dark:text-[#9c9891] hover:border-[#cba135] text-xs px-4 py-2 rounded-full font-medium transition whitespace-nowrap">iPhone 13</button>
          <button onclick="filterGen('12', this)" class="gen-btn bg-white border border-[#e5e1d8] dark:bg-[#1a1a1d] dark:border-[#2a2620] text-[#6b6558] dark:text-[#9c9891] hover:border-[#cba135] text-xs px-4 py-2 rounded-full font-medium transition whitespace-nowrap">iPhone 12</button>
          <button onclick="filterGen('11', this)" class="gen-btn bg-white border border-[#e5e1d8] dark:bg-[#1a1a1d] dark:border-[#2a2620] text-[#6b6558] dark:text-[#9c9891] hover:border-[#cba135] text-xs px-4 py-2 rounded-full font-medium transition whitespace-nowrap">iPhone 11</button>
          <button onclick="filterGen('x', this)" class="gen-btn bg-white border border-[#e5e1d8] dark:bg-[#1a1a1d] dark:border-[#2a2620] text-[#6b6558] dark:text-[#9c9891] hover:border-[#cba135] text-xs px-4 py-2 rounded-full font-medium transition whitespace-nowrap">iPhone X / XS / XR</button>
        </div>
      </div>
      <p id="resultCount" class="text-[11px] font-mono text-[#a09a8c]">Showing all models</p>

      <!-- Wishlist-only mode banner: only visible while that view is on, with a one-click way out -->
      <div id="wishlistModeBanner" class="hidden items-center justify-between gap-3 bg-[#cba135]/10 border border-[#cba135]/40 text-[#8a661a] dark:text-[#cba135] text-xs font-medium px-4 py-2.5 rounded-xl">
        <span class="flex items-center gap-1.5"><i data-lucide="heart" class="w-3.5 h-3.5"></i> Showing wishlist only</span>
        <button onclick="toggleWishlistOnly()" class="underline hover:no-underline font-semibold">Show all iPhones</button>
      </div>
    </div>

    <!-- Product Grid -->
    <div id="productGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <!-- Dynamic Products Grid (rendered by script.js) -->
    </div>
  </section>

  <!-- Smart Match Quiz -->
  <section id="smart-match" class="py-14 px-6 max-w-7xl mx-auto reveal">
    <div class="bg-white dark:bg-[#131316] rounded-3xl p-8 md:p-12 border border-[#e5e1d8] dark:border-[#232019] shadow-sm">
      <span class="text-xs font-semibold uppercase tracking-wider text-[#a87c1f] dark:text-[#cba135] block mb-2">Not sure which one?</span>
      <h2 class="font-display text-2xl md:text-3xl font-semibold tracking-tight mb-6">Find your match in 3 taps.</h2>

      <div class="grid md:grid-cols-3 gap-6">
        <div data-quiz-group>
          <p class="text-xs font-semibold uppercase tracking-wide text-[#a09a8c] mb-2">Budget</p>
          <div class="flex flex-wrap gap-2">
            <button onclick="quizPick('budget','entry', this)" class="text-xs px-3 py-2 rounded-lg border border-[#e5e1d8] dark:border-[#2a2620] bg-[#f5f2ea] dark:bg-[#1a1a1d] hover:border-[#cba135] transition">Entry-level</button>
            <button onclick="quizPick('budget','mid', this)" class="text-xs px-3 py-2 rounded-lg border border-[#e5e1d8] dark:border-[#2a2620] bg-[#f5f2ea] dark:bg-[#1a1a1d] hover:border-[#cba135] transition">Mid-range</button>
            <button onclick="quizPick('budget','premium', this)" class="text-xs px-3 py-2 rounded-lg border border-[#e5e1d8] dark:border-[#2a2620] bg-[#f5f2ea] dark:bg-[#1a1a1d] hover:border-[#cba135] transition">Premium</button>
          </div>
        </div>
        <div data-quiz-group>
          <p class="text-xs font-semibold uppercase tracking-wide text-[#a09a8c] mb-2">What matters most?</p>
          <div class="flex flex-wrap gap-2">
            <button onclick="quizPick('priority','camera', this)" class="text-xs px-3 py-2 rounded-lg border border-[#e5e1d8] dark:border-[#2a2620] bg-[#f5f2ea] dark:bg-[#1a1a1d] hover:border-[#cba135] transition">Camera</button>
            <button onclick="quizPick('priority','battery', this)" class="text-xs px-3 py-2 rounded-lg border border-[#e5e1d8] dark:border-[#2a2620] bg-[#f5f2ea] dark:bg-[#1a1a1d] hover:border-[#cba135] transition">Battery</button>
            <button onclick="quizPick('priority','performance', this)" class="text-xs px-3 py-2 rounded-lg border border-[#e5e1d8] dark:border-[#2a2620] bg-[#f5f2ea] dark:bg-[#1a1a1d] hover:border-[#cba135] transition">Performance</button>
            <button onclick="quizPick('priority','value', this)" class="text-xs px-3 py-2 rounded-lg border border-[#e5e1d8] dark:border-[#2a2620] bg-[#f5f2ea] dark:bg-[#1a1a1d] hover:border-[#cba135] transition">Best Value</button>
          </div>
        </div>
        <div data-quiz-group>
          <p class="text-xs font-semibold uppercase tracking-wide text-[#a09a8c] mb-2">Size</p>
          <div class="flex flex-wrap gap-2">
            <button onclick="quizPick('size','compact', this)" class="text-xs px-3 py-2 rounded-lg border border-[#e5e1d8] dark:border-[#2a2620] bg-[#f5f2ea] dark:bg-[#1a1a1d] hover:border-[#cba135] transition">Compact</button>
            <button onclick="quizPick('size','standard', this)" class="text-xs px-3 py-2 rounded-lg border border-[#e5e1d8] dark:border-[#2a2620] bg-[#f5f2ea] dark:bg-[#1a1a1d] hover:border-[#cba135] transition">Standard</button>
            <button onclick="quizPick('size','max', this)" class="text-xs px-3 py-2 rounded-lg border border-[#e5e1d8] dark:border-[#2a2620] bg-[#f5f2ea] dark:bg-[#1a1a1d] hover:border-[#cba135] transition">Max</button>
          </div>
        </div>
      </div>

      <div id="quizResults" class="hidden mt-8 pt-6 border-t border-[#e5e1d8] dark:border-[#232019]"></div>
    </div>
  </section>

  <!-- Lay-by Section -->
  <section id="layby" class="py-8 px-6 max-w-7xl mx-auto reveal">
    <div class="bg-white dark:bg-[#131316] rounded-3xl p-8 md:p-14 border border-[#e5e1d8] dark:border-[#232019] shadow-sm text-center">
      <span class="text-xs font-semibold uppercase tracking-wider text-[#a87c1f] dark:text-[#cba135] block mb-2">Simple Finance</span>
      <h2 class="font-display text-3xl font-semibold tracking-tight mb-3">Lay-by on your terms. 0% Interest.</h2>
      <p class="text-[#6b6558] dark:text-[#9c9891] text-sm max-w-lg mx-auto mb-10">Lock in your iPhone today with a small deposit and split the balance evenly over 2 to 6 months.</p>

      <div class="grid md:grid-cols-3 gap-6 text-left">
        <div class="bg-[#f5f2ea] dark:bg-[#1a1a1d] p-6 rounded-2xl">
          <div class="font-semibold text-base mb-1">1. Choose Storage &amp; Plan</div>
          <p class="text-xs text-[#6b6558] dark:text-[#9c9891] leading-relaxed">Select your preferred storage tier (128GB, 256GB, 512GB, 1TB) and reserve the exact device.</p>
        </div>
        <div class="bg-[#f5f2ea] dark:bg-[#1a1a1d] p-6 rounded-2xl">
          <div class="font-semibold text-base mb-1">2. Zero Hidden Fees</div>
          <p class="text-xs text-[#6b6558] dark:text-[#9c9891] leading-relaxed">Pay monthly installments across 2, 3, 4, 5, or 6 months with absolute transparency.</p>
        </div>
        <div class="bg-[#f5f2ea] dark:bg-[#1a1a1d] p-6 rounded-2xl">
          <div class="font-semibold text-base mb-1">3. Collect In-Store</div>
          <p class="text-xs text-[#6b6558] dark:text-[#9c9891] leading-relaxed">Pick up your newly unboxed device at any of our 6 retail branches upon final payment.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Lay-by Calculator -->
  <section id="calculator" class="py-8 px-6 max-w-7xl mx-auto reveal">
    <div class="bg-white dark:bg-[#131316] rounded-3xl p-8 md:p-12 border border-[#e5e1d8] dark:border-[#232019] shadow-sm">
      <span class="text-xs font-semibold uppercase tracking-wider text-[#a87c1f] dark:text-[#cba135] block mb-2">Plan Ahead</span>
      <h2 class="font-display text-2xl md:text-3xl font-semibold tracking-tight mb-6">Lay-by Calculator</h2>

      <div class="grid md:grid-cols-2 gap-10">
        <div class="space-y-5">
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-[#a09a8c] block mb-2">Model</label>
            <select id="calcModel" class="w-full bg-[#f5f2ea] dark:bg-[#1a1a1d] border border-[#e5e1d8] dark:border-[#2a2620] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#cba135]/30"></select>
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-[#a09a8c] flex justify-between mb-2">
              <span>Lay-by Term</span><span id="calcMonthsLabel" class="font-mono text-[#16140f] dark:text-white">6 months</span>
            </label>
            <input type="range" id="calcMonths" min="2" max="6" step="1" value="6" class="w-full">
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-[#a09a8c] flex justify-between mb-2">
              <span>Deposit</span><span id="calcDepositLabel" class="font-mono text-[#16140f] dark:text-white">20%</span>
            </label>
            <input type="range" id="calcDeposit" min="10" max="50" step="5" value="20" class="w-full">
          </div>
        </div>

        <div class="bg-[#f5f2ea] dark:bg-[#1a1a1d] rounded-2xl p-6 flex flex-col justify-center gap-3">
          <div class="flex justify-between text-sm"><span class="text-[#6b6558] dark:text-[#9c9891]">Device Price</span><span id="calcTotal" class="font-mono font-semibold">—</span></div>
          <div class="flex justify-between text-sm"><span class="text-[#6b6558] dark:text-[#9c9891]">Balance after deposit</span><span id="calcBalance" class="font-mono font-semibold">—</span></div>
          <div class="border-t border-[#e5e1d8] dark:border-[#2a2620] pt-3 flex justify-between items-baseline">
            <span class="text-sm font-semibold">Monthly Payment</span>
            <span id="calcMonthly" class="font-mono text-xl font-bold text-[#a87c1f] dark:text-[#cba135]">—</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Trade-In Estimator -->
  <section id="trade-in" class="py-8 px-6 max-w-7xl mx-auto reveal">
    <div class="bg-white dark:bg-[#131316] rounded-3xl p-8 md:p-12 border border-[#e5e1d8] dark:border-[#232019] shadow-sm">
      <span class="text-xs font-semibold uppercase tracking-wider text-[#a87c1f] dark:text-[#cba135] block mb-2">Trade In &amp; Save</span>
      <h2 class="font-display text-2xl md:text-3xl font-semibold tracking-tight mb-2">Trade-In Estimator</h2>
      <p class="text-xs text-[#a09a8c] mb-6">Illustrative only — final value is confirmed in-store on inspection.</p>

      <div class="grid md:grid-cols-2 gap-10">
        <div class="space-y-5">
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-[#a09a8c] block mb-2">Your current iPhone</label>
            <select id="tradeModel" class="w-full bg-[#f5f2ea] dark:bg-[#1a1a1d] border border-[#e5e1d8] dark:border-[#2a2620] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#cba135]/30"></select>
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-[#a09a8c] block mb-2">Condition</label>
            <select id="tradeCondition" class="w-full bg-[#f5f2ea] dark:bg-[#1a1a1d] border border-[#e5e1d8] dark:border-[#2a2620] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#cba135]/30">
              <option value="likenew">Like New</option>
              <option value="good">Good</option>
              <option value="fair">Fair</option>
              <option value="broken">Broken / Faulty</option>
            </select>
          </div>
        </div>
        <div class="bg-[#f5f2ea] dark:bg-[#1a1a1d] rounded-2xl p-6 flex flex-col justify-center items-center text-center gap-3">
          <span class="text-xs uppercase tracking-wide text-[#a09a8c]">Estimated Trade-In Value</span>
          <span id="tradeEstimate" class="font-mono text-3xl font-bold text-[#a87c1f] dark:text-[#cba135]">—</span>
          <a id="tradeWaLink" href="#" target="_blank" class="mt-2 bg-[#16140f] dark:bg-[#cba135] text-[#cba135] dark:text-[#16140f] text-xs font-semibold px-5 py-2.5 rounded-full hover:opacity-90 transition flex items-center gap-1.5">
            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i> Get this confirmed on WhatsApp
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Accessories -->
  <section id="accessories" class="py-8 px-6 max-w-7xl mx-auto reveal">
    <div class="text-center mb-8">
      <span class="text-xs font-semibold uppercase tracking-wider text-[#a87c1f] dark:text-[#cba135] block mb-2">Complete the Setup</span>
      <h2 class="font-display text-2xl md:text-3xl font-semibold tracking-tight">Accessories</h2>
    </div>
    <div id="accessoriesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>
  </section>

  <!-- Store Locations -->
  <section id="branches" class="py-12 px-6 max-w-7xl mx-auto border-t border-[#e5e1d8] dark:border-[#232019] reveal">
    <div class="text-center mb-8">
      <h2 class="font-display text-xl font-semibold tracking-tight">Visit An Authorized Location</h2>
      <p class="text-xs text-[#6b6558] dark:text-[#9c9891] mt-1">Store pickup available in 6 South African locations</p>
    </div>

    <div id="storeMap" class="w-full h-72 rounded-2xl overflow-hidden mb-6 border border-[#e5e1d8] dark:border-[#232019]"></div>

    <div id="branchCards" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3"></div>
  </section>

  <?php require __DIR__ . '/includes/footer_brand.php'; ?>

  <!-- ======================= Compare bar (bottom, floating) ======================= -->
  <div id="compareBar" class="fixed bottom-0 left-0 right-0 z-40 bg-[#16140f] text-white px-6 py-3 flex flex-col sm:flex-row items-center gap-3 justify-between transition-all duration-300 translate-y-24 opacity-0 pointer-events-none">
    <div class="flex items-center gap-2 flex-wrap">
      <span class="text-xs font-semibold text-[#cba135] uppercase tracking-wide mr-1">Comparing:</span>
      <div id="compareChips" class="flex items-center gap-2 flex-wrap"></div>
    </div>
    <button id="compareCta" onclick="openCompareModal()" disabled
            class="bg-[#cba135] disabled:opacity-40 text-[#16140f] text-xs font-semibold px-5 py-2 rounded-full hover:opacity-90 transition whitespace-nowrap">
      Compare Models
    </button>
  </div>

  <!-- ======================= Cart drawer ======================= -->
  <div id="cartOverlay" onclick="closeCartDrawer()" class="hidden fixed inset-0 bg-black/50 z-40"></div>
  <aside id="cartDrawer" class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white dark:bg-[#131316] z-50 shadow-2xl transform translate-x-full transition-transform duration-300 flex flex-col">
    <div class="flex items-center justify-between px-5 py-4 border-b border-[#e5e1d8] dark:border-[#232019]">
      <h3 class="font-display text-lg font-bold">Your Cart</h3>
      <button onclick="closeCartDrawer()" class="w-8 h-8 rounded-full hover:bg-[#f5f2ea] dark:hover:bg-[#1a1a1d] flex items-center justify-center"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>
    <div id="cartDrawerBody" class="flex-1 flex flex-col overflow-y-auto"></div>
    <div id="cartDrawerFooter" class="hidden shrink-0 px-5 py-5 border-t border-[#e5e1d8] dark:border-[#232019] bg-white dark:bg-[#131316]"></div>
  </aside>

  <!-- ======================= Reviews modal ======================= -->
  <div id="reviewsModal" onclick="if(event.target===this) closeModal('reviewsModal')" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#131316] rounded-2xl w-full max-w-lg max-h-[85vh] overflow-y-auto p-6">
      <div id="reviewsModalBody"></div>
    </div>
  </div>

  <!-- ======================= Compare modal ======================= -->
  <div id="compareModal" onclick="if(event.target===this) closeModal('compareModal')" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#131316] rounded-2xl w-full max-w-3xl max-h-[85vh] overflow-y-auto p-6">
      <div id="compareModalBody"></div>
    </div>
  </div>

  <!-- ======================= Interactive viewer modal ======================= -->
  <div id="viewerModal" onclick="if(event.target===this) closeModal('viewerModal')" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#131316] rounded-2xl w-full max-w-md p-6">
      <div class="flex items-center justify-between mb-3">
        <h3 id="viewerTitle" class="font-display text-base font-bold"></h3>
        <button onclick="closeModal('viewerModal')" class="w-8 h-8 rounded-full hover:bg-[#f5f2ea] dark:hover:bg-[#1a1a1d] flex items-center justify-center"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <div id="viewerStage" class="w-full h-72 bg-[#f5f2ea] dark:bg-[#0b0b0d] rounded-xl flex items-center justify-center cursor-grab active:cursor-grabbing" style="perspective:800px;"></div>
      <p class="text-[10px] text-[#a09a8c] text-center mt-3">Drag to rotate</p>
    </div>
  </div>


  <!-- ======================= Login / Register modal ======================= -->
  <div id="authModal" onclick="if(event.target===this) closeModal('authModal')" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#131316] rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 id="authTitle" class="font-display text-xl font-bold">Welcome back</h3>
        <button onclick="closeModal('authModal')" class="w-8 h-8 rounded-full hover:bg-[#f5f2ea] dark:hover:bg-[#1a1a1d] flex items-center justify-center"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <p id="authNotice" class="hidden text-xs bg-[#cba135]/10 border border-[#cba135]/40 text-[#8a661a] dark:text-[#cba135] rounded-lg px-3 py-2 mb-4"></p>

      <div class="grid grid-cols-2 gap-1 bg-[#f5f2ea] dark:bg-[#1a1a1d] rounded-full p-1 mb-5 text-xs font-semibold">
        <button id="tabLogin" onclick="switchAuthTab('login')" class="py-2 rounded-full transition">Log in</button>
        <button id="tabRegister" onclick="switchAuthTab('register')" class="py-2 rounded-full transition">Create account</button>
      </div>

      <form id="loginForm" onsubmit="return submitAuth(event,'login')" novalidate>
        <div class="mb-3"><label class="text-[10px] font-semibold uppercase tracking-wide text-[#a09a8c] block mb-1.5">Email</label>
          <input name="email" type="email" placeholder="you@example.com" autocomplete="email" class="w-full bg-[#f5f2ea] dark:bg-[#1a1a1d] border border-[#e5e1d8] dark:border-[#2a2620] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#cba135]/30">
          <p class="text-[11px] text-[#b3272d] min-h-[14px] mt-1" data-err="email"></p></div>
        <div class="mb-3"><label class="text-[10px] font-semibold uppercase tracking-wide text-[#a09a8c] block mb-1.5">Password</label>
          <input name="password" type="password" placeholder="" autocomplete="current-password" class="w-full bg-[#f5f2ea] dark:bg-[#1a1a1d] border border-[#e5e1d8] dark:border-[#2a2620] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#cba135]/30">
          <p class="text-[11px] text-[#b3272d] min-h-[14px] mt-1" data-err="password"></p></div>
        <p class="text-[11px] text-[#b3272d] min-h-[14px]" data-err="form"></p>
        <button type="submit" class="w-full bg-[#16140f] dark:bg-[#cba135] text-[#cba135] dark:text-[#16140f] text-sm font-semibold py-3 rounded-full hover:opacity-90 transition mt-2">Log in</button>
      </form>

      <form id="registerForm" onsubmit="return submitAuth(event,'register')" class="hidden" novalidate>
        <div class="mb-3"><label class="text-[10px] font-semibold uppercase tracking-wide text-[#a09a8c] block mb-1.5">Full name</label>
          <input name="name" type="text" placeholder="Your full name" autocomplete="name" class="w-full bg-[#f5f2ea] dark:bg-[#1a1a1d] border border-[#e5e1d8] dark:border-[#2a2620] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#cba135]/30">
          <p class="text-[11px] text-[#b3272d] min-h-[14px] mt-1" data-err="name"></p></div>
        <div class="mb-3"><label class="text-[10px] font-semibold uppercase tracking-wide text-[#a09a8c] block mb-1.5">Email</label>
          <input name="email" type="email" placeholder="you@example.com" autocomplete="email" class="w-full bg-[#f5f2ea] dark:bg-[#1a1a1d] border border-[#e5e1d8] dark:border-[#2a2620] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#cba135]/30">
          <p class="text-[11px] text-[#b3272d] min-h-[14px] mt-1" data-err="email"></p></div>
        <div class="mb-3"><label class="text-[10px] font-semibold uppercase tracking-wide text-[#a09a8c] block mb-1.5">Phone</label>
          <input name="phone" type="tel" placeholder="0821234567" autocomplete="tel" class="w-full bg-[#f5f2ea] dark:bg-[#1a1a1d] border border-[#e5e1d8] dark:border-[#2a2620] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#cba135]/30">
          <p class="text-[11px] text-[#b3272d] min-h-[14px] mt-1" data-err="phone"></p></div>
        <div class="mb-3"><label class="text-[10px] font-semibold uppercase tracking-wide text-[#a09a8c] block mb-1.5">Password</label>
          <input name="password" type="password" placeholder="8+ characters, a capital and a number" autocomplete="new-password" class="w-full bg-[#f5f2ea] dark:bg-[#1a1a1d] border border-[#e5e1d8] dark:border-[#2a2620] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#cba135]/30">
          <p class="text-[11px] text-[#b3272d] min-h-[14px] mt-1" data-err="password"></p></div>
        <p class="text-[11px] text-[#b3272d] min-h-[14px]" data-err="form"></p>
        <button type="submit" class="w-full bg-[#16140f] dark:bg-[#cba135] text-[#cba135] dark:text-[#16140f] text-sm font-semibold py-3 rounded-full hover:opacity-90 transition mt-2">Create account</button>
        <p class="text-[10px] text-[#a09a8c] mt-3 text-center">We only store what we need to process your orders (POPIA).</p>
      </form>
    </div>
  </div>


  <!-- Server data for the scripts: base URL, CSRF token, who is logged in -->
  <script>
    window.APP = <?= json_encode([
      'base' => BASE_URL,
      'csrf' => csrf_token(),
      'user' => $user ? ['name' => $user['full_name'], 'email' => $user['email']] : null,
      'wa' => WHATSAPP_NUMBER,
      'openCart' => isset($_GET['cart']),
    ], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
  </script>
  <!-- Load order: theme/app helpers -> cart -> auth -> catalogue (script.js fetches api/catalog.php then renders) -->
  <script src="<?= asset('/assets/js/theme.js') ?>"></script>
  <script src="<?= asset('/assets/js/app.js') ?>"></script>
  <script src="<?= asset('/assets/js/cart.js') ?>"></script>
  <script src="<?= asset('/assets/js/auth.js') ?>"></script>
  <script src="<?= asset('/assets/js/script.js') ?>"></script>
  <script src="<?= asset('/assets/js/anim.js') ?>"></script>
</body>
</html>
