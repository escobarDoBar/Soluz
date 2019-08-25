<?php

require_once "autoload.php";

include 'conf.php';

if (isset($_POST['acao'])) $acao = $_POST['acao'];
else if (isset($_GET['acao'])) $acao = $_GET['acao'];
else $acao = '';

require_once "autoload.php";

#### Construção do objeto ##########################################################################

if (!$acao == '') {	
	echo "Ação: ".$acao."<br>";
	
	if ($acao == 'Cadastrar')
	{
		$data_fim = $_POST['dataFinal'];
		$data_fim = str_replace('/', '-', $data_fim);
		$data_fim = date('Y-m-d', strtotime($data_fim));
		$data_fim .= " ".$_POST['horarioFinal'].":00";

		$data_inicio = $_POST['dataInicio'];
		$data_inicio = str_replace('/', '-', $data_inicio);
		$data_inicio = date('Y-m-d', strtotime($data_inicio));
		$data_inicio .= " ".$_POST['horarioInicio'].":00";
		
	} else 
	{
		if(isset($_POST['dataFim']))
		{
			$data_fim = $_POST['dataFim'];
		}		
		if (isset($_POST['dataInicio']))
		{
			$data_inicio = $_POST['dataInicio'];	
		} 
	}

	$avaliacao = new Avaliacao;
	if(isset($_POST['Codigo_Avaliacao'])) $avaliacao->setCodigo($_POST['Codigo_Avaliacao']);
	if(isset($_POST['conteudo'])) $avaliacao->setConteudo($_POST['conteudo']);
	if(isset($data_inicio)) $avaliacao->setDataInicio($data_inicio);
	if(isset($data_fim)) $avaliacao->setDataFim($data_fim);
	if(isset($_POST['peso'])) $avaliacao->setPeso($_POST['peso']);
	if(isset($_POST['embaralhar'])) $avaliacao->setEmbaralhar($_POST['embaralhar']);
	if(isset($_POST['Disciplina_Codigo_Disciplina'])) $disciplina = $_POST['Disciplina_Codigo_Disciplina'];
	else $avaliacao->setEmbaralhar(0);
	
	
	echo $avaliacao;
	//echo "Senha: ".$_POST['senha'];
}

#### PDO ###########################################################################################

$pdo = Conexao::getInstance();

try {
	switch ($acao) {
		case 'cadastrar':
		case 'Cadastrar':
			insertPDO_aval();
			break;
		case 'editar':
		case 'Editar':
			updatePDO_aval();
			break;
		case 'deletar':
			deletePDO_aval();
			break;
		case 'cadastrar_questao':
			insertPDO_avalques();
			break;
		case 'deletar_questao':
			deletePDO_avalques();
			break;
	}	
} catch (PDOException $e) {
	echo "Erro: ".$e->getMessage();
}

#### Funções ###############################################

function selectPDO_aval($criterio = 'Conteudo', $pesquisa = '') {
	try {
		$sql = "SELECT * FROM ".$GLOBALS['tb_avaliacoes']." WHERE ".$criterio." ";
		if ($criterio == 'Conteudo') {
			$sql .= " like '%".$pesquisa."%'";
		} else {
			$sql .= ' = '.$pesquisa;
		}
		$sql .= ";";

		echo $sql;
		//var_dump($sql); echo "<br>";

		$consulta = $GLOBALS['pdo']->query($sql);

		$registros = array();

		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			$registros[$i] = array();
			array_push($registros[$i], $linha['Codigo_Avaliacao']);
			array_push($registros[$i], $linha['Conteudo']);
			array_push($registros[$i], $linha['Data_Inicio']);
			array_push($registros[$i], $linha['Data_Fim']);
			array_push($registros[$i], $linha['Peso']);
			array_push($registros[$i], $linha['Embaralhar']);
		}

		return $registros;
	} catch (PDOException $e) {
		echo "Erro: ".$e->getMessage();
	}
}

function selectPDO_aval_table ($registros) {
	# $registros deve ser o retorno da função selectPDO_aval()
	# ou seja, poderia-se chamar essa função assim: prinselectPDO_avalasTable(selectPDO_aval());
	
	echo "<table class='highlight centered responsive-table'>
	<thead class='black white-text'>
	<tr>
		<th>Codigo Avaliacao</th>
		<th>Conteudo</th>
		<th>Data de Inicio</th>
		<th>Data de Fim</th>
		<th>Peso</th>
		<th>Embaralhar</th>
	</tr>
	</thead>
	<tdbody>";

	for ($i=0; $i < count($registros); $i++) {
		echo "<tr>";
		for ($j=0; $j < count($registros[$i]); $j++) { 
			echo "<td>".$registros[$i][$j]."</td>";
		}
		echo "<tr>";
	}
	echo "</tbody>
	</table>";

}

