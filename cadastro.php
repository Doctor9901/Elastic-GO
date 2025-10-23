<?php
// cadastro.php
require_once 'usuarios.php';  // onde estão as funções cadastrarUsuario e buscarUsuarioPorEmail
require_once 'alunos.php';    // onde está cadastrarAluno

$mensagem = "";

if (isset($_POST['cadastrar'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $confirmar_email = $_POST['confirmar-email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar-senha'];
    $curso_nivel = $_POST['curso_nivel'] ?? null;

    // Valida confirmação de email e senha
    if ($email !== $confirmar_email) {
        $mensagem = "Os emails não coincidem.";
    } elseif ($senha !== $confirmar_senha) {
        $mensagem = "As senhas não coincidem.";
    } else {
        // Verifica se o email já existe
        if (buscarUsuarioPorEmail($email)) {
            $mensagem = "Este email já está cadastrado.";
        } else {
            // Cadastra usuário e pega o ID gerado
            $usuario_id = cadastrarUsuario($nome, $email, $senha);
            if ($usuario_id) {
                // Cadastra dados do aluno com o ID do usuário
                if (cadastrarAluno($usuario_id, $curso_nivel)) {
                    header("Location: login.php");
                    exit;
                } else {
                    $mensagem = "Erro ao cadastrar dados do aluno.";
                }
            } else {
                $mensagem = "Erro ao cadastrar usuário.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cadastro - Elastic GO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="styles.css" />
</head>
<body class="d-flex align-items-center py-4 bg-body-tertiary">
  <main class="w-100 m-auto form-container">
    <form method="POST">
      <h1 class="h1 mb-3 fw-bold text-center elastic-go-title">ELASTIC GO</h1>
      <h2 class="h3 mb-3 fw-normal text-center">Crie sua conta</h2>

      <!-- Exibe mensagem de erro -->
      <?php if ($mensagem): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($mensagem) ?></div>
      <?php endif; ?>

      <!-- Nome -->
      <div class="form-floating mb-3">
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome Completo" required value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '' ?>">
        <label for="nome">Nome Completo</label>
      </div>

      <!-- Email -->
      <div class="form-floating mb-3">
        <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        <label for="email">E-mail</label>
      </div>

      <!-- Confirmar Email -->
      <div class="form-floating mb-3">
        <input type="email" class="form-control" id="confirmar-email" name="confirmar-email" placeholder="Confirmar E-mail" required value="<?= isset($_POST['confirmar-email']) ? htmlspecialchars($_POST['confirmar-email']) : '' ?>">
        <label for="confirmar-email">Confirmar E-mail</label>
      </div>

      <!-- Senha -->
      <div class="form-floating mb-3 position-relative">
        <input type="password" class="form-control" id="senha" name="senha" placeholder="Senha" required>
        <label for="senha">Senha</label>
        <button type="button" class="btn btn-sm btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2" onclick="togglePasswordVisibility('senha', this)">👁️</button>
      </div>

      <!-- Confirmar Senha -->
      <div class="form-floating mb-3 position-relative">
        <input type="password" class="form-control" id="confirmar-senha" name="confirmar-senha" placeholder="Confirmar Senha" required>
        <label for="confirmar-senha">Confirmar Senha</label>
        <button type="button" class="btn btn-sm btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2" onclick="togglePasswordVisibility('confirmar-senha', this)">👁️</button>
      </div>

      <!-- Nível do Curso -->
      <div class="form-floating mb-3">
        <select class="form-select" id="curso_nivel" name="curso_nivel" required>
          <option value="" disabled <?= !isset($_POST['curso_nivel']) ? 'selected' : '' ?>>Selecione o nível do curso</option>
          <option value="básico" <?= (isset($_POST['curso_nivel']) && $_POST['curso_nivel'] === 'básico') ? 'selected' : '' ?>>Básico</option>
          <option value="intermediário" <?= (isset($_POST['curso_nivel']) && $_POST['curso_nivel'] === 'intermediário') ? 'selected' : '' ?>>Intermediário</option>
        </select>
        <label for="curso_nivel">Nível do Curso</label>
      </div>

      <!-- Botão Cadastrar -->
      <button type="submit" name="cadastrar" class="btn btn-primary w-100 py-2">Cadastrar</button>

      <!-- Link para login -->
      <div class="text-center mt-3">
        <span>Já tem uma conta?</span>
        <a href="login.php" class="text-primary">Faça login</a>
      </div>
    </form>
  </main>

  <script>
    function togglePasswordVisibility(inputId, button) {
      const input = document.getElementById(inputId);
      if (input.type === "password") {
        input.type = "text";
        button.textContent = "🙈";
      } else {
        input.type = "password";
        button.textContent = "👁️";
      }
    }
  </script>
</body>
</html>