<?php
session_start();
$msgErro = $_SESSION['login_erro'] ?? '';
unset($_SESSION['login_erro']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login do Cliente</title>
  <link rel="stylesheet" href="/Veterinaria/css/style.css" />
</head>

<style>
      .alert {
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
      font-size: 20px;
    }
    .alert-success {
      color: #37e25fff;
    }
    .alert-error {
      color: #721c24;
    }
    .alert-warning {
      color: #856404;
    }
    </style>
<body>
  <div class="container">

    <?php if (!empty($msgErro)) : ?>
      <div class="alert alert-error">
        <?php echo htmlspecialchars($msgErro); ?>
      </div>
    <?php endif; ?>

    <!-- Mensagens de cadastro -->
    <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
      <div class="alert alert-success">
        Cadastrado com sucesso!
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['erro']) && $_GET['erro'] == 1): ?>
      <div class="alert alert-error">
        Ocorreu um erro ao cadastrar. Tente novamente.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['erro']) && $_GET['erro'] == 2): ?>
      <div class="alert alert-warning">
        Este e-mail já está cadastrado.
      </div>
    <?php endif; ?>
    <!-- Fim das mensagens -->

    <h2>Já é cliente?</h2>
    <form action="/Veterinaria/php/login.php" method="POST">
      <input type="email" name="email" placeholder="seuemail@exemplo.com" required>
      <input type="password" name="senha" placeholder="Digite sua senha" required>
      <button class="btn" type="submit">Entrar</button>
    </form>

    <a href="/Veterinaria/html/cadastro.html" class="link">Não é cliente? Cadastre-se aqui</a>
    <a href="/Veterinaria/html/:p.html" class="link">Esqueceu a senha?</a>
  </div>
</body>
</html>
