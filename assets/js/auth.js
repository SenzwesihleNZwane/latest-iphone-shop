/* auth.js - login / register pop-up on the home page (talks to api/auth.php). */
let authIntent = null;

function openAuthModal(tab, intent, notice) {
  authIntent = intent || null;
  const n = document.getElementById('authNotice');
  if (n) { n.textContent = notice || ''; n.classList.toggle('hidden', !notice); }
  switchAuthTab(tab || 'login');
  const m = document.getElementById('authModal');
  if (m) m.classList.remove('hidden');
  const menu = document.getElementById('acctMenu'); if (menu) menu.classList.add('hidden');
  if (window.lucide) window.lucide.createIcons();
}

function switchAuthTab(tab) {
  const isLogin = tab === 'login';
  document.getElementById('loginForm').classList.toggle('hidden', !isLogin);
  document.getElementById('registerForm').classList.toggle('hidden', isLogin);
  document.getElementById('authTitle').textContent = isLogin ? 'Welcome back' : 'Create your account';
  const on = ['bg-[#16140f]', 'text-[#cba135]', 'dark:bg-[#cba135]', 'dark:text-[#16140f]'];
  const off = ['text-[#6b6558]', 'dark:text-[#9c9891]'];
  [['tabLogin', isLogin], ['tabRegister', !isLogin]].forEach(([id, active]) => {
    const b = document.getElementById(id);
    b.classList.remove(...on, ...off);
    b.classList.add(...(active ? on : off));
  });
  document.querySelectorAll('#authModal [data-err]').forEach(p => p.textContent = '');
}

function authRules(action, f) {
  const e = {};
  const v = n => (f[n] ? f[n].value.trim() : '');
  if (action === 'register' && !v('name')) e.name = 'Enter your full name.';
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v('email'))) e.email = 'Enter a valid email address.';
  if (action === 'register') {
    if (!/^(\+27|0)[0-9]{9}$/.test(v('phone').replace(/\s+/g, ''))) e.phone = 'Use a South African number, e.g. 0821234567.';
    if (!/^(?=.*[A-Z])(?=.*[0-9]).{8,}$/.test(f.password.value)) e.password = 'Min 8 characters with a capital letter and a number.';
  } else if (!f.password.value) e.password = 'Enter your password.';
  return e;
}

function showAuthErrors(form, errors) {
  form.querySelectorAll('[data-err]').forEach(p => p.textContent = errors[p.dataset.err] || '');
}

async function submitAuth(evt, action) {
  evt.preventDefault();
  const form = evt.target;
  const errors = authRules(action, form);
  showAuthErrors(form, errors);
  if (Object.keys(errors).length) return false;

  const body = { action, email: form.email.value.trim(), password: form.password.value };
  if (action === 'register') { body.name = form.name.value.trim(); body.phone = form.phone.value.trim(); }
  const btn = form.querySelector('button[type=submit]');
  btn.disabled = true;
  try {
    const res = await fetch(APP.base + '/api/auth.php', {
      method: 'POST', credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': APP.csrf },
      body: JSON.stringify(body)
    });
    const data = await res.json();
    if (!data.ok) {
      showAuthErrors(form, data.errors || { form: data.error || 'Something went wrong.' });
      btn.disabled = false;
      return false;
    }
    APP.user = data.user;
    if (authIntent === 'checkout') { await syncCartAndGo(); return false; }
    window.location.reload();
  } catch (err) {
    showAuthErrors(form, { form: 'Network problem - please try again.' });
    btn.disabled = false;
  }
  return false;
}
