<?php
session_start();
$nomeUsuario = $_SESSION['usuario'] ?? 'Aluno'; // Caso não tenha nome na sessão

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

// Token de upload único
if (!isset($_SESSION['upload_token'])) {
    $_SESSION['upload_token'] = bin2hex(random_bytes(16));
}
$uploadToken = $_SESSION['upload_token'];
?>

<?php include "sessao.php"; ?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Exercício 3 - Exercício Lateral</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="./imagens/elasticos.jpeg">


<!-- 🧠 TensorFlow e MoveNet -->
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.21.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/pose-detection"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-backend-webgl"></script>

<style>
body { background-color: #f8f9fa; }

/* === Layout principal === */
.main-container {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  gap: 40px;
  flex-wrap: wrap;
}

/* === Cabeçalho do exercício === */
.header-exercicio {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 30px;
  flex-wrap: wrap;
}

#cronometro {
  font-size: 1.5rem;
  color: #0d6efd;
  font-weight: bold;
}

/* === Sessão de vídeo === */
.video-section {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  gap: 250px;
  flex-wrap: nowrap;
  position: relative;
}

/* === Vídeo e Canvas === */
.video-box {
  position: relative;
  margin-top: 50px;
  width: 480px;
  height: 360px;
}
/* 🔥 Colocar o texto "Sua Câmera" por cima do vídeo */
.video-box .label {
  position: absolute;
  top: -45px;
  left: 50%;
  transform: translateX(-50%);
  font-weight: bold;
  font-size: 1.1rem;
  color: #0d6efd;
  z-index: 3;
}

video {
  position: absolute;
  top: 0;
  left: 0;
  width: 700px;
  height: 730px;
  z-index: 1;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
  object-fit: cover;
}

canvas {
  position: absolute;
  top: 0;
  left: 0;
  width: 700px;
  height: 700px;
  z-index: 2;
  pointer-events: none;
  opacity: 1.0;    /* 🔥 Aqui mistura a IA com o vídeo! */
}

.label {
  font-weight: bold;
  color: #0d6efd;
  margin-top: 10px;
}

/* === GIF de Demonstração === */
.demo-box {
  text-align: center;
}

.demo-box img {
  width: 900px;
  height: 730px;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
  object-fit: cover;
}

/* === Linha de feedback === */
#linha-feedback {
  position: absolute;
  top: 230px;
  left: 0;
  width: 490px;
  height: 5px;
  background-color: transparent;
  border-radius: 5px;
  transition: background-color 0.3s ease;
  pointer-events: none;
}

/* === Painel de botões à direita === */
.botoes-laterais {
  display: flex;
  flex-direction: column;
  align-items: stretch;
  justify-content: center;
  gap: 15px;
  margin-left: 30px;
}

.botoes-laterais button {
  font-size: 1.1rem;
  padding: 12px;
  width: 180px;
}

