<?php
// Conexão com o banco
try {
    $pdo = new PDO("mysql:host=localhost;dbname=elastic_db;charset=utf8mb4", "root", "941957");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Função para cadastrar aluno
function cadastrarAluno($usuario_id, $curso) {
    global $pdo;
    $sql = $pdo->prepare("INSERT INTO alunos (usuario_id, curso) VALUES (?, ?)");
    return $sql->execute([$usuario_id, $curso]);
}

// Buscar aluno pelo usuario_id
function buscarAlunoPorUsuarioId($usuario_id) {
    global $pdo;
    $sql = $pdo->prepare("SELECT * FROM alunos WHERE usuario_id = ?");
    $sql->execute([$usuario_id]);
    return $sql->fetch(PDO::FETCH_ASSOC);
}

// Atualizar curso do aluno
function atualizarAluno($usuario_id, $novoCurso) {
    global $pdo;
    $sql = $pdo->prepare("UPDATE alunos SET curso = ? WHERE usuario_id = ?");
    return $sql->execute([$novoCurso, $usuario_id]);
}

// Excluir aluno da tabela alunos
function excluirAluno($usuario_id) {
    global $pdo;
    $sql = $pdo->prepare("DELETE FROM alunos WHERE usuario_id = ?");
    return $sql->execute([$usuario_id]);
}
