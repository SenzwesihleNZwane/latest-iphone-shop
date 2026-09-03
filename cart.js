/* ==========================================================================
   cart.js — shopping cart, checkout summary, and local order history.
   No payment gateway, no backend: checkout builds ONE consolidated WhatsApp
   message and saves a copy of the order to this browser's local storage so
   the customer (and the store owner testing on their own device) can see
   past orders. Loaded AFTER app.js, BEFORE script.js — script.js's card
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

  const lines = getCart();
  const id = lineId(itemId, color.name, storage.size);
  const existing = lines.find(l => l.id === id);

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
  if (!body) return;
  const lines = getCart();

  if (lines.length === 0) {
    body.innerHTML = `<div class="flex-1 flex flex-col items-center justify-center text-center px-6">
      <i data-lucide="shopping-bag" class="w-8 h-8 text-[#a09a8c] mb-3"></i>
      <p class="text-sm text-[#a09a8c]">Your cart is empty.</p>
      <p class="text-xs text-[#a09a8c] mt-1">Add a model from the lineup to get started.</p>
    </div>`;
  } else {
    body.innerHTML = `
      <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3">
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
      <div class="px-5 py-4 border-t border-[#e5e1d8] dark:border-[#232019]">
        <div class="flex justify-between text-sm font-semibold mb-3">
          <span>Subtotal</span><span class="font-mono">${fmtR(cartSubtotal())}</span>
        </div>
        <button onclick="openCheckoutModal()" class="w-full bg-[#16140f] dark:bg-[#cba135] text-[#cba135] dark:text-[#16140f] text-xs font-semibold py-3 rounded-full hover:opacity-90 transition">Proceed to Checkout</button>
      </div>
    `;
  }
  if (window.lucide) window.lucide.createIcons();
}

/* ---------------------------------------------------------------------- */
/*  Checkout — builds one consolidated WhatsApp message, no payment taken  */
/* ---------------------------------------------------------------------- */
function openCheckoutModal() {
  if (getCart().length === 0) return;
  closeCartDrawer();
  const modal = document.getElementById('checkoutModal');
  document.getElementById('checkoutSummary').innerHTML = getCart().map(l =>
    `<div class="flex justify-between text-xs py-1"><span>${l.qty}&times; ${l.name} (${l.colorName}, ${l.storageSize})</span><span class="font-mono">${fmtR(l.price * l.qty)}</span></div>`
  ).join('') + `<div class="flex justify-between text-sm font-semibold pt-2 mt-2 border-t border-[#e5e1d8] dark:border-[#232019]"><span>Total</span><span class="font-mono">${fmtR(cartSubtotal())}</span></div>`;

  const branchSelect = document.getElementById('checkoutBranch');
  if (branchSelect && branchSelect.options.length === 0) {
    branchSelect.innerHTML = branches.map(b => `<option value="${b.name}">${b.name}</option>`).join('');
  }
  modal.classList.remove('hidden');
  if (window.lucide) window.lucide.createIcons();
}

function submitOrder(evt) {
  evt.preventDefault();
  const form = evt.target;
  const name = form.custName.value.trim();
  const phone = form.custPhone.value.trim();
  const branch = form.custBranch.value;
  const paymentPref = form.custPayment.value;
  const notes = form.custNotes.value.trim();
  const lines = getCart();
  if (!name || !phone || lines.length === 0) return false;

  const orderId = 'LI' + Date.now().toString().slice(-8);
  const itemsText = lines.map(l => `\u2022 ${l.qty}x ${l.name} (${l.colorName}, ${l.storageSize}) — ${fmtR(l.price * l.qty)}`).join('\n');
  const message =
`Hi Latest-iPhone, I'd like to place an order (Ref: ${orderId}).

${itemsText}

Total: ${fmtR(cartSubtotal())}
Preferred branch: ${branch}
Payment: ${paymentPref}
Name: ${name}
Phone: ${phone}${notes ? `\nNotes: ${notes}` : ''}`;

  // Save a local copy of the order (this browser only — there is no backend/database)
  const orders = lsGet('orders', []);
  orders.unshift({
    id: orderId,
    date: new Date().toISOString(),
    items: lines,
    total: cartSubtotal(),
    branch, paymentPref, name, phone, notes,
    status: 'Sent via WhatsApp'
  });
  lsSet('orders', orders);

  window.open(`https://wa.me/${waPhone}?text=${encodeURIComponent(message)}`, '_blank');

  saveCart([]);
  closeModal('checkoutModal');
  showToast(`Order ${orderId} sent — check WhatsApp to confirm with us.`);
  return false;
}

/* ---------------------------------------------------------------------- */
/*  Order history (local to this browser — no account, no server)         */
/* ---------------------------------------------------------------------- */
function openOrderHistory() {
  const modal = document.getElementById('ordersModal');
  const body = document.getElementById('ordersModalBody');
  const orders = lsGet('orders', []);

  body.innerHTML = `
    <div class="flex items-center justify-between mb-1">
      <h3 class="font-display text-xl font-bold">Your Orders</h3>
      <button onclick="closeModal('ordersModal')" class="w-8 h-8 rounded-full hover:bg-[#f5f2ea] dark:hover:bg-[#1a1a1d] flex items-center justify-center"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>
    <p class="text-[10px] text-[#a09a8c] font-mono mb-4">Stored on this device only — orders won't appear on other phones or browsers.</p>
    ${orders.length === 0 ? `<p class="text-sm text-[#a09a8c] text-center py-10">No orders placed from this browser yet.</p>` : orders.map(o => `
      <div class="border border-[#e5e1d8] dark:border-[#232019] rounded-xl p-4 mb-3">
        <div class="flex justify-between items-start mb-2">
          <div>
            <div class="text-xs font-mono font-semibold">${o.id}</div>
            <div class="text-[10px] text-[#a09a8c]">${new Date(o.date).toLocaleString()}</div>
          </div>
          <span class="text-[10px] font-mono bg-[#f5f2ea] dark:bg-[#1a1a1d] px-2 py-1 rounded-full text-[#a87c1f] dark:text-[#cba135]">${o.status}</span>
        </div>
        ${o.items.map(l => `<div class="text-xs text-[#6b6558] dark:text-[#9c9891] flex justify-between"><span>${l.qty}&times; ${l.name}</span><span class="font-mono">${fmtR(l.price * l.qty)}</span></div>`).join('')}
        <div class="flex justify-between text-xs font-semibold mt-2 pt-2 border-t border-[#e5e1d8] dark:border-[#232019]">
          <span>Total</span><span class="font-mono">${fmtR(o.total)}</span>
        </div>
      </div>
    `).join('')}
  `;
  modal.classList.remove('hidden');
  if (window.lucide) window.lucide.createIcons();
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