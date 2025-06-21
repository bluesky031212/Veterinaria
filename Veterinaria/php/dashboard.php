<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.html");
    exit();
}

include 'conexao.php';
$usuario_id = $_SESSION['usuario_id'];

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
        case 'Ave':
            return [
                'fixo' => '/Veterinaria/images/galinha pisca.png',
                'gif' => '/Veterinaria/images/galinha-pisca.gif',
                'som' => '/Veterinaria/som/galinha.mp3'
            ];
        case 'Roedor':
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
    <style>
        @font-face {
            font-family: 'minecraft';
            src: url('/Veterinaria/fontes/Minecraft.ttf') format('truetype');
        }

        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-image: url(/Veterinaria/images/background.png);
            background-size: 100%;
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

        .container {
            max-width: 1000px;
            margin: 20px auto;
            background-color: #76767671;
            border-radius: 10px;
            border: black 3px solid;
            padding: 20px;
            text-align: center;
        }

        .animais-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            margin-bottom: 30px;
        }

        .animal-card {
            text-align: center;
        }

        .animal-card img {
            width: 120px;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .animal-card img:hover {
            border: 2px solid #007BFF;
        }

        .animal-nome {
            font-weight: bold;
            color: white;
        }

        .mensagem {
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin: 40px auto;
            padding: 20px;
            color: white;
            width: 50%;
            background-color: rgba(255, 251, 251, 0.31);
            border: 1px solid black;
            border-radius: 10%;
        }

        #calendario-container {
            display: none;
            margin-top: 20px;
        }

        #calendario-container form {
            text-align: center;
        }   

        input[type="date"],
        input[type="time"],
        select[name="hora_consulta"],
        select[name="veterinario_id"] {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            box-sizing: border-box;
            width: 240px;
            margin-bottom: 15px;
        }

        .botoes-container {
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .agendar-button {
            display: inline-block;
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
            height: 48px;
            min-width: 160px;
            line-height: 48px;
            border: none;
            box-sizing: border-box;
        }

        .button,
        .excluir-button {
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

            height: 38px;
            min-width: 140px;
            line-height: 38px;
            border: none;
            box-sizing: border-box;
        }

        input[type="date"]:invalid,
        select:invalid {
            border-color: red;
        }

        .button:hover,
        .agendar-button:hover,
        .excluir-button:hover {
            background-image: url('/Veterinaria/images/butao1.png');
            transform: scale(1.05);
        }


        footer {
            text-align: center;
            padding: 5px;
            font-size: 10px;
            background-color: #76767671;
            margin-top: 40px;
            border: rgba(0, 0, 0, 0.5) 2px solid;
        }
        
    </style>
</head>

<body>

<header>
    <h1>Bem-vindo(a)!</h1>
</header>

<div class="container">

    <?php
    if (isset($_SESSION['erro'])) {
        echo "<p style='color: red; font-weight: bold; text-align: center;'>" . $_SESSION['erro'] . "</p>";
        unset($_SESSION['erro']);
    }

    if (isset($_SESSION['sucesso'])) {
        echo "<p style='color: green; font-weight: bold; text-align: center;'>" . $_SESSION['sucesso'] . "</p>";
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
            <div class="animal-nome"><?= htmlspecialchars($animal['nome_animal']) ?></div>

            <!-- Formulário de exclusão -->
<form action="excluir_animal.php" method="POST" onsubmit="return confirm('Deseja realmente excluir este animal?');" style="display:inline;">
    <input type="hidden" name="animal_id" value="<?= $animal['id'] ?>">
    <button type="submit" class="excluir-button">Excluir</button>
</form>

<!-- Formulário de edição -->
<form action="editar_animal.php" method="GET" style="display:inline;">
    <input type="hidden" name="animal_id" value="<?= $animal['id'] ?>">
    <button type="submit" class="button">Editar</button>
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

            <h3>Escolha a data:</h3>
            <input type="date" name="data_consulta" id="data_consulta" required min="<?= $dataAtual ?>">

            <h3>Escolha o veterinário:</h3>
            <select name="veterinario_id" id="veterinario_id" required>
                <option value="" disabled selected>Selecione um veterinário</option>
                <?php foreach ($veterinarios as $vet): ?>
                    <option value="<?= $vet['id'] ?>"><?= htmlspecialchars($vet['nome']) ?></option>
                <?php endforeach; ?>
            </select>

                        <h3>Escolha o horário:</h3>
            <select name="hora_consulta" id="hora_consulta" required>
                <option value="" disabled selected>Selecione a data e o veterinário</option>
            </select>

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
    <form action="excluir_conta.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir sua conta?');">
        <button type="submit" class="excluir-button">Excluir Conta</button>
    </form>
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

        img.addEventListener("mouseleave", () => {
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

        // Impedir agendamento para domingos
        if (new Date(data).getDay() === 0) {
            alert('Domingos não são permitidos para agendamento.');
            inputData.value = '';
            selectHora.innerHTML = '<option value="" disabled selected>Selecione um horário</option>';
            return;
        }

        fetch(`horarios_ocupados.php?data=${data}&veterinario_id=${vetId}`)
            .then(response => response.json())
            .then(ocupados => {
                selectHora.innerHTML = '<option value="" disabled selected>Selecione um horário</option>';

                const horarios = <?= json_encode($opcoesHorario) ?>;
                const agora = new Date();
                const duasHorasDepois = new Date(agora.getTime() + 2 * 60 * 60 * 1000);
                const hoje = data === agora.toISOString().split('T')[0];

                let encontrou = false;

                horarios.forEach(hora => {
                    const [h, m] = hora.split(':');
                    const dataHora = new Date(data);
                    dataHora.setHours(parseInt(h), parseInt(m), 0, 0);

                    const valido = (!hoje || dataHora > duasHorasDepois) && !ocupados.includes(hora);
                    if (valido) {
                        const opt = document.createElement('option');
                        opt.value = hora;
                        opt.textContent = hora;
                        selectHora.appendChild(opt);
                        encontrou = true;
                    }
                });

                if (!encontrou) {
                    const opt = document.createElement('option');
                    opt.disabled = true;
                    opt.textContent = "Sem horários disponíveis.";
                    selectHora.appendChild(opt);
                }
            })
            .catch(err => {
                console.error("Erro ao buscar horários:", err);
                selectHora.innerHTML = '<option value="">Erro ao carregar horários</option>';
            });
    }
</script>
</body>
