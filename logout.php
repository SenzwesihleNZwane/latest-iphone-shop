<?php
require_once 'includes/functions.php';
$_SESSION = [];
session_destroy();
header('Location: ' . BASE_URL . '/login.php');
exit;
