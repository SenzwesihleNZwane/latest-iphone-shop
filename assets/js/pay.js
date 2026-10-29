/* pay.js - shows the payment methods of the selected provider (PayFast: card + instant EFT, Ozow: instant EFT, Yoco: card). */
(function () {
  var radios = document.querySelectorAll('input[name=gateway]'), box = document.getElementById('method-box'), hidden = document.getElementById('method');
  function draw() {
    var sel = document.querySelector('input[name=gateway]:checked'); if (!sel) return;
    var methods = JSON.parse(sel.dataset.methods), cur = hidden.value;
    if (methods.indexOf(cur) < 0) cur = methods[0];
    hidden.value = cur;
    box.innerHTML = methods.map(function (m) {
      return '<label class="gw-method"><input type="radio" name="m" value="' + m + '"' + (m === cur ? ' checked' : '') + '><span>' + m + '</span></label>';
    }).join('');
    box.querySelectorAll('input').forEach(function (r) { r.addEventListener('change', function () { hidden.value = r.value; }); });
    document.querySelectorAll('.gw-card').forEach(function (c) { c.classList.toggle('on', c.contains(sel)); });
  }
  radios.forEach(function (r) { r.addEventListener('change', draw); });
  draw();
})();
