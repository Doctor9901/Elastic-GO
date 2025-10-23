<?php include "sessao.php"; ?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Exercício 2 - Alongamento</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-light bg-white shadow-sm">
  <div class="container">
    <a href="painel_aluno.php" class="navbar-brand text-primary fw-bold">Voltar ao Início</a>
  </div>
</nav>

<div class="container py-5 text-center">
  <h1>Alongamento</h1>
  <img src="https://tse1.explicit.bing.net/th/id/OIP.9Jy3hasTK22EN7IMhdNY6wHaHa?cb=12&rs=1&pid=ImgDetMain" class="img-fluid rounded mb-3" style="max-height:350px;object-fit:cover">

  <p id="cronometro" class="fs-4 text-primary fw-bold">Tempo: 0s</p>
  <button id="iniciar" class="btn btn-success me-2">Iniciar</button>
  <button id="parar" class="btn btn-danger">Parar</button>

  <form method="POST" action="resultado.php" id="formExercicio">
    <input type="hidden" name="exercicio" value="Alongamento">
    <input type="hidden" name="tempo_segundos" id="tempoInput">
    <button type="submit" id="registrar" class="btn btn-primary mt-3" disabled>Registrar Tempo</button>
  </form>

  <div id="msgAmigavel" class="mt-3 text-warning fw-bold" style="display:none;"></div>
</div>

<script>
let tempo = 0;
let intervalo = null;
const display = document.getElementById('cronometro');
const tempoInput = document.getElementById('tempoInput');
const registrarBtn = document.getElementById('registrar');
const msgAmigavel = document.getElementById('msgAmigavel');

document.getElementById('iniciar').onclick = () => {
  if (intervalo) return;
  intervalo = setInterval(() => {
    tempo++;
    display.textContent = `Tempo: ${tempo}s`;
  }, 1000);
  msgAmigavel.style.display = 'none';
  registrarBtn.disabled = true;
};

document.getElementById('parar').onclick = () => {
  if (!intervalo) return;
  clearInterval(intervalo);
  intervalo = null;
  tempoInput.value = tempo;
  registrarBtn.disabled = false;
};

document.getElementById('formExercicio').onsubmit = (e) => {
  if (!tempoInput.value || tempoInput.value <= 0) {
    e.preventDefault();
    msgAmigavel.textContent = 'Quase finalizado! Continue tentando para registrar seu tempo.';
    msgAmigavel.style.display = 'block';
  }
};
</script>
</body>
</html>