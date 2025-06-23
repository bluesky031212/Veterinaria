<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.html");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];  // Pega o id do usuário logado
$animal_id = $_POST['animal_id'] ?? null;
$data_consulta = $_POST['data_consulta'] ?? null;
$hora_consulta = $_POST['hora_consulta'] ?? null;
$veterinario_id = $_POST['veterinario_id'] ?? null;

if (!$animal_id || !$data_consulta || !$hora_consulta || !$veterinario_id) {
    $_SESSION['erro'] = "Todos os campos são obrigatórios.";
    header("Location: dashboard.php");
    exit();
}

// Validar que não é domingo
if ((new DateTime($data_consulta))->format('w') == 0) {
    $_SESSION['erro'] = "Domingos não são permitidos para agendamento.";
    header("Location: dashboard.php");
    exit();
}


$sql = "SELECT * FROM consultas 
        WHERE data_consulta = ? 
          AND hora_consulta = ? 
          AND veterinario_id = ?
          AND status_consulta = 'agendada'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $data_consulta, $hora_consulta, $veterinario_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['total'] > 0) {
    $_SESSION['erro'] = "O veterinário escolhido já possui uma consulta marcada para este dia e horário.";
    header("Location: dashboard.php");
    exit();
}

// Inserir consulta incluindo usuario_id
$sqlInsert = "INSERT INTO consultas (usuario_id, animal_id, veterinario_id, data_consulta, hora_consulta) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sqlInsert);
$stmt->bind_param("iiiss", $usuario_id, $animal_id, $veterinario_id, $data_consulta, $hora_consulta);

if ($stmt->execute()) {
    $_SESSION['sucesso'] = "Consulta agendada com sucesso!";
} else {
    $_SESSION['erro'] = "Erro ao agendar a consulta. Tente novamente.";
}

header("Location: dashboard.php");
exit();
