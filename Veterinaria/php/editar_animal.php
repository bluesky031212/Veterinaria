<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.html");
    exit();
}

if (!isset($_GET['animal_id'])) {
    echo "ID do animal não fornecido.";
    exit();
}

$animal_id = $_GET['animal_id'];

// Buscar os dados do animal
$stmt = $conn->prepare("SELECT * FROM animais WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $animal_id, $_SESSION['usuario_id']);
$stmt->execute();
$result = $stmt->get_result();
$animal = $result->fetch_assoc();

if (!$animal) {
    echo "Animal não encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Animal</title>
</head>
<body>
    <h2>Editar Animal</h2>
    <form action="salvar_edicao_animal.php" method="POST">
        <input type="hidden" name="animal_id" value="<?= $animal['id'] ?>">
        <label>Nome:</label>
        <input type="text" name="nome_animal" value="<?= htmlspecialchars($animal['nome_animal']) ?>" required><br><br>

        <label>Tipo:</label>
        <select name="tipo_animal" required>
            <option value="Cachorro" <?= $animal['tipo_animal'] === 'Cachorro' ? 'selected' : '' ?>>Cachorro</option>
            <option value="Gato" <?= $animal['tipo_animal'] === 'Gato' ? 'selected' : '' ?>>Gato</option>
            <option value="Ave" <?= $animal['tipo_animal'] === 'Ave' ? 'selected' : '' ?>>Ave</option>
            <option value="Roedor" <?= $animal['tipo_animal'] === 'Roedor' ? 'selected' : '' ?>>Roedor</option>
        </select><br><br>

        <button type="submit">Salvar Alterações</button>
    </form>
</body>
</html>
