<!DOCTYPE html>

<!-- ####################### ( SÓ VISUALIZA ) ##############################  -->
<?php
	//session_start();
	// echo $_SESSION['matricula'];

	include 'valida_secao.php';
	include 'disciplinas_pdo.php';
	include 'conf.php';
	include 'funcoes.php';

	if (isset($_POST['codigo'])) {
		$codigo = $_POST['codigo'];
	} else if (isset($_GET['codigo'])) {
		$codigo = $_GET['codigo'];
	}

	if(isset($codigo)) {
		//echo "Código: ".$codigo;
		$registros = selectPDO_disc('Codigo_Disciplina', $codigo);
		$nome = $registros[0][1];
		$codigo_serie = $registros[0][2];
		$serie = $registros[0][3];
	}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<title>Disciplina: <?php echo $nome; ?> (<?php echo $serie; ?>)</title>
</head>
<body class="text-center">
	<center>
	<?php if(isset($codigo)) { ?>

	<?php //selectPDO_disc_table($registros); ?>

	<h1><?php echo $serie ?> - <?php echo $nome; ?></h1>

	<h1 class="display-4 text-center">Professores:</h1>

	<div id="professores">,
		<?php $reg_prof = selectPDO_discprof($codigo);
			discprof_table ($reg_prof); ?>
	</div>
	<br/><br/><br/><br/><hr/><br/><br/><br/><br/>
	<h1 class="display-4 text-center">Alunos:</h1>
	<div id="alunos">
		<?php if($_SESSION['tipo'] == 'aluno') { ?>
			<a href="disciplina_boletim_aluno.php?codigo=<?php echo $codigo; ?>">Boletim da disciplina</a>
		<?php } else if ($_SESSION['tipo'] == 'professor') { ?>
			<a href="disciplina_boletim_prof.php?codigo=<?php echo $codigo; ?>">Boletim da disciplina</a>
		<?php } ?>
		<?php $reg_alun = selectPDO_discalun($codigo);


			discalun_table($reg_alun); ?>

	</div>
	<br/><br/><br/><br/><hr/><br/><br/><br/><br/>
	<h1 class="display-4 text-center">Avaliações:</h1><br>
	<div id="avaliacoes">
		<?php $reg_aval = selectPDO_discaval($codigo, 'disciplina');
			discaval_table($reg_aval); ?>
	</div>




		<br/><br/><br/>
		<a href="disciplina_editar.php?codigo=<?php echo $codigo; ?>">Editar disciplina</a>





	<?php } else { ?>
		<p><b>Erro:</b> A página não recebeu o código de uma disciplina</p>
	<?php } ?>

	<?php
		function discalun_table ($registros) {
			echo "<table class='highlight centered responsive-table' border='1'>
			<thead class='black white-text'>
			<tr>
				<th>Matrícula</th>
				<th>Nome</th>
				<th>Código</th>
				<th>Disciplina</th>
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

		function discaval_table ($registros) {
			echo "<table class='highlight centered responsive-table' border='1'>
			<thead class='black white-text'>
				<tr>
					<th>ID Disciplina</th>
					<th>Disciplina</th>
					<th>ID Avaliacao</th>
					<th>Conteudo</th>
					<th>Data_Inicio</th>
					<th>Data_Fim</th>
					<th>Peso</th>
					<th>Embaralhar</th>
				</tr>
				</thead>
			<tdbody>";

			for ($i=0; $i < count($registros); $i++) {
				echo "<tr>";
				for ($j=0; $j < count($registros[$i]); $j++) {
					if($j == 3) echo "<td><a href='avaliacao.php?codigo=".$registros[$i][2]."'>".$registros[$i][$j]."</a></td>";
					else echo "<td>".$registros[$i][$j]."</td>";
				}

				echo "<tr>";
			}
			echo "</tbody>
			</table>";
		}

		function discprof_table ($registros) {
			echo "<table class='highlight centered responsive-table' border='1'>
			<thead class='black white-text'>
			<tr>
				<th>Matrícula</th>
				<th>Nome</th>
				<th>Código</th>
				<th>Disciplina</th>
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
</center>
</body>
</html>
