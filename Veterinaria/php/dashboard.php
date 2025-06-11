<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.html");
    exit();
}

include 'conexao.php';

$usuarios_id = $_SESSION['usuario_id'];

$sql = "SELECT u.nome, u.email, an.tipo_animal, an.nome_animal FROM usuarios u JOIN animais an ON u.id = an.usuario_id WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuarios_id);
$stmt->execute();
$result = $stmt->get_result();
$usuarios = $result->fetch_assoc();
$conn->close();

$tipo = $usuarios['tipo_animal'];

if ($tipo === 'Cachorro') {
    $imagemfixa = '/Veterinaria/images/CACHORRO PISCANDO.png';
    $imagemgif = '/Veterinaria/images/CACHORRO-PISCANDO.gif';
} elseif ($tipo === 'Gato') {
    $imagemfixa = '/Veterinaria/images/gato pisca.png';
    $imagemgif = '/Veterinaria/images/gato-pisca.gif';
} elseif ($tipo === 'Ave') {
    $imagemfixa = '/Veterinaria/images/galinha pisca.png';
    $imagemgif = '/Veterinaria/images/galinha-pisca.gif';
} elseif ($tipo === 'Roedor') {
    $imagemfixa = '/Veterinaria/images/hamster-pisca.png';
    $imagemgif = '/Veterinaria/images/hamster-pisca.gif';
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
header {
      font-family: 'minecraft';
      background-color: transparent;
      color: white;
      padding: 20px;
      text-align: center;
    }

    body {
      font-family: 'minecraft';
      text-align: center;
      background-color: #f4f4f4;
      grid-template-rows: 1fr auto;
      min-height: 100vh;
      margin: 0;
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

      .formulario-container {
      color: white;
      background-color: #76767671;
      border-radius: 10px;
      border: black 3px solid;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
      max-width: 600px;
      margin: 20px auto;
      padding: 20px;
    }

    .back-button:hover, .agendar-button:hover {
      background-color: #0056b3;
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
<div class="formulario-container">  
  <div class="img-animal">
    <img id="animal" src="<?php echo $imagemfixa; ?>" alt="<?php echo $usuarios['tipo_animal'] ?>">
  </div>

  <p class="mensagem">Deseja agendar uma nova marcação para <strong><?php echo htmlspecialchars($usuarios['nome_animal']); ?></strong>?</p>

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
<a class="back-button" href="/Veterinaria/php/cadastraranimal.php">ANIMAL</a>
  <a class="back-button" href="/Veterinaria/index.html">Voltar ao Início</a>
  </div>

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
