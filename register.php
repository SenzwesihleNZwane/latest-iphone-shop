<?php
require_once 'includes/auth_layout.php';
if (current_user()) redirect('/index.php');
$errors = [];
$next = ($_GET['next'] ?? '') === 'checkout' ? 'checkout' : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = clean($_POST['full_name'] ?? ''); $email = strtolower(clean($_POST['email'] ?? ''));
    $phone = clean($_POST['phone'] ?? ''); $pass = $_POST['password'] ?? '';
    if ($name === '' || strlen($name) > 100) $errors[] = 'Enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (!valid_phone($phone)) $errors[] = 'Enter a valid South African phone number.';
    if (!valid_password($pass)) $errors[] = 'Password needs 8+ characters, a capital letter and a number.';
    if (!$errors) {
        $s = db()->prepare('SELECT 1 FROM users WHERE email = ?'); $s->execute([$email]);
        if ($s->fetch()) { $errors[] = 'That email is already registered.'; }
        else {
            db()->prepare("INSERT INTO users (full_name,email,phone,password_hash,role) VALUES (?,?,?,?, 'customer')")
                ->execute([$name, $email, $phone, password_hash($pass, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)db()->lastInsertId();
            flash('Welcome to LatestiPhone!', 'ok');
            redirect($next === 'checkout' ? '/checkout.php' : '/index.php');
        }
    }
}
auth_open('Create your account', 'Join LatestiPhone', 'One account for your orders, lay-by plans and wishlist.');
?>
<?php foreach ($errors as $er): ?><div class="alert err"><?= e($er) ?></div><?php endforeach; ?>
<form method="post" data-validate novalidate>
  <?= csrf_field() ?>
  <label>Full name</label><div><input name="full_name" data-rule="text" autocomplete="name" value="<?= e($_POST['full_name'] ?? '') ?>"><div class="error-text"></div></div>
  <label>Email</label><div><input name="email" type="email" data-rule="email" autocomplete="email" value="<?= e($_POST['email'] ?? '') ?>"><div class="error-text"></div></div>
  <label>Phone</label><div><input name="phone" data-rule="phone" placeholder="0821234567" autocomplete="tel" value="<?= e($_POST['phone'] ?? '') ?>"><div class="error-text"></div></div>
  <label>Password</label><div><input name="password" type="password" data-rule="password" autocomplete="new-password"><div class="error-text"></div></div>
  <button class="btn shine" type="submit">Create account</button>
</form>
<p class="text-sm mt-6 text-center muted">Already a member? <a class="font-semibold" style="color:var(--gold-d)" href="login.php<?= $next ? '?next=checkout' : '' ?>">Log in</a></p>
<?php auth_close(); ?>
