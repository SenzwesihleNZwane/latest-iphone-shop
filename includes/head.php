<?php
require_once __DIR__ . '/logo.php';
// Shared <head>: same Tailwind / Lucide / fonts as the original design, plus the theme stylesheet.
// Needs: $pageTitle (string). Optional: $extraHead (string of HTML).
?><!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Home') ?> | LATEST-IPHONE</title>

  <!-- Apply the saved theme BEFORE first paint (no white flash in dark mode) -->
  <script>
    (function(){try{var t=localStorage.getItem('theme');var d=t==='dark'||(!t&&window.matchMedia('(prefers-color-scheme: dark)').matches);if(d)document.documentElement.classList.add('dark');}catch(e){}})();
  </script>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="icon" type="image/svg+xml" href="<?= BASE_URL ?>/assets/img/favicon.svg">

  <link rel="stylesheet" href="<?= asset('/assets/css/theme.css') ?>">
  <link rel="stylesheet" href="<?= asset('/assets/css/anim.css') ?>">
  <?= $extraHead ?? '' ?>
</head>
