<?php
require_once 'includes/functions.php';
$user = require_login();
$items = cart_items();
if (!$items) { flash('Your cart is empty.', 'warn'); redirect('/shop.php?cart=1'); }
$subtotal = cart_total();
$branches = db()->query('SELECT * FROM branches ORDER BY branch_name')->fetchAll();
$errors = [];
$prefBranch = 0;
if (!empty($_SESSION['preferred_branch'])) {
    foreach ($branches as $b) if ($b['branch_name'] === $_SESSION['preferred_branch']) $prefBranch = (int)$b['branch_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $branch  = (int)($_POST['branch_id'] ?? 0);
    $courier = $_POST['courier'] ?? '';
    $ptype   = $_POST['payment_type'] ?? 'Full';
    $paxi    = strtoupper(clean($_POST['paxi_code'] ?? ''));
    $street  = clean($_POST['street'] ?? ''); $suburb = clean($_POST['suburb'] ?? '');
    $city    = clean($_POST['city'] ?? '');   $postal = clean($_POST['postal'] ?? '');
    $months  = (int)($_POST['months'] ?? 0);  $deposit = (float)($_POST['deposit'] ?? 0);

    // validate branch + stock
    $s = db()->prepare('SELECT 1 FROM branches WHERE branch_id = ?'); $s->execute([$branch]);
    if (!$s->fetch()) $errors[] = 'Choose the branch that will fulfil your order.';
    else foreach ($items as $i) {
        if (branch_qty($branch, (int)$i['product_id']) < $i['qty'])
            $errors[] = $i['model_name'] . ' ' . $i['storage_capacity'] . ' does not have enough stock at that branch.';
    }
    if (!in_array($courier, ['PEP Paxi', 'The Courier Guy'], true)) $errors[] = 'Choose a delivery option.';
    if ($courier === 'PEP Paxi' && !preg_match('/^[A-Z0-9]{4,10}$/', $paxi)) $errors[] = 'Enter a valid PEP store code (4-10 letters/numbers).';
    if ($courier === 'The Courier Guy') {
        if ($street === '' || $suburb === '' || $city === '') $errors[] = 'Complete your street address, suburb and city.';
        if (!valid_postal($postal)) $errors[] = 'Postal code must be 4 digits.';
    }
    if (!in_array($ptype, ['Full', 'Layby'], true)) $errors[] = 'Choose a payment type.';

    $fee = $courier === 'PEP Paxi' ? PAXI_FEE : COURIER_FEE;
    $total = $subtotal + $fee;
    if ($ptype === 'Layby') {
        $minDep = ceil($total * MIN_DEPOSIT_PERCENT / 100);
        if ($months < 2 || $months > 6) $errors[] = 'Layby term must be between 2 and 6 months.';
        if ($deposit < $minDep || $deposit >= $total) $errors[] = 'Deposit must be at least ' . money($minDep) . ' and less than the total.';
    }

    if (!$errors) {
        $pdo = db();
        try {
            $pdo->beginTransaction();
            $pdo->prepare('INSERT INTO orders (user_id,branch_id,payment_type,order_status,total_amount,shipping_fee) VALUES (?,?,?,?,?,?)')
                ->execute([$user['user_id'], $branch, $ptype, 'Pending', $total, $fee]);
            $oid = (int)$pdo->lastInsertId();
            $ins = $pdo->prepare('INSERT INTO order_items (order_id,product_id,colour_name,quantity,unit_price) VALUES (?,?,?,?,?)');
            foreach ($items as $i) $ins->execute([$oid, $i['product_id'], $i['colour'], $i['qty'], $i['price']]);
            $pdo->prepare('INSERT INTO shipping_details (order_id,courier,paxi_store_code,street_address,suburb,city,postal_code) VALUES (?,?,?,?,?,?,?)')
                ->execute([$oid, $courier, $courier === 'PEP Paxi' ? $paxi : null,
                    $courier === 'The Courier Guy' ? $street : null, $courier === 'The Courier Guy' ? $suburb : null,
                    $courier === 'The Courier Guy' ? $city : null, $courier === 'The Courier Guy' ? $postal : null]);
            if ($ptype === 'Layby') {
                $inst = round(($total - $deposit) / $months, 2);
                $pdo->prepare('INSERT INTO layby_accounts (order_id,user_id,months,deposit_amount,monthly_instalment,balance_remaining,status) VALUES (?,?,?,?,?,?,?)')
                    ->execute([$oid, $user['user_id'], $months, $deposit, $inst, $total, 'Active']);
            }
            $pdo->commit();
        } catch (Throwable $ex) { $pdo->rollBack(); exit('Could not create your order. Please try again.'); }
        redirect('/pay.php?order_id=' . $oid);
    }
}
$pageTitle = 'Checkout';
require 'includes/header.php';
$v = fn($k, $d = '') => e($_POST[$k] ?? $d);
?>
<h1>Checkout</h1>
<?php foreach ($errors as $er): ?><div class="alert err"><?= e($er) ?></div><?php endforeach; ?>
<div class="two-col">
<form id="checkout-form" class="stack" method="post" data-validate novalidate
      data-subtotal="<?= $subtotal ?>" data-paxi="<?= PAXI_FEE ?>" data-courier="<?= COURIER_FEE ?>" data-min-deposit="<?= MIN_DEPOSIT_PERCENT ?>">
  <?= csrf_field() ?>
  <label>Fulfilling branch (stock is checked here)</label>
  <select name="branch_id" required>
    <?php foreach ($branches as $b): ?><option value="<?= $b['branch_id'] ?>" <?= ((int)($_POST['branch_id'] ?? $prefBranch) === (int)$b['branch_id']) ? 'selected' : '' ?>><?= e($b['branch_name']) ?></option><?php endforeach; ?>
  </select>

  <label>Delivery option</label>
  <label class="opt"><input type="radio" name="courier" value="PEP Paxi" <?= ($_POST['courier'] ?? 'PEP Paxi') === 'PEP Paxi' ? 'checked' : '' ?>> PEP Paxi store pick-up (<?= money(PAXI_FEE) ?>)</label>
  <label class="opt"><input type="radio" name="courier" value="The Courier Guy" <?= ($_POST['courier'] ?? '') === 'The Courier Guy' ? 'checked' : '' ?>> The Courier Guy door delivery (<?= money(COURIER_FEE) ?>)</label>

  <div id="paxi-fields"><label>PEP store code</label>
    <div><input name="paxi_code" data-rule="text" placeholder="e.g. PA1234" value="<?= $v('paxi_code') ?>"><div class="error-text"></div></div></div>
  <div id="courier-fields" class="hidden">
    <label>Street address</label><div><input name="street" data-rule="text" value="<?= $v('street') ?>"><div class="error-text"></div></div>
    <label>Suburb</label><div><input name="suburb" data-rule="text" value="<?= $v('suburb') ?>"><div class="error-text"></div></div>
    <label>City</label><div><input name="city" data-rule="text" value="<?= $v('city') ?>"><div class="error-text"></div></div>
    <label>Postal code</label><div><input name="postal" data-rule="postal" value="<?= $v('postal') ?>"><div class="error-text"></div></div>
  </div>

  <label>Payment type</label>
  <label class="opt"><input type="radio" name="payment_type" value="Full" <?= ($_POST['payment_type'] ?? 'Full') === 'Full' ? 'checked' : '' ?>> Pay in full now (card or instant EFT)</label>
  <label class="opt"><input type="radio" name="payment_type" value="Layby" <?= ($_POST['payment_type'] ?? '') === 'Layby' ? 'checked' : '' ?>> Layby (2 to 6 months)</label>
  <div id="layby-fields" class="hidden">
    <label>Layby term</label>
    <select name="months" id="months"><?php for ($m = 2; $m <= 6; $m++): ?><option value="<?= $m ?>" <?= (int)$v('months') === $m ? 'selected' : '' ?>><?= $m ?> months</option><?php endfor; ?></select>
    <label>Deposit (R) &mdash; minimum <span id="min-deposit"></span></label>
    <input type="number" name="deposit" id="deposit" min="0" step="1" value="<?= $v('deposit') ?>">
  </div>
  <p><button class="btn" type="submit">Continue to secure payment</button></p>
</form>

<div class="summary">
  <h2>Order summary</h2>
  <?php foreach ($items as $i): ?>
    <div class="row"><span><?= e($i['model_name']) ?> <?= e($i['storage_capacity']) ?> &middot; <?= e($i['colour']) ?> x<?= (int)$i['qty'] ?></span><span><?= money($i['line']) ?></span></div>
  <?php endforeach; ?>
  <hr>
  <div class="row"><span>Subtotal</span><span><?= money($subtotal) ?></span></div>
  <div class="row"><span>Shipping</span><span id="sum-ship">R 0.00</span></div>
  <div class="row total"><span>Total</span><span id="sum-total"><?= money($subtotal) ?></span></div>
  <div class="row"><span>Layby instalment</span><span id="sum-instalment">-</span></div>
</div>
</div>
<?php
$extraFoot = '<script src="' . asset('/assets/js/checkout.js') . '"></script>';
require 'includes/footer.php'; ?>
