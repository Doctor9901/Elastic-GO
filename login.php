<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=elastic_db", "root", "941957");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Verifica se email e senha foram enviados
if (empty($_POST['email']) || empty($_POST['senha'])) {
    $_SESSION['erro'] = "Preencha email e senha!";
    header("Location: index.php");
    exit;
}

$email = $_POST['email'];
$senha = $_POST['senha'];

// Busca usuário pelo email
$sql = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$sql->execute([$email]);
$usuario = $sql->fetch(PDO::FETCH_ASSOC);

// Verifica se usuário existe e senha confere
if ($usuario && password_verify($senha, $usuario['senha'])) {

    // 🔹 Salva dados importantes na sessão
    $_SESSION['id'] = $usuario['id'];       // ← ESSENCIAL!
    $_SESSION['usuario'] = $usuario['nome'];
    $_SESSION['tipo'] = $usuario['tipo'];

    // Redireciona para diferentes painéis conforme tipo do usuário
    if ($usuario['tipo'] === 'admin') {
        header("Location: painel_adm.php");
    } else {
        header("Location: painel_aluno.php");
    }
    exit;

} else {
    $_SESSION['erro'] = "Email ou senha inválidos!";
    header("Location: index.php");
    exit;
}