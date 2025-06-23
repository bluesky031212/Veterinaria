<?php
include 'conexao.php';

date_default_timezone_set('Europe/Lisbon');

// Validação de parâmetros
if (!isset($_GET['data']) || !isset($_GET['veterinario_id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados incompletos']);
    exit;
}

$data = $_GET['data'];
$veterinario_id = intval($_GET['veterinario_id']);

// Valida formato da data (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Formato de data inválido']);
    exit;
}

// Gera horários de 10:00 até 16:30, de 30 em 30 minutos
function gerarHorarios($inicio = '10:00', $fim = '17:00', $intervalo = 30) {
    $horarios = [];
    $inicioTimestamp = strtotime($inicio);
    $fimTimestamp = strtotime($fim);

    while ($inicioTimestamp <= $fimTimestamp) {
        $horarios[] = date('H:i', $inicioTimestamp);
        $inicioTimestamp += $intervalo * 60;
    }

    return $horarios;
}

$horariosTotais = gerarHorarios();

// Buscar horários ocupados no banco de dados
$stmt = $conn->prepare("
    SELECT TIME_FORMAT(hora_consulta, '%H:%i') as hora 
    FROM consultas 
    WHERE DATE(data_consulta) = ? 
      AND veterinario_id = ? 
      AND LOWER(TRIM(status_consulta)) = 'agendada'
");
$stmt->bind_param("si", $data, $veterinario_id);
$stmt->execute();
$result = $stmt->get_result();

$horariosOcupados = [];
while ($row = $result->fetch_assoc()) {
    $horariosOcupados[] = $row['hora'];
}

// Apenas horários disponíveis para aquele veterinário naquele dia
$disponiveis = [];

foreach ($horariosTotais as $horario) {
    if (in_array($horario, $horariosOcupados)) continue;

    // Reativar verificação de 2h se necessário
    if ($data === date('Y-m-d')) {
        $horaObj = DateTime::createFromFormat('Y-m-d H:i', "$data $horario", new DateTimeZone('Europe/Lisbon'));
        $agoraMais2h = new DateTime('+2 hours', new DateTimeZone('Europe/Lisbon'));
        if ($horaObj <= $agoraMais2h) continue;
    }

    $disponiveis[] = $horario;
}

sort($disponiveis);

// DEBUG opcional
file_put_contents('debug_horarios.txt', print_r([
    'data' => $data,
    'veterinario_id' => $veterinario_id,
    'horariosTotais' => $horariosTotais,
    'horariosOcupados' => $horariosOcupados,
    'horariosDisponiveis' => $disponiveis,
], true));

header('Content-Type: application/json');
echo json_encode($disponiveis);
