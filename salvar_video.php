<?php
session_start(); // 🔐 Garante que só usuários logados podem enviar vídeos

// ⚠️ 1. Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
  http_response_code(403);
  die("Acesso negado. É necessário estar logado para enviar vídeos.");
}

// ⚙️ 2. Lê os dados JSON enviados pelo fetch()
$data = json_decode(file_get_contents("php://input"), true);

// 🔍 3. Verifica se o vídeo foi realmente enviado
if (!isset($data["video"])) {
  http_response_code(400);
  echo "Nenhum vídeo recebido.";
  exit;
}

// 🧠 4. Verifica um token de segurança enviado pelo JavaScript
// (evita que pessoas externas façam requisições falsas)
if (!isset($data["token"]) || $data["token"] !== $_SESSION['upload_token']) {
  http_response_code(403);
  die("Token inválido ou requisição não autorizada.");
}

// 🎞️ 5. Decodifica o vídeo Base64
$videoData = base64_decode($data["video"]);
if ($videoData === false) {
  http_response_code(400);
  die("Erro ao processar vídeo.");
}

// 🧱 6. (opcional) Limita tamanho máximo do vídeo, ex: 50 MB
if (strlen($videoData) > 50 * 1024 * 1024) {
  http_response_code(413);
  die("Arquivo muito grande.");
}

// 📂 7. Cria uma pasta específica por usuário
$usuario_id = (int)$_SESSION['id'];
$pastaUsuario = __DIR__ . "/uploads/user_" . $usuario_id;

if (!is_dir($pastaUsuario)) {
  mkdir($pastaUsuario, 0775, true);
}

// 🧾 8. Cria um nome único e seguro pro arquivo
$nomeArquivo = "video_" . date("Ymd_His") . ".webm";
$caminho = $pastaUsuario . "/" . $nomeArquivo;

// 💾 9. Salva o vídeo
file_put_contents($caminho, $videoData);

// 🧹 10. (opcional) remove o token para impedir reuso malicioso
unset($_SESSION['upload_token']);

echo "Vídeo salvo com sucesso em: uploads/user_$usuario_id/$nomeArquivo";
?>