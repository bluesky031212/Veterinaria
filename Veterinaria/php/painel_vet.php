<?php
session_start();
if (!isset($_SESSION['vet_id'])) {
    header("Location: /Veterinaria/index.html");
    exit();
}

include 'conexao.php';

$vet_id = $_SESSION['vet_id'];

// Buscar os animais e suas marcações associadas ao veterinário
$sql = "SELECT 
            a.nome_animal,
            a.tipo_animal AS especie,
            a.raca_animal AS raca,
            a.idade_animal AS idade,
            u.nome AS nome_dono,
            u.email AS email_dono,
            m.data_consulta,
            m.hora_consulta,
            m.status_consulta,
            m.descricao_consulta AS sintomas
        FROM animais a
        INNER JOIN usuarios u ON a.usuario_id = u.id
        INNER JOIN consultas m ON a.id = m.animal_id
        WHERE m.veterinario_id = ?
        ORDER BY m.data_consulta DESC, m.hora_consulta DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vet_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel do Veterinário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Bem-vindo, Dr(a). <?= htmlspecialchars($_SESSION['vet_nome']) ?>!</h2>

        <h4>Consultas Agendadas</h4>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Nome do Animal</th>
                            <th>Espécie</th>
                            <th>Raça</th>
                            <th>Idade (anos)</th>
                            <th>Dono</th>
                            <th>Email do Dono</th>
                            <th>Data e Hora</th>
                            <th>Sintomas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nome_animal']) ?></td>
                                <td><?= htmlspecialchars($row['especie']) ?></td>
                                <td><?= htmlspecialchars($row['raca']) ?></td>
                                <td><?= htmlspecialchars($row['idade']) ?></td>
                                <td><?= htmlspecialchars($row['nome_dono']) ?></td>
                                <td><?= htmlspecialchars($row['email_dono']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['data_consulta'] . ' ' . $row['hora_consulta'])) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['sintomas'])) ?></td>
                        <td class="status-<?= htmlspecialchars($row['status_consulta']) ?>">
                            <?= ucfirst(htmlspecialchars($row['status_consulta'])) ?>
                        </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Nenhuma marcação encontrada para você ainda.</div>
        <?php endif; ?>

        <a href="logout.php" class="btn btn-danger mt-3">Sair</a>
    </div>
</body>
</html>
