<?php
// 🔐 Inicia a sessão (necessário para poder destruí-la)
session_start();

// 🧹 Encerra a sessão atual e apaga todos os dados armazenados (como login)
session_destroy();

// ↩️ Redireciona o usuário de volta para a página inicial (index.php)
header("Location: index.php");

// 🚪 Garante que o script pare de rodar após o redirecionamento
exit;
?>