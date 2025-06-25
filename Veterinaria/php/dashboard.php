<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.html");
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
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

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


        .container {
            max-width: 1000px;
            margin: 20px auto;
            background-color:rgba(118, 118, 118, 0.77);
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
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 150px; /* ou ajuste conforme necessário */
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
        display: flex;
        justify-content: center;   /* Centraliza horizontalmente */
        align-items: center;       /* Alinha verticalmente */
        gap: 6px;                  /* Espaço entre nome e lápis */
        color: white;
        font-weight: bold;
        text-align: center;
        }
        .edit-nome-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1em;
        color: white;
        padding: 0;
        }

                .mensagem-sucesso {
            color:rgb(68, 255, 71); /* ou qualquer outra cor */
            font-weight: bold;
            text-align: center;
        }

        .mensagem-erro {
            color:rgb(255, 72, 72); /* ou qualquer outra cor */
            font-weight: bold;
            text-align: center;
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


        .edit-nome-btn {
    background: none;
    border: none;
    font-size: 16px;
    cursor: pointer;
    margin-left: 5px;
    color: white;
}
.edit-nome-btn:hover {
    color: yellow;
}
.inline-edit-form input[type="text"] {
    padding: 5px;
    border-radius: 5px;
}
        
    </style>
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
    <a class="button" href="/Veterinaria/php/editar_usuario.php">Editar Meus Dados</a>
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

</body>
