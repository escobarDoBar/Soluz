<!DOCTYPE html>
<?php
	require_once "autoload.php";

	include 'valida_secao.php';
	include 'funcoes.php';
	include 'disciplinas_pdo.php';

?>
<html lang="pt-br">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

	<title>Home - Prove</title>
</head>

<body>
	<?php printHeader(); ?>

	<main>
		<div class="container"><br><br>

			<?php
				echo "<p class='text-center lead'>Bem vindo, <b>".$_SESSION['nome']."</b>.<br>Matrícula: <b>".$_SESSION['matricula']."</b>.<br>	Você é <b>".$_SESSION['tipo']."</b>.</p>";
			?>
			<?php if($_SESSION['tipo'] == 'aluno') { ?>



			<?php } ?>

			<?php if($_SESSION['tipo'] == 'professor' || $_SESSION['tipo'] == 'aluno') {

				$disciplinas = selectDisciplinas($_SESSION['matricula'], $_SESSION['tipo']);
				//var_dump($disciplinas);
				$contador=count($disciplinas);


				if($_SESSION['tipo'] == 'professor') {?>
					<br><br>
					<div class="text-center">
						<a class="btn btn-primary btn-lg" href="serie_cadastro.php" role="button">+ Nova Turma</a>
						<a class="btn btn-primary btn-lg" href="disciplina_cadastro.php" role="button">+ Nova Disciplina</a>
						<a class="btn btn-primary btn-lg" href="avaliacao_cadastro.php" role="button">+ Nova Avaliação</a>
					</div><br><br>
				<?php } ?>

				<h1 class="display-4 text-center">Disciplinas:</h1><br><br>


					<?php
						echo "<div class='row flex-wrap'>";
						if (isset($disciplinas) && $contador > 0) {
							for ($i=0; $i < $contador ; $i++) {
								$cod = $disciplinas[$i][0];
								$nome = $disciplinas[$i][1];
								$serie = $disciplinas[$i][2];
								echo "<div class='card bg-light mb-3 text' style='max-width: 18rem;'>";
									echo "<div class='card-header text-center'><b><a href='disciplina.php?codigo=".$cod."'>".$serie."</a></b></div>";
									echo "<div class='card-body'>";
										echo "<h5 class='card-title'><a href='disciplina.php?codigo=".$cod."'>".$nome."</h5>";
									echo "</div>";
								echo "</div>";
							}
						}
						echo "</div>";
					?>
					<!-- <div class="card bg-light mb-3" style="max-width: 18rem;">
					  <div class="card-header">Header</div>
					  <div class="card-body">
					    <h5 class="card-title">Light card title</h5>
					    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
					  </div>
					</div> -->


			<?php } ?>


		<a class="btn btn-danger" href="logoff.php" role="button">Sair</a>
		</div>
	</main>

	<!--  Scripts-->
	<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
	<script src="assets/js/materialize.min.js"></script>
	<script src="assets/js/init.js"></script>

	<?php
		function selectDisciplinas($matricula,$tipo) {
			$pdo = Conexao::getInstance();

			try {
				if($tipo == 'aluno') {
					$sql = 'select D.Codigo_Disciplina, D.Nome, S.Descricao FROM '.$GLOBALS['tb_alunos'].' A, '.$GLOBALS['tb_disciplinas'].' D, '.$GLOBALS['tb_disc_alun'].' AD, Serie S
					WHERE AD.Alunos_Matricula = A.Matricula
					AND AD.Disciplina_Codigo_Disciplina = D.Codigo_Disciplina
					AND A.Matricula = \''.$matricula.'\'
					AND S.Codigo_Serie = D.Serie_Codigo_Serie ORDER BY S.Codigo_Serie';
				} else {
					$sql = 'select D.Codigo_Disciplina, D.Nome, S.Descricao FROM '.$GLOBALS['tb_professores'].' P, '.$GLOBALS['tb_disciplinas'].' D, '.$GLOBALS['tb_disc_prof'].' PD, Serie S
					WHERE PD.Professores_Matricula = P.Matricula
					AND PD.Disciplina_Codigo_Disciplina = D.Codigo_Disciplina
					AND P.Matricula = \''.$matricula.'\'
					AND S.Codigo_Serie = D.Serie_Codigo_Serie ORDER BY S.Codigo_Serie';
				}

				//var_dump($sql); echo "<br>";

				$consulta = $pdo->query($sql);

				$registros = array();

				for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
					$registros[$i] = array();
					array_push($registros[$i], $linha['Codigo_Disciplina']);
					array_push($registros[$i], $linha['Nome']);
					array_push($registros[$i], $linha['Descricao']);
				}

				return $registros;
			} catch (PDOException $e) {
				echo "Erro: ".$e->getMessage();
			}
		}
	?>
	</body>

</html>
