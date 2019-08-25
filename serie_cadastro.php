<!DOCTYPE html>
<?php
	include 'conf.php';
	include 'funcoes.php';
	include 'valida_secao.php';
?>
<html>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>

		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

		<title>Cadastro de série</title>
	</head>

	<body>
		<?php printHeader(); /*include 'funcoes.php'; lá em cima*/ ?>

		<main>
			<div class="container">
			<div class="jumbotron">
				<br><br>
				<h1 class="display-4 text-center">Nova Série</h1>
				<br><br>
				<form action="series_pdo.php" method="post">
					<div class="form-group">
						<label for="email">Nome da Turma</label>
						<input type="text" class="form-control" id="nome" name="nome" placeholder="Exemplo: 3 Info">
					</div>
					<button type="submit" name="acao" value="cadastrar" class="btn btn-primary">Cadastrar</button>
				</form>
			</div>
		</main>

		</div>

		<!--  Scripts-->
		<script src="assets/js/jquery-2.1.1.min.js"></script>
		<script src="assets/js/materialize.min.js"></script>
		<script src="assets/js/init.js"></script>

	</body>
</html>
