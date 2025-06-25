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
<body>
  <div class="container">

    <?php if (!empty($msgErro)) : ?>
      <div class="erro-login" style="color: red; margin-bottom: 15px;">
        <?php echo htmlspecialchars($msgErro); ?>
      </div>
    <?php endif; ?>

    <h2>Já é cliente?</h2>
    <form action="/Veterinaria/php/login.php" method="POST">
      <input type="email" name="email" placeholder="seuemail@exemplo.com" required>
      <input type="password" name="senha" placeholder="Digite sua senha" required>
      <button class="btn" type="submit">Entrar</button>
    </form>

    <a href="/Veterinaria/html/cadastro.html" class="link">Não é cliente? Cadastre-se aqui</a>
  </div>
</body>
</html>
