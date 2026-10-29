<?php
// config.php - settings for the LATEST-IPHONE website.
// All the other pages include this file, so changing something here changes it everywhere.

// ---- Database connection ----
// Set these values for your own hosting account. They can also be supplied
// as environment variables so database passwords do not need to be committed.
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'latestiphone');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// ---- Business settings ----
define('SITE_NAME', 'LATEST-IPHONE');
define('WHATSAPP_NUMBER', '27638592102'); // WhatsApp orders go to this number (no + sign)
define('PAXI_FEE', 59.95);                // delivery price for PEP Paxi
define('COURIER_FEE', 99.00);             // delivery price for The Courier Guy
define('MIN_DEPOSIT_PERCENT', 30);        // minimum deposit (%) a customer pays to start a layby

// ---- Payment gateways (PayFast, Ozow, Yoco) ----
// 'simulate' = practice payment pages, no real money is taken (used for this assignment)
// 'sandbox'  = the gateways' test environments
// 'live'     = real payments, only once the merchant accounts are approved
define('GATEWAY_MODE', getenv('GATEWAY_MODE') ?: 'simulate');

// PayFast test details (these are PayFast's public sandbox details, not mine)
define('PAYFAST_MERCHANT_ID',  getenv('PAYFAST_MERCHANT_ID')  ?: '10000100');
define('PAYFAST_MERCHANT_KEY', getenv('PAYFAST_MERCHANT_KEY') ?: '46f0cd694581a');
define('PAYFAST_PASSPHRASE',   getenv('PAYFAST_PASSPHRASE')   ?: '');

// Ozow and Yoco are empty because I don't have merchant accounts yet.
// When a key is empty the site shows the practice payment page instead.
define('OZOW_SITE_CODE',   getenv('OZOW_SITE_CODE')   ?: '');
define('OZOW_PRIVATE_KEY', getenv('OZOW_PRIVATE_KEY') ?: '');
define('YOCO_SECRET_KEY',  getenv('YOCO_SECRET_KEY')  ?: '');

// ---- Site path ----
// Use an empty value when the project is in the web root. If it is in a folder,
// set BASE_URL to that folder path, for example '/latest-iphone'.
define('BASE_URL', getenv('BASE_URL') ?: '');

// ---- Sessions ----
// Sessions keep the user logged in. httponly stops JavaScript from reading the cookie.
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
if (session_status() === PHP_SESSION_NONE) { session_start(); }