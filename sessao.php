<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header("Location: index.php"); // Página de login
    exit;
}
?>