<?php
/** Payment helpers shared by pay.php, pay_sim.php, pay_return.php and the gateway notify endpoints (api/). */
require_once __DIR__ . '/functions.php';

/** Absolute URL of a path on this site (gateways need full URLs). */
function abs_url(string $path): string {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    return ($https ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_URL . $path;
}

/** The three gateways from the project proposal and the payment methods each one offers. */
function gateway_catalogue(): array {
    return [
        'PayFast' => ['methods' => ['Card', 'Instant EFT'], 'color' => '#0b4f9c', 'blurb' => 'Cards and instant EFT in one secure checkout.'],
        'Ozow'    => ['methods' => ['Instant EFT'],         'color' => '#1c1c3a', 'blurb' => 'Pay straight from your own bank account.'],
        'Yoco'    => ['methods' => ['Card'],                'color' => '#0087c1', 'blurb' => 'Visa, Mastercard and other cards.'],
    ];
}
/** Where the customer goes if they cancel: back to the payment page of this order / instalment. */
function pay_page_for(array $ctx): string {
    return '/pay.php?' . ($ctx['instalment'] ? 'layby_id=' . $ctx['layby']['layby_id'] : 'order_id=' . $ctx['order']['order_id']);
}

/** Which gateways can really redirect (keys present and mode is not 'simulate'). */
function gateway_is_real(string $gw): bool {
    if (GATEWAY_MODE === 'simulate') return false;
    return match ($gw) {
        'PayFast' => PAYFAST_MERCHANT_ID !== '' && PAYFAST_MERCHANT_KEY !== '',
        'Ozow'    => OZOW_SITE_CODE !== '' && OZOW_PRIVATE_KEY !== '',
        'Yoco'    => YOCO_SECRET_KEY !== '',
        default   => false,
    };
}

/** What a payment reference ("O12" = order 12, "L5" = lay-by instalment 5) is for, and how much is due now. */
function payment_context(string $ref): ?array {
    $pdo = db();
    if (!preg_match('/^([OL])(\d+)/', $ref, $m)) return null;
    if ($m[1] === 'L') {
        $s = $pdo->prepare("SELECT * FROM layby_accounts WHERE layby_id=? AND status='Active'"); $s->execute([(int)$m[2]]);
        $layby = $s->fetch(); if (!$layby) return null;
        $o = $pdo->prepare('SELECT * FROM orders WHERE order_id=?'); $o->execute([$layby['order_id']]); $order = $o->fetch();
        return ['order' => $order, 'layby' => $layby, 'instalment' => true,
                'amount' => (float)min($layby['monthly_instalment'], $layby['balance_remaining']), 'ref' => 'L' . $layby['layby_id']];
    }
    $o = $pdo->prepare("SELECT * FROM orders WHERE order_id=? AND order_status='Pending'"); $o->execute([(int)$m[2]]); $order = $o->fetch();
    if (!$order) return null;
    $layby = null; $amount = (float)$order['total_amount'];
    if ($order['payment_type'] === 'Layby') {
        $s = $pdo->prepare('SELECT * FROM layby_accounts WHERE order_id=?'); $s->execute([$order['order_id']]); $layby = $s->fetch();
        if ($layby) $amount = (float)$layby['deposit_amount'];
    }
    return ['order' => $order, 'layby' => $layby ?: null, 'instalment' => false, 'amount' => $amount, 'ref' => 'O' . $order['order_id']];
}

/** Records a successful payment: transaction row, order/layby updates, stock deduction. Safe to call twice for the same gateway reference. */
function payment_complete(array $ctx, string $gateway, string $method, ?string $gatewayRef = null): void {
    $pdo = db();
    $order = $ctx['order']; $layby = $ctx['layby']; $amount = $ctx['amount']; $isInstalment = $ctx['instalment'];
    $ref = $gatewayRef ?: strtoupper($gateway[0]) . bin2hex(random_bytes(5));
    $dup = $pdo->prepare('SELECT 1 FROM transactions WHERE gateway=? AND gateway_reference=?'); $dup->execute([$gateway, $ref]);
    if ($dup->fetch()) return;                       // gateways retry notifications; only count a payment once
    $pdo->beginTransaction();
    try {
        $pdo->prepare('INSERT INTO transactions (order_id,gateway,payment_method,amount_paid,payment_status,gateway_reference) VALUES (?,?,?,?,?,?)')
            ->execute([$order['order_id'], $gateway, $method, $amount, 'Complete', $ref]);
        if ($layby) {
            $bal = max(0, $layby['balance_remaining'] - $amount);
            $status = $bal <= 0 ? 'Completed' : 'Active';
            $next = $bal > 0 ? date('Y-m-d', strtotime('+1 month')) : null;
            $pdo->prepare('UPDATE layby_accounts SET balance_remaining=?, status=?, next_due_date=? WHERE layby_id=?')->execute([$bal, $status, $next, $layby['layby_id']]);
            $pdo->prepare('INSERT INTO layby_payments (layby_id,amount) VALUES (?,?)')->execute([$layby['layby_id'], $amount]);
            if (!$isInstalment) { $pdo->prepare("UPDATE orders SET order_status='Layby Active' WHERE order_id=?")->execute([$order['order_id']]); deduct_stock($pdo, $order); }
            elseif ($status === 'Completed') { $pdo->prepare("UPDATE orders SET order_status='Paid' WHERE order_id=?")->execute([$order['order_id']]); }
        } else {
            $pdo->prepare("UPDATE orders SET order_status='Paid' WHERE order_id=?")->execute([$order['order_id']]);
            deduct_stock($pdo, $order);
        }
        $pdo->commit();
    } catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
}
function deduct_stock(PDO $pdo, array $order): void {
    $items = $pdo->prepare('SELECT product_id, quantity FROM order_items WHERE order_id=?'); $items->execute([$order['order_id']]);
    $upd = $pdo->prepare('UPDATE branch_stock SET quantity_available = quantity_available - ? WHERE branch_id=? AND product_id=? AND quantity_available >= ?');
    foreach ($items->fetchAll() as $it) {
        $upd->execute([$it['quantity'], $order['branch_id'], $it['product_id'], $it['quantity']]);
        if ($upd->rowCount() === 0) throw new RuntimeException('Stock changed before payment completed.');
    }
}

/* ============================ PayFast ============================ */
function payfast_url(): string { return GATEWAY_MODE === 'live' ? 'https://www.payfast.co.za/eng/process' : 'https://sandbox.payfast.co.za/eng/process'; }
/** MD5 signature over the fields in the order they are sent (PayFast docs). */
function payfast_signature(array $data, string $passphrase = ''): string {
    $pairs = [];
    foreach ($data as $k => $v) { if ($k === 'signature' || trim((string)$v) === '') continue; $pairs[] = $k . '=' . urlencode(trim((string)$v)); }
    $str = implode('&', $pairs);
    if ($passphrase !== '') $str .= '&passphrase=' . urlencode(trim($passphrase));
    return md5($str);
}
/** ITN signature: every posted field (empty ones too), in the order received, excluding 'signature' (PayFast docs). */
function payfast_itn_signature(array $post, string $passphrase = ''): string {
    $pairs = [];
    foreach ($post as $k => $v) { if ($k === 'signature') continue; $pairs[] = $k . '=' . urlencode(stripslashes((string)$v)); }
    $str = implode('&', $pairs);
    if ($passphrase !== '') $str .= '&passphrase=' . urlencode(trim($passphrase));
    return md5($str);
}
/** Checks an ITN: signature, status, and amount. Returns the payment context to complete, or null (with $why). */
function payfast_itn_verify(array $post, ?string &$why = null): ?array {
    if (!hash_equals(payfast_itn_signature($post, PAYFAST_PASSPHRASE), (string)($post['signature'] ?? ''))) { $why = 'bad signature'; return null; }
    if (($post['payment_status'] ?? '') !== 'COMPLETE') { $why = 'not complete'; return null; }
    $ctx = payment_context(explode('T', (string)($post['m_payment_id'] ?? ''))[0]);
    if (!$ctx) { $why = 'unknown or already paid'; return null; }
    if (abs((float)($post['amount_gross'] ?? 0) - $ctx['amount']) > 0.01) { $why = 'amount mismatch'; return null; }
    return $ctx;
}
/** Asks PayFast to confirm the ITN really came from them. */
function payfast_server_confirm(array $post): bool {
    $pairs = []; foreach ($post as $k => $v) { if ($k !== 'signature') $pairs[] = $k . '=' . urlencode(stripslashes((string)$v)); }
    $host = GATEWAY_MODE === 'live' ? 'www.payfast.co.za' : 'sandbox.payfast.co.za';
    $ch = curl_init('https://' . $host . '/eng/query/validate');
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => implode('&', $pairs), CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20]);
    $res = curl_exec($ch); curl_close($ch);
    return trim((string)$res) === 'VALID';
}
function payfast_form(array $ctx, array $user, string $method): array {
    $d = [
        'merchant_id' => PAYFAST_MERCHANT_ID, 'merchant_key' => PAYFAST_MERCHANT_KEY,
        'return_url' => abs_url('/pay_return.php?ref=' . $ctx['ref']), 'cancel_url' => abs_url('/pay.php?' . ($ctx['instalment'] ? 'layby_id=' . $ctx['layby']['layby_id'] : 'order_id=' . $ctx['order']['order_id'])),
        'notify_url' => abs_url('/api/payfast_itn.php'),
        'name_first' => explode(' ', $user['full_name'])[0], 'name_last' => trim(substr(strstr($user['full_name'] . ' ', ' '), 1)),
        'email_address' => $user['email'],
        'm_payment_id' => $ctx['ref'] . 'T' . time(), 'amount' => number_format($ctx['amount'], 2, '.', ''),
        'item_name' => 'LatestiPhone order #' . $ctx['order']['order_id'],
        'payment_method' => $method === 'Instant EFT' ? 'eft' : 'cc',
    ];
    $d['signature'] = payfast_signature($d, PAYFAST_PASSPHRASE);
    return $d;
}

