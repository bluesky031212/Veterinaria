<?php
 session_start(); // alterado
include("conexao.php");

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

$telefone = $_POST['telefone'];
$idade = $_POST['idade'];
$nif = $_POST['nif'] ?? null;

$animal_nome = $_POST['animal_nome'];
$tipo_animal = $_POST['tipo_animal'];
$porte = $_POST['porte'];
$raca = $_POST['raca'] ?? null;
$idade_animal = $_POST['idade_animal'] ?? null;
$genero = $_POST['genero'] ?? null;
$saude = $_POST['saude'] ?? 'Não informado';
$detalhes_saude = $_POST['detalhes_saude'] ?? '';

$data_consulta = $_POST['data'];
$horario_consulta = $_POST['horario'];

if ($saude === "Sim" && !empty($detalhes_saude)) {
    $saude = "Sim: " . $detalhes_saude;
}

$check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "E-mail já cadastrado. Faça login ou use outro e-mail.";
    exit();
}

$stmt_user = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
$stmt_user->bind_param("sss", $nome, $email, $senha);
$stmt_user->execute();
$usuario_id = $stmt_user->insert_id;

$stmt_agenda = $conn->prepare("INSERT INTO agendamentos 
    (usuario_id, nome_dono, telefone, email_contato, idade, nome_animal, tipo_animal, porte, raca, idade_animal, genero, saude, data_consulta, horario_consulta) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt_agenda->bind_param(
    "isssisssssssss",
    $usuario_id,
    $nome,
    $telefone,
    $email,
    $idade,
    $animal_nome,
    $tipo_animal,
    $porte,
    $raca,
    $idade_animal,
    $genero,
    $saude,
    $data_consulta,
    $horario_consulta
);

$stmt_agenda->execute();

if ($stmt_agenda->affected_rows > 0) {
    echo "<script>alert('Cadastro realizado com sucesso!');</script>";
    // Redireciona após 3 segundos
    header("refresh:3;url=/Veterinaria/index.html"); // Altere para a página que quiser
    exit();
}


$conn->close();
?>


