/* ==========================================================================
   cart.js — shopping cart, checkout summary, and local order history.
   The cart lives in this browser (localStorage) for an instant UI. "Proceed to
   Checkout" sends it to api/cart.php, which re-checks every line (price, colour,
   storage) against the database, then checkout.php takes delivery, payment and
   lay-by details and saves the order to MySQL. Loaded AFTER app.js, BEFORE script.js — script.js's card
   template calls addToCart(), and cart.js reads selectedColorState /
   selectedStorageState which script.js exposes on window.
   ========================================================================== */

/* ---------------------------------------------------------------------- */
/*  Cart storage                                                           */
/* ---------------------------------------------------------------------- */
function getCart() { return lsGet('cart', []); }
function saveCart(lines) { lsSet('cart', lines); renderCartUI(); }

function lineId(itemId, colorName, storageSize) {
  return `${itemId}__${colorName}__${storageSize}`.replace(/\s+/g, '-');
}

function addToCart(itemId) {
  const item = catalog.find(i => i.id === itemId);
  if (!item) return;
  const color = (window.selectedColorState && window.selectedColorState[itemId]) || item.colors[0];
  const storage = (window.selectedStorageState && window.selectedStorageState[itemId]) || item.storageOptions[0];

  if (typeof isOutOfStock === 'function' && isOutOfStock(storage)) {
    showToast(`${item.name} ${storage.size} is out of stock${window.getCurrentBranch && getCurrentBranch() ? ' at ' + getCurrentBranch() : ''}.`);
    return;
  }

  const lines = getCart();
  const id = lineId(itemId, color.name, storage.size);
  const existing = lines.find(l => l.id === id);
  const cap = typeof storage.stock === 'number' ? Math.min(10, Math.max(storage.stock, 1)) : 10;
  if (existing && existing.qty >= cap) { showToast(`Only ${cap} available for this model.`); return; }

  if (existing) {
    existing.qty += 1;
  } else {
    lines.push({
      id,
      itemId,
      name: item.name,
      colorName: color.name,
      storageSize: storage.size,
      price: storage.price,
      img: color.img,
      qty: 1
    });
  }
  saveCart(lines);
  showToast(`Added ${item.name} to your cart`);
  openCartDrawer();
}

function removeFromCart(id) {
  saveCart(getCart().filter(l => l.id !== id));
}

function changeQty(id, delta) {
  const lines = getCart();
  const line = lines.find(l => l.id === id);
  if (!line) return;
  line.qty += delta;
  if (line.qty <= 0) {
    saveCart(lines.filter(l => l.id !== id));
  } else {
    saveCart(lines);
  }
}

function cartCount() { return getCart().reduce((sum, l) => sum + l.qty, 0); }
function cartSubtotal() { return getCart().reduce((sum, l) => sum + l.price * l.qty, 0); }

/* ---------------------------------------------------------------------- */
/*  Cart drawer                                                            */
/* ---------------------------------------------------------------------- */
function openCartDrawer() {
  document.getElementById('cartDrawer').classList.remove('translate-x-full');
  document.getElementById('cartOverlay').classList.remove('hidden');
  renderCartUI();
}
function closeCartDrawer() {
  document.getElementById('cartDrawer').classList.add('translate-x-full');
  document.getElementById('cartOverlay').classList.add('hidden');
}
function toggleCartDrawer() {
  const isOpen = !document.getElementById('cartDrawer').classList.contains('translate-x-full');
  if (isOpen) closeCartDrawer(); else openCartDrawer();
}

