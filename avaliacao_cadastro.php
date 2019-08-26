<!DOCTYPE html>

<?php

	require_once "autoload.php";

	include 'disciplinas_pdo.php';
	include 'avaliacao_pdo.php';
	include 'funcoes.php';
	include 'conf.php';
	include 'avaliacao_funcaoFormulario.php';

	if (isset($_POST['codigo']) || isset($_GET['codigo']))
	{
		$acao = 'Editar';
		if (isset($_POST['codigo']))
		{
			$codigo = $_POST['codigo'];
		} else
		{
			$codigo = $_GET['codigo'];
		}
	} else
	{
		$acao = 'Cadastrar';
	}

	$cod_disciplina='';
	$disciplina='';
	$cod_avaliacao='';
	$conteudo='';
	$data_inicio='';
	$data_fim='';
	$peso='';
	$embaralhar='';
	if ($acao == 'Editar' && (isset($_POST['codigo']) || isset($_GET['codigo'])) )
	{
		$registros = selectPDO_discaval($codigo, 'avaliacao');
		$cod_disciplina = $registros[0][0];
		$disciplina = $registros[0][1];
		$cod_avaliacao = $registros[0][2];
		$conteudo = $registros[0][3];
		$data_inicio = $registros[0][4];
		$data_fim = $registros[0][5];
		$peso = $registros[0][6];
		$embaralhar = $registros[0][7];
	}
?>

<html lang="pt-br">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

	<title><?php echo $acao; ?>Soluz</title>
</head>

