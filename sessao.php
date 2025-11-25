<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

// Token de upload único por sessão
if (!isset($_SESSION['upload_token'])) {
    $_SESSION['upload_token'] = bin2hex(random_bytes(16));
}
$uploadToken = $_SESSION['upload_token'];