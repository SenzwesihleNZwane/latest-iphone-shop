/* ==========================================================================
   app.js — premium feature layer (reviews, comparison, calculator, trade-in,
   countdown, interactive viewer, accessories, store map, scroll reveal).
   Loaded BEFORE script.js so its helpers exist before the
   catalog renders. Depends on `catalog`, `waPhone`, and the color/storage
   state objects defined in script.js — only calls into them from inside
   event handlers (after both scripts have parsed), never at top level.
   ========================================================================== */

/* ---------------------------------------------------------------------- */
/*  0. Small utilities                                                     */
/* ---------------------------------------------------------------------- */
function fmtR(n) { return `R ${Math.round(n).toLocaleString()}`; }

function lsGet(key, fallback) {
  try {
    const raw = localStorage.getItem(key);
    return raw ? JSON.parse(raw) : fallback;
  } catch (e) { return fallback; }
}
function lsSet(key, value) {
  try { localStorage.setItem(key, JSON.stringify(value)); } catch (e) { /* ignore */ }
}

/* ---------------------------------------------------------------------- */
/*  1. Stock status                                                        */
/*  Data-driven, no fabricated urgency: a model shows "In Stock" unless    */
/*  the store owner sets a real `stock` number on a storage option in the  */
/*  catalog (script.js). Edit the catalog to reflect real counts.          */
/* ---------------------------------------------------------------------- */
function getStockInfo(storageOption) {
  const n = storageOption && typeof storageOption.stock === 'number' ? storageOption.stock : null;
  if (n === null) return { label: 'In Stock', dot: 'bg-emerald-500', text: 'text-emerald-700 dark:text-emerald-400' };
  if (n <= 0) return { label: 'Out of Stock', dot: 'bg-[#a09a8c]', text: 'text-[#a09a8c]' };
  if (n <= 3) return { label: `Only ${n} left`, dot: 'bg-[#b3272d] animate-pulse', text: 'text-[#b3272d]' };
  if (n <= 10) return { label: 'Low Stock', dot: 'bg-[#a87c1f]', text: 'text-[#a87c1f] dark:text-[#cba135]' };
  return { label: 'In Stock', dot: 'bg-emerald-500', text: 'text-emerald-700 dark:text-emerald-400' };
}

/* ---------------------------------------------------------------------- */
/*  2. Customer reviews (real, visitor-submitted — stored locally)         */
/*  Starts empty per item. This is a genuine collection mechanism, not     */
/*  pre-seeded testimonials. For reviews shared across all visitors, wire  */
/*  this up to a small backend / spreadsheet later.                       */
/* ---------------------------------------------------------------------- */
function getReviews(itemId) { return lsGet(`reviews:${itemId}`, []); }

function addReview(itemId, name, rating, comment) {
  const list = getReviews(itemId);
  list.unshift({ name: name || 'Anonymous', rating, comment, date: new Date().toISOString() });
  lsSet(`reviews:${itemId}`, list);
}

function getAverageRating(itemId) {
  const list = getReviews(itemId);
  if (!list.length) return null;
  const avg = list.reduce((s, r) => s + r.rating, 0) / list.length;
  return { avg: Math.round(avg * 10) / 10, count: list.length };
}

function starsHTML(rating, sizeClass) {
  sizeClass = sizeClass || 'w-3.5 h-3.5';
  let out = '';
  for (let i = 1; i <= 5; i++) {
    const filled = i <= Math.round(rating);
    out += `<svg class="${sizeClass} ${filled ? 'text-[#cba135]' : 'text-[#d8d3c5] dark:text-[#3a362c]'}" fill="currentColor" viewBox="0 0 20 20"><path d="M10 1.5l2.6 5.27 5.82.85-4.21 4.1.99 5.79L10 14.9l-5.2 2.61.99-5.79-4.21-4.1 5.82-.85z"/></svg>`;
  }
  return out;
}

