<?php

require_once "autoload.php";

if(!isset($_SESSION['matricula'])) { session_start(); }

include 'conf.php';

if (isset($_POST['acao'])) $acao = $_POST['acao'];
else if (isset($_GET['acao'])) $acao = $_GET['acao'];
else $acao = '';

require_once "autoload.php";

#### Construção do objeto ##########################################################################

if ($acao != '' && $acao != 'editar_disciplina') {	
	echo "Ação: ".$acao."<br>";
	
	$disc = new Disciplina;
	if(isset($_POST['codigo'])) $disc->setCodigo($_POST['codigo']);
	if(isset($_POST['nome'])) $disc->setDescricao($_POST['nome']);
	if(isset($_POST['Serie_Codigo_Serie'])) $serie_codigo = intval($_POST['Serie_Codigo_Serie']);
	echo $disc;
	//echo "Senha: ".$_POST['senha'];
}

#### PDO ###########################################################################################

$pdo = Conexao::getInstance();

try {
	switch ($acao) {
		case 'cadastrar':
			insertPDO_disc();
			break;
		case 'editar':
			updatePDO_disc();
			break;
		case 'deletar':
			deletePDO_disc();
			break;
		case 'delete_prof':
			deletePDO_discprof($_GET['matricula'], $_GET['disciplina']);
			break;
		case 'add_prof':
			insertPDO_discprof($_POST['matricula'], $_POST['disciplina']);
			break;
		case 'delete_alun':
			deletePDO_discalun($_GET['matricula'], $_GET['disciplina']);
			break;
		case 'add_alun':
			//var_dump($_POST['matriculas']);
			insertPDO_discalun($_POST['matriculas'], $_POST['disciplina']);
			break;
		case 'delete_aval':
			deletePDO_discaval($_GET['avaliacao'], $_GET['disciplina']);
			break;
	}	
} catch (PDOException $e) {
	echo "Erro: ".$e->getMessage();
}

#### Funções ###############################################

function selectPDO_disc($criterio = 'Nome', $pesquisa = '') {
	try {
		$sql = 'select d.Codigo_Disciplina, d.Nome, d.Serie_Codigo_Serie, s.Descricao as "Serie" FROM '
		.$GLOBALS['tb_disciplinas'].' d 
		, '.$GLOBALS['tb_series'].' s 
		WHERE s.Codigo_Serie = d.Serie_Codigo_Serie
		AND d.'.$criterio.' ';
		
		if ($criterio == 'Nome') 
			$sql .= " like '%".$pesquisa."%'";
		else $sql .= ' = '.$pesquisa;

		$sql .= ";";
		//var_dump($sql); echo "<br>";

		$consulta = $GLOBALS['pdo']->query($sql);

		$registros = array();

		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			$registros[$i] = array();
			array_push($registros[$i], $linha['Codigo_Disciplina']);
			array_push($registros[$i], $linha['Nome']);
			array_push($registros[$i], $linha['Serie_Codigo_Serie']);
			array_push($registros[$i], $linha['Serie']);
		}

		return $registros;
	} catch (PDOException $e) {
		echo "Erro: ".$e->getMessage();
	}
}

