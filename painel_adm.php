<?php
include "sessao.php"; // Inclui o arquivo que controla a sessão do usuário (login, tipo, etc.)

// 🔒 Verifica se o usuário logado é administrador
if ($_SESSION['tipo'] !== 'admin') {
    // Se for aluno, redireciona para o painel do aluno
    header("Location: painel_aluno.php");
    exit;
}

try {
    // Cria a conexão com o banco de dados MySQL
    $pdo = new PDO("mysql:host=localhost;dbname=elastic_db;charset=utf8", "root", "941957");
    // Configura para lançar erros como exceções (facilita o tratamento de erros)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Se der erro na conexão, encerra o script e mostra a mensagem
    die("Erro na conexão: " . $e->getMessage());
}

// 🔍 Configuração da busca por nome ou email
$busca = "";     // Texto digitado na busca
$params = [];    // Parâmetros para a consulta SQL

// Se o usuário digitou algo no campo de busca
if (isset($_GET['busca']) && trim($_GET['busca']) !== "") {
    $busca = trim($_GET['busca']); // Remove espaços extras
    // Adiciona os parâmetros para usar no SQL com LIKE
    $params = ["%$busca%", "%$busca%"];
}

// 🧩 Consulta alunos + último exercício de cada um (usando subconsulta)
$sql = "
    SELECT 
        u.id,
        u.nome,
        u.email,
        COALESCE(t.tipo_exercicio, '-') AS tipo_exercicio,  -- Se não tiver exercício, mostra '-'
        t.tempo_minutos,
        t.data_registro
    FROM usuarios u
    LEFT JOIN (  -- Junta a tabela de exercícios (se existir)
        SELECT 
            e1.usuario_id, 
            e1.tipo_exercicio, 
            e1.tempo_minutos, 
            e1.data_registro
        FROM tempos_exercicios e1
        INNER JOIN (
            SELECT usuario_id, MAX(data_registro) AS max_data  -- Pega o exercício mais recente
            FROM tempos_exercicios
            GROUP BY usuario_id
        ) e2 
        ON e1.usuario_id = e2.usuario_id 
        AND e1.data_registro = e2.max_data
    ) t ON u.id = t.usuario_id
    WHERE u.tipo = 'comum'  -- Mostra apenas usuários do tipo 'comum' (alunos)
";

// Se o campo de busca tiver algo, adiciona a condição de filtro
if ($busca !== "") {
    $sql .= " AND (u.nome LIKE ? OR u.email LIKE ?)";
}

// Ordena os resultados pelo nome
$sql .= " ORDER BY u.nome";

// Prepara e executa a consulta no banco
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Pega todos os resultados em formato de array associativo
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Painel Admin — Elastic Go</title>
  <!-- Importa o Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="style.css">
  <link rel="icon" type="image/png" href="./imagens/elasticos.jpeg">
  <style>
    /* Estilo para texto cinza e em itálico (quando não há dados) */
    .text-muted-italic {
        color: #6c757d;
        font-style: italic;
    }
  </style>
</head>
<body class="bg-body-tertiary">

  <!-- 🔝 Barra superior -->
  <nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h1">Elastic Go - Painel Admin</span>
      <div class="d-flex">
        <!-- Mostra o nome do usuário logado -->
        <span class="navbar-text me-3">Olá, <?= htmlspecialchars($_SESSION['usuario']); ?></span>
        <!-- Botão para sair -->
        <a href="sair.php" class="btn btn-outline-light">Sair</a>
      </div>
    </div>
  </nav>

  <!-- Conteúdo principal -->
  <main class="container py-4">
    <h3 class="mb-4">Lista de Alunos e Últimos Exercícios</h3>

    <!-- 🔍 Formulário de busca -->
    <form class="row g-2 mb-4" method="GET">
      <div class="col-md-8">
        <input
          type="text"
          name="busca"
          class="form-control"
          placeholder="Buscar por nome ou email"
          value="<?= htmlspecialchars($busca); ?>"  <!-- Mantém o texto da busca -->
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-primary w-100">Buscar</button>
      </div>
    </form>

    <!-- 🧾 Tabela de alunos -->
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
            <!-- Loop que mostra cada aluno -->
            <?php foreach ($alunos as $aluno): ?>
              <tr>
                <td><?= htmlspecialchars($aluno['id']); ?></td>
                <td><?= htmlspecialchars($aluno['nome']); ?></td>
                <td><?= htmlspecialchars($aluno['email']); ?></td>

                <!-- Último tipo de exercício (ou traço se não tiver) -->
                <td>
                  <?= $aluno['tipo_exercicio'] !== '-' 
                        ? htmlspecialchars($aluno['tipo_exercicio']) 
                        : '<span class="text-muted-italic">—</span>'; ?>
                </td>

                <!-- Tempo gasto (ou traço) -->
                <td>
                  <?= $aluno['tempo_minutos'] !== null 
                        ? (int)$aluno['tempo_minutos'] 
                        : '<span class="text-muted-italic">—</span>'; ?>
                </td>

                <!-- Data do último exercício (ou traço) -->
                <td>
                  <?= $aluno['data_registro'] 
                        ? htmlspecialchars($aluno['data_registro']) 
                        : '<span class="text-muted-italic">—</span>'; ?>
                </td>

                <!-- Botões de ação -->
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
      <!-- Mensagem se nenhum aluno for encontrado -->
      <div class="alert alert-info">Nenhum aluno encontrado.</div>
    <?php endif; ?>
  </main>

  <!-- Scripts do Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>