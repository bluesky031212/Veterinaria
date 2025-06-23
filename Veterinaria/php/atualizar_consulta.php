<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consulta_id'], $_POST['acao'])) {
    $consulta_id = $_POST['consulta_id'];
    $acao = $_POST['acao'];

    if ($acao === 'realizada' || $acao === 'cancelada') {
        $sql = "UPDATE consultas SET status_consulta = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $acao, $consulta_id);
        $stmt->execute();
    } elseif ($acao === 'editar' && isset($_POST['nova_data'], $_POST['nova_hora'])) {
        $nova_data = $_POST['nova_data'];
        $nova_hora = $_POST['nova_hora'];
        $sql = "UPDATE consultas SET data_consulta = ?, hora_consulta = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nova_data, $nova_hora, $consulta_id);
        $stmt->execute();
    }
}

header("Location: painel_vet.php");
exit();
