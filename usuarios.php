<?php
// Conecta ao banco de dados MySQL
$pdo = new PDO("mysql:host=localhost;dbname=elastic_db", "root", "941957");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Ativa exibição de erros do PDO

// Função para cadastrar um novo usuário
function cadastrarUsuario($nome, $email, $senha, $tipo = 'comum') {
    global $pdo; // Usa a conexão do banco definida fora da função
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT); // Cria hash da senha
    $sql = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)"); // Prepara SQL
    if ($sql->execute([$nome, $email, $senhaHash, $tipo])) { // Executa SQL com os valores fornecidos
        return $pdo->lastInsertId(); // Retorna o ID do usuário criado
    }
    return false; // Retorna false se não conseguiu cadastrar
}

// Função para buscar um usuário pelo e-mail
function buscarUsuarioPorEmail($email) {
    global $pdo; // Usa a conexão do banco definida fora da função
    $sql = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?"); // Prepara SQL
    $sql->execute([$email]); // Executa SQL com o e-mail fornecido
    return $sql->fetch(PDO::FETCH_ASSOC); // Retorna os dados do usuário como array associativo
}