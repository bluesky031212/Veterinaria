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
        // Verifica primeiro se é um usuário comum
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
                header("Location: /Veterinaria/php/dashboard.php");
                exit();
            } else {
                $msgErro = "Senha incorreta.";
            }
        } else {
            // Se não achou em usuários, procura em veterinários
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
                    header("Location: /Veterinaria/php/painel_vet.php");
                    exit();
                } else {
                    $msgErro = "Senha incorreta.";
                }
            } else {
                $msgErro = "Usuário ou veterinário não encontrado.";
            }

            $stmtVet->close();
        }

        $stmtUsuario->close();
    }

    $conn->close();
}
?>
