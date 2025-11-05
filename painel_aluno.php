<?php include "sessao.php"; ?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área do Aluno - Elastic GO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-white shadow-sm fixed-top">
    <div class="container">
      <a class="navbar-brand fw-bold text-primary" href="painel_aluno.php">Elastic GO</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="menu">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link active" href="painel_aluno.php">Início</a></li>
          <li class="nav-item"><a class="nav-link" href="sobre.html">Sobre</a></li>
          <li class="nav-item"><a class="nav-link" href="contato.html">Contato</a></li>
          <li class="nav-item"><a class="nav-link" href="conta.html">Conta</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero d-flex justify-content-center align-items-center text-center">
    <div class="overlay"></div>
    <div class="container position-relative">
      <!--conteudo principal junto do php-->
      <div style="margin-top: 0px;" class="container text-center">
        <h2 class="mb-3 text-white">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']); ?>!</h2>
        <h1 class="display-4 fw-bold text-white">Treine com a Elastic GO</h1>
        <p class="lead text-white">Você acessou o painel do aluno.</p>
        <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
          <a href="exercicios.html" class="btn btn-success btn-lg shadow px-5"> <small>COMEÇAR</small></a>
          <a href="exercicio_video.php" class="btn btn-primary btn-lg shadow px-5">Exercícios Feitos</a>
          <a href="sair.php" class="btn btn-outline-danger btn-lg shadow px-5">Sair</a>
        </div>
      </div>  
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>