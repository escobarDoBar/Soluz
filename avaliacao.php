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
	date_default_timezone_set('America/Sao_Paulo');

	$registros = selectPDO_discaval($codigo, 'avaliacao');

	if($codigo != '') { 
			
		$cod_disciplina = $registros[0][0];
		$disciplina = $registros[0][1];
		$cod_avaliacao = $registros[0][2];
		$conteudo = $registros[0][3];
		$data_inicio = $registros[0][4];
		$data_fim = $registros[0][5];
		$peso = $registros[0][6];
		$embaralhar = $registros[0][7];

		$title = $disciplina." - ".$conteudo;

	} else {
		$title = '';
	}

?>
<html>
<head>
	<title><?php echo $title ?></title> 
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
		<div class="container">
			
			<?php if($codigo != '') { ?>

				<?php if(aval_ainda_disponivel()) {
					if($_SESSION['tipo'] == 'aluno') {
						$reg_disc_alun = selectPDO_discalun($cod_disciplina);
						$alunos_na_disciplina = array();
						for ($i=0; $i < count($reg_disc_alun); $i++) { 
							array_push($alunos_na_disciplina, $reg_disc_alun[$i][0]);
						}
						if(in_array($_SESSION['matricula'], $alunos_na_disciplina)) {
							?>
							<form action="avaliacao_responder.php" method="post">
								<input type="hidden" name="codigo" value="<?php echo $cod_avaliacao; ?>">
								<input type="hidden" name="matricula" value="<?php echo $_SESSION['matricula']; ?>">
								<button type="submit" class="btn waves-effect waves-light">Responder</button>
							</form>
							<?php
						}
					} 
				} 

				if (!aval_ainda_disponivel() && $_SESSION['tipo'] == 'aluno') {
					header("location:avaliacao_viewCorrecao_aluno.php?codigo=".$codigo);
				}

				// Restrição para que o botão "avaliacao_editar.php" seja mostrado apenas a professores
				if($_SESSION['tipo'] == 'professor') {
					if(prof_da_disciplina()) { // função está no fim do arquivo
						echo "<a class=\"btn waves-effect waves-light\" href=\"avaliacao_cadastro.php?codigo=".$codigo."\">Editar</a>";
						
						if(!aval_ainda_disponivel()) {
							echo "<a class=\"btn waves-effect waves-light\" href=\"resposta_discursivaCorrecao.php?codigo=".$codigo."\">Corrigir discursivas</a>";
							echo "<a class=\"btn waves-effect waves-light\" href=\"avaliacao_viewCorrecao_prof.php?codigo=".$codigo."\">Ver notas</a>";
						}	
					}
				}

				?>

				<div id="info-avaliacao">
				<?php
					echo "<p style='color:lightgrey'>#".$cod_avaliacao."</p>";
					echo "<p><b>Conteúdo:</b> ".$conteudo."</p>";
					echo "<p><b>Disponível entre</b> ".$data_inicio." <b>e</b> ".$data_fim."</p>";
					echo "<p><b>Peso: </b>".$peso."</p>";
					echo "<p><b>Embaralhar questões:</b> ".$embaralhar."</p>";
				?>
				</div>
				
				<hr/>			
				
				<div id="questoes">
					<?php
						gerar_formulario_questoes_visualizar($codigo); //função em funcoes
					?>
				</div>

			<?php } else { ?>

				<div id="erro_codigo">
					<p><b>Erro: </b> a página não recebeu nenhum código. Adicione, no final da URL, <code>?codigo=[codigo da disciplina]</code></p>
				</div>

			<?php } ?>
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


	function aval_ainda_disponivel() {
		$data_final=$GLOBALS['data_fim'];
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