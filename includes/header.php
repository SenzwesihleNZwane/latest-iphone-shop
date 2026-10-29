<?php
// Header for every normal (non-home) page.
require_once __DIR__ . '/functions.php';
$user = current_user();
$isHome = false;
$flash = flash();
require __DIR__ . '/head.php';
?>
<body class="bg-[#f5f2ea] dark:bg-[#0b0b0d] text-[#16140f] dark:text-[#f5f1e8] font-sans antialiased selection:bg-[#cba135] selection:text-[#16140f] transition-colors duration-300 min-h-screen">
<?php require __DIR__ . '/nav.php'; ?>
<main class="page">
<?php if ($flash): ?><div class="alert <?= e($flash['t']) ?>"><?= e($flash['m']) ?></div><?php endif; ?>
