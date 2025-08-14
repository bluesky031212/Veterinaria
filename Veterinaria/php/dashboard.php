<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.php");
    exit();
}

include 'conexao.php';
$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT nome, id FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id); // <- Correção aqui
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

$nome_usuario = $usuario['nome'] ?? 'Usuário';

// Pegar animais do usuário
$sql = "SELECT * FROM animais WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$animais = $result->fetch_all(MYSQLI_ASSOC);


// Pegar veterinários
$sqlVet = "SELECT id, nome FROM veterinarios";
$resultVet = $conn->query($sqlVet);
$veterinarios = [];
if ($resultVet) {
    while ($row = $resultVet->fetch_assoc()) {
        $veterinarios[] = $row;
    }
}

$conn->close();

function getImagensAnimal($tipo) {
    switch ($tipo) {
        case 'Cachorro':
            return [
                'fixo' => '/Veterinaria/images/CACHORRO PISCANDO.png',
                'gif' => '/Veterinaria/images/CACHORRO-PISCANDO.gif',
                'som' => '/Veterinaria/som/cachorro.mp3'
            ];
        case 'Gato':
            return [
                'fixo' => '/Veterinaria/images/gato pisca.png',
                'gif' => '/Veterinaria/images/gato-pisca.gif',
                'som' => '/Veterinaria/som/gato.mp3'
            ];
        case 'Galinha':
            return [
                'fixo' => '/Veterinaria/images/galinha pisca.png',
                'gif' => '/Veterinaria/images/galinha-pisca.gif',
                'som' => '/Veterinaria/som/galinha.mp3'
            ];
        case 'Hamster':
            return [
                'fixo' => '/Veterinaria/images/hamster-pisca.png',
                'gif' => '/Veterinaria/images/hamster-pisca.gif',
                'som' => '/Veterinaria/som/hamster.mp3'
            ];
        default:
            return [
                'fixo' => '/Veterinaria/images/default.png',
                'gif' => '/Veterinaria/images/default.png',
                'som' => ''
            ];
    }
}

$dataAtual = date('Y-m-d');

function gerarOpcoesHorario($inicio = '10:00', $fim = '17:00', $intervaloMinutos = 30) {
    $times = [];
    $inicioTimestamp = strtotime($inicio);
    $fimTimestamp = strtotime($fim);

    for ($time = $inicioTimestamp; $time <= $fimTimestamp; $time += $intervaloMinutos * 60) {
        $times[] = date('H:i', $time);
    }

    return $times;
}

$opcoesHorario = gerarOpcoesHorario();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Área do Cliente</title>
<link rel="stylesheet" href="/Veterinaria/css/dashboard.css" />

</head>

<body>

<header>
        <h1>Bem vindo(a), <?php echo htmlspecialchars($usuario['nome']); ?></h1>
</header>

<div class="container">

    <?php
    if (isset($_SESSION['erro'])) {
        echo "<p class='mensagem-erro'>" . $_SESSION['erro'] . "</p>";
        unset($_SESSION['erro']);
    }

    if (isset($_SESSION['sucesso'])) {
        echo "<p class='mensagem-sucesso'>" . $_SESSION['sucesso'] . "</p>";
        unset($_SESSION['sucesso']);
    }
    ?>

    <!-- LISTA DOS ANIMAIS -->
    <div class="animais-container">
        <?php foreach ($animais as $animal):
            $imgs = getImagensAnimal($animal['tipo_animal']);
        ?>
        <div class="animal-card">
            <img 
                src="<?= $imgs['fixo'] ?>" 
                data-fixo="<?= $imgs['fixo'] ?>" 
                data-gif="<?= $imgs['gif'] ?>" 
                data-som="<?= $imgs['som'] ?>" 
                data-nome="<?= htmlspecialchars($animal['nome_animal']) ?>" 
                data-id="<?= $animal['id'] ?>"
            >
            <div class="animal-nome" id="nome-animal-<?= $animal['id'] ?>">
    <?= htmlspecialchars($animal['nome_animal']) ?>
    <button class="edit-nome-btn" data-id="<?= $animal['id'] ?>" data-nome="<?= htmlspecialchars($animal['nome_animal']) ?>">🖉</button>
</div>
<div id="form-edicao-<?= $animal['id'] ?>" style="display: none; margin-top: 5px;">
    <form action="editar_nome_animal.php" method="POST" class="inline-edit-form">
        <input type="hidden" name="animal_id" value="<?= $animal['id'] ?>">
        <input type="text" name="novo_nome" value="<?= htmlspecialchars($animal['nome_animal']) ?>" required>
        <button type="submit">Salvar</button>
        <button type="button" class="cancelar-edicao" data-id="<?= $animal['id'] ?>">Cancelar</button>
    </form>
</div>

            <!-- Formulário de exclusão -->
<form action="excluir_animal.php" method="POST" onsubmit="return confirm('Deseja realmente excluir este animal?');" style="display:inline;">
    <input type="hidden" name="animal_id" value="<?= $animal['id'] ?>">
    <button type="submit" class="excluir-button">Excluir</button>
