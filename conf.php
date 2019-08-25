<?php
	// Inicialização / conexão PDO
	//$pdo = new PDO('mysql:host=localhost;dbname=prove_sistema_avaliacao',"root","");
	//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Tabelas do Banco de Dados
	$tb_alunos = "Alunos";
	$tb_professores = "Professores";
	$tb_disciplinas = "Disciplinas";
	$tb_disc_prof = 'professores_has_disciplina';
	$tb_series = "Serie";
	$tb_disc_alun = 'Disciplina_has_Alunos';
	$tb_avaliacoes = 'Avaliacoes';
	$tb_questoes = 'Questao';
	$tb_aval_ques = 'Questoes_has_Avaliacoes';
	$tb_tipo = 'Tipo';
	$tb_alternativa = 'Alternativa';
	$tb_res_discursiva = 'Discursiva';
	$tb_res_alternativa = 'Resposta_Alternativa';
	//...



	/*// Matriz que relaciona tabelas do banco de dados com as classes
	$matrizClassesTb = array(
		array('Aluno',$tb_alunos),
		array('Professor',$tb_professores),
		array('Disciplina',$tb_disciplinas),
		array('Serie',$tb_series),
		array('Avaliacao',$tb_avaliacoes),
		array('Tipo',$tb_tipo),
		array('Questao',$tb_questoes)
		//...
	);*/
?>