<?php
include "sessao.php";

// 🔒 Verifica se é admin
if ($_SESSION['tipo'] !== 'admin') {
    header("Location: painel_aluno.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=elastic_db;charset=utf8", "root", "941957");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// 🔍 Busca por nome ou email
$busca = "";
$params = [];

if (isset($_GET['busca']) && trim($_GET['busca']) !== "") {
    $busca = trim($_GET['busca']);
    $params = ["%$busca%", "%$busca%"];
}

// 🧩 Consulta alunos + último exercício (da tabela tempos_exercicios)
$sql = "
    SELECT 
        u.id,
        u.nome,
        u.email,
        COALESCE(t.tipo_exercicio, '-') AS tipo_exercicio,
        t.tempo_minutos,
        t.data_registro
    FROM usuarios u
    LEFT JOIN (
        SELECT 
            e1.usuario_id, 
            e1.tipo_exercicio, 
            e1.tempo_minutos, 
            e1.data_registro
        FROM tempos_exercicios e1
        INNER JOIN (
            SELECT usuario_id, MAX(data_registro) AS max_data
            FROM tempos_exercicios
            GROUP BY usuario_id
        ) e2 
        ON e1.usuario_id = e2.usuario_id 
        AND e1.data_registro = e2.max_data
    ) t ON u.id = t.usuario_id
    WHERE u.tipo = 'comum'
";

if ($busca !== "") {
    $sql .= " AND (u.nome LIKE ? OR u.email LIKE ?)";
}

$sql .= " ORDER BY u.nome";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Painel Admin — Elastic Go</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="style.css">
  <style>
    .text-muted-italic {
        color: #6c757d;
        font-style: italic;
    }
  </style>
</head>
<body class="bg-body-tertiary">
  <nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h1">Elastic Go - Painel Admin</span>
      <div class="d-flex">
        <span class="navbar-text me-3">Olá, <?= htmlspecialchars($_SESSION['usuario']); ?></span>
        <a href="sair.php" class="btn btn-outline-light">Sair</a>
      </div>
    </div>
  </nav>

  <main class="container py-4">
    <h3 class="mb-4">Lista de Alunos e Últimos Exercícios</h3>

    <form class="row g-2 mb-4" method="GET">
      <div class="col-md-8">
        <input
          type="text"
          name="busca"
          class="form-control"
          placeholder="Buscar por nome ou email"
          value="<?= htmlspecialchars($busca); ?>"
        />
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-primary w-100">Buscar</button>
      </div>
    </form>

    <?php if (count($alunos) > 0): ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Nome</th>
              <th>Email</th>
              <th>Último Exercício</th>
              <th>Tempo (min)</th>
              <th>Data</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($alunos as $aluno): ?>
              <tr>
                <td><?= htmlspecialchars($aluno['id']); ?></td>
                <td><?= htmlspecialchars($aluno['nome']); ?></td>
                <td><?= htmlspecialchars($aluno['email']); ?></td>

                <td>
                  <?= $aluno['tipo_exercicio'] !== '-' 
                        ? htmlspecialchars($aluno['tipo_exercicio']) 
                        : '<span class="text-muted-italic">—</span>'; ?>
                </td>

                <td>
                  <?= $aluno['tempo_minutos'] !== null 
                        ? (int)$aluno['tempo_minutos'] 
                        : '<span class="text-muted-italic">—</span>'; ?>
                </td>

                <td>
                  <?= $aluno['data_registro'] 
                        ? htmlspecialchars($aluno['data_registro']) 
                        : '<span class="text-muted-italic">—</span>'; ?>
                </td>

                <td>
                  <a href="editar_aluno.php?id=<?= $aluno['id']; ?>" class="btn btn-sm btn-warning me-1">Editar</a>
                  <a href="excluir_aluno.php?id=<?= $aluno['id']; ?>" 
                     class="btn btn-sm btn-danger"
                     onclick="return confirm('Tem certeza que deseja excluir este aluno?')">Excluir</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-info">Nenhum aluno encontrado.</div>
    <?php endif; ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>