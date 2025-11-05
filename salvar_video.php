<?php
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data["video"])) {
  http_response_code(400);
  echo "Nenhum vídeo recebido.";
  exit;
}

$videoData = base64_decode($data["video"]);
$nomeArquivo = "video_" . date("Ymd_His") . ".webm";
$caminho = __DIR__ . "/uploads/" . $nomeArquivo;

if (!is_dir(__DIR__ . "/uploads")) {
  mkdir(__DIR__ . "/uploads", 0777, true);
}

file_put_contents($caminho, $videoData);

echo "Vídeo salvo em: uploads/$nomeArquivo";