function renderCartUI() {
  const badge = document.getElementById('cartCount');
  const count = cartCount();
  if (badge) {
    badge.textContent = String(count);
    badge.classList.toggle('hidden', count === 0);
    badge.classList.toggle('flex', count > 0);
  }

  const body = document.getElementById('cartDrawerBody');
  const foot = document.getElementById('cartDrawerFooter');
  if (!body) return;
  if (foot) foot.classList.add('hidden');
  const lines = getCart();

  if (lines.length === 0) {
    body.innerHTML = `<div class="flex-1 flex flex-col items-center justify-center text-center px-6">
      <i data-lucide="shopping-bag" class="w-8 h-8 text-[#a09a8c] mb-3"></i>
      <p class="text-sm text-[#a09a8c]">Your cart is empty.</p>
      <p class="text-xs text-[#a09a8c] mt-1">Add a model from the lineup to get started.</p>
    </div>`;
  } else {
    body.innerHTML = `
      <div class="overflow-y-auto px-5 py-4 space-y-3" >
        ${lines.map(l => `
          <div class="flex gap-3 border-b border-[#e5e1d8] dark:border-[#232019] pb-3">
            <img src="${encodeURI(l.img)}" alt="${l.name}" class="w-14 h-14 object-contain bg-[#f5f2ea] dark:bg-[#1a1a1d] rounded-lg flex-shrink-0" onerror="this.onerror=null; this.src=(typeof IMG_FALLBACK !== 'undefined' ? IMG_FALLBACK : '');">
            <div class="flex-1 min-w-0">
              <div class="text-xs font-semibold truncate">${l.name}</div>
              <div class="text-[10px] text-[#a09a8c] font-mono">${l.colorName} &middot; ${l.storageSize}</div>
              <div class="flex items-center justify-between mt-1.5">
                <div class="flex items-center gap-2">
                  <button onclick="changeQty('${l.id}', -1)" class="w-6 h-6 rounded-full border border-[#e5e1d8] dark:border-[#2a2620] flex items-center justify-center hover:border-[#cba135] text-xs">&minus;</button>
                  <span class="text-xs font-mono w-4 text-center">${l.qty}</span>
                  <button onclick="changeQty('${l.id}', 1)" class="w-6 h-6 rounded-full border border-[#e5e1d8] dark:border-[#2a2620] flex items-center justify-center hover:border-[#cba135] text-xs">+</button>
                </div>
                <span class="text-xs font-mono font-semibold">${fmtR(l.price * l.qty)}</span>
              </div>
            </div>
            <button onclick="removeFromCart('${l.id}')" class="text-[#a09a8c] hover:text-[#b3272d] flex-shrink-0"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
          </div>
        `).join('')}
      </div>
    `;
    foot.innerHTML = `
        <div class="flex justify-between text-sm font-semibold mb-3">
          <span>Subtotal</span><span class="font-mono">${fmtR(cartSubtotal())}</span>
        </div>
        <button onclick="proceedToCheckout()" class="w-full bg-[#16140f] dark:bg-[#cba135] text-[#cba135] dark:text-[#16140f] text-sm font-semibold py-4 rounded-full hover:opacity-90 transition shine">Proceed to Checkout &rarr;</button>
        <p class="text-[10px] text-[#a09a8c] text-center mt-3">Next: choose delivery, then pay with PayFast, Ozow or Yoco.</p>`;
    foot.classList.remove('hidden');
  }
  if (window.lucide) window.lucide.createIcons();
}

/* ---------------------------------------------------------------------- */
/*  Checkout - hand the cart to the server (it re-validates every line)    */
/* ---------------------------------------------------------------------- */
async function syncCartAndGo() {
  const lines = getCart();
  if (!lines.length) return;
  try {
    const res = await fetch(APP.base + '/api/cart.php', {
      method: 'POST', credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': APP.csrf },
      body: JSON.stringify({
        lines: lines.map(l => ({ itemId: l.itemId, colorName: l.colorName, storageSize: l.storageSize, qty: l.qty })),
        branch: (window.getCurrentBranch && getCurrentBranch()) || ''
      })
    });
    const data = await res.json();
    if (!data.ok) { showToast(data.error || 'Could not start checkout.'); return; }
    window.location.href = APP.base + '/checkout.php';
  } catch (err) {
    showToast('Network problem - please try again.');
  }
}

function proceedToCheckout() {
  if (getCart().length === 0) return;
  closeCartDrawer();
  if (!window.APP || !APP.user) {
    // Customers must have an account so orders and lay-by plans are saved to them
    openAuthModal('login', 'checkout', 'Please log in or create an account to check out.');
    return;
  }
  syncCartAndGo();
}

/* ---------------------------------------------------------------------- */
/*  Init                                                                   */
/* ---------------------------------------------------------------------- */
function initCart() { renderCartUI(); }

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initCart);
} else {
  initCart();
}