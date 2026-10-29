<?php
// Returns the whole catalogue (models, colours, storage options with price + live branch stock),
// branches and accessories as JSON, in the same shape the front end (script.js) already used.
require_once __DIR__ . '/../includes/functions.php';

try {
    $pdo = db();
    $branches = $pdo->query('SELECT branch_id, branch_name, province, latitude, longitude FROM branches ORDER BY branch_id')->fetchAll();

    $models = $pdo->query('SELECT * FROM product_models WHERE is_active = 1 ORDER BY sort_order, model_id')->fetchAll();
    $colours = [];
    foreach ($pdo->query('SELECT model_id, colour_name, hex_code, image_path FROM model_colours ORDER BY colour_id') as $c) {
        $colours[$c['model_id']][] = ['name' => $c['colour_name'], 'hex' => $c['hex_code'], 'img' => $c['image_path']];
    }

    // stock: product_id => [branch_name => qty]
    $stock = [];
    $q = $pdo->query('SELECT bs.product_id, b.branch_name, bs.quantity_available FROM branch_stock bs JOIN branches b ON b.branch_id = bs.branch_id');
    foreach ($q as $r) $stock[$r['product_id']][$r['branch_name']] = (int)$r['quantity_available'];

    $options = [];
    $pq = $pdo->query('SELECT product_id, model_id, storage_capacity, price, original_price FROM products WHERE is_active = 1 ORDER BY price');
    foreach ($pq as $p) {
        $by = $stock[$p['product_id']] ?? [];
        $opt = [
            'size' => $p['storage_capacity'],
            'price' => (int)round($p['price']),
            'productId' => (int)$p['product_id'],
            'stock' => array_sum($by),
            'stockByBranch' => $by,
        ];
        if ($p['original_price'] !== null) $opt['originalPrice'] = (int)round($p['original_price']);
        $options[$p['model_id']][] = $opt;
    }

    $catalog = [];
    foreach ($models as $m) {
        if (empty($options[$m['model_id']]) || empty($colours[$m['model_id']])) continue;
        $catalog[] = [
            'id' => $m['slug'], 'name' => $m['model_name'], 'gen' => $m['generation'],
            'colors' => $colours[$m['model_id']],
            'display' => $m['display_spec'], 'chip' => $m['chip'], 'camera' => $m['camera'], 'battery' => $m['battery'],
            'storageOptions' => $options[$m['model_id']],
        ];
    }

    $acc = $pdo->query('SELECT accessory_id AS id, accessory_name AS name, category, price, icon FROM accessories ORDER BY accessory_id')->fetchAll();
    foreach ($acc as &$a) { $a['price'] = (int)round($a['price']); } unset($a);

    json_out([
        'ok' => true,
        'catalog' => $catalog,
        'branches' => array_map(fn($b) => ['id' => (int)$b['branch_id'], 'name' => $b['branch_name'], 'province' => $b['province'],
                                           'lat' => (float)$b['latitude'], 'lng' => (float)$b['longitude']], $branches),
        'accessories' => $acc,
    ]);
} catch (Throwable $ex) {
    json_out(['ok' => false, 'error' => 'The catalogue could not be loaded. Check the database connection in includes/config.php.'], 500);
}
