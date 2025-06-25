<?php
session_start();

// Determinar o destino com base na sessão
$redirect_url = isset($_SESSION['usuario_id']) 
    ? "/Veterinaria/php/dashboard.php" 
    : "/Veterinaria/index.php";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Carregando</title>
  <link rel="stylesheet" href="/Veterinaria/css/formulario.css">
  <style>
    body {
      font-family: 'minecraft', sans-serif;
      margin: 0;
      padding: 0;
      height: 50vh;
      background-color: #111;
      color: white;
    }

    .container {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .logo {
      width: 600px;
      max-width: 90vw;
    }

    .carregar {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 50vh;
    }

    h1 {
      font-size: 2em;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <img src="/Veterinaria/images/logo.png" alt="Logo da Clínica Pet Vida" class="logo">
  </div>
  <div class="carregar">
    <h1 id="loadingText">Carregando</h1>
  </div>

  <script>
    const loadingText = document.getElementById("loadingText");
    let dotCount = 0;

    setInterval(() => {
      dotCount = (dotCount + 1) % 4;
      loadingText.textContent = "Carregando" + ".".repeat(dotCount);
    }, 500);

    // Redirecionamento automático após 3 segundos
    const redirectUrl = "<?php echo $redirect_url; ?>";
    setTimeout(() => {
      window.location.href = redirectUrl;
    }, 3000);
  </script>
</body>
</html>