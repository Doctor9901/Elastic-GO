<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Elastic GO - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css" />
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="w-100 m-auto form-container">
        <form method="post" action="login.php">

            <!-- Título -->
            <h1 class="h1 mb-3 fw-bold text-center elastic-go-title">ELASTIC GO</h1>
            <h1 class="h3 mb-3 fw-normal">Por favor, faça login</h2>

            <!-- Mensagem de erro (se houver) -->
            <?php if (isset($_SESSION['erro'])): ?>
                <div class="alert alert-danger text-center">
                    <?= $_SESSION['erro']; unset($_SESSION['erro']); ?>
                </div>
            <?php endif; ?>

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

            <!-- Checkbox lembrar-me -->
            <div class="form-check text-start mb-3">
                <input type="checkbox" class="form-check-input" id="rememberMeCheck">
                <label class="form-check-label" for="rememberMeCheck">Lembrar-me</label>
            </div>

            <!-- Botão Entrar -->
            <button class="btn btn-primary w-100 py-2" type="submit">Entrar</button>

            <!-- Link para cadastro -->
            <div class="text-center mt-3">
                <span>Não tem uma conta?</span>
                <a href="cadastro.php" class="text-primary">Cadastre-se</a>
            </div>
        </form>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>