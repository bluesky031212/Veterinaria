<?php
include 'conexao.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.php");
    exit();
}

$usuarios_id = $_SESSION['usuario_id'];

// Buscar o nome do usuário
$sql = "SELECT nome, id FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuarios_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$usuarios_id = $usuario['id'] ?? null;
$nome_usuario = $usuario['nome'] ?? 'Usuário';


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Receber dados do formulário
    $nome_animal     = $_POST['nome_animal'];
    $tipo_animal     = $_POST['tipo_animal'];
$raca_animal = trim($_POST['raca_animal']);
if (empty($raca_animal)) {
    $raca_animal = "Sem Raça Definida/ Não sei...";
}
    $idade_animal    = intval($_POST['idade_animal']);
    $genero_animal   = $_POST['genero_animal'];
    $saude_animal    = isset($_POST['saude_animal']) ? intval($_POST['saude_animal']) : 0;
    $saude_detalhe   = $saude_animal ? $_POST['detalhes_saude'] : '';

    // Inserir animal no banco de dados
    $stmt_animal = $conn->prepare("INSERT INTO animais 
        (usuario_id, nome_animal, tipo_animal, raca_animal, idade_animal, genero_animal, saude_animal, saude_detalhe) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt_animal->bind_param(
        "isssisis",
        $usuarios_id,
        $nome_animal,
        $tipo_animal,
        $raca_animal,
        $idade_animal,
        $genero_animal,
        $saude_animal,
        $saude_detalhe
    );

    $stmt_check = $conn->prepare("SELECT usuario_id FROM animais WHERE nome_animal = ? AND usuario_id = ? AND tipo_animal = ?");
    $stmt_check->bind_param("sis", $nome_animal, $usuarios_id, $tipo_animal);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<script>alert('Animal já está cadastrado'); window.history.back();</script>";
        $stmt_check->close();
        exit();
    }
    $stmt_check->close();



    if ($stmt_animal->execute()) {
        // Redirecionar ou exibir mensagem
        header("Location: /Veterinaria/php/telacarregamento.php");
        exit();
    } else {
        echo "Erro ao cadastrar animal: " . $stmt_animal->error;
    }

    $stmt_animal->close();
}

$conn->close();
?>



<!-- *Começo do HTML -->

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Clínica Pet Vida - Agendamento</title>
  <link rel="stylesheet" href="/Veterinaria/css/formulario.css" />
</head>

<body>
  <header>
    <h1>Vamos adicionar mais um animalzinho, <?php echo htmlspecialchars($usuario['nome']); ?>?</h1>
  </header>
  <div class="formulario-container">
      <!-- !Dados do animal -->
