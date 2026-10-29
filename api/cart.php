<?php
// Receives the browser cart, validates EVERY line against the database (prices are never trusted
// from the browser) and stores it in the session for checkout.php.
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false, 'error' => 'POST required.'], 405);
csrf_check(true);
$in = json_input();
$lines = is_array($in['lines'] ?? null) ? $in['lines'] : [];
if (!$lines) json_out(['ok' => false, 'error' => 'Your cart is empty.'], 422);

$find = db()->prepare('SELECT p.product_id FROM products p JOIN product_models m ON m.model_id = p.model_id
                       WHERE m.slug = ? AND p.storage_capacity = ? AND p.is_active = 1 AND m.is_active = 1');
$col  = db()->prepare('SELECT 1 FROM model_colours c JOIN product_models m ON m.model_id = c.model_id WHERE m.slug = ? AND c.colour_name = ?');

$clean = []; $skipped = 0;
foreach ($lines as $l) {
    $slug = (string)($l['itemId'] ?? ''); $size = (string)($l['storageSize'] ?? ''); $colour = (string)($l['colorName'] ?? '');
    $qty = max(1, min(10, (int)($l['qty'] ?? 1)));
    $find->execute([$slug, $size]); $pid = $find->fetchColumn();
    $col->execute([$slug, $colour]);
    if (!$pid || !$col->fetchColumn()) { $skipped++; continue; }
    $key = $pid . '|' . $colour;
    if (isset($clean[$key])) $clean[$key]['qty'] = min(10, $clean[$key]['qty'] + $qty);
    else $clean[$key] = ['product_id' => (int)$pid, 'colour' => $colour, 'qty' => $qty];
}
if (!$clean) json_out(['ok' => false, 'error' => 'None of those items are available any more.'], 422);

$_SESSION['cart'] = $clean;
$branch = clean((string)($in['branch'] ?? ''));
if ($branch !== '') $_SESSION['preferred_branch'] = $branch;
json_out(['ok' => true, 'count' => cart_count(), 'subtotal' => cart_total(), 'skipped' => $skipped]);
