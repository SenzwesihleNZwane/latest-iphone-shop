<?php
$pageTitle = 'My Orders';
require_once 'includes/functions.php';
$user = require_login();
require 'includes/header.php';
$s = db()->prepare('SELECT o.*, b.branch_name, sd.courier FROM orders o JOIN branches b ON b.branch_id=o.branch_id
    LEFT JOIN shipping_details sd ON sd.order_id=o.order_id WHERE o.user_id=? ORDER BY o.created_at DESC');
$s->execute([$user['user_id']]);
$orders = $s->fetchAll();
?>
<h1>My Orders</h1>
<?php if (!$orders): ?><p>No orders yet. <a href="<?= BASE_URL ?>/shop.php">Start shopping</a></p><?php else: ?>
<div class="table-wrap"><table>
<tr><th>Order</th><th>Date</th><th>Branch</th><th>Courier</th><th>Type</th><th>Total</th><th>Status</th><th></th></tr>
<?php foreach ($orders as $o): ?>
<tr><td>#<?= (int)$o['order_id'] ?></td><td><?= e(substr($o['created_at'], 0, 10)) ?></td><td><?= e($o['branch_name']) ?></td>
<td><?= e($o['courier']) ?></td><td><?= e($o['payment_type']) ?></td><td><?= money($o['total_amount']) ?></td>
<td><span class="badge"><?= e($o['order_status']) ?></span></td>
<td><?php if ($o['order_status'] === 'Pending'): ?><a class="btn small" href="<?= BASE_URL ?>/pay.php?order_id=<?= (int)$o['order_id'] ?>">Pay now</a>
<?php else: ?><a class="btn small secondary" href="<?= BASE_URL ?>/confirmation.php?order_id=<?= (int)$o['order_id'] ?>">View</a><?php endif; ?></td></tr>
<?php endforeach; ?>
</table></div><?php endif; ?>
<?php require 'includes/footer.php'; ?>
