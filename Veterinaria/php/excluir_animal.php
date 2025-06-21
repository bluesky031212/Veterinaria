<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['animal_id'])) {
    include 'conexao.php';
    
    $animal_id = $_POST['animal_id'];
    $usuario_id = $_SESSION['usuario_id'];

    $stmt = $conn->prepare("DELETE FROM animais WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $animal_id, $usuario_id);
    
    if ($stmt->execute()) {
        $_SESSION['sucesso'] = "Animal excluído com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao excluir animal.";
    }

    $stmt->close();
    $conn->close();

    header("Location: dashboard.php");
    exit();
}
?>