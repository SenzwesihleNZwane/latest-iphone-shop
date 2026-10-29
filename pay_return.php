<?php
// Where the real gateways send the customer back to (PayFast/Ozow/Yoco return_url / success / failure URLs).
// The money is only recorded by the server-to-server notification (api/*), or - for Yoco - by asking Yoco's API here.
require_once __DIR__ . '/includes/payments.php';
$user = require_login();
$ref = (string)($_GET['ref'] ?? '');
if (!preg_match('/^([OL])(\d+)$/', $ref, $m)) redirect('/orders.php');
if (!empty($_GET['error'])) {
    $ctx = payment_context($ref);
    flash('The payment was not completed. You have not been charged.', 'err');
    redirect($ctx ? pay_page_for($ctx) : '/orders.php');
}
$ctx = payment_context($ref);
if ($ctx && !empty($_SESSION['yoco_checkout'][$ref]) && gateway_is_real('Yoco') && yoco_is_paid($_SESSION['yoco_checkout'][$ref])) {
    try { payment_complete($ctx, 'Yoco', 'Card', $_SESSION['yoco_checkout'][$ref]); } catch (Throwable $e) {}
    unset($_SESSION['yoco_checkout'][$ref]);
    $ctx = null;
}
if ($m[1] === 'L') redirect('/layby.php');
$o = db()->prepare('SELECT order_id FROM orders WHERE order_id=? AND user_id=?'); $o->execute([(int)$m[2], $user['user_id']]);
redirect($o->fetch() ? '/confirmation.php?order_id=' . (int)$m[2] : '/orders.php');
