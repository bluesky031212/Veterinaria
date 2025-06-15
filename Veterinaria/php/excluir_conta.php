<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.html");
    exit();
}

include 'conexao.php';

// Recupera o ID do usuário da sessão
$usuario_id = $_SESSION['usuario_id'];

// Inicia uma transação para garantir que todos os dados sejam removidos de forma segura
$conn->begin_transaction();

try {
    // Exclui as consultas do usuário
    $sql = "DELETE FROM consultas WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();

    // Exclui os animais do usuário
    $sql = "DELETE FROM animais WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();

    // Exclui o usuário
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();

    // Se tudo correr bem, confirma a transação
    $conn->commit();

    // Destrói a sessão do usuário
    session_destroy();

    // Redireciona para a página de confirmação ou página inicial
    header("Location: /Veterinaria/index.html");
    exit();

} catch (Exception $e) {
    // Em caso de erro, faz o rollback da transação
    $conn->rollback();

    // Exibe uma mensagem de erro
    echo "Erro ao excluir a conta: " . $e->getMessage();
}

// Fecha a conexão
$conn->close();
?>