<form action="/Veterinaria/php/cadastraranimal.php" method="POST" id="form-animal">
       <h3>Dados do Animal</h3>

      <!-- *Nome do animal -->

      <div class="form-group">
            <label>Nome do animal *</label>
            <input type="text" name="nome_animal" required maxlength="50" onblur="validarNome_animal(this)" />
            <div class="invalid-feedback"></div>
      </div>

      <!-- JS Validação do nome do animal -->
     <script>
          function validarNome_animal(input) {
            if (!input.value) return;
            input.value = input.value
              .split(' ')
              .filter(word => word.length > 0)
              .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
              .join(' ');

            const regex = /^([A-ZÀ-Ÿ][a-zà-ÿ]*\s?)*$/

            const feedback = input.nextElementSibling;

            if (!regex.test(input.value)) {
              feedback.textContent = 'Por favor, insira o nome válido.';
              input.classList.add('is-invalid');
            } else {
              feedback.textContent = '';
              input.classList.remove('is-invalid');
            }
          }
      </script>

      <!-- *Tipo do animal -->

      <div class="form-group">
          <label>Tipo de animal *</label>
          <select name="tipo_animal" id="tipo_animal" required class="form-control">
              <option value="" disabled selected>Selecione o tipo</option>
              <option value="Cachorro">Cachorro</option>
              <option value="Gato">Gato</option>
              <option value="Hamster">Hamster</option>
              <option value="Galinha">Galinha</option>
              <!-- Adicione mais opções conforme necessário -->
          </select>
      </div>

      <!-- *Raça do animal -->

      <div class="form-group">
        <label>Raça</label>
        <input type="text" name="raca_animal" placeholder="Sem Raça Definida/ Não sei..." oninput="validarRaca(this)" />
          <div class="invalid-feedback"></div>
      </div>
      
      <!-- JS Validação da Raça -->
      <script>
        function validarRaca(input) {
            const regex = /^[A-Za-zÀ-ÿ\s]*$/; // Permite apenas letras e espaços
            const feedback = input.nextElementSibling;

            if (!regex.test(input.value)) {
                feedback.textContent = 'Por favor, insira apenas letras e espaço.';
                input.classList.add('is-invalid');
            } else {
                feedback.textContent = '';
                input.classList.remove('is-invalid');
            }
        }
      </script>

      <!-- *Idade do animal -->

     <div class="form-group">
        <label>Idade do animal *</label>
        <input type="number" name="idade_animal" id="idade_animal" required oninput="validarIdadeAnimal.this">
        <div id="idade_animal-feedback" class="invalid-feedback"></div>
      </div>

      <!-- JS Validação de idade do animal -->
        <script>
          const idadeAnimalInput = document.getElementById('idade_animal');
          const idadeAnimalFeedback = document.getElementById('idade_animal-feedback');
          const tipoAnimalSelect = document.getElementById('tipo_animal');

          function validarIdadeAnimal() {
            const idadeAnimal = parseInt(idadeAnimalInput.value, 10);
            const tipoAnimal = tipoAnimalSelect.value;

            if (idadeAnimal < 0) {
              idadeAnimalFeedback.textContent = 'Por favor, insira uma idade válida (número positivo).';
              idadeAnimalInput.classList.add('is-invalid');
            } else if (!tipoAnimal) {
              idadeAnimalFeedback.textContent = 'Selecione o tipo de animal antes de inserir a idade.';
              idadeAnimalInput.classList.add('is-invalid');
            } else if (tipoAnimal === 'Hamster' && idadeAnimal > 12) {
              idadeAnimalFeedback.textContent = 'A idade do Hamster não pode ser maior que 12 anos.';
              idadeAnimalInput.classList.add('is-invalid');
            } else if (tipoAnimal !== 'Hamster' && idadeAnimal > 30) {
              idadeAnimalFeedback.textContent = `A idade não pode ser maior que 30 anos para ${tipoAnimal.toLowerCase()}.`;
              idadeAnimalInput.classList.add('is-invalid');
            } else {
              idadeAnimalFeedback.textContent = '';
              idadeAnimalInput.classList.remove('is-invalid');
            }
          }

          idadeAnimalInput.addEventListener('blur', validarIdadeAnimal);
          tipoAnimalSelect.addEventListener('change', validarIdadeAnimal); // Atualiza a validação se o tipo for alterado
        </script>

      <!-- *Gênero do animal -->

      <div class="form-group">
        <label>Gênero</label>
        <select name="genero_animal" required>
          <option value="">Selecione...</option>
          <option value="Macho">Macho</option>
          <option value="Femea">Fêmea</option>
        </select>
      </div>

      <h3>Saúde do Animal</h3>

      <!-- *Informações de saúde do animal -->

      <div class="form-group">
<p style="text-align: justify;">
  Possui alergias, doenças, toma medicamentos ou já fez cirurgia? *
</p>
        <div class="health-options">
          <label>
            <input type="radio" name="saude_animal" value="1" onclick="document.getElementById('details').style.display='block'" required />
            Sim
          </label>
          <label>
            <input type="radio" name="saude_animal" value="0" onclick="document.getElementById('details').style.display='none'" />
            Não
          </label>
        </div>

        <!-- *Detalhes de saúde (exibe somente se "Sim" for selecionado) -->

        <div id="details" style="display:none;">
          <textarea name="detalhes_saude" placeholder="Especifique alergias, doenças, medicamentos ou cirurgias..."></textarea>
        </div>
      </div>

      <!-- *Botões de ação -->

      <div class="form-actions">
        <a href="/Veterinaria/php/dashboard.php" class="voltar">Voltar</a>
        <button type="submit" name="submit" id="submit">Enviar Formulário</button>
      </div>

    </form>
  </div>

  <iframe name="invisible" style="display: none;"></iframe>
</body>

</html>
