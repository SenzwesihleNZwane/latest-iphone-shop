<?php
// Practice payment page. Looks like the provider's hosted page; nothing is charged.
// This is what the customer sees when GATEWAY_MODE is 'simulate' or a provider has no keys yet.
require_once __DIR__ . '/includes/payments.php';
$user = require_login();
$sim = $_SESSION['sim'] ?? null;
$ctx = $sim ? payment_context($sim['ref']) : null;
if (!$ctx) { flash('That payment session has ended.', 'warn'); redirect('/orders.php'); }
$owner = $ctx['instalment'] ? (int)$ctx['layby']['user_id'] : (int)$ctx['order']['user_id'];
if ($owner !== (int)$user['user_id']) redirect('/orders.php');
$gw = $sim['gateway']; $method = $sim['method'];
$G = gateway_catalogue()[$gw]; $color = $G['color'];
$errors = [];
$banks = ['FNB', 'Standard Bank', 'Absa', 'Nedbank', 'Capitec', 'Investec', 'TymeBank'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (isset($_POST['cancel'])) { unset($_SESSION['sim']); flash('Payment cancelled. You have not been charged.', 'warn'); redirect(pay_page_for($ctx)); }
    if ($method === 'Card') {
        $num = preg_replace('/\D/', '', $_POST['card'] ?? '');
        if (strlen($num) < 13 || strlen($num) > 19) $errors[] = 'Enter a valid card number (practice mode: any 16 digits).';
        if (!preg_match('/^(0[1-9]|1[0-2])\s*\/\s*(\d{2})$/', trim($_POST['exp'] ?? ''), $m) || ((int)('20' . $m[2]) * 12 + (int)$m[1]) < ((int)date('Y') * 12 + (int)date('n'))) $errors[] = 'Enter a card expiry date in the future (MM/YY).';
        if (!preg_match('/^\d{3,4}$/', trim($_POST['cvv'] ?? ''))) $errors[] = 'Enter the 3 or 4 digit security code.';
        if (trim($_POST['holder'] ?? '') === '') $errors[] = 'Enter the name on the card.';
    } else {
        if (!in_array($_POST['bank'] ?? '', $banks, true)) $errors[] = 'Choose your bank.';
    }
    if (!$errors) {
        try {
            payment_complete($ctx, $gw, $method, strtoupper($gw[0]) . 'SIM' . strtoupper(bin2hex(random_bytes(4))));
        } catch (Throwable $ex) { flash('Payment could not be completed: ' . $ex->getMessage(), 'err'); unset($_SESSION['sim']); redirect('/orders.php'); }
        unset($_SESSION['sim']);
        redirect($ctx['instalment'] ? '/layby.php' : '/confirmation.php?order_id=' . (int)$ctx['order']['order_id']);
    }
}
?><!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($gw) ?> | Secure payment (practice)</title>
<link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/img/favicon.svg">
<style>
*{box-sizing:border-box}body{margin:0;font-family:Inter,system-ui,sans-serif;background:#eef1f5;color:#1b2330}
.top{background:<?= e($color) ?>;color:#fff;padding:18px 24px;display:flex;justify-content:space-between;align-items:center}
.top b{font-size:1.25rem;letter-spacing:.01em}.top span{font-size:.8rem;opacity:.85}
.wrap{max-width:880px;margin:28px auto;padding:0 16px;display:grid;grid-template-columns:1.2fr .8fr;gap:20px}
.box{background:#fff;border-radius:14px;padding:26px;box-shadow:0 8px 28px -14px rgba(0,0,0,.25)}
h2{margin:0 0 16px;font-size:1.1rem}label{display:block;font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#6b7686;margin:14px 0 6px}
input[type=text],input[type=tel]{width:100%;border:1px solid #cfd6e0;border-radius:9px;padding:12px;font-size:.95rem}
input:focus{outline:2px solid <?= e($color) ?>;border-color:transparent}.row2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.pay{width:100%;margin-top:22px;background:<?= e($color) ?>;color:#fff;border:0;border-radius:10px;padding:15px;font-size:1rem;font-weight:700;cursor:pointer}
.cancel{width:100%;margin-top:10px;background:none;border:0;color:#6b7686;font-size:.85rem;cursor:pointer;text-decoration:underline}
.sum div{display:flex;justify-content:space-between;margin:10px 0;font-size:.9rem}.sum .t{font-weight:800;font-size:1.15rem;border-top:1px solid #e3e8ef;padding-top:12px}
.sandbox{background:#fff7e0;border:1px solid #f0d58a;color:#7a5b00;border-radius:10px;padding:10px 14px;font-size:.78rem;margin-bottom:16px}
.err{background:#fdecec;border:1px solid #f1b4b4;color:#a02424;border-radius:10px;padding:10px 14px;font-size:.82rem;margin-bottom:10px}
.banks{display:grid;grid-template-columns:1fr 1fr;gap:10px}.banks label{display:flex;align-items:center;gap:8px;text-transform:none;letter-spacing:0;font-size:.9rem;font-weight:500;color:#1b2330;border:1px solid #cfd6e0;border-radius:10px;padding:12px;margin:0;cursor:pointer}
.lock{font-size:.72rem;color:#6b7686;text-align:center;margin-top:14px}
@media(max-width:760px){.wrap{grid-template-columns:1fr}}
</style></head><body>
<div class="top"><b><?= e($gw) ?></b><span>&#128274; Secure payment &middot; <?= e($method) ?></span></div>
<div class="wrap">
  <form class="box" method="post" novalidate>
    <?= csrf_field() ?>
    <div class="sandbox"><b>Practice mode.</b> This page imitates <?= e($gw) ?>'s checkout so the full process can be demonstrated. No money moves.</div>
    <?php foreach ($errors as $er): ?><div class="err"><?= e($er) ?></div><?php endforeach; ?>
<?php if ($method === 'Card'): ?>
    <h2>Pay with card</h2>
    <label>Card number</label><input type="tel" name="card" inputmode="numeric" placeholder="4000 0000 0000 0002" autocomplete="off" value="<?= e($_POST['card'] ?? '') ?>">
    <div class="row2"><div><label>Expiry (MM/YY)</label><input type="text" name="exp" placeholder="09/29" value="<?= e($_POST['exp'] ?? '') ?>"></div>
      <div><label>Security code</label><input type="tel" name="cvv" placeholder="123" maxlength="4"></div></div>
    <label>Name on card</label><input type="text" name="holder" value="<?= e($_POST['holder'] ?? $user['full_name']) ?>">
<?php else: ?>
    <h2>Pay with instant EFT</h2>
    <label style="margin-top:0">Choose your bank</label>
    <div class="banks"><?php foreach ($banks as $b): ?><label><input type="radio" name="bank" value="<?= e($b) ?>" <?= ($_POST['bank'] ?? '') === $b ? 'checked' : '' ?>> <?= e($b) ?></label><?php endforeach; ?></div>
    <p style="font-size:.8rem;color:#6b7686;margin-top:14px">In the real service you would now log in to your bank and approve the payment.</p>
<?php endif; ?>
    <button class="pay" type="submit">Pay <?= money($ctx['amount']) ?></button>
    <button class="cancel" type="submit" name="cancel" value="1" formnovalidate>Cancel and return to LatestiPhone</button>
    <div class="lock">Powered by <?= e($gw) ?> (practice mode)</div>
  </form>
  <div class="box sum">
    <h2>Payment summary</h2>
    <div><span>Merchant</span><span>LatestiPhone</span></div>
    <div><span>Reference</span><span><?= e($ctx['ref']) ?></span></div>
    <div><span><?= $ctx['instalment'] ? 'Lay-by instalment' : 'Order #' . (int)$ctx['order']['order_id'] ?></span><span><?= money($ctx['amount']) ?></span></div>
    <div class="t"><span>Total</span><span><?= money($ctx['amount']) ?></span></div>
  </div>
</div>
</body></html>
