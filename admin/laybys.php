<?php
$pageTitle = 'Layby Accounts';
require '_header.php';
$rows = db()->query("SELECT l.*, u.full_name, u.phone, o.total_amount FROM layby_accounts l
  JOIN users u ON u.user_id=l.user_id JOIN orders o ON o.order_id=l.order_id
  WHERE o.order_status <> 'Pending' ORDER BY l.status, l.next_due_date")->fetchAll();
?>
<h1>Layby accounts</h1>
<div class="table-wrap"><table>
<tr><th>Order</th><th>Customer</th><th>Term</th><th>Total</th><th>Balance</th><th>Next due</th><th>Status</th></tr>
<?php foreach ($rows as $r): $overdue = $r['status'] === 'Active' && $r['next_due_date'] && $r['next_due_date'] < date('Y-m-d'); ?>
<tr><td>#<?= (int)$r['order_id'] ?></td><td><?= e($r['full_name']) ?><br><small><?= e($r['phone']) ?></small></td>
<td><?= (int)$r['months'] ?> months</td><td><?= money($r['total_amount']) ?></td><td><?= money($r['balance_remaining']) ?></td>
<td><?= e($r['next_due_date'] ?? '-') ?> <?= $overdue ? '<span class="badge out">Overdue</span>' : '' ?></td><td><?= e($r['status']) ?></td></tr>
<?php endforeach; ?></table></div>
<?php require '_footer.php'; ?>