/* ============================== Ozow ============================== */
function ozow_hash(array $values, string $privateKey): string { return hash('sha512', strtolower(implode('', $values) . $privateKey)); }
/** Ozow's server-to-server notification hash: SiteCode, TransactionId, TransactionReference, Amount, Status, Optional1-5, CurrencyCode, IsTest, StatusMessage + private key. */
function ozow_notify_verify(array $p, ?string &$why = null): ?array {
    $order = ['SiteCode','TransactionId','TransactionReference','Amount','Status','Optional1','Optional2','Optional3','Optional4','Optional5','CurrencyCode','IsTest','StatusMessage'];
    $vals = []; foreach ($order as $k) $vals[] = (string)($p[$k] ?? '');
    if (!hash_equals(ozow_hash($vals, OZOW_PRIVATE_KEY), strtolower((string)($p['Hash'] ?? '')))) { $why = 'bad hash'; return null; }
    if (strcasecmp((string)($p['Status'] ?? ''), 'Complete') !== 0) { $why = 'not complete'; return null; }
    $ctx = payment_context(explode('T', (string)($p['TransactionReference'] ?? ''))[0]);
    if (!$ctx) { $why = 'unknown or already paid'; return null; }
    if (abs((float)($p['Amount'] ?? 0) - $ctx['amount']) > 0.01) { $why = 'amount mismatch'; return null; }
    return $ctx;
}
function ozow_form(array $ctx): array {
    $f = ['SiteCode' => OZOW_SITE_CODE, 'CountryCode' => 'ZA', 'CurrencyCode' => 'ZAR', 'Amount' => number_format($ctx['amount'], 2, '.', ''),
          'TransactionReference' => $ctx['ref'] . 'T' . time(), 'BankReference' => 'LATESTIPHONE' . $ctx['order']['order_id'],
          'CancelUrl' => abs_url('/pay.php?' . ($ctx['instalment'] ? 'layby_id=' . $ctx['layby']['layby_id'] : 'order_id=' . $ctx['order']['order_id'])),
          'ErrorUrl' => abs_url('/pay_return.php?ref=' . $ctx['ref'] . '&error=1'), 'SuccessUrl' => abs_url('/pay_return.php?ref=' . $ctx['ref']),
          'NotifyUrl' => abs_url('/api/ozow_notify.php'), 'IsTest' => GATEWAY_MODE === 'live' ? 'false' : 'true'];
    // hash order per Ozow docs: SiteCode, CountryCode, CurrencyCode, Amount, TransactionReference, BankReference, Cancel, Error, Success, Notify, IsTest
    $f['HashCheck'] = ozow_hash([$f['SiteCode'], $f['CountryCode'], $f['CurrencyCode'], $f['Amount'], $f['TransactionReference'], $f['BankReference'],
        $f['CancelUrl'], $f['ErrorUrl'], $f['SuccessUrl'], $f['NotifyUrl'], $f['IsTest']], OZOW_PRIVATE_KEY);
    return $f;
}

