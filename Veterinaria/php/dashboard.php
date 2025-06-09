<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver logado, manda para login
    header("Location: /html/login.html");
    exit();
}

include 'conexao.php';

$usuario_id = $_SESSION['usuario_id'];


// Busca dados adicionais do usuário (se quiser)
$sql = "SELECT u.nome, u.email, a.tipo_animal, a.nome_animal FROM usuarios u JOIN agendamentos a ON u.id = a.usuario_id WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$conn->close();

$tipo_animal = $usuario['tipo_animal'];

if ($tipo_animal === 'Cachorro') 
{
    $imagemfixa = '/Veterinaria/images/CACHORRO PISCANDO.png';
    $imagemgif = '/Veterinaria/images/CACHORRO-PISCANDO.gif';
} 
elseif ($tipo_animal === 'Gato') 
{
    $imagemfixa = '/Veterinaria/images/gato pisca.png';
    $imagemgif = '/Veterinaria/images/gato-pisca.gif';
} 
elseif ($tipo_animal === 'Ave') 
{
    $imagemfixa = '/Veterinaria/images/galinha pisca.png';
    $imagemgif = '/Veterinaria/images/galinha-pisca.gif';
} 
else 
{
    $imagemfixa = '/Veterinaria/images/default.png'; // uma imagem padrão caso não bata nenhum
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
    .img-animal img {
      width: 100px;
      cursor: pointer;
    }

    .back-button {
      display: inline-block;
      padding: 10px 20px;
      background-color: #333;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
      margin-top: 20px;
    }

    .back-button:hover {
      background-color: #555;
    }

    footer {
      margin-top: 50px;
      text-align: center;
      font-size: 14px;
      color: #888;
    }
  </style>
</head>

<body>
  <header>
    <h1>Bem-vindo <?php $partes = explode(" ", trim($usuario['nome']));
    echo htmlspecialchars($partes[0] . " " . end($partes)); ?>, à sua área de cliente</h1>
  </header>

  <div class="corpo">
    <div class="animais">
      <div class="img-animal">
        <img id="animal" src="<?php echo $imagemfixa; ?>" alt="<?php echo $usuario['tipo_animal'] ?>">
      </div>
    </div>
  </div>

  <a class="back-button" href="/Veterinaria/index.html">BACK</a>

  <footer>
    © 2025 ClínicaGubrielvin - Todos os direitos reservados.
  </footer>

  <script>
    const animal = document.getElementById("animal");

    animal.addEventListener("mouseenter", () => {
      // Adiciona timestamp pra reiniciar o GIF
      animal.src = "<?php echo $imagemgif; ?>?t=" + Date.now();
    });

    animal.addEventListener("mouseleave", () => {
      animal.src = "<?php echo $imagemfixa; ?>";
    });
  </script>
</body>

</html>