<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /Veterinaria/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Exclusão de Conta</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url(/Veterinaria/images/background.png);
            background-size: 100%;
            background-repeat: no-repeat;
            background-attachment: fixed;
              font-family: 'minecraft';
  font-size: 16px;
  line-height: 1.4; /* ou ajuste conforme desejado */
        }

        .container {
            max-width: 1000px;
            margin: 20px auto;
            background-color: rgba(118, 118, 118, 0.77);
            border-radius: 10px;
            border: black 3px solid;
            padding: 20px;
        }

        h2 {
            color: #c0392b;
        }

        ul {
            margin-top: 15px;
            padding-left: 20px;
        }

        label {
            display: block;
            margin-top: 20px;
        }

        input[type="checkbox"] {
            margin-right: 10px;
        }

        button {
            font-family: 'minecraft', sans-serif;
            padding: 0 25px;
            background-color: transparent;
            background-image: url('/Veterinaria/images/butao2.png');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            color: white;
            font-size: 13px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 45px;
            width: 160px;
            line-height: 45px;
            border: none;
            box-sizing: border-box;
        }

.cancelar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    text-decoration: none;
    font-family: 'minecraft', sans-serif;
    padding: 0 25px;
    background-color: transparent;
    background-image: url('/Veterinaria/images/butao2.png');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    color: white;
    font-size: 13px;
    height: 45px;
    width: 160px;
    line-height: 45px;
    border: none;
    box-sizing: border-box;
    cursor: pointer;
}



        button {
            cursor: not-allowed;
            opacity: 0.4;
        }

        button.ativo {
            cursor: pointer;
            opacity: 1;
        }

        button.ativo:hover,
        .cancelar:hover {
            background-image: url('/Veterinaria/images/butao1.png');
            transform: scale(1.05);
        }

        .cancelar {
            cursor: pointer;
        }

        .botoes {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>

<div class="container">
<section>
  <h1>Eliminação de Conta e Dados Pessoais</h1>
  <p>
    Ao prosseguir com a eliminação da sua conta, todos os seus dados pessoais e informações associadas serão permanentemente apagados do nosso sistema. Este processo é irreversível, e nenhuma informação poderá ser recuperada após a confirmação da exclusão.
  </p>

  <h2>Dados que serão eliminados incluem:</h2>

  <h3>Dados do Cliente:</h3>
  <ul>
    <li>ID do cliente</li>
    <li>Nome completo</li>
    <li>Número de telefone</li>
    <li>NIF (Número de Identificação Fiscal)</li>
    <li>Idade</li>
    <li>Endereço de e-mail</li>

  </ul>

  <h3>Dados dos Animais Associados à Conta:</h3>
  <ul>
    <li>ID do usuário associado</li>
    <li>Nome do animal</li>
    <li>Tipo de animal (ex: cão, gato)</li>
    <li>Espécie</li>
    <li>Porte do animal (pequeno, médio, grande)</li>
    <li>Raça do animal</li>
    <li>Idade do animal</li>
    <li>Gênero do animal (macho/fêmea)</li>
    <li>Detalhes sobre a saúde do animal</li>
  </ul>

  <h3>Dados das Consultas Veterinárias:</h3>
  <ul>
    <li>Data da consulta</li>
    <li>Hora da consulta</li>
    <li>Descrição da consulta</li>
  </ul>

  <h2>Importante:</h2>
  <ul>
    <li>Todos os registos de animais e consultas veterinárias serão eliminados permanentemente.</li>
    <li>Esta ação não pode ser desfeita.</li>
  </ul>

  <p>Se estiver seguro da sua decisão, pode continuar com a exclusão definitiva da sua conta.</p>
</section>


    <form action="excluir_conta.php" method="POST" onsubmit="return confirmarExclusaoFinal();">
        <label>
            <input type="checkbox" id="confirmacaoCheckbox">
            Eu li e entendi que todos os meus dados serão permanentemente excluídos.
        </label>

        <div class="botoes">
            <a class="cancelar" href="/Veterinaria/php/dashboard.php">Cancelar</a>
            <button type="submit" id="botaoExcluir" disabled>Excluir Conta</button>
        </div>
    </form>
</div>

<script>
    const checkbox = document.getElementById("confirmacaoCheckbox");
    const botao = document.getElementById("botaoExcluir");

    checkbox.addEventListener('change', () => {
        if (checkbox.checked) {
            botao.disabled = false;
            botao.classList.add('ativo');
        } else {
            botao.disabled = true;
            botao.classList.remove('ativo');
        }
    });

    function confirmarExclusaoFinal() {
        return confirm("Tem certeza que deseja excluir sua conta? Essa ação é irreversível!");
    }
</script>

</body>
</html>