function insertPDO_aval() {
	$sql = "INSERT INTO ".$GLOBALS['tb_avaliacoes']." (Conteudo, Data_Inicio, Data_Fim, Embaralhar, Disciplina_Codigo_Disciplina) VALUES (:Conteudo, :Data_Inicio, :Data_Fim, :Embaralhar, :Disciplina)";
	//var_dump ($sql);

	$stmt = $GLOBALS['pdo']->prepare($sql);

	$stmt->bindParam(':Conteudo', $conteudo);
	$stmt->bindParam(':Data_Inicio', $data_inicio);
	$stmt->bindParam(':Data_Fim', $data_fim);
	$stmt->bindParam(':Embaralhar', $embaralhar);
	$stmt->bindParam(':Disciplina', $disciplina);

	$conteudo = $GLOBALS['avaliacao']->getConteudo();
	$data_inicio = $GLOBALS['avaliacao']->getDataInicio();
	$data_fim = $GLOBALS['avaliacao']->getDataFim();
	$embaralhar = $GLOBALS['avaliacao']->getEmbaralhar();
	$disciplina = $_POST['Disciplina_Codigo_Disciplina'];

	//echo "Data início:". $data_inicio."<br/>Data fim: ".$data_fim;
	
	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();

	header("location:disciplina.php?codigo=".$disciplina);
}

function updatePDO_aval() {
	$stmt = $GLOBALS['pdo']->prepare("UPDATE ".$GLOBALS['tb_avaliacoes']." SET Conteudo = :Conteudo, Data_Inicio = :Data_Inicio, Data_Fim = :Data_Fim, Embaralhar = :Embaralhar, Disciplina_Codigo_Disciplina = :Disciplina WHERE Codigo_Avaliacao = :Codigo");

	$stmt->bindParam(':Codigo', $codigo);
	$stmt->bindParam(':Conteudo', $conteudo);
	$stmt->bindParam(':Data_Inicio', $data_inicio);
	$stmt->bindParam(':Data_Fim', $data_fim);
	//$stmt->bindParam(':Peso', $peso);
	$stmt->bindParam(':Embaralhar', $embaralhar);
	$stmt->bindParam(':Disciplina', $disciplina);
	
	$codigo = $GLOBALS['avaliacao']->getCodigo();
	$conteudo = $GLOBALS['avaliacao']->getConteudo();
	$data_inicio = $GLOBALS['avaliacao']->getDataInicio();
	$data_fim = $GLOBALS['avaliacao']->getDataFim();
	$peso = $GLOBALS['avaliacao']->getPeso();
	$embaralhar = $GLOBALS['avaliacao']->getEmbaralhar();
	$disciplina = $_POST['Disciplina_Codigo_Disciplina'];

	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();

	header("location:avaliacao.php?codigo=".$codigo);
}

function deletePDO_aval() {
	$stmt = $GLOBALS['pdo']->prepare("DELETE FROM ".$GLOBALS['$tb_avaliacoes']." WHERE Codigo_Avaliacao = :Codigo_Avaliacao");

	$stmt->bindParam(':Codigo_Avaliacao', $codigo);

	$codigo = $GLOBALS['aluno']->getCodigo_Avaliacao();

	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();

	header("location:avaliacao.php?codigo=".$codigo);
}


/////////////////////////////////////////////////////////////////////
// Funções para comandos referentes a relação avaliação-questão (N:N)

function selectPDO_avalques($codigo_aval = '') {
	try {	
		if ($codigo_aval == 'só_questão') {
			$sql = 'select Codigo_Questao, Texto, Enunciado, Tipo_Codigo as \'Tipo\' FROM Questao';
		}
		else {	
			$sql = 'select A.Codigo_Avaliacao, A.Conteudo, Q.Codigo_Questao, Q.Texto, Q.Enunciado, T.Descricao as \'Tipo\'
				FROM Questao Q, Questoes_has_Avaliacoes QA, Avaliacoes A, Tipo T
				WHERE QA.Questoes_Codigo_Questao = Q.Codigo_Questao
				AND QA.Avaliacoes_Codigo_Avaliacao = A.Codigo_Avaliacao
				AND Q.Tipo_Codigo = T.Codigo_Tipo ';
				
			if($codigo_aval != '') {
				$sql .= ' AND QA.Avaliacoes_Codigo_Avaliacao = '.$codigo_aval;
			}
		}

		//var_dump($sql);

		$consulta = $GLOBALS['pdo']->query($sql);

		$registros = array();

		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			$registros[$i] = array();
			if(isset($linha['Codigo_Avaliacao'])) array_push($registros[$i], $linha['Codigo_Avaliacao']);
			if(isset($linha['Conteudo'])) array_push($registros[$i], $linha['Conteudo']);
			array_push($registros[$i], $linha['Codigo_Questao']);
			array_push($registros[$i], $linha['Texto']);
			array_push($registros[$i], $linha['Enunciado']);
			array_push($registros[$i], $linha['Tipo']);
		}

		return $registros;


		//if($tipo == 'unica escolha' || $tipo == 'verdadeiro ou falso') { mostrar alternativas }

	} catch (PDOException $e) {
		echo "Erro: ".$e->getMessage();
	}
}