<body>
	<header>
		<?php printHeader(); ?>
	</header>

	<main>

		<center>
			<div class="container">
				<div class="jumbotron">
						<br><br>
						<h3 class="display-4 prove-text"><?php echo $acao; ?> Prova</h3><br><br>
					<?php if($acao == 'Editar')
					{ ?>
						<a class="btn waves-light waves-effect" href="avaliacao.php?codigo=<?php echo $codigo ?>">Voltar para prova</a>
					<?php } ?>
					<div class="col s12 container">
						<form action="avaliacao_pdo.php" method="POST">
							<input type="hidden" name="Codigo_Avaliacao" id="Codigo_Avaliacao" value="<?php echo $cod_avaliacao; ?>">

								<div class="form-group">
									<label for="email">Conteúdo</label>
									<input type="text" class="form-control" id="conteudo" name="conteudo" placeholder="Exemplo: 3 Info" value="<?php echo $conteudo; ?>">
								</div>
								<div class="form-group">
										Embaralhar questões
								<input type="checkbox" name="embaralhar" value="<?php echo $embaralhar; ?>">
								</div>

							<?php if($acao == 'Cadastrar')
							{ ?>
								<div id="data-cadastro">
									<div class="row">
										<div class="input-field col s12 m6">
											<input id="dataInicio" name="dataInicio" type="text" class="datepicker" />
											<label for="dataInicio">Data de início</label>
										</div>
										<div class="input-field col s12 m6">
											<input id="horarioInicio" name="horarioInicio" type="text" class="timepicker" />
											<label for="horarioInicio">Horário de início</label>
										</div>
									</div>
									<div class="row">
										<div class="input-field col s12 m6">
											<input id="dataFinal" name="dataFinal" type="text" class="datepicker" />
											<label for="dataFinal">Data final</label>
										</div>
										<div class="input-field col s12 m6">
											<input id="horarioFinal" name="horarioFinal" type="text" class="timepicker" />
											<label for="horarioFinal">Horário final</label>
										</div>
									</div>
								</div>
							<?php } else if ($acao == 'Editar')
							{ ?>
								<div id="data-editar">
									<div class="row">
										<div class="input-field col s12 m6">
											<input type="text" name="dataInicio" id="dataInicio" value="<?php echo $data_inicio; ?>">
											<label for="dataInicio">Data e hora de início (AAAA-MM-DD HH:mm:SS)</label>
										</div>
										<div class="input-field col s12 m6">
											<input type="text" name="dataFim" id="dataFim" value="<?php echo $data_fim; ?>">
											<label for="dataFim">Data e hora de fim (AAAA-MM-DD HH:mm:SS)</label>
										</div>
									</div>
								</div>
							<?php } ?>

							<div class="row">
								<div class="input-field col s12">
									<?php gerarSelect($tb_disciplinas, 'Disciplina_Codigo_Disciplina', 0, 'Codigo_Disciplina', 'Nome'); ?>
									<span class="helper-text">Disciplina</label>
								</div>
							</div>

							<div class="row">
								<div class="col s12 center">
									<br><br>
									<button type="submit" name="acao" value="<?php echo $acao; ?>" class="btn btn-primary"><?php echo $acao; ?></button>
								</div>
							</div>
						</form>
					</div>


				<?php if($acao == 'Editar')
				{ ?>
					<div class="row">
						<div class="col s12">
							<h4 class="title left prove-text text-verde">Questões</h4>
						</div>
					</div>
					<div class="row">
						<div class="col s12">
							<div class="card-panel">
								<div class="row">
									<h6 class="title prove-text">Adicionar questão</h6>
									<ul class="tabs tabs-fixed-width">
										<li class="tab col s3"><a href="#tab-discursiva">Discursiva</a></li>
										<li class="tab col s3"><a href="#tab-unica">Única Escolha</a></li>
										<li class="tab col s3"><a href="#tab-multipla">Múltipla Escolha</a></li>
									</ul>
									<div id="tab-discursiva" class="col s12">
										<form action="avaliacao_pdo.php" method="POST">
											<input type="hidden" name="Tipo_Codigo" id="Tipo_Codigo" value="1">
											<input type="hidden" name="cod_avaliacao" id="cod_avaliacao" value="<?php echo $codigo; ?>">
											<div class="row">
												<div class="input-field col s12 margin-top-thin">
													<input id="enunciado" name="enunciado" type="text" class="validate">
													<label for="enunciado">Enunciado</label>
												</div>
												<div class="input-field col s12">
													<input id="texto" name="texto" type="text" class="validate">
													<label for="texto">Texto de apoio (opcional)</label>
												</div>
												<div class="col s12 center">
													<button type="submit" name="acao" value="cadastrar_questao" class="waves-effect waves-light btn">Adicionar</a>
												</div>
											</div>
										</form>
									</div>
									<div id="tab-unica" class="col s12">
										<form action="avaliacao_pdo.php" method="POST">
											<input type="hidden" name="Tipo_Codigo" id="Tipo_Codigo" value="2">
											<input type="hidden" name="cod_avaliacao" id="cod_avaliacao" value="<?php echo $codigo; ?>">
											<div class="row">
												<div class="input-field col s12 margin-top-thin">
													<input id="enunciado" name="enunciado" type="text" class="validate">
													<label for="enunciado">Enunciado</label>
												</div>
												<div class="input-field col s12">
													<input id="texto" name="texto" type="text" class="validate">
													<label for="texto">Texto de apoio (opcional)</label>
												</div>
												<div class="col s12">
													<div class="row" id="unicaContainer">
													</div>
												</div>
												<input type="hidden" id="qtdUnica" name="qtdUnica" value="0" />
												<div class="col s12 center">
													<button type="submit" name="acao" value="cadastrar_questao" class="margin-top-thin waves-effect waves-light btn">Adicionar</a>
												</div>
												<div class="col s12 m6">
													<button type="button" onclick="alternativaUnicaAdicionar()" class="left waves-effect prove verde waves-light btn">Adicionar alternativa</a>
												</div>
												<div class="col s12 m6">
													<button type="button" onclick="alternativaUnicaRemover()" class="right waves-effect prove vermelho waves-light btn">Remover alternativa</a>
												</div>
											</div>
										</form>
									</div>
									<div id="tab-multipla" class="col s12">
										<form action="avaliacao_pdo.php" method="POST">
											<input type="hidden" name="Tipo_Codigo" id="Tipo_Codigo" value="3">
											<input type="hidden" name="cod_avaliacao" id="cod_avaliacao" value="<?php echo $codigo; ?>">
											<div class="row">
												<div class="input-field col s12 margin-top-thin">
													<input id="enunciado" name="enunciado" type="text" class="validate"/>
													<label for="enunciado-multipla">Enunciado</label>
												</div>
												<div class="input-field col s12">
													<input id="texto" name="texto" type="text" class="validate"/>
													<label for="texto-multipla">Texto de apoio (opcional)</label>
												</div>
												<div class="col s12">
													<div class="row" id="multiplaContainer">
													</div>
												</div>
												<input type="hidden" id="qtdMultipla" name="qtdMultipla" value="0" />
												<div class="col s12 center">
													<button type="submit" name="acao" value="cadastrar_questao" class="margin-top-thin waves-effect waves-light btn">Adicionar</a>
												</div>
												<div class="col s12 m6">
													<button type="button" onclick="alternativaMultiplaAdicionar()" class="left waves-effect prove verde waves-light btn">Adicionar alternativa</a>
												</div>
												<div class="col s12 m6">
													<button type="button" onclick="alternativaMultiplaRemover()" class="right waves-effect prove vermelho waves-light btn">Remover alternativa</a>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<div>
					<?php
						if (isset($codigo))
						{
							gerar_formulario_questoes_visualizar($codigo, 'professor');
						}
					?>
				</div>

			</div>
	</main>

	<!--  Scripts-->
	<script src="assets/js/materialize.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			var elems = document.querySelectorAll('.timepicker');
			var options = {
					i18n: {
							cancel: 'Cancelar',
							clear: 'Limpar',
							done: 'Ok'
					},
					twelveHour: false
			}
			var instances = M.Timepicker.init(elems , options);
			var elems = document.querySelectorAll('select');
			var instances = M.FormSelect.init(elems);
			var tabs = document.querySelectorAll('.tabs');
			for (var i = 0; i < tabs.length; i++){
				M.Tabs.init(tabs[i]);
			}
			var elems = document.querySelectorAll('.datepicker');
			var data = new Date();
			var options = {
				format: 'dd/mm/yyyy',
				yearRange: [data.getFullYear(), data.getFullYear() + 1],
				minDate: data,
				i18n: {
					cancel: 'Cancelar',
					clear: 'Limpar',
					months: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
					monthsShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
					weekdays: ['Domingo', 'Segunda Feira', 'Terça Feira', 'Quarta Feira', 'Quinta Feira', 'Sexta Feira', 'Sábado'],
					weekdaysShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
					weekdaysAbbrev: ['D','S','T','Q','Q','S','S']
				}
			};
			var instances = M.Datepicker.init(elems, options);
		});
		function CriaRequest() {
			try {
				request = new XMLHttpRequest();
			} catch (IEAtual){
			try{
				request = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(IEAntigo){
				try{
				 request = new ActiveXObject("Microsoft.XMLHTTP");
				} catch(falha){
					request = false;
				}
			}
		}
		if (!request)
			alert("Seu Navegador não suporta o Prove! Atualize ou mude de navegador");
		else
			return request;
		}
		function alternativaUnicaAdicionar () {
			var xmlreq = CriaRequest();
			var qtdUnica = parseInt(document.getElementById('qtdUnica').value) + 1;
			var unicaContainer = document.getElementById('unicaContainer');
			unicaContainer.innerHTML = '<div class="progress"><div class="indeterminate"></div></div>';
			xmlreq.open("GET", "alternativa-unica.php?qtdUnica=" + qtdUnica, true);
			xmlreq.onreadystatechange = function(){
				if (xmlreq.readyState == 4) {
					if (xmlreq.status == 200) {
						parseInt(document.getElementById('qtdUnica').value = qtdUnica);
						unicaContainer.innerHTML = xmlreq.responseText;
					} else{
						unicaContainer.innerHTML = "Erro: " + xmlreq.statusText;
					}
				}
			};
			xmlreq.send(null);
		}
		function alternativaUnicaRemover () {
			var xmlreq = CriaRequest();
			var qtdUnica = parseInt(document.getElementById('qtdUnica').value) - 1;
			var unicaContainer = document.getElementById('unicaContainer');
			unicaContainer.innerHTML = '<div class="progress"><div class="indeterminate"></div></div>';
			xmlreq.open("GET", "alternativa-unica.php?qtdUnica=" + qtdUnica, true);
			xmlreq.onreadystatechange = function(){
				if (xmlreq.readyState == 4) {
					if (xmlreq.status == 200) {
						parseInt(document.getElementById('qtdUnica').value = qtdUnica);
						unicaContainer.innerHTML = xmlreq.responseText;
					} else{
						unicaContainer.innerHTML = "Erro: " + xmlreq.statusText;
					}
				}
			};
			xmlreq.send(null);
		}
		function alternativaMultiplaAdicionar () {
			var xmlreq = CriaRequest();
			var qtdMultipla = parseInt(document.getElementById('qtdMultipla').value) + 1;
			var multiplaContainer = document.getElementById('multiplaContainer');
			multiplaContainer.innerHTML = '<div class="progress"><div class="indeterminate"></div></div>';
			xmlreq.open("GET", "alternativa-multipla.php?qtdMultipla=" + qtdMultipla, true);
			xmlreq.onreadystatechange = function(){
				if (xmlreq.readyState == 4) {
					if (xmlreq.status == 200) {
						parseInt(document.getElementById('qtdMultipla').value = qtdMultipla);
						multiplaContainer.innerHTML = xmlreq.responseText;
					} else{
						multiplaContainer.innerHTML = "Erro: " + xmlreq.statusText;
					}
				}
			};
			xmlreq.send(null);
		}
		function alternativaMultiplaRemover () {
			var xmlreq = CriaRequest();
			var qtdMultipla = parseInt(document.getElementById('qtdMultipla').value) - 1;
			var multiplaContainer = document.getElementById('multiplaContainer');
			multiplaContainer.innerHTML = '<div class="progress"><div class="indeterminate"></div></div>';
			xmlreq.open("GET", "alternativa-multipla.php?qtdMultipla=" + qtdMultipla, true);
			xmlreq.onreadystatechange = function(){
				if (xmlreq.readyState == 4) {
					if (xmlreq.status == 200) {
						parseInt(document.getElementById('qtdMultipla').value = qtdMultipla);
						multiplaContainer.innerHTML = xmlreq.responseText;
					} else{
						multiplaContainer.innerHTML = "Erro: " + xmlreq.statusText;
					}
				}
			};
			xmlreq.send(null);
		}
	</script>
	<script src="assets/js/init.js"></script>

	</body>

</html>
