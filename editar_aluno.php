<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=elastic_db", "root", "941957");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Verifica se foi passado o ID
if (!isset($_GET['id'])) {
    echo "ID do aluno não informado.";
    exit;
}

$id = $_GET['id'];

// Buscar dados do aluno e usuário
$sql = $pdo->prepare("
    SELECT u.id, u.nome, u.email, a.curso 
    FROM usuarios u 
    LEFT JOIN alunos a ON u.id = a.usuario_id 
    WHERE u.id = ? AND u.tipo = 'comum'
");
$sql->execute([$id]);
$aluno = $sql->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    echo "Aluno não encontrado.";
    exit;
}

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $curso = $_POST['curso'];

    $pdo->beginTransaction();

    try {
        $sql1 = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
        $sql1->execute([$nome, $email, $id]);

        $sql2 = $pdo->prepare("UPDATE alunos SET curso = ? WHERE usuario_id = ?");
        $sql2->execute([$curso, $id]);

        $pdo->commit();

        header("Location: painel_adm.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $mensagem = "Erro ao atualizar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar Aluno - Elastic GO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="styles.css" />
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary">
  <main class="w-100 m-auto form-container">
    <form method="POST" novalidate>
      <h1 class="h1 mb-3 fw-bold text-center elastic-go-title">ELASTIC GO</h1>
      <h2 class="h3 mb-3 fw-normal text-center">Editar Aluno</h2>

      <!-- Mensagem de erro -->
      <?php if ($mensagem): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($mensagem) ?></div>
      <?php endif; ?>

      <!-- Nome -->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Completo" required
          value="<?= htmlspecialchars($aluno['nome'] ?? '') ?>">
        <label for="nome">Nome Completo</label>
      </div>

      <!-- Email -->
      <div class="form-floating mb-3">
        <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" required
          value="<?= htmlspecialchars($aluno['email'] ?? '') ?>">
        <label for="email">E-mail</label>
      </div>

      <!-- Curso -->
      <div class="form-floating mb-3">
        <select class="form-select" id="curso" name="curso" required>
          <option value="" disabled <?= empty($aluno['curso']) ? 'selected' : '' ?>>Selecione o nível do curso</option>
          <option value="básico" <?= (isset($aluno['curso']) && $aluno['curso'] === 'básico') ? 'selected' : '' ?>>Básico</option>
          <option value="médio" <?= (isset($aluno['curso']) && $aluno['curso'] === 'médio') ? 'selected' : '' ?>>Médio</option>
        </select>
        <label for="curso">Nível do Curso</label>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2">Salvar</button>

      <div class="text-center mt-3">
        <a href="painel_adm.php" class="text-primary">Voltar</a>
      </div>
    </form>
  </main>
</body>
</html>