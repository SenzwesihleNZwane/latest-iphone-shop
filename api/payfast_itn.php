<?php
// PayFast Instant Transaction Notification (server-to-server). Register this URL as notify_url (done automatically in pay.php).
require_once __DIR__ . '/../includes/payments.php';
header('Content-Type: text/plain');
http_response_code(200);                       // PayFast expects a 200 quickly
if (!gateway_is_real('PayFast') || $_SERVER['REQUEST_METHOD'] !== 'POST') { exit; }
$ctx = payfast_itn_verify($_POST, $why);
if ($ctx && payfast_server_confirm($_POST)) {
    try { payment_complete($ctx, 'PayFast', ($_POST['payment_method'] ?? '') === 'eft' ? 'Instant EFT' : 'Card', (string)($_POST['pf_payment_id'] ?? '')); }
    catch (Throwable $e) { error_log('PayFast ITN: ' . $e->getMessage()); }
} else { error_log('PayFast ITN rejected: ' . ($why ?? 'server validation failed')); }
echo 'OK';
