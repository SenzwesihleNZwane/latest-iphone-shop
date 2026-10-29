<?php
// JSON authentication endpoint used by the login / register pop-up on the home page.
require_once __DIR__ . '/../includes/functions.php';

$in = json_input();
$action = $in['action'] ?? ($_GET['action'] ?? '');

if ($action === 'me') {
    $u = current_user();
    json_out(['ok' => true, 'user' => $u ? ['name' => $u['full_name'], 'email' => $u['email'], 'role' => $u['role']] : null, 'csrf' => csrf_token()]);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_out(['ok' => false, 'error' => 'POST required.'], 405);
csrf_check(true);

if ($action === 'logout') {
    unset($_SESSION['user_id'], $_SESSION['cart']);
    session_regenerate_id(true);
    json_out(['ok' => true]);
}

if ($action === 'register') {
    $name = clean((string)($in['name'] ?? '')); $email = strtolower(clean((string)($in['email'] ?? '')));
    $phone = clean((string)($in['phone'] ?? '')); $pass = (string)($in['password'] ?? '');
    $errors = [];
    if ($name === '' || mb_strlen($name) > 100) $errors['name'] = 'Enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email address.';
    if (!valid_phone($phone)) $errors['phone'] = 'Use a South African number, e.g. 0821234567.';
    if (!valid_password($pass)) $errors['password'] = 'Min 8 characters with a capital letter and a number.';
    if (!$errors) {
        $s = db()->prepare('SELECT 1 FROM users WHERE email = ?'); $s->execute([$email]);
        if ($s->fetch()) $errors['email'] = 'That email is already registered.';
    }
    if ($errors) json_out(['ok' => false, 'errors' => $errors], 422);

    db()->prepare("INSERT INTO users (full_name,email,phone,password_hash,role) VALUES (?,?,?,?, 'customer')")
        ->execute([$name, $email, $phone, password_hash($pass, PASSWORD_DEFAULT)]);
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)db()->lastInsertId();
    json_out(['ok' => true, 'user' => ['name' => $name, 'email' => $email, 'role' => 'customer']]);
}

if ($action === 'login') {
    $email = strtolower(clean((string)($in['email'] ?? '')));
    $s = db()->prepare("SELECT user_id, full_name, email, password_hash FROM users WHERE email = ? AND role = 'customer'");
    $s->execute([$email]);
    $u = $s->fetch();
    if ($u && password_verify((string)($in['password'] ?? ''), $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$u['user_id'];
        json_out(['ok' => true, 'user' => ['name' => $u['full_name'], 'email' => $u['email'], 'role' => 'customer']]);
    }
    json_out(['ok' => false, 'errors' => ['password' => 'Incorrect email or password.']], 401);
}

json_out(['ok' => false, 'error' => 'Unknown action.'], 400);