function openReviewsModal(itemId) {
  const item = catalog.find(i => i.id === itemId);
  if (!item) return;
  const modal = document.getElementById('reviewsModal');
  const body = document.getElementById('reviewsModalBody');
  const rating = getAverageRating(itemId);

  const reviewsListHTML = getReviews(itemId).map(r => `
    <div class="border-b border-[#e5e1d8] dark:border-[#232019] py-3">
      <div class="flex items-center justify-between mb-1">
        <span class="text-sm font-semibold">${escapeHTML(r.name)}</span>
        <div class="flex">${starsHTML(r.rating)}</div>
      </div>
      <p class="text-xs text-[#6b6558] dark:text-[#9c9891]">${escapeHTML(r.comment)}</p>
    </div>
  `).join('') || `<p class="text-sm text-[#a09a8c] py-6 text-center">No reviews yet — be the first to share your experience with the ${item.name}.</p>`;

  body.innerHTML = `
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="font-display text-xl font-bold">${item.name}</h3>
        ${rating ? `<div class="flex items-center gap-1.5 mt-1"><div class="flex">${starsHTML(rating.avg)}</div><span class="text-xs font-mono text-[#6b6558] dark:text-[#9c9891]">${rating.avg} &middot; ${rating.count} review${rating.count === 1 ? '' : 's'}</span></div>` : `<p class="text-xs text-[#a09a8c] mt-1">No ratings yet</p>`}
      </div>
      <button onclick="closeModal('reviewsModal')" class="w-8 h-8 rounded-full hover:bg-[#f5f2ea] dark:hover:bg-[#1a1a1d] flex items-center justify-center"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>

    <form id="reviewForm" class="bg-[#f5f2ea] dark:bg-[#1a1a1d] rounded-xl p-4 mb-4" onsubmit="return submitReview(event, '${itemId}')">
      <div class="grid grid-cols-2 gap-2 mb-2">
        <input required name="name" placeholder="Your name" class="col-span-2 bg-white dark:bg-[#131316] border border-[#e5e1d8] dark:border-[#2a2620] rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#cba135]/30">
        <select name="rating" class="col-span-2 bg-white dark:bg-[#131316] border border-[#e5e1d8] dark:border-[#2a2620] rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#cba135]/30">
          <option value="5">&#9733;&#9733;&#9733;&#9733;&#9733; Excellent</option>
          <option value="4">&#9733;&#9733;&#9733;&#9733; Good</option>
          <option value="3">&#9733;&#9733;&#9733; Average</option>
          <option value="2">&#9733;&#9733; Poor</option>
          <option value="1">&#9733; Bad</option>
        </select>
      </div>
      <textarea required name="comment" rows="2" placeholder="Share your experience with this model..." class="w-full bg-white dark:bg-[#131316] border border-[#e5e1d8] dark:border-[#2a2620] rounded-lg px-3 py-2 text-xs mb-2 focus:outline-none focus:ring-2 focus:ring-[#cba135]/30"></textarea>
      <button type="submit" class="w-full bg-[#16140f] dark:bg-[#cba135] text-[#cba135] dark:text-[#16140f] text-xs font-semibold py-2 rounded-lg hover:opacity-90 transition">Post Review</button>
    </form>

    <div id="reviewsList">${reviewsListHTML}</div>
  `;
  modal.classList.remove('hidden');
  if (window.lucide) window.lucide.createIcons();
}

function submitReview(evt, itemId) {
  evt.preventDefault();
  const form = evt.target;
  const name = form.name.value.trim();
  const rating = parseInt(form.rating.value, 10);
  const comment = form.comment.value.trim();
  if (!comment) return false;
  addReview(itemId, name, rating, comment);
  openReviewsModal(itemId); // re-render
  if (typeof applyFilters === 'function') applyFilters(); // refresh star summary on card
  return false;
}

