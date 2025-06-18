<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'conexao.php';

$msgErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!$email || !$senha) {
        $msgErro = "Por favor, preencha todos os campos.";
    } else {
        $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if (password_verify($senha, $row['senha'])) {
                    $_SESSION['usuario_id'] = $row['id'];
                    $_SESSION['usuario_nome'] = $row['nome'];
                    $_SESSION['usuario_email'] = $row['email'];
                    header("Location: /Veterinaria/php/dashboard.php");
                    exit();
                } else {
                    $msgErro = "Senha incorreta.";
                }
            } else {
                $msgErro = "Usuário não encontrado.";
            }
            $stmt->close();
        } else {
            $msgErro = "Erro na preparação da consulta: " . $conn->error;
        }
    }

    $conn->close();
}
?>