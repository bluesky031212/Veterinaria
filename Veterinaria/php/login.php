<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $_SESSION['login_erro'] = "Por favor, preencha todos os campos.";
        header("Location: /Veterinaria/index.php");
        exit();
    }

    // Verificar em usuários (clientes)
    $sqlUsuario = "SELECT id, nome, email, senha FROM usuarios WHERE email = ?";
    $stmtUsuario = $conn->prepare($sqlUsuario);
    $stmtUsuario->bind_param("s", $email);
    $stmtUsuario->execute();
    $resultUsuario = $stmtUsuario->get_result();

    if ($rowUsuario = $resultUsuario->fetch_assoc()) {
        if (password_verify($senha, $rowUsuario['senha'])) {
            $_SESSION['usuario_id'] = $rowUsuario['id'];
            $_SESSION['usuario_nome'] = $rowUsuario['nome'];
            $_SESSION['usuario_email'] = $rowUsuario['email'];
            $_SESSION['tipo'] = 'cliente'; // 🔥 Define o tipo de usuário
            header("Location: /Veterinaria/php/dashboard.php");
            exit();
        } else {
            $_SESSION['login_erro'] = "Senha incorreta.";
            header("Location: /Veterinaria/index.php");
            exit();
        }
    }

    // Verificar em veterinários
    $sqlVet = "SELECT id, nome, email, senha FROM veterinarios WHERE email = ?";
    $stmtVet = $conn->prepare($sqlVet);
    $stmtVet->bind_param("s", $email);
    $stmtVet->execute();
    $resultVet = $stmtVet->get_result();

    if ($rowVet = $resultVet->fetch_assoc()) {
        if (password_verify($senha, $rowVet['senha'])) {
            $_SESSION['vet_id'] = $rowVet['id'];
            $_SESSION['vet_nome'] = $rowVet['nome'];
            $_SESSION['vet_email'] = $rowVet['email'];
            $_SESSION['tipo'] = 'veterinario'; // 🔥 Define o tipo de usuário
            header("Location: /Veterinaria/php/painel_vet.php");
            exit();
        } else {
            $_SESSION['login_erro'] = "Senha incorreta.";
            header("Location: /Veterinaria/index.php");
            exit();
        }
    }

    $_SESSION['login_erro'] = "Usuário ou veterinário não encontrado.";
    header("Location: /Veterinaria/index.php");
    exit();
}
?>
