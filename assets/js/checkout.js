/* checkout.js - live fee / total / lay-by instalment and show-hide of delivery + lay-by fields. */
(function () {
  var f = document.getElementById('checkout-form');
  if (!f) return;
  var sub = parseFloat(f.dataset.subtotal), paxi = parseFloat(f.dataset.paxi),
      courier = parseFloat(f.dataset.courier), pct = parseFloat(f.dataset.minDeposit);
  var R = function (n) { return 'R ' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' '); };
  function val(n) { var c = f.querySelector('input[name=' + n + ']:checked'); return c ? c.value : ''; }
  function update() {
    var isPaxi = val('courier') === 'PEP Paxi', isLay = val('payment_type') === 'Layby';
    document.getElementById('paxi-fields').classList.toggle('hidden', !isPaxi);
    document.getElementById('courier-fields').classList.toggle('hidden', isPaxi);
    document.getElementById('layby-fields').classList.toggle('hidden', !isLay);
    var fee = isPaxi ? paxi : courier, total = sub + fee;
    document.getElementById('sum-ship').textContent = R(fee);
    document.getElementById('sum-total').textContent = R(total);
    var min = Math.ceil(total * pct / 100);
    document.getElementById('min-deposit').textContent = R(min);
    var inst = document.getElementById('sum-instalment');
    if (isLay) {
      var m = parseInt(document.getElementById('months').value, 10) || 2;
      var dep = parseFloat(document.getElementById('deposit').value) || min;
      inst.textContent = R(Math.max(0, (total - dep) / m)) + ' x ' + m;
    } else inst.textContent = '-';
  }
  f.addEventListener('change', update);
  f.addEventListener('input', update);
  update();
})();
