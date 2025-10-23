<?php
// banco de dados junto do arquivo usuários.php
$pdo = new PDO("mysql:host=localhost;dbname=elastic_db", "root", "941957");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function cadastrarUsuario($nome, $email, $senha, $tipo = 'comum') {
    global $pdo;
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $sql = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
    if ($sql->execute([$nome, $email, $senhaHash, $tipo])) {
        return $pdo->lastInsertId();
    }
    return false;
}

function buscarUsuarioPorEmail($email) {
    global $pdo;
    $sql = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $sql->execute([$email]);
    return $sql->fetch(PDO::FETCH_ASSOC);
}