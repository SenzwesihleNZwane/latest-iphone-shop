<?php
$pageTitle = 'Dashboard';
require '_header.php';
$pdo = db();
$st = [
  'Paid / active orders' => $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status IN ('Paid','Layby Active','Dispatched','Completed')")->fetchColumn(),
  'Awaiting dispatch' => $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status IN ('Paid','Layby Active')")->fetchColumn(),
  'Revenue received' => money($pdo->query("SELECT COALESCE(SUM(amount_paid),0) FROM transactions WHERE payment_status='Complete'")->fetchColumn()),
  'Active laybys' => $pdo->query("SELECT COUNT(*) FROM layby_accounts WHERE status='Active'")->fetchColumn(),
  'Customers' => $pdo->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn(),
];
$low = $pdo->query("SELECT b.branch_name, m.model_name, p.storage_capacity, bs.quantity_available FROM branch_stock bs
   JOIN branches b ON b.branch_id=bs.branch_id JOIN products p ON p.product_id=bs.product_id
   JOIN product_models m ON m.model_id=p.model_id
   WHERE bs.quantity_available <= 1 AND p.is_active=1 ORDER BY bs.quantity_available, b.branch_name LIMIT 10")->fetchAll();
$recent = $pdo->query("SELECT o.order_id,o.order_status,o.total_amount,u.full_name FROM orders o JOIN users u ON u.user_id=o.user_id ORDER BY o.created_at DESC LIMIT 8")->fetchAll();
?>
<h1>Dashboard</h1>
<div class="stats"><?php foreach ($st as $k => $v): ?><div class="stat"><span><?= e($k) ?></span><b><?= e($v) ?></b></div><?php endforeach; ?></div>
<div class="two-col">
  <div><h2>Low stock</h2><div class="table-wrap"><table><tr><th>Branch</th><th>Item</th><th>Qty</th></tr>
  <?php foreach ($low as $r): ?><tr><td><?= e($r['branch_name']) ?></td><td><?= e($r['model_name'] . ' ' . $r['storage_capacity']) ?></td><td><?= (int)$r['quantity_available'] ?></td></tr><?php endforeach; ?></table></div></div>
  <div><h2>Recent orders</h2><div class="table-wrap"><table><tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th></tr>
  <?php foreach ($recent as $r): ?><tr><td><?= (int)$r['order_id'] ?></td><td><?= e($r['full_name']) ?></td><td><?= money($r['total_amount']) ?></td><td><?= e($r['order_status']) ?></td></tr><?php endforeach; ?></table></div></div>
</div>
<?php require '_footer.php'; ?>
