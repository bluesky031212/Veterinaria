<?php
session_start();
include("conexao.php");

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

$telefone = $_POST['telefone'];
$idade = $_POST['idade'];
$nif = $_POST['nif'] ?? null;
$aceitou_politica = $_POST['politica'] ?? null;

$nome_animal = $_POST['nome_animal'];
$tipo_animal = $_POST['tipo_animal'];
$porte_animal = $_POST['porte_animal'];
$raca_animal = $_POST['raca_animal'] ?? null;
$idade_animal = $_POST['idade_animal'] ?? null;
$genero_animal = $_POST['genero_animal'] ?? null;
$saude_animal = $_POST['saude_animal'] ?? 'Não informado';
$saude_detalhe = $_POST['sdetalhes_saude'] ?? ''; // Corrigido

// Verifica se o email já está cadastrado
$stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Email já existe
    header("Location: /Veterinaria/index.php?erro=2"); // erro=2 → email duplicado
    exit();
}

// Se a saúde do animal for informada como "1", inclui os detalhes
if ($saude_animal === "1" && !empty($saude_detalhe)) {
    $saude_animal = "1: " . $saude_detalhe;
}

$raca_animal = trim($raca_animal);
if (empty($raca_animal)) {
    $raca_animal = "Sem raça definida ou não sabe";
}

// Insere o usuário
$stmt_user = $conn->prepare("
    INSERT INTO usuarios (nome, email, senha, telefone, nif, idade, aceitou_politica) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt_user->bind_param("sssssis", $nome, $email, $senha, $telefone, $nif, $idade, $aceitou_politica);
$stmt_user->execute();
$usuario_id = $stmt_user->insert_id;

// Insere o animal vinculado ao usuário
$stmt_animal = $conn->prepare("
    INSERT INTO animais (usuario_id, nome_animal, tipo_animal, porte_animal, raca_animal, idade_animal, genero_animal, saude_animal, saude_detalhe) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");
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
    $saude_detalhe
);
$stmt_animal->execute();

// Redireciona conforme o resultado
if ($stmt_user->affected_rows > 0 && $stmt_animal->affected_rows > 0) {
    header("Location: /Veterinaria/index.php?sucesso=1");
    exit();
} else {
    header("Location: /Veterinaria/index.php?erro=1");
    exit();
}

// Fecha a conexão
$conn->close();
