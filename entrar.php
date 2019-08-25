<!DOCTYPE html>
<?php
	include 'funcoes.php';

	$matricula = isset($_POST['matricula']) ? $_POST['matricula'] : '';
	$senha = isset($_POST['senha']) ? sha1($_POST['senha']) : '';
	$tipo_usuario = isset($_POST['tipo_usuario']) ? $_POST['tipo_usuario'] : '';

	if (isset($_POST['erro'])) { $erro = $_POST['erro']; }
	else if (isset($_GET['erro'])) { $erro = $_GET['erro']; }
?>

<html lang="pt-br">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

	<title>Entrar - Prove</title>
</head>

<body>
	<?php printHeader(); ?>

	<br><br>
			<div class="container">
				<h1 class="text-center display-4">Faça login para continuar</h1><br><br>
				<div class="login">
					<p id='msg'></p>
					<?php
						if(isset($_SESSION['msg'])){
							echo $_SESSION['msg'];
							unset($_SESSION['msg']);
						}
						if(isset($_SESSION['msgcad'])){
							echo $_SESSION['msgcad'];
							unset($_SESSION['msgcad']);
						}
					?>
	        			<form method="POST" action="">





									  <div class="form-row">
									    <div class="form-group col-md-6">
									      <label for="usuario">Nome de Usuário</label>
									      <input type="text" class="form-control" name="matricula" id="matricula" placeholder="Matrícula">
									    </div>
									    <div class="form-group col-md-6">
									      <label for="senha">Senha</label>
									      <input type="password" class="form-control" name="senha" id="senha" placeholder="Senha">
									    </div>
									  </div>
			                <label>
											  <input type="radio" class="with-gap tipo_usuario" value="aluno" name="tipo_usuario">
												<span>Aluno</span>
											</label>
											<br>
											<label>
											  <input type="radio" class="with-gap tipo_usuario" value="professor" name="tipo_usuario">
												<span>Professor</span>
											</label>
											<br>
											<?php if(isset($erro)) { if($erro == '1') { ?>
						            <br/>
												<div class="alert alert-danger" role="alert">
												  <b>Erro:</b> a senha informada está incorreta.
												</div>
											<?php } } ?>
											<br>
									  <button type="submit" name="acao" value="login" class="btn btn-primary">Entrar</button><br><br>

							Ainda não é inscrito? <a class="login__signup" href="cadastre-se.php"><strong>Cadastre-se.</strong></a>

				<?php
					if($matricula != '' && $senha != '') {
						if (isset($_GET['acao'])) $acao = $_GET['acao'];
						else if (isset($_POST['acao'])) $acao = $_POST['acao'];
						else $acao = '';

						if ($acao == 'login') {
							if($tipo_usuario == 'aluno'){
								include 'alunos_pdo.php';
								$tipo='aluno';
								$linha_usuario = selectPDO_alun('Matricula',$matricula);
							}
							else if($tipo_usuario == 'professor'){
								include 'professores_pdo.php';
								$tipo='professor';
								$linha_usuario = selectPDO_prof('Matricula',$matricula);
							}


							//var_dump($linha_usuario);

							if ($senha == $linha_usuario[0][1]) {
								session_start();
								$_SESSION['matricula'] = $matricula;
								$_SESSION['nome'] = $linha_usuario[0][2];
								$_SESSION['tipo'] = $tipo;
								header("location:index.php");
							} else {
								header("location:entrar.php?erro=1");
							}
						}
					}
				?>

			</div>
		</div>

	</center>
	</main>

	<!--  Scripts-->
	<script src="assets/js/jquery-2.1.1.min.js"></script>
	<script src="assets/js/materialize.min.js"></script>
	<script src="assets/js/init.js"></script>

	</body>

</html>
