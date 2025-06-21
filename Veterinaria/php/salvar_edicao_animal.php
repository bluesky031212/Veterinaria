<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    echo '<span style="color: red;">Sessão expirada. Faça login novamente.</span>';
    exit();
}

if (!isset($_POST['animal_id'], $_POST['nome_animal'], $_POST['tipo_animal'])) {
    echo '<span style="color: red;">Dados incompletos.</span>';
    exit();
}

$animal_id = $_POST['animal_id'];
$nome_animal = trim($_POST['nome_animal']);
$tipo_animal = $_POST['tipo_animal'];

// Validação no servidor
if (!preg_match('/^([A-ZÀ-Ý][a-zà-ÿ]+)(\s[A-ZÀ-Ý][a-zà-ÿ]+)*$/', $nome_animal)) {
    echo '<span style="color: red;">Nome inválido. Use apenas letras e espaços.</span>';
    exit();
}

$stmt = $conn->prepare("UPDATE animais SET nome_animal = ?, tipo_animal = ? WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ssii", $nome_animal, $tipo_animal, $animal_id, $_SESSION['usuario_id']);

if ($stmt->execute()) {
    echo '<span style="color: green;">Animal atualizado com sucesso!</span>';
} else {
    echo '<span style="color: red;">Erro ao atualizar animal.</span>';
}
?>