function selectPDO_avalques_table ($registros) {
	
	echo "<table class='highlight centered responsive-table'>
	<thead class='black white-text'>
	<tr>
		<th>Codigo Avaliacao</th>
		<th>Conteudo</th>
		<th>Codigo Questao</th>
		<th>Texto</th>
		<th>Enunciado</th>
		<th>Tipo</th>
	</tr>
	</thead>
	<tdbody>";

	for ($i=0; $i < count($registros); $i++) {
		echo "<tr>";
		for ($j=0; $j < count($registros[$i]); $j++) { 
			echo "<td>".$registros[$i][$j]."</td>";
		}
		echo "<tr>";
	}
	echo "</tbody>
	</table>";

}

function insertPDO_avalques() {
	$cod_avaliacao = $_POST['cod_avaliacao'];
	//echo $cod_avaliacao;

	$stmt = $GLOBALS['pdo']->prepare("INSERT INTO ".$GLOBALS['tb_questoes']." (Enunciado, Texto, Tipo_Codigo) VALUES (:Enunciado, :Texto, :Tipo_Codigo)");

	$stmt->bindParam(':Enunciado', $_POST['enunciado']);
	$stmt->bindParam(':Texto', $_POST['texto']);
	$stmt->bindParam(':Tipo_Codigo', $_POST['Tipo_Codigo']);

	$stmt->execute();

	echo "Linhas afetadas (questão criada): ".$stmt->rowCount();

	//// Adicionar questão na avaliação

	$sql = "SELECT Codigo_Questao FROM Questao ORDER BY Codigo_Questao";
	$query = $GLOBALS['pdo']->query($sql);
	while ($row = $query->fetch(PDO::FETCH_ASSOC))
	{
		$cod_questao = $row['Codigo_Questao']; // não fica num array, o valor atualiza a cada loop e no final fica com o código da última questão registrada
	}

	$stmt = $GLOBALS['pdo']->prepare("INSERT INTO ".$GLOBALS['tb_aval_ques']." (Questoes_Codigo_Questao, Avaliacoes_Codigo_Avaliacao) VALUES (:Questao, :Avaliacao)");

	$stmt->bindParam(':Questao', $cod_questao);
	$stmt->bindParam(':Avaliacao', $cod_avaliacao);
		
	$stmt->execute();

	echo "Linhas afetadas (foi p/ prova): ".$stmt->rowCount();

	if($_POST['Tipo_Codigo'] == 1)
	{
		header("location:avaliacao_cadastro.php?codigo=".$cod_avaliacao);
	} else 	if ($_POST['Tipo_Codigo'] == 2)
	{
		$qtd_alts = $_POST['qtdUnica'];

		for ($i=1; $i <= $qtd_alts; $i++)
		{ 
			$index_desc = 'alternativaUnica-'.$i;
			$descricao = $_POST[$index_desc];
			$correta = $_POST['alternativaUnica-correta'];

			if ($i == $correta)
				$correta = 1;
			else
				$correta = 0;

			$sql = "INSERT INTO Alternativa (Descricao, Correta, Questao_Codigo) VALUES (:Descricao, :Correta, :Questao_Codigo)";

			$stmt = $GLOBALS['pdo']->prepare($sql);

			$stmt->bindParam(':Descricao', $descricao);
			$stmt->bindParam(':Correta', $correta);
			$stmt->bindParam(':Questao_Codigo', $cod_questao);

			$stmt->execute();

			echo "Linhas afetadas (cadastro de alternativa): ".$stmt->rowCount()."<br/>";

			header("location:avaliacao_cadastro.php?codigo=".$cod_avaliacao);
		}
	} else if ($_POST['Tipo_Codigo'] == 3)
	{
		$qtd_alts = $_POST['qtdMultipla'];

		for ($i=1; $i <= $qtd_alts; $i++)
		{ 

			$index_desc = 'alternativaMultipla-'.$i;
			$descricao = $_POST[$index_desc];
			$correta = $_POST['alternativaMultipla-correta'];

			if (in_array($i, $correta))
				$correta = 1;
			else
				$correta = 0;

			$sql = "INSERT INTO Alternativa (Descricao, Correta, Questao_Codigo) VALUES (:Descricao, :Correta, :Questao_Codigo)";

			$stmt = $GLOBALS['pdo']->prepare($sql);

			$stmt->bindParam(':Descricao', $descricao);
			$stmt->bindParam(':Correta', $correta);
			$stmt->bindParam(':Questao_Codigo', $cod_questao);

			$stmt->execute();

			echo "Linhas afetadas (cadastro de alternativa): ".$stmt->rowCount()."<br/>";

			header("location:avaliacao_cadastro.php?codigo=".$cod_avaliacao);
		}
	}
}

