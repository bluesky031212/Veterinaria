<?php
session_start();

// Protege a página apenas para veterinários
if (!isset($_SESSION['vet_id']) || $_SESSION['tipo'] !== 'veterinario') {
    header("Location: /Veterinaria/index.php");
    exit();
}

include 'conexao.php';

$vet_id = $_SESSION['vet_id'];

$sql = "SELECT 
            m.id AS consulta_id,
            a.nome_animal,
            a.tipo_animal AS especie,
            a.raca_animal AS raca,
            a.idade_animal AS idade,
            a.saude_detalhe AS saude,
            u.nome AS nome_dono,
            u.email AS email_dono,
            m.data_consulta,
            m.hora_consulta,
            m.status_consulta
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
                            <th>Animal</th>
                            <th>Espécie</th>
                            <th>Raça</th>
                            <th>Idade</th>
                            <th>Dono</th>
                            <th>Email</th>
                            <th>Data/Hora</th>
                            <th>Detalhes da Saúde</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nome_animal']) ?></td>
                                <td><?= htmlspecialchars($row['especie']) ?></td>
                                <td><?= htmlspecialchars($row['raca']) ?></td>
                                <td><?= htmlspecialchars($row['idade']) ?> anos</td>
                                <td><?= htmlspecialchars($row['nome_dono']) ?></td>
                                <td><?= htmlspecialchars($row['email_dono']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['data_consulta'] . ' ' . $row['hora_consulta'])) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['saude'])) ?></td>
                                <td class="status-<?= htmlspecialchars(strtolower($row['status_consulta'])) ?>">
                                    <?= ucfirst(strtolower(htmlspecialchars($row['status_consulta']))) ?>
                                </td>
                                <td>
    <!-- Ações: Confirmar / Cancelar / Editar -->
    <form action="atualizar_consulta.php" method="POST" class="d-flex flex-column gap-1">
        <input type="hidden" name="consulta_id" value="<?= $row['consulta_id'] ?>">
        <button name="acao" value="realizada" class="btn btn-success btn-sm">Confirmar</button>
        <button name="acao" value="cancelada" class="btn btn-warning btn-sm">Cancelar</button>
        <button name="acao" value="editar" class="btn btn-info btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#editarModal<?= $row['consulta_id'] ?>">Editar</button>
    </form>

    <!-- Excluir -->
    <form action="excluir_consulta.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta marcação?');">
        <input type="hidden" name="consulta_id" value="<?= $row['consulta_id'] ?>">
        <button class="btn btn-danger btn-sm mt-1">Excluir</button>
    </form>

    <!-- Ver Consulta -->
    <a href="response_medico.php?id=<?= $row['consulta_id'] ?>" class="btn btn-secondary btn-sm mt-1">Ver Consulta</a>

    <!-- Modal de Edição -->
    <div class="modal fade" id="editarModal<?= $row['consulta_id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <form action="atualizar_consulta.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Consulta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="consulta_id" value="<?= $row['consulta_id'] ?>">
                        <input type="hidden" name="acao" value="editar">
                        <label>Nova Data:</label>
                        <input type="date" name="nova_data" class="form-control" required>
                        <label>Nova Hora:</label>
                        <input type="time" name="nova_hora" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Salvar</button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</td>

                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Nenhuma marcação encontrada.</div>
        <?php endif; ?>

        <a href="logout.php" class="btn btn-danger mt-3">Sair</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
