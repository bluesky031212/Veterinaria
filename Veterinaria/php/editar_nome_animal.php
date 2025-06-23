<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animal_id = $_POST['animal_id'];
    $novo_nome = trim($_POST['novo_nome']);

    if (!empty($novo_nome)) {
        $stmt = $conn->prepare("UPDATE animais SET nome_animal = ? WHERE id = ?");
        $stmt->bind_param("si", $novo_nome, $animal_id);
        if ($stmt->execute()) {
            $_SESSION['sucesso'] = "Nome atualizado com sucesso.";
        } else {
            $_SESSION['erro'] = "Erro ao atualizar nome.";
        }
        $stmt->close();
    } else {
        $_SESSION['erro'] = "Nome não pode estar vazio.";
    }

    $conn->close();
}

header("Location: dashboard.php"); // ajuste o nome do seu arquivo principal
exit();