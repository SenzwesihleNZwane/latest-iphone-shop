<?php
require_once __DIR__ . '/../includes/functions.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $s = db()->prepare("SELECT user_id, password_hash FROM users WHERE email = ? AND role = 'admin'");
    $s->execute([strtolower(clean($_POST['email'] ?? ''))]);
    $u = $s->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$u['user_id'];
        redirect('/admin/index.php');
    }
    $error = 'Incorrect admin credentials.';
}
$pageTitle = 'Admin Login';
$isHome = false;
require __DIR__ . '/../includes/head.php';
?>
<body class="bg-[#f5f2ea] dark:bg-[#0b0b0d] text-[#16140f] dark:text-[#f5f1e8] font-sans antialiased min-h-screen">
<main class="page" style="max-width:520px;padding-top:80px">
<span class="eyebrow">Staff only</span><h1>Admin login</h1>
<?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
<form class="stack" method="post" data-validate novalidate>
  <?= csrf_field() ?>
  <label>Email</label><div><input name="email" type="email" data-rule="email"><div class="error-text"></div></div>
  <label>Password</label><div><input name="password" type="password" data-rule="text"><div class="error-text"></div></div>
  <p><button class="btn" type="submit">Log in</button></p>
</form>
<p><a href="<?= BASE_URL ?>/login.php" class="muted">&larr; Customer login</a></p>
</main><script src="<?= asset('/assets/js/theme.js') ?>"></script></body></html>
