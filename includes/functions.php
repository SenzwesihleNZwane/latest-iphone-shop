<?php
require_once __DIR__ . '/db.php';

/** Escape output (XSS protection). */
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function money($v): string { return 'R ' . number_format((float)$v, 2, '.', ' '); }
/** URL of a CSS/JS file with a version stamp, so browsers never keep an old copy after an update. */
function asset(string $path): string { $f = __DIR__ . '/../' . ltrim($path, '/'); return BASE_URL . $path . '?v=' . (is_file($f) ? filemtime($f) : 1); }
function redirect(string $path): void { header('Location: ' . BASE_URL . $path); exit; }

function flash(?string $msg = null, string $type = 'info') {
    if ($msg !== null) { $_SESSION['flash'] = ['m' => $msg, 't' => $type]; return null; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}

/* ---------- JSON helpers (for /api) ---------- */
function json_out(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
function json_input(): array {
    $raw = file_get_contents('php://input');
    $d = json_decode($raw ?: '[]', true);
    return is_array($d) ? $d : [];
}

/* ---------- CSRF ---------- */
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
/** Accepts the token from a form field or from the X-CSRF-Token header (fetch/JSON calls). */
function csrf_check(bool $json = false): void {
    $sent = $_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)$sent)) {
        if ($json) json_out(['ok' => false, 'error' => 'Your session expired. Please refresh the page.'], 419);
        http_response_code(419); exit('Invalid form token. Please go back and try again.');
    }
}

/* ---------- Auth ---------- */
function current_user(): ?array {
    if (empty($_SESSION['user_id'])) return null;
    $s = db()->prepare('SELECT user_id, full_name, email, phone, role FROM users WHERE user_id = ?');
    $s->execute([$_SESSION['user_id']]);
    return $s->fetch() ?: null;
}
function require_login(): array {
    $u = current_user();
    if (!$u) { flash('Please log in to continue.', 'warn'); redirect('/login.php?next=checkout'); }
    return $u;
}
/** Everything except the login screen is for signed-in members only. */
function require_member(): array {
    $u = current_user();
    if (!$u) { redirect('/login.php'); }
    return $u;
}
function require_admin(): array {
    $u = current_user();
    if (!$u || $u['role'] !== 'admin') { redirect('/admin/login.php'); }
    return $u;
}

/* ---------- Cart ----------
   The browser keeps the cart in localStorage (instant UI). When the customer presses
   "Proceed to Checkout", api/cart.php validates every line against the database and
   stores it here: $_SESSION['cart'] = ["productId|Colour" => [product_id, colour, qty]]  */
function cart_lines(): array { return $_SESSION['cart'] ?? []; }

function cart_items(): array {
    $lines = cart_lines();
    if (!$lines) return [];
    $ids = array_values(array_unique(array_map(fn($l) => (int)$l['product_id'], $lines)));
    $in = implode(',', array_fill(0, count($ids), '?'));
    $s = db()->prepare("SELECT p.product_id, p.storage_capacity, p.price, m.model_id, m.model_name
                        FROM products p JOIN product_models m ON m.model_id = p.model_id
                        WHERE p.is_active = 1 AND m.is_active = 1 AND p.product_id IN ($in)");
    $s->execute($ids);
    $byId = [];
    foreach ($s->fetchAll() as $r) $byId[(int)$r['product_id']] = $r;

    $img = db()->prepare('SELECT image_path FROM model_colours WHERE model_id = ? AND colour_name = ?');
    $rows = [];
    foreach ($lines as $l) {
        $p = $byId[(int)$l['product_id']] ?? null;
        if (!$p) continue;
        $img->execute([$p['model_id'], $l['colour']]);
        $p['colour'] = $l['colour'];
        $p['image'] = $img->fetchColumn() ?: '';
        $p['qty'] = (int)$l['qty'];
        $p['price'] = (float)$p['price'];
        $p['line'] = $p['qty'] * $p['price'];
        $rows[] = $p;
    }
    return $rows;
}
function cart_total(): float { return array_sum(array_column(cart_items(), 'line')); }
function cart_count(): int { return array_sum(array_map(fn($l) => (int)$l['qty'], cart_lines())); }

/* ---------- Validation helpers ---------- */
function clean(string $s): string { return trim(strip_tags($s)); }
function valid_phone(string $p): bool { return (bool)preg_match('/^(\+27|0)[0-9]{9}$/', preg_replace('/\s+/', '', $p)); }
function valid_postal(string $p): bool { return (bool)preg_match('/^[0-9]{4}$/', $p); }
function valid_password(string $p): bool { return (bool)preg_match('/^(?=.*[A-Z])(?=.*[0-9]).{8,}$/', $p); }

/** Stock available in a branch for a product (model + storage). */
function branch_qty(int $branch_id, int $product_id): int {
    $s = db()->prepare('SELECT quantity_available FROM branch_stock WHERE branch_id = ? AND product_id = ?');
    $s->execute([$branch_id, $product_id]);
    return (int)($s->fetchColumn() ?: 0);
}
