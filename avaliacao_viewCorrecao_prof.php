<!DOCTYPE html>
<?php

	include 'valida_secao.php';

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
	include 'funcoes_correcao.php';
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

	if (aval_ainda_disponivel()) header("avaliacao.php?codigo=".$codigo);

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
	<header>
		<?php printHeader(); ?>
	</header>

	<main>
		<div class="container">
			
			<div id="info-avaliacao">
			<?php
				echo "<p><b>Disciplina:</b> ".$disciplina."</p>";
				echo "<p><b>Conteúdo:</b> ".$conteudo."</p>";
				echo "<p><b>Disponível entre</b> ".$data_inicio." <b>e</b> ".$data_fim."</p>";
				echo "<hr/>";
			?>

			</div>

			<div id="avaliacao-correcao">
				<?php notaTodasAvalProf_view($codigo); ?>
			</div>

			<?php
				function aval_ainda_disponivel() {
					$data_final=$GLOBALS['data_fim'];
					$data_atual=date('Y-m-d H:i:s');
				
					if(strtotime($data_final) > strtotime($data_atual)){
						return true;
					} else {
						return false;
					}
				}
			?>

			

		</div>
	</main>

	<!--  Scripts-->
	<script src="assets/js/jquery-2.1.1.min.js"></script>
	<script src="assets/js/materialize.min.js"></script>
	<script src="assets/js/init.js"></script>
</body>
</html>