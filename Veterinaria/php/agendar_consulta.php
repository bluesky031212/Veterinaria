<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.html");
    exit();
}

include 'conexao.php'; // arquivo que conecta ao banco

// Pega os dados do formulário
$usuario_id = $_SESSION['usuario_id'];
$animal_id = $_POST['animal_id'] ?? null;
$data_consulta = $_POST['data_consulta'] ?? null;
$hora_consulta = $_POST['hora_consulta'] ?? null;

// Valida os dados mínimos
if (!$animal_id || !$data_consulta || !$hora_consulta) {
    die("Dados incompletos para o agendamento.");
}

// Verifica se o animal pertence ao usuário
$sqlVerifica = "SELECT id FROM animais WHERE id = ? AND usuario_id = ?";
$stmtVerifica = $conn->prepare($sqlVerifica);
$stmtVerifica->bind_param("ii", $animal_id, $usuario_id);
$stmtVerifica->execute();
$resultVerifica = $stmtVerifica->get_result();

if ($resultVerifica->num_rows === 0) {
    die("Animal inválido ou não pertence a você.");
}

// Insere o agendamento
$sqlInsere = "INSERT INTO consultas (usuario_id, animal_id, data_consulta, hora_consulta) VALUES (?, ?, ?, ?)";
$stmtInsere = $conn->prepare($sqlInsere);
$stmtInsere->bind_param("iiss", $usuario_id, $animal_id, $data_consulta, $hora_consulta);

if ($stmtInsere->execute()) {
    // Sucesso - redireciona para página de confirmação ou área do cliente
    header("Location: dashboard.php?msg=agendamento_sucesso");
} else {
    echo "Erro ao agendar: " . $conn->error;
}

$conn->close();
?>