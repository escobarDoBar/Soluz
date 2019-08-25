<!DOCTYPE html>
<?php

	require_once "autoload.php";

	if (isset($_POST['codigo'])) {
		$codigo = $_POST['codigo'];
	} else if (isset($_GET['codigo'])) {
		$codigo = $_GET['codigo'];
	} else {
		$codigo = '';
	}

	include 'disciplinas_pdo.php';
	include 'avaliacao_pdo.php';
	include 'funcoes.php';
	include 'conf.php';
	include 'avaliacao_funcaoFormulario.php';
	include 'funcoes_correcao.php';
	date_default_timezone_set('America/Sao_Paulo');

	$registros = selectPDO_discaval($codigo, 'avaliacao');

	if(isset($codigo)) {
		//echo "Código: ".$codigo;
		$registros = selectPDO_disc('Codigo_Disciplina', $codigo); 
		$nome = $registros[0][1];
		$codigo_serie = $registros[0][2];
		$serie = $registros[0][3];
	} else {
		header("location:index.php");
	}

?>
<html>
<head>
	<title>Boletim da disciplina</title> 
	<meta charset="utf-8">

	<!-- Compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

	<!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<style type="text/css">
		select { display: block; } /*Tive que colocar, porque por padrão o select estava com display:none por algum motivo*/
	</style>

	<!-- Compiled and minified JavaScript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

	<!-- CSS -->
	<link href="assets/css/login/login.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
	<header> <?php printHeader(); ?> </header>

	<main>	
		<?php
			$notas = notasDisciplinaAluno($codigo, $_SESSION['matricula']);
			$avaliacoes = select_avaliacoes($codigo);						
		?>
		<div class="container">
			<div class="card-panel">
				<p>OBS: Algumas notas podem estar baixas porque o(a) professor(a) ainda não corrigiu todas as respostas.</p>
				<table class="highlight table-responsive centered">
					<thead>
						<tr>
							<th>Conteúdo</th>
							<th>Data final</th>
							<th>Nota</th>
						</tr>
					</thead>
					<tbody>
						<?php
						for ($i=0; $i < count($notas); $i++) { 
							$cor_nota = $notas[$i] >= 7 ? ' green lighten-2 ' : ' red lighten-2 ';
							if(aval_ainda_disponivel($avaliacoes[$i][1])) {
								$cor_data = 'yellow lighten-3';
							} else $cor_data = '';

							echo "<tr>";
								echo "<td>".$avaliacoes[$i][0]."</td>";
								echo "<td class='".$cor_data."'>".$avaliacoes[$i][1]."</td>";
								if(aval_ainda_disponivel($avaliacoes[$i][1])) {
									echo "<td> -- </td>";
								} else {
									echo "<td class='".$cor_nota."'> ".$notas[$i]." </td>";
								}
							echo "</tr>";
						}

						$media = mediaDisciplinaAluno($codigo, $_SESSION['matricula']);
						$cor_media = $media >= 7 ? ' green lighten-2 ' : 'red lighten-2 ';
						echo "<tr>";
							echo "<td></td>";
							echo "<td><b>Média</b></td>";
							echo "<td class='".$cor_media."'>".$media."</td>";
						echo "</tr>";
						?>
					</tbody>
				</table>
			</div>
		</div>
	</main>

	<footer class="page-footer">
		<?php printFooter(); ?>
	</footer>

	<!--  Scripts-->
	<script src="assets/js/jquery-2.1.1.min.js"></script>
	<script src="assets/js/materialize.min.js"></script>
	<script src="assets/js/init.js"></script>
	
</body>
</html>

<?php

	function select_avaliacoes($codigo) {
		$pdo = Conexao::getInstance();

		$sql = "SELECT Conteudo, Data_Fim FROM ".$GLOBALS['tb_avaliacoes']." WHERE Disciplina_Codigo_Disciplina = ".$codigo." ORDER BY Codigo_Avaliacao";
		//echo $sql;
		$avaliacoes = array();
		$query = $pdo->query($sql);
		for ($i=0; $row = $query->fetch(PDO::FETCH_ASSOC); $i++) {
			$avaliacoes[$i] = array();
			array_push($avaliacoes[$i], $row['Conteudo']);
			array_push($avaliacoes[$i], $row['Data_Fim']);
		}

		return $avaliacoes;
	}

	function aval_ainda_disponivel($data_final) {
		$data_atual=date('Y-m-d H:i:s');

		//echo "Data final: ".$data_final."<br/>";
		//echo "Data atual: ".$data_atual."<br/>";
		//echo "strtotime(Data Final): ".strtotime($data_final)."<br/>";
		//echo "strtotime(Data Atual): ".strtotime($data_atual)."<br/>";
		
		if(strtotime($data_final) > strtotime($data_atual)){
			return true;
		} else {
			return false;
		}		
	}

	?>