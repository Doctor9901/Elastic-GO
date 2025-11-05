<?php include "sessao.php"; ?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Meus Vídeos de Exercícios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    video {
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      width: 100%;
      height: auto;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-light bg-white shadow-sm">
  <div class="container">
    <a href="painel_aluno.php" class="navbar-brand text-primary fw-bold">Voltar ao Início</a>
    <span class="navbar-text text-secondary fw-semibold">📹 Meus Vídeos Gravados</span>
  </div>
</nav>

<div class="container py-5">
  <h1 class="text-center mb-4 text-primary">Histórico de Exercícios Gravados</h1>

  <div class="row g-4">
  <?php
    // 🟩 PASTA ONDE OS VÍDEOS SÃO SALVOS
    $dir = "uploads/";

    if (!is_dir($dir)) {
      echo "<p class='text-center text-danger'>Nenhum vídeo encontrado. Realize um exercício para gravar!</p>";
    } else {
      // Lista os arquivos em ordem decrescente (mais recente primeiro)
      $arquivos = array_diff(scandir($dir, SCANDIR_SORT_DESCENDING), ['.', '..']);
      $temVideo = false;

      foreach ($arquivos as $arquivo) {
        $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));

        // Exibe apenas arquivos de vídeo
        if (in_array($extensao, ['mp4', 'webm', 'mov', 'avi'])) {
          $temVideo = true;
          $caminho = $dir . $arquivo;
          $data = date("d/m/Y H:i:s", filemtime($caminho));

          echo "
          <div class='col-md-4'>
            <div class='card shadow-sm'>
              <div class='card-body text-center'>
                <video controls>
                  <source src='$caminho' type='video/mp4'>
                  Seu navegador não suporta o formato de vídeo.
                </video>
                <h6 class='text-primary fw-bold mt-2'>Gravado em: $data</h6>
                <a href='$caminho' download class='btn btn-outline-primary btn-sm mt-2'>Baixar vídeo</a>
              </div>
            </div>
          </div>";
        }
      }

      if (!$temVideo) {
        echo "<p class='text-center text-warning'>Nenhum vídeo gravado até o momento.</p>";
      }
    }
  ?>
  </div>
</div>
</body>
</html>