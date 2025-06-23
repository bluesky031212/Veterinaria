<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.html");
    exit();
}

include 'conexao.php';
$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT c.id, a.nome_animal, v.nome AS veterinario_nome, 
               c.data_consulta, c.hora_consulta, 
               c.status_consulta, c.descricao_consulta
        FROM consultas c
        JOIN animais a ON c.animal_id = a.id
        JOIN veterinarios v ON c.veterinario_id = v.id
        WHERE a.usuario_id = ?
        ORDER BY c.data_consulta DESC, c.hora_consulta DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$consultas = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Minhas Consultas</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-image: url(/Veterinaria/images/background.png);
            background-size: 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        header {
            padding: 30px;
            background-color: transparent;
            background-position: center;
            background-size: contain;
            text-align: center;
        }


        .tabela-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color:rgba(118, 118, 118, 0.77);
            border-radius: 10px;
            border: black 3px solid;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
            color: white;
            background-color: #343a40;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        .status-agendada {
            color: #28a745;
            font-weight: bold;
        }

        .status-cancelada {
            color: #dc3545;
            font-weight: bold;
        }

        .status-realizada {
            color: #ffc107;
            font-weight: bold;
        }

.cancelar-btn {
    font-family: 'minecraft', sans-serif;
    background-color: transparent;
    background-image: url('/Veterinaria/images/butao2.png');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    color: white;
    cursor: pointer;
    font-size: 11px;
    text-decoration: none;
    height: 25px;
    width: 110px;
    border: none;
    background-size: 100% 100%;
    text-align: center;
    line-height: 25px;
    margin: auto;
    display: block; /*  Isso garante alinhamento no centro da <td> */
}


        .voltar {
            font-family: 'minecraft', sans-serif;
            padding: 0 25px;
            background-color: transparent;
            background-image: url('/Veterinaria/images/butao2.png');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            color: white;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 45px;
            min-width: 140px;
            line-height: 45px;
            border: none;
            box-sizing: border-box;
        }

        .voltar:hover,
        .cancelar-btn:hover {
            background-image: url('/Veterinaria/images/butao1.png');
            transform: scale(1.05);
        }

        .botao-container {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>

<header>
    <h1>Minhas Consultas</h1>
</header>

<?php if (count($consultas) > 0): ?>
    <div class="tabela-container">
        <table>
            <thead>
                <tr>
                    <th>Animal</th>
                    <th>Veterinário</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Status</th>
                    <th>Descrição</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultas as $consulta): ?>
                    <tr>
                        <td><?= htmlspecialchars($consulta['nome_animal']) ?></td>
                        <td><?= htmlspecialchars($consulta['veterinario_nome']) ?></td>
                        <td><?= date('d/m/Y', strtotime($consulta['data_consulta'])) ?></td>
                        <td><?= htmlspecialchars($consulta['hora_consulta']) ?></td>
                        <td class="status-<?= htmlspecialchars($consulta['status_consulta']) ?>">
                            <?= ucfirst(htmlspecialchars($consulta['status_consulta'])) ?>
                        </td>
                        <td><?= htmlspecialchars($consulta['descricao_consulta']) ?></td>
                        <td>
                            <?php if ($consulta['status_consulta'] === 'agendada'): ?>
                                <form action="cancelar_consulta.php" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar esta consulta?');">
                                    <input type="hidden" name="consulta_id" value="<?= $consulta['id'] ?>">
                                    <button type="submit" class="cancelar-btn">Cancelar</button>
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="botao-container">
            <button class="voltar" onclick="window.location.href='dashboard.php'">Voltar para minha área</button>
        </div>
    </div>
<?php else: ?>
    <div style="text-align:center; font-size:18px;">Você ainda não marcou nenhuma consulta.</div>
<?php endif; ?>

</body>
</html>