/* ============================== Yoco ============================== */
/** Creates a Yoco hosted checkout and returns its redirect URL (or null with $err set). */
function yoco_checkout(array $ctx, ?string &$err = null): ?string {
    $body = json_encode(['amount' => (int)round($ctx['amount'] * 100), 'currency' => 'ZAR',
        'successUrl' => abs_url('/pay_return.php?ref=' . $ctx['ref']), 'cancelUrl' => abs_url('/pay.php?' . ($ctx['instalment'] ? 'layby_id=' . $ctx['layby']['layby_id'] : 'order_id=' . $ctx['order']['order_id'])),
        'failureUrl' => abs_url('/pay_return.php?ref=' . $ctx['ref'] . '&error=1'), 'metadata' => ['ref' => $ctx['ref']]]);
    $ch = curl_init('https://payments.yoco.com/api/checkouts');
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $body, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . YOCO_SECRET_KEY, 'Content-Type: application/json']]);
    $res = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    $j = json_decode((string)$res, true);
    if ($code >= 200 && $code < 300 && !empty($j['redirectUrl'])) { $_SESSION['yoco_checkout'][$ctx['ref']] = $j['id'] ?? ''; return $j['redirectUrl']; }
    $err = 'Yoco could not start the payment (' . $code . ').'; return null;
}
function yoco_is_paid(string $checkoutId): bool {
    $ch = curl_init('https://payments.yoco.com/api/checkouts/' . rawurlencode($checkoutId));
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . YOCO_SECRET_KEY]]);
    $res = curl_exec($ch); curl_close($ch); $j = json_decode((string)$res, true);
    return is_array($j) && (($j['status'] ?? '') === 'completed' || !empty($j['paymentId']));
}

/** Sends the browser to a gateway with an auto-submitting POST form. */
function post_redirect(string $url, array $fields): void {
    echo '<!DOCTYPE html><html><body style="font-family:sans-serif;text-align:center;padding:60px;background:#f5f2ea"><p>Taking you to the secure payment page&hellip;</p><form id="f" method="post" action="' . e($url) . '">';
    foreach ($fields as $k => $v) echo '<input type="hidden" name="' . e($k) . '" value="' . e($v) . '">';
    echo '<noscript><button>Continue</button></noscript></form><script>document.getElementById("f").submit()</script></body></html>'; exit;
}
