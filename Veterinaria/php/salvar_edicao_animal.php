<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animal_id = $_POST['animal_id'];
    $nome_animal = trim($_POST['nome_animal']);
    $tipo_animal = $_POST['tipo_animal'];

    $stmt = $conn->prepare("UPDATE animais SET nome_animal = ?, tipo_animal = ? WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ssii", $nome_animal, $tipo_animal, $animal_id, $_SESSION['usuario_id']);

    if ($stmt->execute()) {
        $_SESSION['sucesso'] = "Animal atualizado com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao atualizar animal.";
    }

    header("Location: dashboard.php");
    exit();
}
?>
