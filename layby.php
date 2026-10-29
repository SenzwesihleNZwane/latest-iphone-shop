<?php
$pageTitle = 'My Layby';
require_once 'includes/functions.php';
$user = require_login();
require 'includes/header.php';
$s = db()->prepare('SELECT l.*, o.total_amount, o.order_status FROM layby_accounts l JOIN orders o ON o.order_id=l.order_id
    WHERE l.user_id=? AND o.order_status <> "Pending" ORDER BY l.layby_id DESC');
$s->execute([$user['user_id']]);
$accounts = $s->fetchAll();
?>
<h1>My Layby Accounts</h1>
<?php if (!$accounts): ?><p>You have no active layby plans. Choose "Layby" at checkout to start one.</p><?php endif; ?>
<?php foreach ($accounts as $a):
  $p = db()->prepare('SELECT amount, paid_at FROM layby_payments WHERE layby_id=? ORDER BY paid_at'); $p->execute([$a['layby_id']]);
  $pays = $p->fetchAll();
  $paidPct = $a['total_amount'] > 0 ? round(100 * ($a['total_amount'] - $a['balance_remaining']) / $a['total_amount']) : 0; ?>
<div class="card" style="margin-bottom:16px">
  <h3>Order #<?= (int)$a['order_id'] ?> &middot; <?= (int)$a['months'] ?> month plan <span class="badge"><?= e($a['status']) ?></span></h3>
  <div class="row"><span>Total</span><span><?= money($a['total_amount']) ?></span></div>
  <div class="row"><span>Deposit paid</span><span><?= money($a['deposit_amount']) ?></span></div>
  <div class="row"><span>Monthly instalment</span><span><?= money($a['monthly_instalment']) ?></span></div>
  <div class="row total"><span>Balance remaining</span><span><?= money($a['balance_remaining']) ?></span></div>
  <div class="progress"><div style="width:<?= $paidPct ?>%"></div></div>
  <?php if ($a['next_due_date']): ?><p>Next payment due: <b><?= e($a['next_due_date']) ?></b></p><?php endif; ?>
  <?php if ($a['status'] === 'Active'): ?><a class="btn" href="<?= BASE_URL ?>/pay.php?layby_id=<?= (int)$a['layby_id'] ?>">Pay instalment</a><?php endif; ?>
  <details><summary>Payment history (<?= count($pays) ?>)</summary>
    <?php foreach ($pays as $py): ?><div class="row"><span><?= e($py['paid_at']) ?></span><span><?= money($py['amount']) ?></span></div><?php endforeach; ?></details>
</div>
<?php endforeach; ?>
<?php require 'includes/footer.php'; ?>