</form>
            </form>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- MOVIDO PARA FORA DO FOREACH -->
    <p class="mensagem" id="mensagem">Selecione um animal para agendar.</p>

    <div id="calendario-container">
        <form action="agendar_consulta.php" method="POST" id="form-agendamento">
            <input type="hidden" name="animal_id" id="animal_id">
            
                 <h3>Escolha o veterinário:</h3>
            <select name="veterinario_id" id="veterinario_id" required>
                     <option value="" disabled selected>Selecione um veterinário</option>
                  <?php foreach ($veterinarios as $vet): ?>
                     <option value="<?= $vet['id'] ?>"><?= htmlspecialchars($vet['nome']) ?></option>
                   <?php endforeach; ?>
            </select>

            <h3>Escolha a data:</h3>
            <input type="date" name="data_consulta" id="data_consulta" required min="<?= $dataAtual ?>">

                        <h3>Escolha o horário:</h3>
            <select name="hora_consulta" id="hora_consulta" required>
                <option value="" disabled selected>Selecione a data</option>
            </select>
             <h3>Motivo da Consulta:</h3>
            <textarea name="motivo" id="motivo" maxlength="100" required rows="3" cols="40"
            placeholder="Descreva brevemente o motivo da consulta (até 100 caracteres)"></textarea>
            <br><br>
            <button type="submit" class="agendar-button">Confirmar Agendamento</button>
        </form>
    </div>

    <!-- BOTÕES INFERIORES -->
<div class="botoes-container">

    <form action="/Veterinaria/php/logout.php" method="POST">
        <button type="submit" class="button">Encerrar Sessão</button>
    </form>
    <a class="button" href="/Veterinaria/php/cadastraranimal.php">Adicionar Animal</a>
    <a class="button" href="/Veterinaria/php/minhas_consultas.php">Minhas Consultas</a> <!-- NOVO BOTÃO -->
<a class="button" href="confirmar_exclusao.php">Excluir Conta</a>

</div>

</div>

<!-- ÁUDIO -->
<audio id="som-hover"></audio>

<script>
    const animais = document.querySelectorAll('.animal-card img');
    const mensagem = document.getElementById("mensagem");
    const calendario = document.getElementById("calendario-container");
    const inputAnimalId = document.getElementById("animal_id");
    const som = document.getElementById("som-hover");
    const inputData = document.getElementById('data_consulta');
    const selectHora = document.getElementById('hora_consulta');
    const selectVet = document.getElementById('veterinario_id');

    animais.forEach(img => {
        img.addEventListener("mouseenter", () => {
            img.src = img.dataset.gif + "?t=" + Date.now();
            if (img.dataset.som) {
                som.src = img.dataset.som;
                som.currentTime = 0;
                som.play();
            }
        });

        img.addEventListener("mouseleGalinha", () => {
            img.src = img.dataset.fixo;
            som.pause();
            som.currentTime = 0;
        });

        img.addEventListener("click", () => {
            mensagem.textContent = `Deseja agendar uma nova marcação para ${img.dataset.nome}?`;
            inputAnimalId.value = img.dataset.id;
            calendario.style.display = "block";
        });
    });

    inputData.addEventListener('change', atualizarHorarios);
    selectVet.addEventListener('change', atualizarHorarios);

    function atualizarHorarios() {
        const data = inputData.value;
        const vetId = selectVet.value;

        if (!data || !vetId) {
            selectHora.innerHTML = '<option value="" disabled selected>Selecione a data e o veterinário</option>';
            return;
        }

        // Bloqueia domingo
        if (new Date(data).getDay() === 0) {
            alert('Domingos não são permitidos para agendamento.');
            inputData.value = '';
            selectHora.innerHTML = '<option value="" disabled selected>Selecione um horário</option>';
            return;
        }

        fetch(`/Veterinaria/php/horarios_ocupados.php?data=${data}&veterinario_id=${vetId}`)
            .then(response => response.json())
            .then(disponiveis => {
                selectHora.innerHTML = '<option value="" disabled selected>Selecione um horário</option>';

                if (disponiveis.length === 0) {
                    const opt = document.createElement('option');
                    opt.disabled = true;
                    opt.textContent = "Sem horários disponíveis.";
                    selectHora.appendChild(opt);
                    return;
                }

                disponiveis.forEach(hora => {
                    const opt = document.createElement('option');
                    opt.value = hora;
                    opt.textContent = hora;
                    selectHora.appendChild(opt);
                });
            })
            .catch(err => {
                console.error("Erro ao buscar horários:", err);
                selectHora.innerHTML = '<option value="">Erro ao carregar horários</option>';
            });
    }
</script>


<script>
document.querySelectorAll('.edit-nome-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        document.getElementById('nome-animal-' + id).style.display = 'none';
        document.getElementById('form-edicao-' + id).style.display = 'block';
    });
});

document.querySelectorAll('.cancelar-edicao').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        document.getElementById('form-edicao-' + id).style.display = 'none';
        document.getElementById('nome-animal-' + id).style.display = 'block';
    });
});

</script>

<script>
document.getElementById('form-agendamento').addEventListener('submit', function(event) {
    const motivoTextarea = document.getElementById('motivo');
    const textoOriginal = motivoTextarea.value.trim();
    
    if (textoOriginal && !textoOriginal.startsWith("Motivo da Consulta:")) {
        motivoTextarea.value = "Motivo da Consulta:\n" + textoOriginal;
    }
});
</script>



</body>