function escapeHTML(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

/* ---------------------------------------------------------------------- */
/*  3. Comparison tool (up to 3 models side by side)                       */
/* ---------------------------------------------------------------------- */
let compareList = [];

function toggleCompare(itemId, checkboxEl) {
  const idx = compareList.indexOf(itemId);
  if (idx > -1) {
    compareList.splice(idx, 1);
  } else {
    if (compareList.length >= 3) {
      if (checkboxEl) checkboxEl.checked = false;
      showToast('You can compare up to 3 models at a time.');
      return;
    }
    compareList.push(itemId);
  }
  renderCompareBar();
}

function renderCompareBar() {
  const bar = document.getElementById('compareBar');
  if (!bar) return;
  if (compareList.length === 0) {
    bar.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
    return;
  }
  bar.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
  const chips = compareList.map(id => {
    const item = catalog.find(i => i.id === id);
    if (!item) return '';
    return `<span class="flex items-center gap-1.5 bg-white/10 text-white text-[11px] font-medium px-2.5 py-1 rounded-full">${item.name}<button onclick="toggleCompare('${id}')" class="hover:text-[#cba135]">&times;</button></span>`;
  }).join('');
  document.getElementById('compareChips').innerHTML = chips;
  document.getElementById('compareCta').disabled = compareList.length < 2;
}

function openCompareModal() {
  if (compareList.length < 2) return;
  const items = compareList.map(id => catalog.find(i => i.id === id)).filter(Boolean);
  const modal = document.getElementById('compareModal');
  const body = document.getElementById('compareModalBody');

  const rows = [
    ['Price (from)', it => fmtR(it.storageOptions[0].price)],
    ['Display', it => it.display],
    ['Chip', it => it.chip],
    ['Camera', it => it.camera],
    ['Battery', it => it.battery],
    ['Storage Options', it => it.storageOptions.map(o => o.size).join(' / ')],
  ];

  body.innerHTML = `
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display text-xl font-bold">Compare Models</h3>
      <button onclick="closeModal('compareModal')" class="w-8 h-8 rounded-full hover:bg-[#f5f2ea] dark:hover:bg-[#1a1a1d] flex items-center justify-center"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-xs border-collapse">
        <thead>
          <tr>
            <th class="text-left p-2 font-mono text-[#a09a8c] w-32"></th>
            ${items.map(it => `<th class="text-left p-2 font-display font-bold text-sm">${it.name}</th>`).join('')}
          </tr>
        </thead>
        <tbody>
          ${rows.map(([label, fn]) => `
            <tr class="border-t border-[#e5e1d8] dark:border-[#232019]">
              <td class="p-2 font-mono text-[#a09a8c] uppercase text-[10px] tracking-wide align-top">${label}</td>
              ${items.map(it => `<td class="p-2 align-top">${fn(it)}</td>`).join('')}
            </tr>
          `).join('')}
        </tbody>
      </table>
    </div>
    <div class="mt-5 grid gap-2" style="grid-template-columns: repeat(${items.length}, minmax(0,1fr));">
      ${items.map(it => `<a href="https://wa.me/${waPhone}?text=${encodeURIComponent('Hi Latest-iPhone, I am comparing the ' + it.name + ' and would like more info.')}" target="_blank" class="text-center bg-[#16140f] dark:bg-[#cba135] text-[#cba135] dark:text-[#16140f] text-[11px] font-semibold py-2 rounded-lg hover:opacity-90 transition">Ask about ${it.name.split(' ').slice(-1)}</a>`).join('')}
    </div>
  `;
  modal.classList.remove('hidden');
  if (window.lucide) window.lucide.createIcons();
}

/* ---------------------------------------------------------------------- */
/*  4. Lay-by payment calculator                                           */
/* ---------------------------------------------------------------------- */
function initCalculator() {
  const modelSelect = document.getElementById('calcModel');
  if (!modelSelect) return;

  modelSelect.innerHTML = catalog.map(it =>
    `<option value="${it.id}">${it.name} — from ${fmtR(it.storageOptions[0].price)}</option>`
  ).join('');

  ['calcModel', 'calcMonths', 'calcDeposit'].forEach(id => {
    document.getElementById(id).addEventListener('input', updateCalculator);
  });
  updateCalculator();
}

function updateCalculator() {
  const modelSelect = document.getElementById('calcModel');
  const months = parseInt(document.getElementById('calcMonths').value, 10);
  const depositPct = parseInt(document.getElementById('calcDeposit').value, 10);

  const item = catalog.find(i => i.id === modelSelect.value);
  if (!item) return;
  const price = item.storageOptions[0].price;
  const deposit = Math.round(price * (depositPct / 100));
  const balance = price - deposit;
  const monthly = Math.round(balance / months);

  document.getElementById('calcMonthsLabel').textContent = `${months} month${months === 1 ? '' : 's'}`;
  document.getElementById('calcDepositLabel').textContent = `${depositPct}% (${fmtR(deposit)})`;
  document.getElementById('calcMonthly').textContent = fmtR(monthly) + '/mo';
  document.getElementById('calcTotal').textContent = fmtR(price);
  document.getElementById('calcBalance').textContent = fmtR(balance);
}

/* ---------------------------------------------------------------------- */
/*  5. Trade-in estimator (illustrative — final value confirmed in-store)  */
/* ---------------------------------------------------------------------- */
const TRADE_IN_MULTIPLIERS = { likenew: 0.55, good: 0.40, fair: 0.25, broken: 0.10 };

function initTradeIn() {
  const select = document.getElementById('tradeModel');
  if (!select) return;
  select.innerHTML = catalog.map(it => `<option value="${it.id}">${it.name}</option>`).join('');
  document.getElementById('tradeCondition').addEventListener('change', updateTradeIn);
  document.getElementById('tradeModel').addEventListener('change', updateTradeIn);
  updateTradeIn();
}

function updateTradeIn() {
  const item = catalog.find(i => i.id === document.getElementById('tradeModel').value);
  const condition = document.getElementById('tradeCondition').value;
  if (!item) return;
  const basePrice = item.storageOptions[0].price;
  const estimate = Math.round(basePrice * TRADE_IN_MULTIPLIERS[condition]);
  document.getElementById('tradeEstimate').textContent = fmtR(estimate);
  document.getElementById('tradeWaLink').href = `https://wa.me/${waPhone}?text=${encodeURIComponent(`Hi Latest-iPhone, I'd like to trade in my ${item.name} (condition: ${condition}). Estimated value: ${fmtR(estimate)}.`)}`;
}

/* ---------------------------------------------------------------------- */
/*  6. Smart Match quiz (rule-based recommendation, not a live AI call)    */
/* ---------------------------------------------------------------------- */
const quizState = { budget: null, priority: null, size: null };

function quizPick(field, value, btn) {
  quizState[field] = value;
  const group = btn.closest('[data-quiz-group]');
  group.querySelectorAll('button').forEach(b => b.classList.remove('bg-[#16140f]', 'text-[#cba135]', 'dark:bg-[#cba135]', 'dark:text-[#16140f]'));
  btn.classList.add('bg-[#16140f]', 'text-[#cba135]', 'dark:bg-[#cba135]', 'dark:text-[#16140f]');

  if (quizState.budget && quizState.priority && quizState.size) {
    runQuiz();
  }
}

function runQuiz() {
  const budgetRange = {
    entry: [0, 12000],
    mid: [10000, 20000],
    premium: [18000, 40000]
  }[quizState.budget];

  const scored = catalog.map(item => {
    const price = item.storageOptions[0].price;
    let score = 0;

    if (price >= budgetRange[0] && price <= budgetRange[1]) score += 3;
    else score -= Math.abs(price - budgetRange[1]) / 5000;

    if (quizState.priority === 'camera' && /Pro|Triple|Fusion/i.test(item.camera + item.name)) score += 2;
    if (quizState.priority === 'battery' && parseInt(item.battery) >= 26) score += 2;
    if (quizState.priority === 'performance' && /A1[6-9]/.test(item.chip)) score += 2;
    if (quizState.priority === 'value' && genOrderSafe(item.gen) <= 4) score += 2;

    if (quizState.size === 'compact' && !/Plus|Max/i.test(item.name)) score += 2;
    if (quizState.size === 'standard' && !/Max/i.test(item.name)) score += 1;
    if (quizState.size === 'max' && /Max/i.test(item.name)) score += 2;

    score += genOrderSafe(item.gen) * 0.3; // mild recency bias
    return { item, score };
  }).sort((a, b) => b.score - a.score);

  const top3 = scored.slice(0, 3).map(s => s.item);
  renderQuizResults(top3);
}

function genOrderSafe(gen) {
  const order = { '17': 7, '16': 6, '15': 5, '14': 4, '13': 3, '12': 2, '11': 1, 'x': 0 };
  return order[gen] !== undefined ? order[gen] : 0;
}

function renderQuizResults(items) {
  const el = document.getElementById('quizResults');
  if (!el) return;
  el.classList.remove('hidden');
  el.innerHTML = `
    <p class="text-xs font-mono uppercase tracking-wide text-[#a87c1f] dark:text-[#cba135] mb-3">Recommended for you</p>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      ${items.map(it => `
        <button onclick="jumpToModel('${it.id}')" class="text-left bg-white dark:bg-[#131316] border border-[#e5e1d8] dark:border-[#232019] hover:border-[#cba135] rounded-xl p-3 transition">
          <div class="font-display font-bold text-sm mb-0.5">${it.name}</div>
          <div class="text-xs text-[#6b6558] dark:text-[#9c9891] font-mono">from ${fmtR(it.storageOptions[0].price)}</div>
        </button>
      `).join('')}
    </div>
  `;
  if (window.lucide) window.lucide.createIcons();
}

function jumpToModel(itemId) {
  filterGen('all', document.querySelector('.gen-btn'));
  const searchInput = document.getElementById('searchInput');
  if (searchInput) searchInput.value = '';
  applyFilters();
  requestAnimationFrame(() => {
    const cardImg = document.getElementById(`img-${itemId}`);
    const card = cardImg ? cardImg.closest('.card-enter') : null;
    if (card) {
      card.scrollIntoView({ behavior: 'smooth', block: 'center' });
      card.classList.add('ring-2', 'ring-[#cba135]');
      setTimeout(() => card.classList.remove('ring-2', 'ring-[#cba135]'), 2200);
    }
  });
}

/* ---------------------------------------------------------------------- */
/*  7. Clearance countdown                                                 */
/*  Set SALE_END_DATE to a real deadline you intend to honor. The banner   */
/*  hides itself automatically once the date passes (no fake reset loop).  */
/* ---------------------------------------------------------------------- */
const SALE_END_DATE = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0, 23, 59, 59); // end of this month

