<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=elastic_db", "root", "941957");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

if (!isset($_GET['id'])) {
    echo "ID do aluno não informado.";
    exit;
}

$id = (int)$_GET['id'];

if ($id === (int)$_SESSION['id']) {
    echo "Erro: você não pode excluir a si mesmo.";
    exit;
}

$sql = $pdo->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo = 'comum'");
$sql->execute([$id]);
$usuario = $sql->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Aluno não encontrado ou não é um usuário comum.";
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt1 = $pdo->prepare("DELETE FROM tempos_exercicios WHERE usuario_id = ?");
    $stmt1->execute([$id]);

    $stmt2 = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt2->execute([$id]);

    $pdo->commit();

    header("Location: painel_adm.php?msg=aluno_excluido");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Erro ao excluir aluno: " . $e->getMessage();
}