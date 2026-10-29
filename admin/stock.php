<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $q = max(0, min(999, (int)($_POST['qty'] ?? 0)));
    db()->prepare('INSERT INTO branch_stock (branch_id,product_id,quantity_available) VALUES (?,?,?)
        ON DUPLICATE KEY UPDATE quantity_available = VALUES(quantity_available)')
        ->execute([(int)$_POST['branch_id'], (int)$_POST['product_id'], $q]);
    flash('Stock updated.', 'ok');
    redirect('/admin/stock.php?branch=' . (int)$_POST['branch_id']);
}
$pageTitle = 'Branch Stock';
require '_header.php';
$branches = db()->query('SELECT * FROM branches ORDER BY branch_name')->fetchAll();
$bid = (int)($_GET['branch'] ?? $branches[0]['branch_id']);
$s = db()->prepare('SELECT p.product_id,m.model_name,p.storage_capacity,p.condition_status, COALESCE(bs.quantity_available,0) qty
    FROM products p JOIN product_models m ON m.model_id=p.model_id LEFT JOIN branch_stock bs ON bs.product_id=p.product_id AND bs.branch_id=? WHERE p.is_active=1 ORDER BY m.sort_order,m.model_name,p.price');
$s->execute([$bid]); $rows = $s->fetchAll();
?>
<h1>Branch stock</h1>
<form class="filters" method="get"><select name="branch" onchange="this.form.submit()">
<?php foreach ($branches as $b): ?><option value="<?= $b['branch_id'] ?>" <?= $b['branch_id'] == $bid ? 'selected' : '' ?>><?= e($b['branch_name']) ?></option><?php endforeach; ?></select></form>
<div class="table-wrap"><table><tr><th>Product</th><th>Condition</th><th>Quantity</th><th></th></tr>
<?php foreach ($rows as $r): ?>
<tr><td><form id="f<?= (int)$r['product_id'] ?>" method="post"><?= csrf_field() ?><input type="hidden" name="branch_id" value="<?= $bid ?>"><input type="hidden" name="product_id" value="<?= (int)$r['product_id'] ?>"></form><?= e($r['model_name'] . ' ' . $r['storage_capacity']) ?></td><td><?= e($r['condition_status']) ?></td>
<td><input form="f<?= (int)$r['product_id'] ?>" type="number" name="qty" min="0" max="999" value="<?= (int)$r['qty'] ?>" style="width:90px"></td>
<td><button form="f<?= (int)$r['product_id'] ?>" class="btn small" type="submit">Save</button></td></tr>
<?php endforeach; ?></table></div>
<?php require '_footer.php'; ?>
