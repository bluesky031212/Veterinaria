<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.html");
    exit();
}

if (!isset($_GET['animal_id'])) {
    echo "ID do animal não fornecido.";
    exit();
}

$animal_id = $_GET['animal_id'];

// Buscar os dados do animal
$stmt = $conn->prepare("SELECT * FROM animais WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $animal_id, $_SESSION['usuario_id']);
$stmt->execute();
$result = $stmt->get_result();
$animal = $result->fetch_assoc();

if (!$animal) {
    echo "Animal não encontrado.";
    exit();
}
?>

 <style>
        @font-face {
            font-family: 'minecraft';
            src: url('/Veterinaria/fontes/Minecraft.ttf') format('truetype');
        }

        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-image: url(/Veterinaria/images/background.png);
            background-size: 100%;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        header {
            padding: 30px;
            background-color: transparent;
            background-position: center;
            background-size: contain;
            text-align: center;
        }

        .container {
        color: white;
        background-color: #76767671;
        border-radius: 10px;
        border: 3px solid black;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        }

        .botoes-container {
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }


        .button,
        .excluir-button {
            font-family: 'minecraft', sans-serif;
            padding: 0 25px;
            background-color: transparent;
            background-image: url('/Veterinaria/images/butao2.png');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            color: white;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;

            display: flex;
            align-items: center;
            justify-content: center;

            height: 38px;
            min-width: 140px;
            line-height: 38px;
            border: none;
            box-sizing: border-box;
        }

        input[type="date"]:invalid,
        select:invalid {
            border-color: red;
        }

        .button:hover,
        .agendar-button:hover,
        .excluir-button:hover {
            background-image: url('/Veterinaria/images/butao1.png');
            transform: scale(1.05);
        }


        footer {
            text-align: center;
            padding: 5px;
            font-size: 10px;
            background-color: #76767671;
            margin-top: 40px;
            border: rgba(0, 0, 0, 0.5) 2px solid;
        }
        
    </style>
  <link rel="stylesheet" href="/Veterinaria/css/formulario.css" />
  <body>
    <div class="container">
<form id="form-editar-animal" method="POST">
  <input type="hidden" name="animal_id" value="<?= $animal['id'] ?>">


    
 <div class="form-group">
  <label>Nome:</label>
  <input type="text" name="nome_animal" value="<?= htmlspecialchars($animal['nome_animal']) ?>" required maxlength="50" onblur="validarNome_animal(this)">
  <br><br>
  <div class="invalid-feedback" role="alert"></div>

  <label>Tipo:</label>
  <select name="tipo_animal" required>
      <option value="Cachorro" <?= $animal['tipo_animal'] === 'Cachorro' ? 'selected' : '' ?>>Cachorro</option>
      <option value="Gato" <?= $animal['tipo_animal'] === 'Gato' ? 'selected' : '' ?>>Gato</option>
      <option value="Ave" <?= $animal['tipo_animal'] === 'Ave' ? 'selected' : '' ?>>Ave</option>
      <option value="Roedor" <?= $animal['tipo_animal'] === 'Roedor' ? 'selected' : '' ?>>Roedor</option>
  </select>
  <br><br>
</div>
<div class="button">
  <button type="submit">Salvar Alterações</button>
</form>
</div>
<!-- Mensagem -->
<div id="mensagem" style="margin-top: 15px;"></div>

<script>
function validarNome_animal(input) {
  if (!input.value.trim()) return;

  input.value = input.value
    .split(' ')
    .filter(word => word.length > 0)
    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(' ');

  const regex = /^([A-ZÀ-Ý][a-zà-ÿ]+)(\s[A-ZÀ-Ý][a-zà-ÿ]+)*$/;
  const feedback = input.nextElementSibling;

  if (!regex.test(input.value)) {
    feedback.textContent = 'Por favor, insira um nome válido (somente letras).';
    input.classList.add('is-invalid');
    input.setAttribute('aria-invalid', 'true');
  } else {
    feedback.textContent = '';
    input.classList.remove('is-invalid');
    input.removeAttribute('aria-invalid');
  }
}

// Envio AJAX
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("form-editar-animal");
  const mensagemDiv = document.getElementById("mensagem");

  form.addEventListener("submit", async function (e) {
    e.preventDefault(); // Impede o envio tradicional

    const formData = new FormData(form);

    const resposta = await fetch("salvar_edicao_animal.php", {
      method: "POST",
      body: formData,
    });

    const texto = await resposta.text();
    mensagemDiv.innerHTML = texto;
  });
});
</script>
</div>
 </body>