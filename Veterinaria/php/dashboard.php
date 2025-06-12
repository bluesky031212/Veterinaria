<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.html");
    exit();
}

include 'conexao.php';
$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT * FROM animais WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$animais = $result->fetch_all(MYSQLI_ASSOC);
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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Área do Cliente</title>
    <link rel="stylesheet" href="/Veterinaria/css/area_cliente.css">
    <style>
        body {
            font-family: 'minecraft';
            background-color: #f4f4f4;
            margin: 0;
            text-align: center;
        }

        header {
            background-color: transparent;
            color: white;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 20px auto;
            background-color: #76767671;
            border-radius: 10px;
            border: black 3px solid;
            padding: 20px;
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

        p {
            color: white;
        }

      .mensagem {
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        margin: 40px auto; /* centraliza horizontalmente */
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

        .back-button, .agendar-button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px;
            cursor: pointer;
        }

        .back-button:hover, .agendar-button:hover {
            background-color: #0056b3;
        }

        .animal-nome {
            font-weight: bold;
            color: white;
        }
    </style>
</head>

<body>
<header>
    <h1>Bem-vindo(a)!</h1>
</header>

<div class="container">
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
            </div>
        <?php endforeach; ?>
    </div>

    <p class="mensagem" id="mensagem">Selecione um animal para agendar.</p>

    <div id="calendario-container">
        <form action="agendar_consulta.php" method="POST">
            <input type="hidden" name="animal_id" id="animal_id">
            <h3>Escolha a data:</h3>
            <input type="date" name="data_consulta" required>
            <h3>Escolha o horário:</h3>
            <input type="time" name="hora_consulta" required>
            <br><br>
            <button type="submit" class="agendar-button">Confirmar Agendamento</button>
        </form>
    </div>

    <a class="back-button" href="/Veterinaria/php/cadastraranimal.php">Adicionar Animal</a>
    <a class="back-button" href="/Veterinaria/index.html">Voltar ao Início</a>
</div>

<!-- Áudio para som de hover -->
<audio id="som-hover"></audio>

<script>
    const animais = document.querySelectorAll('.animal-card img');
    const mensagem = document.getElementById("mensagem");
    const calendario = document.getElementById("calendario-container");
    const inputAnimalId = document.getElementById("animal_id");
    const som = document.getElementById("som-hover");

    animais.forEach(img => {
        img.addEventListener("mouseenter", () => {
            img.src = img.dataset.gif + "?t=" + Date.now();
            const somAnimal = img.dataset.som;
            if (somAnimal) {
                som.src = somAnimal;
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
            const nome = img.dataset.nome;
            const id = img.dataset.id;
            mensagem.textContent = `Deseja agendar uma nova marcação para ${nome}?`;
            inputAnimalId.value = id;
            calendario.style.display = "block";
        });
    });
</script>
</body>
</html>