function selectPDO_disc_table ($registros) {
	# $registros deve ser o retorno da função selectPDO_disc()
	# ou seja, poderia-se chamar essa função assim: prinselectPDO_discasTable(selectPDO_disc());
	#A função de select do PDO só retorna os valores da tabela em uma matriz
	#A função printSelectTable imprime os dados da matriz em uma tabela
	
	echo "<table class='highlight centered responsive-table'>
	<thead class='black white-text'>
	<tr>
		<th>Código</th>
		<th>Nome</th>
		<th> # Série </th>
		<th>Série</th>
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

function insertPDO_disc() {
	$stmt = $GLOBALS['pdo']->prepare("INSERT INTO ".$GLOBALS['tb_disciplinas']." (Nome, Serie_Codigo_Serie) VALUES (:Nome, :Serie_Codigo)");

	$stmt->bindParam(':Nome', $nome);
	$stmt->bindParam(':Serie_Codigo', $serie_cod);
	
	$nome = $GLOBALS['disc']->getDescricao();
	$serie_cod = $GLOBALS['serie_codigo'];

	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();


	// Quando um professor cria uma disciplina, ele deve ser automaticamente registrado nela
	
	//echo '..'.$_SESSION['matricula'].'..';

	$stmt2 = $GLOBALS['pdo']->prepare("INSERT INTO ".$GLOBALS['tb_disc_prof']." (Professores_Matricula, Disciplina_Codigo_Disciplina) VALUES (:Professores_Matricula, :Disciplina_Codigo)");
	$stmt2->bindParam(':Professores_Matricula', $_SESSION['matricula']);
	$stmt2->bindParam(':Disciplina_Codigo', $codigo);

	$matricula = $_SESSION['matricula'];
	$codigo = proxCodigo();

	$stmt2->execute();

	echo "Linhas afetadas: ".$stmt2->rowCount();

	header("location:disciplina.php?codigo=".$codigo);
}

function proxCodigo() {
	// Devido ao fato de que, em seguida do cadastro da disciplina, se cadastra, na tabela n:n, o professor que acabou de criar a disciplina, se torna necessário saber qual será o próximo código que o MySQL geraria pelo auto_increment
	$registros = selectPDO_disc();
	$codigos = array();
	for ($i=0; $i < count($registros); $i++) { 
		array_push($codigos, $registros[$i][0]);
	}
	sort($codigos);
	return $codigos[(count($codigos)-1)];
}



function updatePDO_disc() {
	$stmt = $GLOBALS['pdo']->prepare("UPDATE ".$GLOBALS['tb_disciplinas']." SET Nome = :Nome, Serie_Codigo_Serie = :Serie_Codigo WHERE Codigo_Disciplina = :Codigo");

	$stmt->bindParam(':Codigo', $codigo);
	$stmt->bindParam(':Nome', $nome);
	$stmt->bindParam(':Serie_Codigo', $serie_cod);
	
	$codigo = $GLOBALS['disc']->getCodigo();
	$nome = $GLOBALS['disc']->getDescricao();
	$serie_cod = $GLOBALS['serie_codigo'];

	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();

	// //////////////////////////////////////////////////////////////////////////////////////////////////

	$stmt2 = $GLOBALS['pdo']->prepare("INSERT INTO ".$GLOBALS['tb_disc_prof']." (Professores_Matricula, Disciplina_Codigo_Disciplina) VALUES (:Professores_Matricula, :Disciplina_Codigo)");

	$stmt2->bindParam(':Professores_Matricula', $_POST['professor']);
	$stmt2->bindParam(':Disciplina_Codigo', $codigo);

	$stmt2->execute();

	echo "Linhas afetadas: ".$stmt2->rowCount();
}

function deletePDO_disc() {
	$stmt = $GLOBALS['pdo']->prepare("DELETE FROM ".$GLOBALS['tb_disciplinas']." WHERE Codigo = :Codigo");

	$stmt->bindParam(':Codigo', $codigo);

	$codigo = $GLOBALS['disc']->getCodigo();

	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();
}






//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Funções com comandos referentes a tabela N:N que conecta Professores e Disciplina (professores_has_disciplina)







function selectPDO_discprof($cod_disciplina) {
	try {
		$sql = 'select p.Matricula as \'Matricula\', p.Nome as \'Professor\', d.Codigo_Disciplina as \'Codigo_Disciplina\', d.Nome as \'Disciplina\' from ';

		$sql .= $GLOBALS['tb_disc_prof'].' dp , ';
		$sql .= $GLOBALS['tb_professores'].' p , ';
		$sql .= $GLOBALS['tb_disciplinas'].' d ';
		$sql .= ' WHERE dp.Professores_Matricula = p.Matricula'; 
		$sql .= ' AND dp.Disciplina_Codigo_Disciplina = d.Codigo_Disciplina'; 
		$sql .= ' AND dp.Disciplina_Codigo_Disciplina = '.$cod_disciplina;
		//var_dump($sql); echo "<br>";

		$consulta = $GLOBALS['pdo']->query($sql);

		$registros = array();

		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			$registros[$i] = array();
			array_push($registros[$i], $linha['Matricula']);
			array_push($registros[$i], $linha['Professor']);
			array_push($registros[$i], $linha['Codigo_Disciplina']);
			array_push($registros[$i], $linha['Disciplina']);
		}

		return $registros;
	} catch (PDOException $e) {
		echo "Erro: ".$e->getMessage();
	}
}

