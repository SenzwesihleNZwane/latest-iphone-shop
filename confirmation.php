<?php
require_once 'includes/functions.php';
$user = require_login();
$s = db()->prepare('SELECT o.*, b.branch_name FROM orders o JOIN branches b ON b.branch_id=o.branch_id WHERE o.order_id=? AND o.user_id=?');
$s->execute([(int)($_GET['order_id'] ?? 0), $user['user_id']]);
$o = $s->fetch();
if (!$o) redirect('/orders.php');
if (in_array($o['order_status'], ['Paid', 'Layby Active'], true)) { unset($_SESSION['cart']); $clearCart = true; }

$i = db()->prepare('SELECT m.model_name,p.storage_capacity,oi.colour_name,oi.quantity FROM order_items oi JOIN products p ON p.product_id=oi.product_id JOIN product_models m ON m.model_id=p.model_id WHERE oi.order_id=?');
$i->execute([$o['order_id']]); $items = $i->fetchAll();
$summary = implode(', ', array_map(fn($x) => $x['quantity'] . 'x ' . $x['model_name'] . ' ' . $x['storage_capacity'] . ' (' . $x['colour_name'] . ')', $items));

// WhatsApp bridge: pre-filled message containing the validated Order ID
$text = "Hi LATEST-IPHONE, my order #{$o['order_id']} is " . $o['order_status'] . ". Items: $summary. Total: " . money($o['total_amount']) . ". Name: {$user['full_name']}. Please dispatch from {$o['branch_name']}.";
$wa = 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . rawurlencode($text);

$pageTitle = 'Order Confirmation';
require 'includes/header.php';
?>
<?php if ($o['order_status'] === 'Pending'): ?>
<meta http-equiv="refresh" content="6">
<h1>Order #<?= (int)$o['order_id'] ?></h1>
<div class="alert warn">Waiting for your payment provider to confirm the payment. This page refreshes by itself. If you did not finish paying, <a href="<?= BASE_URL ?>/pay.php?order_id=<?= (int)$o['order_id'] ?>">go back to the payment page</a>.</div>
<?php else: ?>
<h1>Thank you, order #<?= (int)$o['order_id'] ?></h1>
<div class="alert ok">Status: <b><?= e($o['order_status']) ?></b></div>
<?php endif; ?>
<div class="card" style="max-width:560px">
  <p><?= e($summary) ?></p>
  <p>Fulfilled from: <b><?= e($o['branch_name']) ?></b></p>
  <p class="total">Total: <?= money($o['total_amount']) ?> (shipping <?= money($o['shipping_fee']) ?>)</p>
  <p>Send your order ID to our team on WhatsApp for fast dispatch:</p>
  <p style="margin-top:14px"><a class="btn" href="<?= e($wa) ?>" target="_blank" rel="noopener">Send on WhatsApp</a>
  <a class="btn secondary" href="<?= BASE_URL ?>/orders.php">My orders</a></p>
</div>
<?php
if (!empty($clearCart)) $extraFoot = '<script>try{localStorage.removeItem("cart")}catch(e){}</script>';
require 'includes/footer.php'; ?>