/* === Responsividade === */
@media (max-width: 992px) {
  .main-container { flex-direction: column; align-items: center; }
  .video-section { flex-direction: column; gap: 20px; }
  .botoes-laterais { flex-direction: row; flex-wrap: wrap; justify-content: center; margin-left: 0; }
  .header-exercicio { flex-direction: column; gap: 10px; }
  video, canvas, .demo-box img { width: 100%; max-width: 320px; height: auto; }
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
  
  <!-- 🏋️ Cabeçalho com cronômetro -->
  <div class="header-exercicio mb-4">
    <h1 class="mb-0">🏋️ Exercício: Alogamento Lateral</h1>
    <span id="cronometro">Tempo: 0s</span>
  </div>

  <div class="main-container">
    <!-- 🎥 Sessão de vídeo -->
    <div class="video-section">
      <!-- 🎥 Câmera -->
      <div class="video-box">
        <video id="video" autoplay playsinline muted></video>
        <canvas id="canvas" width="480" height="360"></canvas>
        <div id="linha-feedback"></div>
        <p class="label">Sua Câmera</p>
      </div>

      <!-- 💡 Demonstração -->
      <div class="demo-box">
        <p class="label">Demonstração</p>
        <img src="https://www.bing.com/th/id/OGC.a598d4fc4118f5ea0cb49e2c9a34524d?o=7&pid=1.7&rm=3&rurl=https%3a%2f%2fi.gifer.com%2forigin%2fb0%2fb058fb5bbc350f862ce65b7dc48ab474.gif&ehk=5P%2bDYptmYA1g4OE8k%2f93pZ3VMTsKhC3aOntSOJo4mt0%3d" alt="Demonstração do exercício com bola">
      </div>

      <!-- 🟩 Botões à direita -->
      <div class="botoes-laterais">
        <button id="iniciarExercicio" class="btn btn-success">▶ Iniciar</button>
        <button id="pararExercicio" class="btn btn-danger" disabled>⏹ Parar</button>
        <form method="POST" action="resultado.php" id="formExercicio">
          <input type="hidden" name="exercicio" value="Exercício com a Bola">
          <input type="hidden" name="tempo_segundos" id="tempoInput">
          <input type="hidden" name="video_arquivo" id="videoInput">
          <button type="submit" id="registrar" disabled class="btn btn-primary">💾 Registrar</button>
        </form>
        <button id="sairExercicio" class="btn btn-secondary" onclick="window.location.href='painel_aluno.php'">🚪 Sair</button>
      </div>
    </div>
  </div>

  <div id="resultado" class="mt-4 text-success fw-bold"></div>
</div>

<script>
let detector, stream, mediaRecorder;
let tempo = 0, intervalo = null, chunks = [];

const video = document.getElementById("video");
const canvas = document.getElementById("canvas");
const ctx = canvas.getContext("2d");
const linha = document.getElementById("linha-feedback");
const iniciarBtn = document.getElementById("iniciarExercicio");
const pararBtn = document.getElementById("pararExercicio");
const registrarBtn = document.getElementById("registrar");
const cronometro = document.getElementById("cronometro");
const resultado = document.getElementById("resultado");
const tempoInput = document.getElementById("tempoInput");
const videoInput = document.getElementById("videoInput");
const nomeUsuario = "<?= $nomeUsuario ?>"; // 🧾 Nome do usuário vindo da sessão PHP

// 🔊 Fala
function falar(texto) {
  const fala = new SpeechSynthesisUtterance(texto);
  fala.lang = "pt-BR";
  fala.rate = 0.95;
  window.speechSynthesis.speak(fala);
}

// 📹 Câmera
async function iniciarCamera() {
  try {
    stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
  } catch (err) {
    alert("Erro ao acessar a câmera: " + err.message);
  }
}

// ⏱️ Cronômetro
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

// 🤖 IA MoveNet
async function carregarModelo() {
  detector = await poseDetection.createDetector(
    poseDetection.SupportedModels.MoveNet,
    { modelType: "SinglePose.Lightning" }
  );
  console.log("🤖 Modelo MoveNet carregado!");
}

// 📌 Lógica do movimento lateral (DIREITA / ESQUERDA)
let movimentoAnterior = ""; // para evitar repetir fala

// === Ligações do esqueleto (CARTOON COLORIDO) ===
const bones = [
  // cabeça
  { a: 0, b: 1, color: "#ffcc00", width: 6 },
  { a: 1, b: 3, color: "#ffcc00", width: 6 },

  // braço esquerdo
  { a: 5, b: 7, color: "#ff6600", width: 6 },
  { a: 7, b: 9, color: "#ff6600", width: 6 },

  // braço direito
  { a: 6, b: 8, color: "#0099ff", width: 6 },
  { a: 8, b: 10, color: "#0099ff", width: 6 },

  // tronco
  { a: 5, b: 6, color: "#00cc66", width: 8 },
  { a: 5, b: 11, color: "#00cc66", width: 7 },
  { a: 6, b: 12, color: "#00cc66", width: 7 },

  // quadril
  { a: 11, b: 12, color: "#6633cc", width: 10 },

  // perna esquerda
  { a: 11, b: 13, color: "#cc0099", width: 7 },
  { a: 13, b: 15, color: "#cc0099", width: 6 },

  // perna direita
  { a: 12, b: 14, color: "#cc3300", width: 7 },
  { a: 14, b: 16, color: "#cc3300", width: 6 }
];

// === CABEÇA CARTOON ===
function desenharCabeca(p0, p1) {
  const centroX = (p0.x + p1.x) / 2;
  const centroY = p0.y - (p1.y - p0.y) * 0.8;
  const raio = Math.abs(p1.y - p0.y) * 0.9;

  ctx.beginPath();
  ctx.arc(centroX, centroY, raio, 0, 2 * Math.PI);
  ctx.fillStyle = "#ffe680";    
  ctx.strokeStyle = "#ffcc00";
  ctx.lineWidth = 5;
  ctx.fill();
  ctx.stroke();
}

// =============================
// FINAL — ANÁLISE DA POSTURA
// =============================
async function analisarPostura() {
  if (!detector) return;

  const poses = await detector.estimatePoses(video);
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  if (poses.length > 0) {
    const pose = poses[0].keypoints;

    // === CABEÇA CARTOON
    if (pose[0].score > 0.5 && pose[1].score > 0.5) {
      desenharCabeca(pose[0], pose[1]);
    }

    // === DESENHO CARTOON DOS OSSOS (LINHAS COLORIDAS)
    bones.forEach(({ a, b, color, width }) => {
      const p1 = pose[a];
      const p2 = pose[b];

      if (p1.score > 0.5 && p2.score > 0.5) {
        ctx.beginPath();
        ctx.moveTo(p1.x, p1.y);
        ctx.lineTo(p2.x, p2.y);
        ctx.strokeStyle = color;
        ctx.lineWidth = width;
        ctx.lineCap = "round";
        ctx.stroke();
      }
    });

    // === LÓGICA DE MOVIMENTO LATERAL (SEU CÓDIGO MANTIDO!)
    const maoDir = pose[10];
    const maoEsq = pose[9];
    const ombroDir = pose[6];
    const ombroEsq = pose[5];

    if (maoDir && maoEsq && ombroDir && ombroEsq) {
      const dxDir = maoDir.x - ombroDir.x;
      const dxEsq = ombroEsq.x - maoEsq.x;

      if (dxDir > 80) {
        linha.style.backgroundColor = "blue";
        if (movimentoAnterior !== "direita") {
          falar("Muito bem! Movimentou para a direita!");
          movimentoAnterior = "direita";
        }
      } else if (dxEsq > 80) {
        linha.style.backgroundColor = "green";
        if (movimentoAnterior !== "esquerda") {
          falar("Excelente! Movimentou para a esquerda!");
          movimentoAnterior = "esquerda";
        }
      } else {
        linha.style.backgroundColor = "red";
        if (movimentoAnterior !== "errado") {
          falar("Mexa mais os braços para os lados.");
          movimentoAnterior = "errado";
        }
      }
    }
  }

  if (stream) requestAnimationFrame(analisarPostura);
}

// ▶ Iniciar
iniciarBtn.onclick = async () => {
  await iniciarCamera();
  await carregarModelo();
  iniciarCronometro();

  iniciarBtn.disabled = true;
  pararBtn.disabled = false;
  registrarBtn.disabled = true;
  resultado.textContent = "🏋️ Exercício iniciado...";

  falar("Bem-Vindo ao exercício, antes de começar siga as orientações para ter um ótimo desempenho, primeiro ajuste a câmera para aparecer a cabeça até o joelho. Dois fique visivel de frente para câmera com uma distância de 2 a 4 passos. Você tem 10 segundos para se preparar, contando. um. dois. três. quatro. cinco. seis. sete. oito. nove. dez. Iniciando Alogamento Lateral. Mova os braços para os lados como na demonstração.");
  analisarPostura();

  mediaRecorder = new MediaRecorder(stream, { mimeType: "video/webm" });
  chunks = [];
  mediaRecorder.ondataavailable = (e) => { if (e.data.size > 0) chunks.push(e.data); };
  mediaRecorder.start();
};

// ⏹ Parar
pararBtn.onclick = () => {
  pararCronometro();
  window.speechSynthesis.cancel();
  pararBtn.disabled = true;
  iniciarBtn.disabled = false;

  falar(`Exercício finalizado. Parabéns pelo ótimo trabalho, ${nomeUsuario}! Vejo você no nosso próximo encontro!`);
  linha.style.backgroundColor = "transparent";

  mediaRecorder.stop();
  stream.getTracks().forEach((t) => t.stop());
  stream = null;

  resultado.textContent = `✅ Exercício finalizado! Duração: ${tempo}s`;

  mediaRecorder.onstop = async () => {
    const blob = new Blob(chunks, { type: "video/webm" });
    const reader = new FileReader();
    reader.onloadend = async () => {
      const base64Video = reader.result.split(",")[1];
      videoInput.value = base64Video;
      tempoInput.value = tempo;
      registrarBtn.disabled = false;

      await fetch("salvar_video.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          video: base64Video,
          token: "<?= $uploadToken ?>"
        })
      });
    };
    reader.readAsDataURL(blob);
  };
};
</script>
</body>
</html>