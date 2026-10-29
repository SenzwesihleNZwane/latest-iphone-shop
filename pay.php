<?php
// Payment page: choose PayFast, Ozow or Yoco, then either go to the real gateway (when keys are set in config.php)
// or to our practice gateway page (pay_sim.php) so everything can be demonstrated without real money.
require_once __DIR__ . '/includes/payments.php';
$user = require_login();

// What are we paying? A new order (order_id) or a lay-by instalment (layby_id)
if (isset($_GET['layby_id'])) {
    $ctx = payment_context('L' . (int)$_GET['layby_id']);
    if ($ctx && (int)$ctx['layby']['user_id'] !== (int)$user['user_id']) $ctx = null;
    if (!$ctx) { flash('That lay-by account could not be found.', 'err'); redirect('/layby.php'); }
} else {
    $oid = (int)($_GET['order_id'] ?? 0);
    $ctx = payment_context('O' . $oid);
    if ($ctx && (int)$ctx['order']['user_id'] !== (int)$user['user_id']) $ctx = null;
    if (!$ctx) { redirect('/confirmation.php?order_id=' . $oid); }   // already paid, or not yours
}
$gateways = gateway_catalogue();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $gw = $_POST['gateway'] ?? ''; $method = $_POST['method'] ?? '';
    if (!isset($gateways[$gw]) || !in_array($method, $gateways[$gw]['methods'], true)) {
        flash('Choose a payment provider and method.', 'err'); redirect(pay_page_for($ctx));
    }
    if (gateway_is_real($gw)) {
        if ($gw === 'PayFast') post_redirect(payfast_url(), payfast_form($ctx, $user, $method));
        if ($gw === 'Ozow')    post_redirect('https://pay.ozow.com', ozow_form($ctx));
        if ($gw === 'Yoco') {
            $url = yoco_checkout($ctx, $err);
            if ($url) { header('Location: ' . $url); exit; }
            flash($err ?: 'Could not reach Yoco.', 'err'); redirect(pay_page_for($ctx));
        }
    }
    $_SESSION['sim'] = ['ref' => $ctx['ref'], 'gateway' => $gw, 'method' => $method];
    redirect('/pay_sim.php');
}

$pageTitle = 'Secure Payment';
$extraFoot = '<script src="' . asset('/assets/js/pay.js') . '"></script>';
require 'includes/header.php';
$sel = $_POST['gateway'] ?? 'PayFast';
?>
<span class="eyebrow">Secure payment</span>
<h1><?= $ctx['instalment'] ? 'Pay your lay-by instalment' : 'Complete your payment' ?></h1>
<?php if (GATEWAY_MODE === 'simulate'): ?>
<div class="alert info">Practice mode: choose a provider and you will see its payment page, but <b>no real money is charged</b>. Real payments switch on when your PayFast, Ozow and Yoco keys are added in <code>includes/config.php</code>.</div>
<?php endif; ?>

<div class="two-col">
  <form method="post" id="pay-form">
    <?= csrf_field() ?>
    <input type="hidden" name="method" id="method" value="">
    <h2>1. Choose how to pay</h2>
    <div class="gw-grid">
<?php foreach ($gateways as $name => $g): ?>
      <label class="gw-card" style="--gw:<?= e($g['color']) ?>">
        <input type="radio" name="gateway" value="<?= e($name) ?>" data-methods='<?= e(json_encode($g['methods'])) ?>' <?= $sel === $name ? 'checked' : '' ?>>
        <span class="gw-badge"><?= e(strtoupper($name[0])) ?></span>
        <span class="gw-name"><?= e($name) ?></span>
        <span class="gw-blurb"><?= e($g['blurb']) ?></span>
        <span class="gw-methods"><?= e(implode(' · ', $g['methods'])) ?></span>
        <?php if (gateway_is_real($name)): ?><span class="gw-live">Live</span><?php endif; ?>
      </label>
<?php endforeach; ?>
    </div>
    <h2 style="margin-top:26px">2. Payment method</h2>
    <div id="method-box" class="gw-methods-box"></div>
    <p style="margin-top:22px"><button class="btn shine" type="submit">Pay <?= money($ctx['amount']) ?> securely &rarr;</button></p>
    <p class="muted" style="font-size:.75rem">You will be taken to the provider's page to enter your details. We never see or store your card number.</p>
  </form>

  <div class="summary">
    <h2><?= $ctx['instalment'] ? 'Instalment' : 'Order #' . (int)$ctx['order']['order_id'] ?></h2>
<?php if ($ctx['instalment']): ?>
    <div class="row"><span>Order</span><span>#<?= (int)$ctx['order']['order_id'] ?></span></div>
    <div class="row"><span>Balance remaining</span><span><?= money($ctx['layby']['balance_remaining']) ?></span></div>
<?php else: ?>
    <div class="row"><span>Order total</span><span><?= money($ctx['order']['total_amount']) ?></span></div>
<?php if ($ctx['layby']): ?><div class="row"><span>Lay-by (<?= (int)$ctx['layby']['months'] ?> months)</span><span>Deposit now</span></div><?php endif; ?>
<?php endif; ?>
    <hr>
    <div class="row total"><span>Due now</span><span><?= money($ctx['amount']) ?></span></div>
  </div>
</div>
<?php require 'includes/footer.php'; ?>
