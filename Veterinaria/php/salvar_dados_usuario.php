<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.html");
    exit();
}

include 'conexao.php';
$usuario_id = $_SESSION['usuario_id'];

$email = $_POST['email'];
$telefone = $_POST['telefone'];
$senha = $_POST['senha'];

if (!empty($senha)) {
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $sql = "UPDATE usuarios SET email = ?, telefone = ?, senha = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $email, $telefone, $senha_hash, $usuario_id);
} else {
    $sql = "UPDATE usuarios SET email = ?, telefone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $email, $telefone, $usuario_id);
}

if ($stmt->execute()) {
    $_SESSION['sucesso'] = "Dados atualizados com sucesso.";
} else {
    $_SESSION['erro'] = "Erro ao atualizar dados.";
}

$conn->close();
header("Location: dashboard.php"); // Redireciona de volta para a área do cliente
exit();
