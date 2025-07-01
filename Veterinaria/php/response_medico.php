<?php
session_start();

if (!isset($_GET['id'])) {
    echo "Consulta não especificada.";
    exit();
}

include 'conexao.php';

$consulta_id = intval($_GET['id']);

// Buscar dados da consulta e garantir que pertence ao médico logado
$sql = "SELECT c.descricao_consulta, a.nome_animal, v.nome AS veterinario_nome, c.veterinario_id
        FROM consultas c
        JOIN animais a ON c.animal_id = a.id
        JOIN veterinarios v ON c.veterinario_id = v.id
        WHERE c.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $consulta_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Consulta não encontrada.";
    exit();
}

$consulta = $result->fetch_assoc();

// Verifica se o usuário logado é o médico da consulta
$usuario_logado = $_SESSION['vet_id'] ?? null; // corrigido aqui
$tipo_logado = $_SESSION['tipo'] ?? null;
$acesso_permitido = $tipo_logado === 'veterinario' && $usuario_logado == $consulta['veterinario_id'];

// Se for POST e acesso for permitido, atualiza a descrição
if ($_SERVER["REQUEST_METHOD"] === "POST" && $acesso_permitido) {
    $nova_descricao = trim($_POST['descricao']);

    $update = $conn->prepare("UPDATE consultas SET descricao_consulta = ? WHERE id = ?");
    $update->bind_param("si", $nova_descricao, $consulta_id);
    $update->execute();
    $update->close();

    header("Location: response_medico.php?id=" . $consulta_id);
    exit();
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Consulta</title>
    <style>
        body {
            font-family: sans-serif;
            background-image: url(/Veterinaria/images/background.png);
            background-size: cover;
            background-attachment: fixed;
            padding: 50px;
            color: white;
        }

        .container {
            background-color: #00000088;
            padding: 30px;
            border-radius: 12px;
            max-width: 700px;
            margin: 0 auto;
            border: 2px solid #007BFF;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 18px;
        }

        textarea {
            width: 100%;
            height: 180px;
            resize: none;
            background-color: #343a40;
            color: white;
            border: 1px solid #555;
            border-radius: 6px;
            padding: 12px;
            font-size: 16px;
            box-sizing: border-box;
            white-space: pre-wrap;
        }

        .botao-salvar {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .botao-salvar:hover {
            background-color: #0056b3;
        }

        .botao-voltar {
            background-color: #6c757d;
            margin-left: 10px;
        }

        .botao-voltar:hover {
            background-color: #565e64;
        }
    </style>
</head>
<body>

<div class="container">
    <form method="POST">
        <label for="descricao">Descrição da consulta</label>
        <textarea id="descricao" name="descricao" <?= $acesso_permitido ? '' : 'readonly' ?>><?= htmlspecialchars($consulta['descricao_consulta']) ?></textarea>

        <?php if ($acesso_permitido): ?>
            <button type="submit" class="botao-salvar">Salvar Alterações</button>
        <?php endif; ?>

        <button type="button" class="botao-salvar botao-voltar" onclick="window.location.href='painel_vet.php'">Voltar</button>
    </form>
</div>

</body>
</html>
