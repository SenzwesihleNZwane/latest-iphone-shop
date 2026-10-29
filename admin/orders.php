<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$statuses = ['Pending', 'Paid', 'Layby Active', 'Dispatched', 'Completed', 'Cancelled'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $new = $_POST['status'] ?? '';
    if (in_array($new, $statuses, true)) {
        db()->prepare('UPDATE orders SET order_status=? WHERE order_id=?')->execute([$new, (int)$_POST['order_id']]);
        flash('Order status updated.', 'ok');
    }
    redirect('/admin/orders.php');
}
$pageTitle = 'Orders';
require '_header.php';
$itemsQ = db()->prepare("SELECT m.model_name,p.storage_capacity,oi.colour_name,oi.quantity FROM order_items oi JOIN products p ON p.product_id=oi.product_id JOIN product_models m ON m.model_id=p.model_id WHERE oi.order_id=?");
$rows = db()->query("SELECT o.*, u.full_name, u.phone, b.branch_name, sd.courier, sd.paxi_store_code, sd.street_address, sd.suburb, sd.city, sd.postal_code
  FROM orders o JOIN users u ON u.user_id=o.user_id JOIN branches b ON b.branch_id=o.branch_id
  LEFT JOIN shipping_details sd ON sd.order_id=o.order_id ORDER BY o.created_at DESC LIMIT 100")->fetchAll();
?>
<h1>Orders</h1>
<div class="table-wrap"><table>
<tr><th>#</th><th>Customer</th><th>Branch / items</th><th>Delivery</th><th>Total</th><th>Status</th></tr>
<?php foreach ($rows as $r): ?>
<tr><td><?= (int)$r['order_id'] ?></td>
<td><?= e($r['full_name']) ?><br><small><?= e($r['phone']) ?></small></td>
<td><?= e($r['branch_name']) ?><br><small><?php $itemsQ->execute([$r['order_id']]); foreach ($itemsQ->fetchAll() as $it) echo e($it['quantity'].'x '.$it['model_name'].' '.$it['storage_capacity'].' ('.$it['colour_name'].')').'<br>'; ?></small></td>
<td><?= e($r['courier']) ?><br><small><?= $r['courier'] === 'PEP Paxi' ? 'Store ' . e($r['paxi_store_code']) : e("{$r['street_address']}, {$r['suburb']}, {$r['city']} {$r['postal_code']}") ?></small></td>
<td><?= money($r['total_amount']) ?><br><small><?= e($r['payment_type']) ?></small></td>
<td><form method="post" style="display:flex;gap:6px"><?= csrf_field() ?><input type="hidden" name="order_id" value="<?= (int)$r['order_id'] ?>">
<select name="status"><?php foreach ($statuses as $s): ?><option <?= $s === $r['order_status'] ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select>
<button class="btn small">Save</button></form></td></tr>
<?php endforeach; ?></table></div>
<?php require '_footer.php'; ?>