function selectPDO_discprof_table ($registros) {
	echo "<table class='highlight centered responsive-table' border='5'>
	<thead class='black white-text'>
	<tr>
		<th>Matrícula</th>
		<th>Nome</th>
		<th>Código</th>
		<th>Disciplina</th>
		<th>Excluir</th>
	</tr>
	</thead>
	<tdbody>";

	for ($i=0; $i < count($registros); $i++) {
		echo "<tr>";
		for ($j=0; $j < count($registros[$i]); $j++) { 
			echo "<td>".$registros[$i][$j]."</td>";
		}
		if(count($registros) > 1){
			echo "<td><a href='disciplinas_pdo.php?acao=delete_prof&matricula=".$registros[$i][0]."&disciplina=".$registros[$i][2]."'>X</a></td>";
		}else{
			echo "<td>Adicione outro antes de excluir</td>";
		}

		echo "<tr>";
	}
	echo "</tbody>
	</table>";
}

function deletePDO_discprof($matricula, $disciplina) {
	$stmt = $GLOBALS['pdo']->prepare("DELETE FROM ".$GLOBALS['tb_disc_prof']." WHERE Professores_Matricula = :Matricula AND Disciplina_Codigo_Disciplina = :Disciplina_Codigo");

	$stmt->bindParam(':Matricula', $matricula);
	$stmt->bindParam(':Disciplina_Codigo', $disciplina);
	
	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();

	header("location:disciplina.php?codigo=$disciplina");
}

function insertPDO_discprof ($matricula, $disciplina) {
	$stmt = $GLOBALS['pdo']->prepare('INSERT INTO '.$GLOBALS["tb_disc_prof"].' (Professores_Matricula, Disciplina_Codigo_Disciplina) VALUES (:Matricula, :Disciplina)');

	$stmt->bindParam(":Matricula", $matricula);
	$stmt->bindParam(":Disciplina", $disciplina);

	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();

	header("location:disciplina.php?codigo=$disciplina");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////








///////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Funções com comandos referentes a tabela N:N que conecta Alunos e Disciplinas (Disciplina_has_Alunos)








function selectPDO_discalun($cod_disciplina) {
	try {
		$sql = 'select a.Matricula as \'Matricula\', a.Nome as \'Aluno\', d.Codigo_Disciplina as \'Codigo_Disciplina\', d.Nome as \'Disciplina\' from ';

		$sql .= $GLOBALS['tb_disc_alun'].' da , ';
		$sql .= $GLOBALS['tb_alunos'].' a , ';
		$sql .= $GLOBALS['tb_disciplinas'].' d ';
		$sql .= ' WHERE da.Alunos_Matricula = a.Matricula'; 
		$sql .= ' AND da.Disciplina_Codigo_Disciplina = d.Codigo_Disciplina'; 
		$sql .= ' AND da.Disciplina_Codigo_Disciplina = '.$cod_disciplina;
		//var_dump($sql); echo "<br>";

		$consulta = $GLOBALS['pdo']->query($sql);

		$registros = array();

		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			$registros[$i] = array();
			array_push($registros[$i], $linha['Matricula']);
			array_push($registros[$i], $linha['Aluno']);
			array_push($registros[$i], $linha['Codigo_Disciplina']);
			array_push($registros[$i], $linha['Disciplina']);
		}

		return $registros;
	} catch (PDOException $e) {
		echo "Erro: ".$e->getMessage();
	}
}

function selectPDO_discalun_table ($registros) {
	echo "<table class='highlight centered responsive-table' border='5'>
	<thead class='black white-text'>
	<tr>
		<th>Matrícula</th>
		<th>Nome</th>
		<th>Código</th>
		<th>Disciplina</th>
		<th>Excluir</th>
	</tr>
	</thead>
	<tdbody>";

	for ($i=0; $i < count($registros); $i++) {
		echo "<tr>";
		for ($j=0; $j < count($registros[$i]); $j++) { 
			echo "<td>".$registros[$i][$j]."</td>";
		}
		echo "<td><a href='disciplinas_pdo.php?acao=delete_alun&matricula=".$registros[$i][0]."&disciplina=".$registros[$i][2]."'>X</a></td>";

		echo "<tr>";
	}
	echo "</tbody>
	</table>";
}

function deletePDO_discalun($matricula, $disciplina) {
	$stmt = $GLOBALS['pdo']->prepare("DELETE FROM ".$GLOBALS['tb_disc_alun']." WHERE Alunos_Matricula = :Matricula AND Disciplina_Codigo_Disciplina = :Disciplina_Codigo");

	$stmt->bindParam(':Matricula', $matricula);
	$stmt->bindParam(':Disciplina_Codigo', $disciplina);
	
	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();

	header("location:disciplina.php?codigo=$disciplina");
}

