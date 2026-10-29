<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $a = $_POST['action'] ?? '';
    $pdo = db();
    if ($a === 'add_model') {
        $name = clean($_POST['model_name'] ?? ''); $gen = clean($_POST['generation'] ?? '');
        $stor = clean($_POST['storage_capacity'] ?? ''); $price = (float)($_POST['price'] ?? 0);
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
        if ($name === '' || $gen === '' || $stor === '' || $price <= 0 || $slug === '') {
            flash('Complete the model name, generation, first storage size and price.', 'err');
        } else {
            try {
                $pdo->beginTransaction();
                $pdo->prepare('INSERT INTO product_models (slug,model_name,generation,display_spec,chip,camera,battery,sort_order) VALUES (?,?,?,?,?,?,?,0)')
                    ->execute([$slug, $name, $gen, clean($_POST['display_spec'] ?? '') ?: null, clean($_POST['chip'] ?? '') ?: null, clean($_POST['camera'] ?? '') ?: null, clean($_POST['battery'] ?? '') ?: null]);
                $mid = (int)$pdo->lastInsertId();
                $pdo->prepare('INSERT INTO products (model_id,storage_capacity,price) VALUES (?,?,?)')->execute([$mid, $stor, $price]);
                $img = clean($_POST['image_path'] ?? '');
                if ($img !== '') $pdo->prepare('INSERT INTO model_colours (model_id,colour_name,hex_code,image_path) VALUES (?,?,?,?)')
                    ->execute([$mid, clean($_POST['colour_name'] ?? '') ?: 'Standard', '#cccccc', $img]);
                $pdo->commit();
                flash('Model added. Set its branch stock under Branch Stock.', 'ok');
            } catch (Throwable $ex) { if ($pdo->inTransaction()) $pdo->rollBack(); flash('Could not add model (is the name already used?).', 'err'); }
        }
    } elseif ($a === 'add_storage') {
        $stor = clean($_POST['storage_capacity'] ?? ''); $price = (float)($_POST['price'] ?? 0);
        if ($stor === '' || $price <= 0) flash('Enter a storage size and a price.', 'err');
        else try {
            $pdo->prepare('INSERT INTO products (model_id,storage_capacity,price) VALUES (?,?,?)')->execute([(int)$_POST['model_id'], $stor, $price]);
            flash('Storage option added.', 'ok');
        } catch (Throwable $ex) { flash('That storage size already exists for this model.', 'err'); }
    } elseif ($a === 'price') {
        $orig = trim($_POST['original_price'] ?? '');
        $pdo->prepare('UPDATE products SET price=?, original_price=? WHERE product_id=?')
            ->execute([max(1, (float)$_POST['price']), $orig !== '' && (float)$orig > 0 ? (float)$orig : null, (int)$_POST['product_id']]);
        flash('Price updated.', 'ok');
    } elseif ($a === 'toggle') {
        $pdo->prepare('UPDATE products SET is_active = 1 - is_active WHERE product_id=?')->execute([(int)$_POST['product_id']]);
        flash('Visibility changed.', 'ok');
    } elseif ($a === 'toggle_model') {
        $pdo->prepare('UPDATE product_models SET is_active = 1 - is_active WHERE model_id=?')->execute([(int)$_POST['model_id']]);
        flash('Model visibility changed.', 'ok');
    }
    redirect('/admin/products.php');
}
$pageTitle = 'Products';
require '_header.php';
$rows = db()->query('SELECT m.model_id,m.model_name,m.is_active AS model_active,p.product_id,p.storage_capacity,p.price,p.original_price,p.is_active
    FROM product_models m LEFT JOIN products p ON p.model_id=m.model_id ORDER BY m.sort_order,m.model_name,p.price')->fetchAll();
$models = [];
foreach ($rows as $r) $models[$r['model_id']][] = $r;
?>
<h1>Products</h1>
<div class="two-col">
<div>
<?php foreach ($models as $mid => $vars): $m = $vars[0]; ?>
  <div class="card" style="margin-bottom:14px">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:8px">
      <h3><?= e($m['model_name']) ?> <?= $m['model_active'] ? '' : '<span class="badge out">Hidden</span>' ?></h3>
      <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="toggle_model"><input type="hidden" name="model_id" value="<?= (int)$mid ?>">
        <button class="btn small secondary"><?= $m['model_active'] ? 'Hide model' : 'Show model' ?></button></form>
    </div>
    <div class="table-wrap" style="margin-top:8px"><table><tr><th>Storage</th><th>Price (R)</th><th>Was (R)</th><th></th><th></th></tr>
    <?php foreach ($vars as $r): if (!$r['product_id']) continue; ?>
      <tr><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="price"><input type="hidden" name="product_id" value="<?= (int)$r['product_id'] ?>">
        <td><?= e($r['storage_capacity']) ?> <?= $r['is_active'] ? '' : '<span class="badge out">Hidden</span>' ?></td>
        <td><input type="number" step="0.01" name="price" value="<?= e($r['price']) ?>" style="width:110px"></td>
        <td><input type="number" step="0.01" name="original_price" value="<?= e($r['original_price']) ?>" style="width:110px" placeholder="optional"></td>
        <td><button class="btn small">Save</button></td></form>
        <td><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="product_id" value="<?= (int)$r['product_id'] ?>">
          <button class="btn small secondary"><?= $r['is_active'] ? 'Hide' : 'Show' ?></button></form></td></tr>
    <?php endforeach; ?></table></div>
    <form method="post" class="filters" style="margin:10px 0 0"><?= csrf_field() ?><input type="hidden" name="action" value="add_storage"><input type="hidden" name="model_id" value="<?= (int)$mid ?>">
      <input name="storage_capacity" placeholder="e.g. 2TB" style="width:110px"><input name="price" type="number" step="0.01" placeholder="Price" style="width:120px"><button class="btn small secondary">Add storage</button></form>
  </div>
<?php endforeach; ?>
</div>
<form class="stack" method="post" data-validate novalidate><?= csrf_field() ?><input type="hidden" name="action" value="add_model">
  <h2>Add a new model</h2>
  <label>Model name</label><div><input name="model_name" data-rule="text" placeholder="iPhone 17"><div class="error-text"></div></div>
  <label>Generation</label><div><input name="generation" data-rule="text" placeholder="17"><div class="error-text"></div></div>
  <label>First storage size</label><div><input name="storage_capacity" data-rule="text" placeholder="256GB"><div class="error-text"></div></div>
  <label>Price (R)</label><div><input name="price" type="number" step="0.01" data-rule="text"><div class="error-text"></div></div>
  <label>Display (optional)</label><input name="display_spec"><label>Chip (optional)</label><input name="chip">
  <label>Camera (optional)</label><input name="camera"><label>Battery (optional)</label><input name="battery">
  <label>Colour name (optional)</label><input name="colour_name" placeholder="Black">
  <label>Photo path (optional)</label><input name="image_path" placeholder="images/iphone-17.jpg">
  <p><button class="btn" type="submit">Add model</button></p>
</form>
</div>
<?php require '_footer.php'; ?>
