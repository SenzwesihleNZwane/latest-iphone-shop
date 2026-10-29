<?php
require_once 'includes/auth_layout.php';
if (current_user()) redirect('/index.php');
$error = ''; $flash = flash();
$next = ($_GET['next'] ?? '') === 'checkout' ? 'checkout' : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = strtolower(clean($_POST['email'] ?? ''));
    $s = db()->prepare("SELECT user_id, password_hash FROM users WHERE email = ? AND role = 'customer'");
    $s->execute([$email]);
    $u = $s->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$u['user_id'];
        redirect($next === 'checkout' ? '/checkout.php' : '/index.php');
    }
    $error = 'Incorrect email or password.';
}
auth_open('Welcome back', 'Member sign in', 'Log in to see our full iPhone lineup, your orders and lay-by plans.');
?>
<?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
<?php if ($flash && !$error): ?><div class="alert <?= e($flash['t']) ?>"><?= e($flash['m']) ?></div><?php endif; ?>
<form method="post" data-validate novalidate>
  <?= csrf_field() ?>
  <label>Email</label><div><input name="email" type="email" data-rule="email" autocomplete="email" value="<?= e($_POST['email'] ?? '') ?>"><div class="error-text"></div></div>
  <label>Password</label><div><input name="password" type="password" data-rule="text" autocomplete="current-password"><div class="error-text"></div></div>
  <button class="btn shine" type="submit">Log in</button>
</form>
<p class="text-sm mt-6 text-center muted">New to LatestiPhone? <a class="font-semibold" style="color:var(--gold-d)" href="register.php<?= $next ? '?next=checkout' : '' ?>">Create an account</a></p>
<?php auth_close(); ?>