function insertPDO_discalun($matriculas, $disciplina) {
	for ($i=0; $i < count($matriculas); $i++) { 
		$stmt = $GLOBALS['pdo']->prepare('INSERT INTO '.$GLOBALS["tb_disc_alun"].' (Alunos_Matricula, Disciplina_Codigo_Disciplina) VALUES (:Matricula, :Disciplina)');

		$stmt->bindParam(":Matricula", $matriculas[$i]);
		$stmt->bindParam(":Disciplina", $disciplina);

		$stmt->execute();

		echo "Linhas afetadas: ".$stmt->rowCount();
	}
	header("location:disciplina.php?codigo=$disciplina");
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////








///////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Funcções com comandos referentes a relação da tabela Avaliacoes para Disciplinas (1:N)








function selectPDO_discaval ($codigo, $tabela /* o valor deve ser ou avaliacao ou disciplina */) {
	try {
		$sql = 'select d.Codigo_Disciplina as "ID Disciplina", d.Nome as "Disciplina", a.Codigo_Avaliacao as "ID Avaliacao", a.Conteudo, a.Data_Inicio, a.Data_Fim, a.Peso, a.Embaralhar
		 FROM avaliacoes a, disciplinas d 
		 WHERE d.Codigo_Disciplina = a.Disciplina_Codigo_Disciplina ';
		
		if($tabela == 'disciplina') {
			$sql .= ' and d.Codigo_Disciplina = '.$codigo;
		} else if ($tabela == 'avaliacao') {
			$sql .=  'and a.Codigo_Avaliacao = '.$codigo;
		}
		$sql .= " ORDER BY a.Data_Fim";

		//var_dump($sql); echo "<br/>";

		$consulta = $GLOBALS['pdo']->query($sql);

		$registros = array();

		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			$registros[$i] = array();
			array_push($registros[$i], $linha['ID Disciplina']);
			array_push($registros[$i], $linha['Disciplina']);
			array_push($registros[$i], $linha['ID Avaliacao']);
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

function selectPDO_discaval_table ($registros) {
	echo "<table class='highlight centered responsive-table' border='5'>
	<thead class='black white-text'>
		<tr>
			<th>ID Disciplina</th>
			<th>Disciplina</th>
			<th>ID Avaliacao</th>
			<th>Conteudo</th>
			<th>Data_Inicio</th>
			<th>Data_Fim</th>
			<th>Peso</th>
			<th>Embaralhar</th>
			<th>Excluir</th>
			<th>Editar</th>
		</tr>
		</thead>
	<tdbody>";

	for ($i=0; $i < count($registros); $i++) {
		echo "<tr>";
		for ($j=0; $j < count($registros[$i]); $j++) { 
			if($j == 3) echo "<td><a href='avaliacao.php?codigo=".$registros[$i][2]."'>".$registros[$i][$j]."</a></td>";
					else echo "<td>".$registros[$i][$j]."</td>";
		}
		echo "<td><a href='disciplinas_pdo.php?acao=delete_aval&avaliacao=".$registros[$i][2]."&disciplina=".$registros[$i][0]."'>X</a></td>";
		echo "<td><a href='avaliacao.php?codigo=".$registros[$i][2]."'>Editar</a></td>";

		echo "<tr>";
	}
	echo "</tbody>
	</table>";
}

function deletePDO_discaval($avaliacao, $disciplina) {
####////
	$stmt = $GLOBALS['pdo']->prepare("DELETE FROM Questoes_has_Avaliacoes WHERE Avaliacoes_Codigo_Avaliacao = :Avaliacao");

	$stmt->bindParam(":Avaliacao", $avaliacao);

	$stmt->execute();

	echo "Linhas afetadas (remover questoes da avaliacao): ".$stmt->rowCount();

####////
	$stmt = $GLOBALS['pdo']->prepare("DELETE FROM ".$GLOBALS['tb_avaliacoes']." WHERE Codigo_Avaliacao = :Codigo_Avaliacao AND Disciplina_Codigo_Disciplina = :Disciplina_Codigo");

	$stmt->bindParam(':Codigo_Avaliacao', $avaliacao);
	$stmt->bindParam(':Disciplina_Codigo', $disciplina);
	
	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();

	header("location:disciplina.php?codigo=$disciplina");
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////


?>