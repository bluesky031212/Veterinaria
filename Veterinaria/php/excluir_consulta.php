<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consulta_id'])) {
    $consulta_id = $_POST['consulta_id'];
    $sql = "DELETE FROM consultas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $consulta_id);
    $stmt->execute();
}

header("Location: painel_vet.php");
exit();
