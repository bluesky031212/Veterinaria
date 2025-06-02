<?php
include("conexao.php");

// Coleta dos dados do usuário
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografar senha

// Coleta dos dados do agendamento
$animal_nome = $_POST['animal_nome'];
$tipo_animal = $_POST['tipo_animal'];
$porte = $_POST['porte'];
$data = $_POST['data'];
$horario = $_POST['horario'];

// Verifica se o e-mail já está cadastrado
$check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "E-mail já cadastrado. Faça login ou use outro e-mail.";
    exit();
}

// Cadastra o usuário
$stmt_user = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
$stmt_user->bind_param("sss", $nome, $email, $senha);
$stmt_user->execute();

// Pega o ID do novo usuário
$usuario_id = $stmt_user->insert_id;

// Cadastra o agendamento
$stmt_agenda = $conn->prepare("INSERT INTO agendamentos (usuario_id, data, horario, animal_nome, tipo_animal, porte) VALUES (?, ?, ?, ?, ?, ?)");
$stmt_agenda->bind_param("isssss", $usuario_id, $data, $horario, $animal_nome, $tipo_animal, $porte);
$stmt_agenda->execute();

if ($stmt_agenda->affected_rows > 0) {
    echo "Cadastro e agendamento realizados com sucesso!";
} else {
    echo "Erro ao agendar.";
}

$conn->close();
?>
