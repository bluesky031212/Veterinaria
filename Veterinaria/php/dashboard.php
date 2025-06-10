<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /html/login.html");
    exit();
}

include 'conexao.php';

$usuarios_id = $_SESSION['usuario_id'];

$sql = "SELECT u.nome, u.email, an.tipo, an.nome FROM usuarios u JOIN animais an ON u.id = an.usuario_id WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuarios_id);
$stmt->execute();
$result = $stmt->get_result();
$usuarios = $result->fetch_assoc();
$conn->close();

$tipo = $usuarios['tipo'];

if ($tipo === 'Cachorro') {
    $imagemfixa = '/Veterinaria/images/CACHORRO PISCANDO.png';
    $imagemgif = '/Veterinaria/images/CACHORRO-PISCANDO.gif';
} elseif ($tipo === 'Gato') {
    $imagemfixa = '/Veterinaria/images/gato pisca.png';
    $imagemgif = '/Veterinaria/images/gato-pisca.gif';
} elseif ($tipo === 'Ave') {
    $imagemfixa = '/Veterinaria/images/galinha pisca.png';
    $imagemgif = '/Veterinaria/images/galinha-pisca.gif';
} else {
    $imagemfixa = '/Veterinaria/images/default.png';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Página do Cliente</title>
  <link rel="stylesheet" href="/Veterinaria/css/area_cliente.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      background-color: #f4f4f4;
      padding: 20px;
    }

    .img-animal img {
      width: 120px;
      cursor: pointer;
      margin: 20px 0;
    }

    .back-button, .agendar-button {
      display: inline-block;
      padding: 10px 20px;
      background-color: #007BFF;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      margin: 10px;
      cursor: pointer;
    }

    .back-button:hover, .agendar-button:hover {
      background-color: #0056b3;
    }

    footer {
      margin-top: 50px;
      text-align: center;
      font-size: 14px;
      color: #888;
    }

    .mensagem {
      font-size: 18px;
      margin-top: 10px;
    }

    #calendario-container {
      margin-top: 20px;
    }
  </style>
</head>

<body>
  <header>
    <h1>Bem-vindo, 
      <?php 
        $partes = explode(" ", trim($usuarios['nome']));
        echo htmlspecialchars($partes[0] . " " . end($partes)); 
      ?>
    </h1>
  </header>

  <div class="img-animal">
    <img id="animal" src="<?php echo $imagemfixa; ?>" alt="<?php echo $animais['tipo'] ?>">
  </div>

  <p class="mensagem">Deseja agendar uma nova marcação para <strong><?php echo htmlspecialchars($animais['nome']); ?></strong>?</p>

  <!-- Botão para mostrar/esconder o calendário -->
  <button id="agendar-btn" class="agendar-button">Agendar Consulta</button>

  <!-- Calendário oculto -->
  <div id="calendario-container" style="display: none;">
    <h2>Agenda aqui</h2>
    <h3>Escolha uma data:</h3>
    <input type="date" name="data-consulta">
    <br><br>
    <button>Confirmar Agendamento</button>
  </div>

  <a class="back-button" href="/Veterinaria/index.html">Voltar ao Início</a>

  <footer>
    © 2025 ClínicaGubrielvin - Todos os direitos reservados.
  </footer>

  <script>
    const animal = document.getElementById("animal");
    animal.addEventListener("mouseenter", () => {
      animal.src = "<?php echo $imagemgif; ?>?t=" + Date.now();
    });
    animal.addEventListener("mouseleave", () => {
      animal.src = "<?php echo $imagemfixa; ?>";
    });

    // Mostrar/esconder o calendário ao clicar no botão
    document.getElementById("agendar-btn").addEventListener("click", function() {
      const calendario = document.getElementById("calendario-container");
      calendario.style.display = calendario.style.display === "none" ? "block" : "none";
    });
  </script>
</body>

</html>
