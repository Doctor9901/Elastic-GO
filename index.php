<?php session_start(); ?>  
<!-- Inicia a sessão PHP (necessário para usar $_SESSION e mostrar erros) -->

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">  
<!-- Define o idioma da página e ativa o tema escuro do Bootstrap -->

<head>
    <meta charset="UTF-8" />
    <!-- Define o padrão de caracteres (suporta acentos corretamente) -->

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Torna a página responsiva em celulares -->

    <title>Elastic GO - Login</title>
    <!-- Título da aba do navegador -->

    <!-- Importação do Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="styles.css" />

    <!-- Ícone da aba -->
    <link rel="icon" type="image/png" href="./imagens/elasticos.jpeg" />
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <!-- Centraliza tudo verticalmente com fundo padrão do Bootstrap -->

    <main class="w-100 m-auto form-container">
        <!-- Container central do formulário -->

        <form method="post" action="login.php">
            <!-- Envia os dados para login.php via POST -->

            <!-- Logo -->
            <h1 class="elastic-go-title">
                <img src="imagens/elastic.jpeg" style="width: 320px; height: 300px;">
            </h1>

            <h2 class="h3 mb-3 fw-normal">Por favor, faça login</h2>

            <!-- Mensagem de erro -->
            <?php if (isset($_SESSION['erro'])): ?>
                <div class="alert alert-danger text-center">
                    <?= $_SESSION['erro']; unset($_SESSION['erro']); ?>
                </div>
            <?php endif; ?>
            <!-- Se houver erro salvo na sessão, ele é exibido aqui -->

            <!-- Campo de e-mail -->
            <div class="form-floating mb-3">
                <input type="email" class="form-control" name="email" id="floatingInputEmail" placeholder="SEU-EMAIL@GMAIL.COM" required>
                <label for="floatingInputEmail">E-mail</label>
            </div>

            <!-- Campo de senha -->
            <div class="form-floating mb-3">
                <input type="password" class="form-control" name="senha" id="floatingInputPassword" placeholder="SENHA" required>
                <label for="floatingInputPassword">Senha</label>
            </div>

            <!-- Checkbox lembrar-me (não salva de verdade por enquanto) -->
            <div class="form-check text-start mb-3">
                <input type="checkbox" class="form-check-input" id="rememberMeCheck">
                <label class="form-check-label" for="rememberMeCheck">Lembrar-me</label>
            </div>

            <!-- Botão de login -->
            <button class="btn btn-primary w-100 py-2" type="submit">Entrar</button>

            <!-- Link para cadastro -->
            <div class="text-center mt-3">
                <span>Não tem uma conta?</span>
                <a href="cadastro.php" class="text-primary">Cadastre-se</a>
            </div>
        </form>
    </main>

    <!-- Script do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>