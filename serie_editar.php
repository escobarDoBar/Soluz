<!DOCTYPE html>
<?php
	include 'valida_secao.php';

	include 'series_pdo.php';

	if (isset($_GET['codigo'])) {
		$codigo = $_GET['codigo'];
	} else if (isset($_POST['codigo'])) {
		$codigo = $_POST['codigo'];
	} else {
		$codigo = '';
	}

	$title = "Série: ";

	if (isset($codigo)) {
		$registros = selectPDO_serie('Codigo_Serie', $codigo); 
		$descricao = $registros[0][1];
		$title .= $descricao;
	}

	// Restrição de acesso
	if ($_SESSION['tipo']!='professor'){
		header("location:serie.php?codigo=$codigo");
	}

?>
<html>
<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
</head>
<body>
	<?php
	if($codigo == '') {
		echo "<p><b>ERRO: </b> A página não recebeu o código de nenhuma série. </p>";
	} else {
		?>


		<main>
			<?php echo "<a href='disciplina_cadastro.php'>Adicionar disciplina</a>"; ?>
			<form action="series_pdo.php" method="post">
				<div class="input-field col s12">
					<label for="nome">Nome</label>
					<input type="text" name="nome" value="<?php echo $descricao; ?>">
				</div>
				<input type="hidden" name="codigo" value="<?php echo $codigo; ?>">
				<button type="submit" name="acao" value="editar">Mudar nome</button>
			</form>

			<div id="disciplnas">
				<h4>Disciplinas:</h4>
				<?php
					$reg_disc = selectPDO_seriedisc($codigo);
					selectPDO_seriedisc_table($reg_disc);
				?>
			</div>

		</main>


		<?php 
	}
	?>
</body>
</html>