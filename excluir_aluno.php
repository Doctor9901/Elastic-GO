<?php
// Inicia a sessão (necessário para acessar variáveis de sessão)
session_start();

// Verifica se o usuário está logado e se é administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 'admin') {
    // Se não for admin ou não estiver logado, volta para a página inicial
    header("Location: index.php");
    exit;
}

try {
    // Conecta ao banco de dados (MySQL)
    $pdo = new PDO("mysql:host=localhost;dbname=elastic_db", "root", "941957");
    // Define que erros do banco serão exibidos como exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Se der erro na conexão, mostra mensagem e encerra
    die("Erro na conexão: " . $e->getMessage());
}

// Verifica se o ID do aluno foi passado na URL
if (!isset($_GET['id'])) {
    echo "ID do aluno não informado.";
    exit;
}

// Converte o ID recebido para número inteiro (por segurança)
$id = (int)$_GET['id'];

// Impede que o administrador exclua a si mesmo
if ($id === (int)$_SESSION['id']) {
    echo "Erro: você não pode excluir a si mesmo.";
    exit;
}

// Busca o usuário no banco e verifica se ele é um aluno comum
$sql = $pdo->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo = 'comum'");
$sql->execute([$id]);
$usuario = $sql->fetch(PDO::FETCH_ASSOC);

// Se não encontrar o usuário ou ele não for do tipo 'comum', mostra erro
if (!$usuario) {
    echo "Aluno não encontrado ou não é um usuário comum.";
    exit;
}

try {
    // Inicia uma transação (para garantir que todas as exclusões ocorram juntas)
    $pdo->beginTransaction();

    // Exclui todos os registros do aluno na tabela de tempos de exercícios
    $stmt1 = $pdo->prepare("DELETE FROM tempos_exercicios WHERE usuario_id = ?");
    $stmt1->execute([$id]);

    // Exclui o usuário da tabela de usuários
    $stmt2 = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt2->execute([$id]);

    // Confirma as exclusões no banco de dados
    $pdo->commit();

    // Redireciona de volta ao painel do admin com uma mensagem de sucesso
    header("Location: painel_adm.php?msg=aluno_excluido");
    exit;

} catch (Exception $e) {
    // Se der algum erro, desfaz as mudanças e mostra a mensagem de erro
    $pdo->rollBack();
    echo "Erro ao excluir aluno: " . $e->getMessage();
}
?>