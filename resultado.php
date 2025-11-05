<?php
session_start();

if (!isset($_SESSION['id'])) {
    die("Usuário não autenticado. Faça login antes de enviar resultados.");
}

if (!isset($_POST['exercicio'], $_POST['tempo_segundos'])) {
    die("Dados do exercício incompletos.");
}

$usuario_id = (int)$_SESSION['id'];
$exercicio = trim($_POST['exercicio']);
$tempo_segundos = (int)$_POST['tempo_segundos'];
$data_registro = date('Y-m-d H:i:s');
$nivel = "iniciante"; // Ajuste conforme a lógica do seu sistema

if ($tempo_segundos <= 0) {
    // Mensagem amigável para tempo inválido
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Quase Finalizado</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    </head>
    <body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="text-center bg-white p-5 rounded shadow" style="max-width: 400px;">
            <h1 class="text-warning mb-4">Quase finalizado!</h1>
            <p class="fs-5">Você não completou o exercício, mas está quase lá. Continue tentando!</p>
            <a href="beta1.php" class="btn btn-primary mt-3">Voltar e tentar novamente</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=elastic_db;charset=utf8mb4", "root", "941957");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO tempos_exercicios (usuario_id, tipo_exercicio, tempo_minutos, data_registro)
            VALUES (:usuario_id, :tipo_exercicio, :tempo_minutos, :data_registro)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(':tipo_exercicio', $exercicio, PDO::PARAM_STR);
    $stmt->bindParam(':tempo_minutos', $tempo_segundos, PDO::PARAM_INT); // Se o banco é minutos, ajuste se necessário
    $stmt->bindParam(':data_registro', $data_registro, PDO::PARAM_STR);
    $stmt->execute();

    // Buscar nome do usuário para exibir na tela
    $sqlUser = "SELECT nome FROM usuarios WHERE id = :usuario_id LIMIT 1";
    $stmtUser = $pdo->prepare($sqlUser);
    $stmtUser->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmtUser->execute();
    $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);
    $nome_usuario = $usuario ? $usuario['nome'] : "Usuário desconhecido";

} catch (PDOException $e) {
    die("Erro ao salvar resultado no banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Resultado do Exercício</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="text-center bg-white p-5 rounded shadow" style="max-width: 400px;">
        <h1 class="text-success mb-4">Parabéns!</h1>
        <p class="fs-5">
            Parabéns, <strong><?= htmlspecialchars($nome_usuario); ?></strong>! Você concluiu a tarefa de <strong><?= htmlspecialchars($exercicio); ?></strong> em <strong><?= $tempo_segundos; ?> segundos</strong>.
        </p>
        <a href="painel_aluno.php" class="btn btn-primary mt-3">Voltar ao Painel</a>
    </div>
</body>
</html>