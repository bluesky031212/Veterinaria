<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$animal_id = $_POST['animal_id'] ?? null;
$veterinario_id = $_POST['veterinario_id'] ?? null;
$data_consulta = $_POST['data_consulta'] ?? null;
$hora_consulta = $_POST['hora_consulta'] ?? null;
$motivo = trim($_POST['motivo'] ?? ''); // campo novo

// Validação
if (!$animal_id || !$veterinario_id || !$data_consulta || !$hora_consulta || empty($motivo)) {
    $_SESSION['erro'] = "Preencha todos os campos para agendar a consulta.";
    header("Location: dashboard.php");
    exit();
}

$sql = "INSERT INTO consultas (usuario_id, animal_id, data_consulta, hora_consulta, veterinario_id, descricao_consulta, status_consulta)
        VALUES (?, ?, ?, ?, ?, ?, 'agendada')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iissis", $usuario_id, $animal_id, $data_consulta, $hora_consulta, $veterinario_id, $motivo);

if ($stmt->execute()) {
    $_SESSION['sucesso'] = "Consulta agendada com sucesso!";
} else {
    $_SESSION['erro'] = "Erro ao agendar a consulta: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: dashboard.php");
exit();
