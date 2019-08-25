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

?>
<html>
<head>
	<title><?php echo $title; ?></title>
</head>
<body>
	<?php
	if($codigo == '') {
		echo "<p><b>ERRO: </b> A página não recebeu o código de nenhuma série. </p>";
	} else {
		?>


		<main>

			<h1><?php echo $descricao ?></h1>

			<?php if ($_SESSION['tipo'] == 'professor') 
				echo "<a href='serie_editar.php?codigo=$codigo'>Editar</a>";?>
			
			<div id="disciplnas">
				<h4>Disciplinas:</h4>
				<?php
					$reg_disc = selectPDO_seriedisc($codigo);
					seriedisc_table($reg_disc);
				?>
			</div>

		</main>


		<?php 
	}

	function seriedisc_table($registros) {
		echo "<table class='highlight centered responsive-table' border='5'>
			<thead class='black white-text'>
			<tr>
				<th>ID Disciplina</th>
				<th>Disciplina</th>
				<th>ID Série</th>
				<th>Série</th>
			</tr>
			</thead>
			<tdbody>";

		for ($i=0; $i < count($registros); $i++) {
			echo "<tr>";
			for ($j=0; $j < count($registros[$i]); $j++) { 
				echo "<td>".$registros[$i][$j]."</td>";
			}
			echo "<tr>";
		}
		echo "</tbody>
		</table>";
	}
	?>
</body>


</html>