<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.php");
    exit();
}

include 'conexao.php';
$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT nome, email, telefone FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$conn->close();
?>

<style>
@font-face {
    font-family: 'MinecraftRegular';
  src: url(/Veterinaria/fonts/Minercraftory.ttf) format('truetype');
}

body {
  font-family: 'minecraft';
  margin: 0;
  padding: 0;
  background-image: url(/Veterinaria/images/background.png);
  background-size: 100%;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
}

/* === Cabeçalho === */
header {
  padding: 30px;
  background-color: transparent;
  background-position: center;
  font-family: 'MinecraftRegular';
  text-align: center;
  font-weight: bold;
  text-transform: uppercase;
  font-size: 15px;
  letter-spacing: 5.3px;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.5);
  color: rgb(190, 190, 190);
}

header img {
  width: 60px;
  height: 25px;
  vertical-align: middle;
}

h3 {
  text-align: center;
  margin-top: 0;
}

/* === Formulário === */
.formulario-container {
  color: white;
  background-color:rgba(118, 118, 118, 0.77);
  border-radius: 10px;
  border: 3px solid black;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  max-width: 600px;
  margin: 20px auto;
  padding: 20px;
}

input[type="email"],
input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.form-actions {
    display: flex;
    justify-content: center;
    width: 100%;
    margin-top: 20px;
}

.form-actions button {
    width: 160px;
    height: 38px;
    font-family: 'minecraft', sans-serif;
    background-color: transparent;
    background-image: url('/Veterinaria/images/butao2.png');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    color: white;
    cursor: pointer;
    font-size: 13px;
    text-decoration: none;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
}

.form-actions button:hover {
 background-image: url('/Veterinaria/images/butao1.png');
    transform: scale(1.05);
}


</style>

<!DOCTYPE html>
<html lang="pt-BR">
<header>  
    <meta charset="UTF-8">
    <title>Editar Meus Dados</title>
    <h1>Editar Meus Dados</h1>
</header>
<body>
<div class="formulario-container">

    <form action="salvar_dados_usuario.php" method="POST">
        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

        <label>Telefone:</label>
        <input type="text" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>" required>

        <label>Nova Senha (opcional):</label>
        <input type="password" name="senha">

<div class="form-actions">
      <button type="button" onclick="window.location.href='dashboard.php'">Voltar</button>
        <button type="submit">Salvar Alterações</button>
        </div>
    </form>
</div>
</body>

</html>
