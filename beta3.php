<?php include "sessao.php"; ?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Exercício 3 - Exercício com a Bola</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }

    video, img {
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      width: 480px;
      height: 360px;
      object-fit: cover;
    }

    .video-section {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
      gap: 30px;
      margin-bottom: 30px;
    }

    .video-section div {
      text-align: center;
    }

    .label {
      font-weight: bold;
      color: #0d6efd;
      margin-top: 10px;
    }

    @media (max-width: 768px) {
      video, img {
        width: 100%;
        max-width: 320px;
        height: auto;
      }
    }
  </style>
</head>
<body>
<nav class="navbar navbar-light bg-white shadow-sm">
  <div class="container">
    <a href="painel_aluno.php" class="navbar-brand text-primary fw-bold">Voltar ao Início</a>
  </div>
</nav>

<div class="container py-5 text-center">
  <h1 class="mb-4">Exercício: Exercício com a Bola</h1>

  <!-- GIF e câmera lado a lado -->
  <div class="video-section">
    <!-- Demonstração -->
    <div>
      <img src="https://blog.bodytech.com.br/content/arquivos/blog/Bola-exercicio-corrida.gif" 
           alt="Demonstração de Exercício com Elástico" class="border">
      <p class="label">Demonstração</p>
    </div>

    <!-- Câmera -->
    <div>
      <video id="video" autoplay playsinline muted class="border"></video>
      <p class="label">Sua Câmera</p>
    </div>
  </div>

  <!-- Cronômetro -->
  <p id="cronometro" class="fs-4 text-primary fw-bold">Tempo: 0s</p>

  <!-- Botões -->
  <div class="d-flex justify-content-center gap-2 mb-4">
    <button id="iniciarExercicio" class="btn btn-success">Iniciar Exercício</button>
    <button id="pararExercicio" class="btn btn-danger" disabled>Parar Exercício</button>
  </div>

  <!-- Resultado -->
  <div id="resultado" class="mt-3 text-success fw-bold"></div>

  <!-- Formulário -->
  <form method="POST" action="resultado.php" id="formExercicio" class="mt-4">
    <input type="hidden" name="exercicio" value="Elástico">
    <input type="hidden" name="tempo_segundos" id="tempoInput">
    <input type="hidden" name="video_arquivo" id="videoInput">
    <button type="submit" id="registrar" disabled class="btn btn-primary">Registrar Resultado</button>
  </form>
</div>

<script>
let stream = null;
let mediaRecorder = null;
let chunks = [];
let tempo = 0;
let intervalo = null;

// Elementos
const video = document.getElementById("video");
const iniciarBtn = document.getElementById("iniciarExercicio");
const pararBtn = document.getElementById("pararExercicio");
const registrarBtn = document.getElementById("registrar");
const tempoInput = document.getElementById("tempoInput");
const videoInput = document.getElementById("videoInput");
const resultado = document.getElementById("resultado");
const cronometro = document.getElementById("cronometro");

// ====== INICIAR CÂMERA ======
async function iniciarCamera() {
  try {
    stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
    video.srcObject = stream;
  } catch (err) {
    alert("Erro ao acessar a câmera: " + err.message);
  }
}

// ====== CRONÔMETRO ======
function iniciarCronometro() {
  tempo = 0;
  cronometro.textContent = "Tempo: 0s";
  intervalo = setInterval(() => {
    tempo++;
    cronometro.textContent = `Tempo: ${tempo}s`;
  }, 1000);
}

function pararCronometro() {
  clearInterval(intervalo);
  intervalo = null;
}

// ====== INICIAR EXERCÍCIO ======
iniciarBtn.onclick = async () => {
  await iniciarCamera();
  iniciarBtn.disabled = true;
  pararBtn.disabled = false;
  resultado.textContent = "💪 Exercício iniciado, gravando...";

  iniciarCronometro();

  mediaRecorder = new MediaRecorder(stream, { mimeType: "video/webm" });
  chunks = [];

  mediaRecorder.ondataavailable = (event) => {
    if (event.data.size > 0) chunks.push(event.data);
  };

  mediaRecorder.onstop = async () => {
    const blob = new Blob(chunks, { type: "video/webm" });
    const videoURL = URL.createObjectURL(blob);
    resultado.innerHTML = `<p>✅ Exercício finalizado! Duração: ${tempo}s</p>
                           <video controls width="400" class="rounded mt-3 border"><source src="${videoURL}" type="video/webm"></video>`;

    const reader = new FileReader();
    reader.onloadend = async () => {
      const base64Video = reader.result.split(',')[1];
      videoInput.value = base64Video;
      tempoInput.value = tempo;
      registrarBtn.disabled = false;

      // Envia o vídeo para o servidor
      await fetch("salvar_video.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ video: base64Video })
      });
    };
    reader.readAsDataURL(blob);
  };

  mediaRecorder.start();
};

// ====== PARAR EXERCÍCIO ======
pararBtn.onclick = () => {
  pararCronometro();
  pararBtn.disabled = true;
  iniciarBtn.disabled = false;
  mediaRecorder.stop();
  stream.getTracks().forEach(track => track.stop());
  stream = null;
};
</script>
</body>
</html>