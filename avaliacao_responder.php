<!DOCTYPE html>
<?php
	session_start();

	require_once "autoload.php";

	include 'disciplinas_pdo.php';
	include 'avaliacao_pdo.php';
	include 'funcoes.php';
	include 'conf.php';
	include 'avaliacao_funcaoFormulario.php';
	date_default_timezone_set('America/Sao_Paulo');

	if (isset($_POST['codigo'])) { $codigo = $_POST['codigo']; }
	else if (isset($_GET['codigo'])) { $codigo = $_GET['codigo']; }
	else { $codigo = ''; }

	if (isset($_POST['res_alert'])) { $res_alert = $_POST['res_alert']; }
	else if (isset($_GET['res_alert'])) { $res_alert = $_GET['res_alert']; }

	//echo "Código=".$codigo."=";

	$matricula = $_SESSION['matricula'];

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
			<div id="info-avaliacao">

			<?php
				echo "<p><b>Aluno:</b> ".$_SESSION['nome']."</p>";
				echo "<p><b>Matrícula:</b> ".$_SESSION['matricula']."</p>";
				$embaralharTxt = $embaralhar == 1 ? 'Sim' : 'Não';
				echo "<p><b>Conteúdo:</b> ".$conteudo."
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<small style='color:grey'>#".$cod_avaliacao."</small></p>";	
				echo "<p><b>Disponível entre</b> ".$data_inicio." <b>e</b> ".$data_fim."</p>";
				echo "<p><b>Peso: </b>".$peso."</p>";
				echo "<p><b>Embaralhar questões:</b> ".$embaralharTxt."</p>";
			?>
			</div>
			<hr/>

			<div id="info-ao-aluno">
				<p>Caso você já tenha respondido certa questão mas queira mudar a resposta, basta respondê-la novamente e o sistema a atualizará.</p>
			</div>
			<hr/>

			<div id="formulario-questoes">
				<?php
					if(isset($res_alert)) {
						echo "<div class='card-panel green darken-5 green-text text-lighten-5 center-align'>";
						if ($res_alert == 'registrada') echo "<b>A resposta foi registrada.</b>";
						else if ($res_alert = 'atualizada') echo "<b>A resposta foi atualizada.</b>";
						echo "</div>";
					}
				?>
				<?php
					gerar_formulario_questoes($cod_avaliacao, $embaralhar, $_SESSION['matricula']);
					// função em avaliacao_funcaoFormulario.php
				?>
			</div>
		</div>
	</main>

	<footer>
	</footer>

	<!--  Scripts-->
	<script src="assets/js/jquery-2.1.1.min.js"></script>
	<script src="assets/js/materialize.min.js"></script>
	<script src="assets/js/init.js"></script>
</body>
</html>