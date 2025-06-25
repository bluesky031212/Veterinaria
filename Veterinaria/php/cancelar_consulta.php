
<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /Veterinaria/index.php");
    exit();
}

include 'conexao.php';

$consulta_id = $_POST['consulta_id'] ?? null;

if ($consulta_id) {
    $sql = "UPDATE consultas SET status_consulta = 'cancelada' WHERE id = ? AND status_consulta = 'agendada'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $consulta_id);
    $stmt->execute();
}

$conn->close();
header("Location: minhas_consultas.php");
exit();