function initCountdown() {
  const el = document.getElementById('countdownBanner');
  if (!el) return;
  const tick = () => {
    const diff = SALE_END_DATE.getTime() - Date.now();
    if (diff <= 0) { el.classList.add('hidden'); return; }
    const d = Math.floor(diff / 86400000);
    const h = Math.floor((diff % 86400000) / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    document.getElementById('cdD').textContent = String(d).padStart(2, '0');
    document.getElementById('cdH').textContent = String(h).padStart(2, '0');
    document.getElementById('cdM').textContent = String(m).padStart(2, '0');
    document.getElementById('cdS').textContent = String(s).padStart(2, '0');
  };
  tick();
  setInterval(tick, 1000);
}

/* ---------------------------------------------------------------------- */
/*  8. Interactive viewer (tilt/zoom — a practical stand-in for true 360°, */
/*  which needs 24-36 multi-angle photos per colour that aren't available) */
/* ---------------------------------------------------------------------- */
function openViewer(itemId) {
  const item = catalog.find(i => i.id === itemId);
  if (!item) return;
  const color = (window.selectedColorState && window.selectedColorState[itemId]) || item.colors[0];
  const modal = document.getElementById('viewerModal');
  const stage = document.getElementById('viewerStage');
  const tint = typeof hexToRecolorFilter === 'function' ? hexToRecolorFilter(color.hex) : 'none';
  stage.innerHTML = `<img src="${encodeURI(color.img)}" alt="${item.name}" id="viewerImg" style="filter:${tint}" class="w-full h-full object-contain transition-transform duration-100 ease-out select-none" draggable="false" onerror="this.onerror=null; this.src=(typeof IMG_FALLBACK !== 'undefined' ? IMG_FALLBACK : '');">`;
  document.getElementById('viewerTitle').textContent = `${item.name} — Interactive View`;
  modal.classList.remove('hidden');

  let rotX = 0, rotY = 0, dragging = false, lastX = 0, lastY = 0;
  const img = document.getElementById('viewerImg');

  const apply = () => { img.style.transform = `scale(1.05) rotateY(${rotY}deg) rotateX(${rotX}deg)`; };

  stage.onpointerdown = (e) => { dragging = true; lastX = e.clientX; lastY = e.clientY; };
  window.addEventListener('pointerup', () => dragging = false);
  stage.onpointermove = (e) => {
    if (!dragging) return;
    rotY += (e.clientX - lastX) * 0.4;
    rotX -= (e.clientY - lastY) * 0.4;
    rotX = Math.max(-20, Math.min(20, rotX));
    lastX = e.clientX; lastY = e.clientY;
    apply();
  };
  stage.onpointerleave = () => dragging = false;
}

/* ---------------------------------------------------------------------- */
/*  9. Accessories                                                         */
/* ---------------------------------------------------------------------- */
const accessories = [
  { id: 'acc1', name: 'MagSafe Leather Case', category: 'Cases & Covers', price: 899, icon: 'shield' },
  { id: 'acc2', name: 'Tempered Glass Screen Protector', category: 'Protection', price: 349, icon: 'square' },
  { id: 'acc3', name: '20W USB-C Fast Charger', category: 'Power', price: 549, icon: 'zap' },
  { id: 'acc4', name: 'MagSafe Wireless Charger', category: 'Power', price: 899, icon: 'battery-charging' },
  { id: 'acc5', name: 'AirPods Pro (2nd Gen)', category: 'Audio', price: 4499, icon: 'headphones' },
  { id: 'acc6', name: 'Braided USB-C Cable (2m)', category: 'Cables', price: 299, icon: 'cable' },
];

function renderAccessories() {
  const grid = document.getElementById('accessoriesGrid');
  if (!grid) return;
  grid.innerHTML = accessories.map(a => `
    <div class="bg-white dark:bg-[#131316] border border-[#e5e1d8] dark:border-[#232019] rounded-2xl p-5 hover:border-[#cba135]/60 transition flex flex-col">
      <div class="w-11 h-11 rounded-full bg-[#f5f2ea] dark:bg-[#1a1a1d] flex items-center justify-center mb-4">
        <i data-lucide="${a.icon}" class="w-5 h-5 text-[#a87c1f] dark:text-[#cba135]"></i>
      </div>
      <div class="text-[10px] font-mono uppercase tracking-wide text-[#a09a8c] mb-1">${a.category}</div>
      <div class="font-display font-bold text-sm mb-2 flex-1">${a.name}</div>
      <div class="flex items-center justify-between mt-2">
        <span class="font-mono font-semibold text-sm">${fmtR(a.price)}</span>
        <a href="https://wa.me/${waPhone}?text=${encodeURIComponent('Hi Latest-iPhone, I would like to inquire about the ' + a.name + '.')}" target="_blank" class="text-[11px] font-semibold text-[#a87c1f] dark:text-[#cba135] hover:underline">Inquire &rarr;</a>
      </div>
    </div>
  `).join('');
  if (window.lucide) window.lucide.createIcons();
}

/* ---------------------------------------------------------------------- */
/*  10. Store map (Leaflet + OpenStreetMap — no API key required)          */
/* ---------------------------------------------------------------------- */
const branches = [
  { name: 'Pretoria', lat: -25.7479, lng: 28.2293 },
  { name: 'Durban', lat: -29.8587, lng: 31.0218 },
  { name: 'Pinetown', lat: -29.8149, lng: 30.8709 },
  { name: 'Richards Bay', lat: -28.7807, lng: 32.0383 },
  { name: 'Nelspruit', lat: -25.4753, lng: 30.9700 },
  { name: 'Manguzi', lat: -26.9761, lng: 32.7513 },
];

function initStoreMap() {
  const mapEl = document.getElementById('storeMap');
  if (!mapEl || typeof L === 'undefined') return;

  const map = L.map('storeMap', { scrollWheelZoom: false }).setView([-27.8, 30.5], 5.6);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 18
  }).addTo(map);

  const goldIcon = L.divIcon({
    className: '',
    html: `<div style="width:16px;height:16px;border-radius:50%;background:#cba135;border:3px solid #16140f;box-shadow:0 0 0 3px rgba(203,161,53,0.35);"></div>`,
    iconSize: [16, 16],
    iconAnchor: [8, 8]
  });

  branches.forEach(b => {
    L.marker([b.lat, b.lng], { icon: goldIcon }).addTo(map)
      .bindPopup(`<strong>${b.name}</strong><br><a href="https://www.google.com/maps/search/?api=1&query=${b.lat},${b.lng}" target="_blank">Get Directions</a>`);
  });
}