function selectPDO_avalques_all($codigo) {
	$sql = 'select Q.Codigo_Questao, Q.Enunciado, Q.Texto, T.Codigo_Tipo, T.Descricao as \'Tipo\', AL.Codigo_Alternativa, AL.Descricao, AL.Correta
		FROM Avaliacoes A, Questao Q, Questoes_has_Avaliacoes QA, Tipo T, Alternativa AL
		WHERE T.Codigo_Tipo = Q.Tipo_Codigo
		AND AL.Questao_Codigo = Q.Codigo_Questao
		AND QA.Questoes_Codigo_Questao = Q.Codigo_Questao
		AND QA.Avaliacoes_Codigo_Avaliacao = A.Codigo_Avaliacao
		AND A.Codigo_Avaliacao = '.$codigo;

	$consulta = $GLOBALS['pdo']->query($sql);

	$registros = array();

	for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
		$registros[$i] = array();

		array_push($registros[$i], $linha['Codigo_Questao']);
		array_push($registros[$i], $linha['Texto']);
		array_push($registros[$i], $linha['Enunciado']);
		array_push($registros[$i], $linha['Codigo_Tipo']);
		array_push($registros[$i], $linha['Tipo']);
		array_push($registros[$i], $linha['Codigo_Alternativa']);
		array_push($registros[$i], $linha['Descricao']);
		array_push($registros[$i], $linha['Correta']);
	}


	$sql = 'select Q.Codigo_Questao, Q.Enunciado, Q.Texto, T.Codigo_Tipo, T.Descricao as "Tipo"
	FROM Avaliacoes A, Questao Q, Questoes_has_Avaliacoes QA, Tipo T
	WHERE QA.Questoes_Codigo_Questao = Q.Codigo_Questao
	AND QA.Avaliacoes_Codigo_Avaliacao = A.Codigo_Avaliacao
	AND T.Codigo_Tipo = Q.Tipo_Codigo
	AND Q.Tipo_Codigo != 2
	AND  Q.Tipo_Codigo != 3
	AND A.Codigo_Avaliacao = '.$codigo;

	$consulta = $GLOBALS['pdo']->query($sql);

	# $i = (count($registros)-1) !!!!! não pode ser $i = 0 porque dessa forma iria sobrepor o que foi registrado na consulta anterior
	for ($i = (count($registros)); $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
		$registros[$i] = array();
		array_push($registros[$i], $linha['Codigo_Questao']);
		array_push($registros[$i], $linha['Texto']);
		array_push($registros[$i], $linha['Enunciado']);
		array_push($registros[$i], $linha['Codigo_Tipo']);
		array_push($registros[$i], $linha['Tipo']);
	}

	return $registros;

}

function deletePDO_avalques ()
{
	$cod_questao = $_GET['q'];
	$cod_avaliacao = $_GET['a'];

	$sql = "DELETE FROM Questoes_has_Avaliacoes WHERE Questoes_Codigo_Questao = :Q AND Avaliacoes_Codigo_Avaliacao = :A";

	var_dump($sql);

	$stmt = $GLOBALS['pdo']->prepare($sql);

	$stmt->bindParam(":A",$cod_avaliacao);
	$stmt->bindParam(":Q",$cod_questao);

	$stmt->execute();

	echo "linhas afetadas ".$stmt->rowCount();

	$sql = "DELETE FROM Alternativa WHERE Questao_Codigo = :Q";
	$stmt = $GLOBALS['pdo']->prepare($sql);
	$stmt->bindParam(":Q",$cod_questao);
	$stmt->execute();
	echo "linhas afetadas ".$stmt->rowCount();

	$sql = "DELETE FROM Questao WHERE Codigo_Questao = :Q";
	$stmt = $GLOBALS['pdo']->prepare($sql);
	$stmt->bindParam(":Q",$cod_questao);
	$stmt->execute();
	echo "linhas afetadas ".$stmt->rowCount();

	header("location:avaliacao_cadastro.php?codigo=".$cod_avaliacao);
}


?>