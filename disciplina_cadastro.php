<!DOCTYPE html>
<?php
	require_once "autoload.php";

	include 'conf.php';
	include 'funcoes.php';
	include 'valida_secao.php';
	include 'disciplinas_pdo.php';

	if (isset($_POST['acao'])) $acao = $_POST['acao'];
	else if (isset($_GET['acao'])) $acao = $_GET['acao'];
	else $acao = '';
?>
<html>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>

		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

		<title>Cadastro de disciplina</title>
	</head>

	<body>
		<?php printHeader(); /*include 'funcoes.php'; lá em cima*/ ?>

		<main>
			<div class="container">
			<div class="jumbotron">
				<br><br>
				<h1 class="display-4 text-center">Nova Disciplina</h1>
				<br><br>
				<form action="series_pdo.php" method="post">
					<div class="form-group">
						<label for="email">Nome da Turma</label>
						<input type="text" class="form-control" id="nome" name="nome" placeholder="Exemplo: 3 Info">
					</div>
					Turma
						<?php gerarSelect($tb_series, 'Serie_Codigo_Serie', 0, 'Codigo_Serie', 'Descricao'); //funcoes.php ?>
						<br><br>
						<button type="submit" name="acao" value="cadastrar" class="btn btn-primary">Cadastrar</button>
				</form>
			</div>
		</main>

		<footer>
		</footer>
	</body>

	<!--  Scripts-->
	<script src="assets/js/jquery-2.1.1.min.js"></script>
	<script src="assets/js/materialize.min.js"></script>
	<script src="assets/js/init.js"></script>

</html>