/* ---------------------------------------------------------------------- */
/*  11. Scroll-reveal (IntersectionObserver, respects reduced motion)      */
/* ---------------------------------------------------------------------- */
function initScrollReveal() {
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const els = document.querySelectorAll('.reveal');
  if (prefersReduced || !('IntersectionObserver' in window)) {
    els.forEach(el => el.classList.add('reveal-visible'));
    return;
  }
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('reveal-visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
  els.forEach(el => observer.observe(el));
}

/* ---------------------------------------------------------------------- */
/*  12. Modal + toast helpers                                              */
/* ---------------------------------------------------------------------- */
function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.classList.add('hidden');
}

let toastTimer = null;
function showToast(msg) {
  let toast = document.getElementById('appToast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'appToast';
    toast.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] bg-[#16140f] text-[#cba135] text-xs font-medium px-4 py-2.5 rounded-full shadow-lg opacity-0 transition-opacity duration-300 pointer-events-none';
    document.body.appendChild(toast);
  }
  toast.textContent = msg;
  toast.style.opacity = '1';
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => { toast.style.opacity = '0'; }, 2400);
}

/* ---------------------------------------------------------------------- */
/*  13. Init everything that doesn't depend on the product grid            */
/* ---------------------------------------------------------------------- */
function initApp() {
  // Each feature is independent — if one fails (e.g. the map can't reach
  // OpenStreetMap), it's logged to the console instead of breaking the rest
  // of the page (including the product images and cart).
  const steps = [initCalculator, initTradeIn, initCountdown, renderAccessories, initStoreMap, initScrollReveal, renderCompareBar];
  steps.forEach(fn => {
    try { fn(); } catch (err) { console.error(`[app.js] ${fn.name} failed:`, err); }
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApp);
} else {
  initApp();
}