<?php
	require_once "autoload.php";

	include 'valida_secao.php';
	include 'conf.php';
	include 'funcoes.php';

	if (isset($_POST['codigo'])) { $codigo = $_POST['codigo']; }
	else if (isset($_GET['codigo'])) { $codigo = $_GET['codigo']; }
	else { header("location:index.php"); }


	
	if ($_SESSION['tipo'] != 'professor') {
		//header("location:avaliacao.php?codigo=".$codigo);
		echo 'seçao correta';
	} else {
		if (!prof_da_disciplina()) {
			header("location:avaliacao.php?codigo=".$codigo);
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Correção de respostas discursivas</title> 
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
			<div id="form_correcao_respostas" class="container">
				<?php
					$pdo = Conexao::getInstance();

					// Seleciona as questões da avaliação, para depois selecionar as respostas de cada
					$query1 = "SELECT Q.Codigo_Questao, Q.Texto, Q.Enunciado
					FROM Questao Q, Questoes_has_Avaliacoes AQ
					WHERE AQ.Questoes_Codigo_Questao = Q.Codigo_Questao
					AND Tipo_Codigo = 1
					AND AQ.Avaliacoes_Codigo_Avaliacao = ".$codigo;
					$consulta = $GLOBALS['pdo']->query($query1);
					$questoes = array();
					for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
						$questoes[$i] = array();
						array_push($questoes[$i], $linha['Codigo_Questao']);
						array_push($questoes[$i], $linha['Texto']);
						array_push($questoes[$i], $linha['Enunciado']);
					}

					// Para cada questão, seleciona as respostas e gera os formulários de correção
					for ($i=0; $i < count($questoes); $i++) { 
						$query2 = "SELECT D.Codigo_Discursiva, D.Resposta, D.Correta
						FROM Discursiva D, Questao Q
						WHERE Q.Codigo_Questao = D.Questao_Codigo
						AND Q.Codigo_Questao = ".$questoes[$i][0];

						$consulta = $GLOBALS['pdo']->query($query2);
						$respostas = array();
						for ($j = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $j++) {
							$respostas[$j] = array();
							array_push($respostas[$j], $linha['Codigo_Discursiva']);
							array_push($respostas[$j], $linha['Resposta']);
							array_push($respostas[$j], $linha['Correta']);
						}

						echo "<div class='card-panel questao grey darken-3 white-text'>";
							echo "<p><b>Questão</b> #".$questoes[$i][0]."</p>";
							if(isset($questoes[$i][1])) { echo "<p><b>Texto: </b>".$questoes[$i][1]."</p>"; }
							echo "<p><b>Enunciado: </b>".$questoes[$i][2]."</p>";
						echo "</div>";

						// Criação formulário correção respostas
						for ($j=0; $j < count($respostas); $j++) {
							switch ($respostas[$j][2]) {
								case 0:
									$correcao = 'Errado';
									$cor = ' red-text text-darken-2 ';
									break;
								case 1:
									$correcao = 'Meio certo';
									$cor = ' yellow-text text-darken-3 ';
									break;
								case 2:
									$correcao = 'Certo';
									$cor = ' green-text text-darken-2 ';
									break;
							}
							echo "<div class='resposta card-panel'>";
							echo "<form action='resposta_pdo.php' method='post'>";
								echo "<div class='row'>";
								echo "<div class='col s12 m9'>";	
									echo "<p><b>Resposta do aluno: </b> ".$respostas[$j][1]."</p>";
									if(isset($respostas[$j][2])) { echo "<p><b>Correção: </b> <b class='".$cor."'>".$correcao."</b></p>"; }
									echo "<input type='hidden' name='cod_discursiva' value='".$respostas[$j][0]."'>";
									echo "<input type='hidden' name='acao' value='addCorrecaoDiscursiva'>";
									echo "<input type='hidden' name='cod_avaliacao' value='".$codigo."'>";
								echo "</div>";

								echo "<div class='s12 m3 right-align'>";
									echo "<button type='submit' name='correcao' value='0' class='btn red darken-2 text-white'><i class='material-icons left'>close</i> Errado</button> <br/><br/>";
									echo "<button type='submit' name='correcao' value='1' class='btn yellow darken-3 text-white'><i class='material-icons left'>waves</i> Meio certo</button> <br/><br/>";
									echo "<button type='submit' name='correcao' value='2' class='btn green darken-2 text-white'><i class='material-icons left'>done</i> Certo</button> <br/><br/>";
								echo "</div>";
								echo "</div>";
							echo "</form>";
							echo "</div>";
						}
					}
				?>
			</div>
		</main>

		<footer>
		</footer>
	</body>
</html>

<?php
	function prof_da_disciplina() {
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