<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conexao.php'; // Certifique-se que este arquivo contém sua conexão com o banco

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!$nome || !$telefone || !$email || !$senha) {
        $msg = "Preencha todos os campos!";
    } else {
        // Criptografar a senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Inserir no banco
        $sql = "INSERT INTO veterinarios (nome, telefone, email, senha) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssss", $nome, $telefone, $email, $senhaHash);
            if ($stmt->execute()) {
                $msg = "Veterinário cadastrado com sucesso!";
            } else {
                $msg = "Erro ao cadastrar: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $msg = "Erro na preparação da consulta: " . $conn->error;
        }
    }

    $conn->close();
}
?>

<!-- Formulário HTML simples -->
<!DOCTYPE html>
<html lang="pt">
<header>
    <meta charset="UTF-8">
    <title>Cadastrar Veterinário</title>
        <h2>Cadastrar Veterinário</h2>
</header>
 <link rel="stylesheet" href="/Veterinaria/css/formulario.css" />
<body>
<div class="formulario-container">
<div class="form-group">
    <?php if ($msg): ?>
        <p><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Nome: <input type="text" name="nome" required></label><br>
        <label>Telefone: <input type="text" name="telefone" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Senha: <input type="password" name="senha" required></label><br><br>
        <div class="form-actions">
        <button type="submit">Cadastrar</button>
        </div>
    </form>
    </div>
    </div>
</body>
</html>
