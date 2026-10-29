/* Shared on every page: dark-mode toggle, account menu, icons, client-side form validation. */
function updateThemeUI(isDark) {
  var icon = document.getElementById('themeIcon');
  if (icon) icon.setAttribute('data-lucide', isDark ? 'sun' : 'moon');
  if (window.lucide && typeof window.lucide.createIcons === 'function') window.lucide.createIcons();
}
function toggleDarkMode() {
  var isDark = document.documentElement.classList.toggle('dark');
  try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (e) {}
  updateThemeUI(isDark);
}
function toggleAcctMenu(ev) {
  if (ev) ev.stopPropagation();
  var m = document.getElementById('acctMenu');
  if (m) m.classList.toggle('hidden');
}
document.addEventListener('click', function (ev) {
  var m = document.getElementById('acctMenu');
  if (m && !m.classList.contains('hidden') && !m.contains(ev.target)) m.classList.add('hidden');
});
document.addEventListener('DOMContentLoaded', function () {
  updateThemeUI(document.documentElement.classList.contains('dark'));

  /* Forms with data-validate: block submit and show inline messages (server re-validates everything). */
  document.querySelectorAll('form[data-validate]').forEach(function (form) {
    form.addEventListener('submit', function (ev) {
      var ok = true;
      form.querySelectorAll('[data-rule]').forEach(function (el) {
        var rule = el.dataset.rule, v = el.value.trim(), msg = '';
        if (el.offsetParent === null) return; // hidden field
        if (!v) msg = 'This field is required.';
        else if (rule === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) msg = 'Enter a valid email address.';
        else if (rule === 'phone' && !/^(\+27|0)[0-9]{9}$/.test(v.replace(/\s+/g, ''))) msg = 'Use a South African number, e.g. 0821234567.';
        else if (rule === 'password' && !/^(?=.*[A-Z])(?=.*[0-9]).{8,}$/.test(v)) msg = 'Min 8 characters with a capital letter and a number.';
        else if (rule === 'postal' && !/^[0-9]{4}$/.test(v)) msg = 'Postal code must be 4 digits.';
        var err = el.parentNode.querySelector('.error-text');
        if (err) err.textContent = msg;
        if (msg) ok = false;
      });
      if (!ok) ev.preventDefault();
    });
  });
});
