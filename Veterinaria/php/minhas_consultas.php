<?php
session_start();
include 'conexao.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$sql = "SELECT c.id, a.nome_animal, v.nome AS nome_veterinario,
               c.data_consulta, c.hora_consulta,
               c.status_consulta, a.saude_detalhe
        FROM consultas c
        JOIN animais a ON c.animal_id = a.id
        JOIN veterinarios v ON c.veterinario_id = v.id
        ORDER BY c.data_consulta DESC, c.hora_consulta DESC";

$resultado = $conn->query($sql);

date_default_timezone_set('Europe/Lisbon');
$hoje = new DateTime();
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <meta charset="UTF-8">
  <title>Minhas Marcações</title>
  <style>
        @font-face {
    font-family: 'MinecraftRegular';
  src: url(/Veterinaria/fonts/Minercraftory.ttf) format('truetype');
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


        body {
            font-family: 'minecraft';
            margin: 0;
            padding: 0;
            color: white;
            background-image: url(/Veterinaria/images/background.png);
            background-size: 100%;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

    .tabela-container {
            max-width: 1000px;
            margin: 20px auto;
            color: white;
            background-color:rgba(118, 118, 118, 0.77);
            border-radius: 10px;
            border: black 3px solid;
            padding: 20px;
            text-align: center;
    }

table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border: 1px solid white;
  border-radius: 12px;
  overflow: hidden;
  background-color: transparent;
}

tr {
  background-color: transparent;
}

th, td {
  padding: 12px;
  text-align: center;
  border: 1px solid rgb(255, 255, 255); /* linhas internas suaves */
  background-color: transparent;
  color: white;
}

th {
  background-color: #007bff;
  color: white;
  border: 1px solid rgb(255, 255, 255);
}

th:first-child {
  border-top-left-radius: 12px;
}

th:last-child {
  border-top-right-radius: 12px;
}
    .agendada {
      color: limegreen;
      font-weight: bold;
    }

    .realizada {
      color: lightblue;
      font-weight: bold;
    }

    .cancelada {
      color: red;
      font-weight: bold;
    }

    .botao-cancelar {
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

    .botao-voltar {
            font-family: 'minecraft', sans-serif;
            padding: 25px;
            background-color: transparent;
            background-image: url('/Veterinaria/images/butao2.png');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            color: white;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;

            height: 45px;
            min-width: 140px;
            line-height: 45px;
            border: none;
            box-sizing: border-box;
    }

    .botao-voltar:hover, .botao-cancelar:hover, .descricao-btn:hover {
            background-image: url('/Veterinaria/images/butao1.png');
            transform: scale(1.05);
    }

    .descricao-btn {
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

    .descricao-texto {
      display: none;
      margin-top: 5px;
    }
  </style>
  <script>
    function toggleDescricao(id) {
      var el = document.getElementById('descricao-' + id);
      el.style.display = el.style.display === 'block' ? 'none' : 'block';
    }
  </script>
</head>
<header>
  <h1>Minhas Consultas</h1>
  </header>

<body>
  <div class="tabela-container">
    <table>
      <tr>
        <th>Animal</th>
        <th>Veterinário</th>
        <th>Data</th>
        <th>Hora</th>
        <th>Status</th>
        <th>Descrição</th>
        <th>Ação</th>
      </tr>

      <?php
      while ($row = $resultado->fetch_assoc()) {
          $dataConsulta = new DateTime($row['data_consulta']);
          $isPassado = $dataConsulta->format('Y-m-d') < $hoje->format('Y-m-d');
          $diferencaDias = $hoje->diff($dataConsulta)->days;
          $status = strtolower($row['status_consulta']);
          $mostrarLinha = !($status === 'cancelada' && $isPassado && $diferencaDias >= 4);

          if ($mostrarLinha) {
              $id = htmlspecialchars($row['id']);
              $animal = htmlspecialchars($row['nome_animal']);
              $veterinario = htmlspecialchars($row['nome_veterinario']);
              $data = htmlspecialchars($row['data_consulta']);
              $hora = htmlspecialchars($row['hora_consulta']);
              $descricao = htmlspecialchars($row['saude_detalhe']);
              $classeStatus = $status;

              echo "<tr>";
              echo "<td>$animal</td>";
              echo "<td>$veterinario</td>";
              echo "<td>$data</td>";
              echo "<td>$hora</td>";
              echo "<td class='$classeStatus'>" . ucfirst($status) . "</td>";
              echo "<td>
                      <button class='descricao-btn' onclick='toggleDescricao($id)'>Ver</button>
                      <div id='descricao-$id' class='descricao-texto'>$descricao</div>
                    </td>";

              if ($status === 'agendada') {
                  echo "<td>
                          <form method='post' action='cancelar_consulta.php' style='margin:0;'>
                            <input type='hidden' name='id' value='$id'>
                            <input type='hidden' name='csrf_token' value='{$_SESSION['csrf_token']}'>
                            <button type='submit' class='botao-cancelar'>Cancelar</button>
                          </form>
                        </td>";
              } else {
                  echo "<td>–</td>";
              }

              echo "</tr>";
          }
      }
      ?>

    </table>
    <a href="dashboard.php" class="botao-voltar">Voltar para minha área</a>
  </div>
</body>
</html>
