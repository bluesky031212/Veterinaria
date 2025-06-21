<?php
include 'conexao.php';

if (!isset($_GET['data']) || !isset($_GET['veterinario_id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos']);
    exit;
}

$data = $_GET['data'];
$veterinario_id = intval($_GET['veterinario_id']);

// Gera todos os horários possíveis
function gerarOpcoesHorario($inicio = '10:00', $fim = '17:00', $intervaloMinutos = 30) {
    $times = [];
    $inicioTimestamp = strtotime($inicio);
    $fimTimestamp = strtotime($fim);
    for ($time = $inicioTimestamp; $time <= $fimTimestamp; $time += $intervaloMinutos * 60) {
        $times[] = date('H:i', $time);
    }
    return $times;
}

$horariosTotais = gerarOpcoesHorario();

// Buscar horários já agendados para esse dia e veterinário
$stmt = $conn->prepare("SELECT hora_consulta FROM consultas WHERE data_consulta = ? AND veterinario_id = ? AND status_consulta = 'agendada'");
$stmt->bind_param("si", $data, $veterinario_id);
$stmt->execute();
$result = $stmt->get_result();

$horariosOcupados = [];
while ($row = $result->fetch_assoc()) {
    $horariosOcupados[] = $row['hora_consulta'];
}

// Verifica se é hoje
$dataHoje = date('Y-m-d');
$horaAgora = date('H:i');
$duasHorasDepois = date('H:i', strtotime('+2 hours'));

$horariosDisponiveis = [];

foreach ($horariosTotais as $horario) {
    // Bloqueia se já está ocupado
    if (in_array($horario, $horariosOcupados)) continue;

    // Se for hoje, bloqueia horários passados ou com menos de 2h
    if ($data === $dataHoje && $horario <= $duasHorasDepois) continue;

    $horariosDisponiveis[] = $horario;
}

echo json_encode($horariosDisponiveis);