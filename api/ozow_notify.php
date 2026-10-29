<?php
// Ozow notification (server-to-server). Registered as NotifyUrl in pay.php.
require_once __DIR__ . '/../includes/payments.php';
header('Content-Type: text/plain');
http_response_code(200);
if (!gateway_is_real('Ozow') || $_SERVER['REQUEST_METHOD'] !== 'POST') { exit; }
$ctx = ozow_notify_verify($_POST, $why);
if ($ctx) {
    try { payment_complete($ctx, 'Ozow', 'Instant EFT', (string)($_POST['TransactionId'] ?? '')); }
    catch (Throwable $e) { error_log('Ozow notify: ' . $e->getMessage()); }
} else { error_log('Ozow notification rejected: ' . $why); }
echo 'OK';
