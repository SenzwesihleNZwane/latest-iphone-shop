/* anim.js - scroll reveals, count-up numbers, nav shadow, scroll progress bar, hero parallax. */
(function () {
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* reveal on scroll */
  var els = document.querySelectorAll('[data-anim], .tl-line');
  if (reduce || !('IntersectionObserver' in window)) {
    els.forEach(function (e) { e.classList.add('in-view'); e.style.opacity = 1; });
  } else {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('in-view'); io.unobserve(en.target); } });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });
    els.forEach(function (e) { io.observe(e); });
  }

  /* count-up: <span data-count="29" data-suffix="+"></span> */
  var counters = document.querySelectorAll('[data-count]');
  function run(el) {
    var end = parseFloat(el.dataset.count), suf = el.dataset.suffix || '', dur = 1600, t0 = null;
    if (reduce) { el.textContent = end + suf; return; }
    function step(t) {
      if (!t0) t0 = t;
      var p = Math.min((t - t0) / dur, 1), eased = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.round(end * eased) + suf;
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }
  if ('IntersectionObserver' in window) {
    var co = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { run(e.target); co.unobserve(e.target); } }); }, { threshold: 0.6 });
    counters.forEach(function (c) { co.observe(c); });
  } else counters.forEach(run);

  /* nav shadow + progress bar */
  var nav = document.getElementById('mainNav'), bar = document.getElementById('scrollBar');
  function onScroll() {
    var y = window.scrollY || 0, h = document.documentElement.scrollHeight - innerHeight;
    if (nav) nav.classList.toggle('scrolled', y > 8);
    if (bar) bar.style.width = (h > 0 ? (y / h) * 100 : 0) + '%';
  }
  addEventListener('scroll', onScroll, { passive: true }); onScroll();

  /* hero parallax: elements with data-depth drift as the mouse moves */
  var hero = document.querySelector('[data-parallax]');
  if (hero && !reduce && matchMedia('(pointer:fine)').matches) {
    hero.addEventListener('mousemove', function (ev) {
      var r = hero.getBoundingClientRect(), x = (ev.clientX - r.left) / r.width - 0.5, y = (ev.clientY - r.top) / r.height - 0.5;
      hero.querySelectorAll('[data-depth]').forEach(function (n) {
        var d = parseFloat(n.dataset.depth);
        n.style.translate = (x * d * 40) + 'px ' + (y * d * 40) + 'px';
      });
    });
  }

  /* cart badge bump whenever its number changes */
  var cc = document.getElementById('cartCount');
  if (cc && 'MutationObserver' in window) {
    new MutationObserver(function () { cc.classList.remove('bump'); void cc.offsetWidth; cc.classList.add('bump'); }).observe(cc, { childList: true, characterData: true, subtree: true });
  }
})();
