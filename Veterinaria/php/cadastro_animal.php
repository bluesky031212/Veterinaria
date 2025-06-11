<?php
 session_start(); // alterado
include("conexao.php");

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

$telefone = $_POST['telefone'];
$idade = $_POST['idade'];
$nif = $_POST['nif'] ?? null;

$nome_animal = $_POST['nome_animal'];
$tipo_animal = $_POST['tipo_animal'];
$porte_animal = $_POST['porte_animal'];
$raca_animal = $_POST['raca_animal'] ?? null;
$idade_animal = $_POST['idade_animal'] ?? null;
$genero_animal = $_POST['genero_animal'] ?? null;
$saude_animal = $_POST['saude_animal'] ?? 'Não informado';
$saude_detalhe = $_POST['sdetalhes_saude'] ?? '';


$email = $_POST['email']; // ou de onde você pegar o dado

// Prepare a query para verificar
$stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Email já existe
    echo "<script>alert('Este email já está cadastrado.'); window.history.back();</script>";
    exit();
}




if ($saude_animal === "1" && !empty($detalhes_saude)) {
    $saude_animal = "1: " . $detalhes_saude;
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

$stmt_user = $conn->prepare("INSERT INTO usuarios (nome, email, senha, telefone, nif, idade) VALUES (?, ?, ?, ?, ?, ?)");
$stmt_user->bind_param("ssssss", $nome, $email, $senha, $telefone, $nif, $idade);
$stmt_user->execute();
$usuario_id = $stmt_user->insert_id;


// Depois insere o animal com o ID do usuário
$stmt_animal = $conn->prepare("INSERT INTO animais 
    (usuario_id, nome_animal, tipo_animal, porte_animal, raca_animal, idade_animal, genero_animal,saude_animal, saude_detalhe) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt_animal->bind_param(
    "issssisis",
    $usuario_id,
    $nome_animal,
    $tipo_animal,
    $porte_animal,
    $raca_animal,
    $idade_animal,
    $genero_animal,
    $saude_animal,
    $saude_detalhe);

$stmt_animal->execute();

if ($stmt_user->affected_rows > 0 && $stmt_animal->affected_rows > 0) {	
    echo "<script>
        alert('Cadastro realizado com sucesso!');
        window.location.href = '/Veterinaria/php/telacarregamento.php';
    </script>";
    exit();
}



$conn->close();
?>


