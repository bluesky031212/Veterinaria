<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animal_id = $_POST['animal_id'];
    $novo_nome = trim($_POST['novo_nome']);

    // Validação: nome não pode estar vazio, conter números ou ter mais de 20 caracteres
    if (empty($novo_nome)) {
        $_SESSION['erro'] = "Nome não pode estar vazio.";
    } elseif (strlen($novo_nome) > 20) {
        $_SESSION['erro'] = "O nome não pode ter mais de 20 caracteres.";
    } elseif (preg_match('/\d/', $novo_nome)) {
        $_SESSION['erro'] = "O nome não pode conter números.";
    } else {
        // Se passar nas validações, atualiza
        $stmt = $conn->prepare("UPDATE animais SET nome_animal = ? WHERE id = ?");
        $stmt->bind_param("si", $novo_nome, $animal_id);
        if ($stmt->execute()) {
            $_SESSION['sucesso'] = "Nome atualizado com sucesso.";
        } else {
            $_SESSION['erro'] = "Erro ao atualizar nome.";
        }
        $stmt->close();
    }

    $conn->close();
}

header("Location: dashboard.php");
exit();
