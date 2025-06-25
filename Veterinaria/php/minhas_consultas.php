<?php
// Conexão com base de dados
include 'conexao.php';

// Obter marcações do banco de dados
$sql = "SELECT * FROM marcacoes ORDER BY data DESC, hora DESC";
$resultado = $conn->query($sql);

// Data atual
date_default_timezone_set('Europe/Lisbon');
$hoje = new DateTime();
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <meta charset="UTF-8">
  <title>Minhas Marcações</title>
  <style>
    body {
      background-image: url('fundo.jpg'); /* Substitua pela imagem de fundo */
      background-size: cover;
      background-position: center;
      font-family: Arial, sans-serif;
    }

    .tabela-container {
      background-color: rgba(0, 0, 0, 0.7);
      max-width: 90%;
      margin: 100px auto;
      padding: 30px;
      border-radius: 10px;
      color: #fff;
      box-shadow: 0 0 15px #000;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      text-align: center;
      border: 1px solid #444;
    }

    th {
      background-color: #007bff;
      color: white;
    }

    .agendada {
      color: limegreen;
      font-weight: bold;
    }

    .cancelada {
      color: red;
      font-weight: bold;
    }

    .botao-cancelar {
      background-color: #ccc;
      border: 1px solid #333;
      padding: 6px 10px;
      border-radius: 5px;
      cursor: pointer;
    }

    .botao-voltar {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #eee;
      color: #000;
      border: 1px solid #555;
      border-radius: 5px;
      text-decoration: none;
      display: inline-block;
    }

    .descricao-oculta {
      display: none;
    }
  </style>
</head>
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
          $dataConsulta = new DateTime($row['data']);
          $diferencaDias = $hoje->diff($dataConsulta)->days;
          $isPassado = $dataConsulta < $hoje;

          $status = $row['status'];
          $mostrarLinha = !($status === 'Cancelada' && $isPassado && $diferencaDias >= 4);

          if ($mostrarLinha) {
              echo "<tr>";
              echo "<td>{$row['animal']}</td>";
              echo "<td>{$row['veterinario']}</td>";
              echo "<td>{$row['data']}</td>";
              echo "<td>{$row['hora']}</td>";

              // Classe de status
              $classeStatus = ($status === 'Agendada') ? 'agendada' : 'cancelada';
              echo "<td class='$classeStatus'>$status</td>";

              // Descrição oculta
              echo "<td class='descricao-oculta'>{$row['descricao']}</td>";

              // Ação
              if ($status === 'Agendada') {
                  echo "<td>
                          <form method='post' action='cancelar.php' style='margin:0;'>
                            <input type='hidden' name='id' value='{$row['id']}'>
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

    <a href="painel_usuario.php" class="botao-voltar">Voltar para minha área</a>
  </div>
</body>
</html>
