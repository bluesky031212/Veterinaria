<?php
 session_start(); // alterado
include("conexao.php");

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

$telefone = $_POST['telefone'];
$idade = $_POST['idade'];
$nif = $_POST['nif'] ?? null;

$animal_nome = $_POST['nome_animal'];
$tipo_animal = $_POST['tipo_animal'];
$porte = $_POST['porte_animal'];
$raca = $_POST['raca_animal'] ?? null;
$idade_animal = $_POST['idade_animal'] ?? null;
$genero = $_POST['genero_animal'] ?? null;
$saude = $_POST['saude_animal'] ?? 'Não informado';
$detalhes_saude = $_POST['sdetalhes_saude'] ?? '';


if ($saude === "Sim" && !empty($detalhes_saude)) {
    $saude = "Sim: " . $detalhes_saude;
}

$check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "<script>
        alert('Email já cadastrado. Por favor, faça login ou use outro email.');
    </script>";
    exit();
}

$stmt_user = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
$stmt_user->bind_param("sss", $nome, $email, $senha);
$stmt_user->execute();
$usuario_id = $stmt_user->insert_id;

// Primeiro insere o usuário
$stmt_usuario = $conn->prepare("INSERT INTO usuarios 
    (nome, telefone, email, idade) 
    VALUES (?, ?, ?, ?)");

// Depois insere o animal com o ID do usuário
$stmt_animal = $conn->prepare("INSERT INTO animais 
    (usuario_id, nome_animal, tipo_animal, porte_animal, raca_animal, idade_animal, genero_animal,saude_animal, saude_detalhes) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt_agenda->bind_param(
    "isssisssssssss",
    $usuario_id,
    $nome,
    $telefone,
    $email,
    $idade,
    $animal_nome,
    $tipo_animal,
    $porte_animal,
    $raca_animal,
    $idade_animal,
    $genero_animal,
    $saude_animal
    $detalhes_saude
);

$stmt_agenda->execute();

if ($stmt_agenda->affected_rows > 0) {
    echo "<script>
        alert('Cadastro realizado com sucesso!');
        window.location.href = '/Veterinaria/html/telacarregamento.html';
    </script>";
    exit();
}

<!-- *Verifica a idade do usuário -->

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $idade = filter_input(INPUT_POST, 'idade', FILTER_VALIDATE_INT);
  
  if ($idade < 18) {
    $erro = "Você deve ter pelo menos 18 anos";
  } 
  elseif ($idade > 100) {
    $erro = "Idade máxima permitida é 100 anos";
  }
  
  if (isset($erro)) {
    // Mostra o erro na página (pode reutilizar a mesma div se quiser)
    echo "<script>document.getElementById('idade-feedback').textContent = '$erro';</script>";
    // Ou redireciona com mensagem de erro
  }
}


$conn->close();
?>


