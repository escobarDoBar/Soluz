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

	if(!prof_da_disciplina()) {
		header("location:disciplina.php?codigo=".$codigo);
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

	<?php
		$avaliacoes = select_avaliacoes($codigo);

		$alunos = select_alunos($codigo);

		//var_dump($alunos); echo "<br/>"; var_dump($avaliacoes);
	?>

	<main>
		<div class="container">
			<div class="card-panel">
				<table class="highlight table-responsive centered">
					<thead>
						<tr>
							<th>Aluno</th>
							<?php
								for ($i=0; $i < count($avaliacoes); $i++) { 
									echo "<th>".$avaliacoes[$i][1]."</th>";
								}
								echo "<th>MÉDIA</th>";
							?>
						</tr>
					</thead>
					<tbody>
						<?php
							for ($i=0; $i < count($alunos); $i++) { 
								echo "<tr>";
									echo "<td><b>".$alunos[$i][1]."</b></td>";

									

									$soma_notas = 0;
									$cnt_notas = 0;
									for ($j=0; $j < count($avaliacoes); $j++) { 
										$nota = notaAvaliacao($avaliacoes[$j][0], $alunos[$i][0]);
										$soma_notas += $nota;
										$cnt_notas++;
										if ($nota >= 7) { $cor = ' green lighten-3 '; } else { $cor = ' red lighten-3'; }
										echo "<td class='".$cor."'>".$nota."</td>";
									}
									$media = round(($soma_notas / $cnt_notas),1);

									if ($media >= 7) { $cor = ' green lighten-3 '; } else { $cor = ' red lighten-3'; }
									echo "<td class='".$cor."'>".$media."</td>";


								echo "</tr>";
							}
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

		$sql = "SELECT Codigo_Avaliacao, Conteudo FROM ".$GLOBALS['tb_avaliacoes']." WHERE Disciplina_Codigo_Disciplina = ".$codigo." ORDER BY Codigo_Avaliacao";
		//echo $sql;
		$avaliacoes = array();
		$query = $pdo->query($sql);
		for ($i=0; $row = $query->fetch(PDO::FETCH_ASSOC); $i++) {
			$avaliacoes[$i] = array();
			array_push($avaliacoes[$i], $row['Codigo_Avaliacao']);
			array_push($avaliacoes[$i], $row['Conteudo']);
		}

		return $avaliacoes;
	}

	function select_alunos($codigo) {
		$pdo = Conexao::getInstance();

		$sql = "SELECT DA.Alunos_Matricula, A.Nome FROM Disciplina_has_Alunos DA, Alunos A WHERE DA.Disciplina_Codigo_Disciplina = ".$codigo." AND DA.Alunos_Matricula = A.Matricula ORDER BY DA.Alunos_Matricula";
		//echo $sql;
		$query = $pdo->query($sql);
		$matriculas = array();
		for ($i=0; $row = $query->fetch(PDO::FETCH_ASSOC); $i++) { 
			$matriculas[$i] = array();
			array_push($matriculas[$i], $row['Alunos_Matricula']);
			array_push($matriculas[$i], $row['Nome']);
		}

		return $matriculas;
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

	function prof_da_disciplina() {
		//echo $_SESSION['matricula'];
		$pdo = Conexao::getInstance();

		try {
			// Primeiro se consulta o código da disciplina da qual a avaliação faz parte...
			$query1 = "SELECT Disciplina_Codigo_Disciplina FROM ".$GLOBALS['tb_avaliacoes']." WHERE Codigo_Avaliacao = ".$GLOBALS['codigo'];
			//var_dump($query1);

			$consulta = $pdo->query($query1);

			for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
				$cod_disciplina = $linha['Disciplina_Codigo_Disciplina'];
			}

			// ... então se consulta se o professor está nessa disciplina
			$query2 = "SELECT P.Matricula ";
			$query2 .= " FROM Professores P, Disciplinas D, Professores_has_Disciplina DP "; 
			$query2 .= " WHERE P.Matricula = DP.Professores_Matricula ";
			$query2 .= " AND D.Codigo_Disciplina = DP.Disciplina_Codigo_Disciplina ";
			$query2 .= " AND D.Codigo_Disciplina = ".$cod_disciplina;
			
			$consulta = $pdo->query($query2);

			$matriculas = array();
			for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
				array_push($matriculas, $linha['Matricula']);
			}

			//var_dump($matriculas);

			if (in_array($_SESSION['matricula'], $matriculas)) {
				return 1;
			} else {
				return 0;
			}
		} catch (PDOException $e) {
			echo "Erro: ".$e->getMessage();
		}
	}

	?>