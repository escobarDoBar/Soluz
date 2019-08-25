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
	
	$ser = new Serie;
	if(isset($_POST['codigo'])) $ser->setCodigo($_POST['codigo']);
	if(isset($_POST['nome'])) $ser->setDescricao($_POST['nome']);
	echo $ser;
	//echo "Senha: ".$_POST['senha'];
}

#### PDO ###########################################################################################

$pdo = Conexao::getInstance();

try {
	switch ($acao) {
		case 'cadastrar':
			insertPDO_serie();
			break;
		case 'editar':
			updatePDO_serie();
			break;
		case 'deletar':
			deletePDO_serie();
			break;
	}	
} catch (PDOException $e) {
	echo "Erro: ".$e->getMessage();
}

#### Funções ###############################################

function selectPDO_serie($criterio = 'Descricao', $pesquisa = '') {
	try {
		$sql = "SELECT * FROM ".$GLOBALS['tb_series']." WHERE ".$criterio." ";
		if ($criterio == 'Descricao') 
			$sql .= " like '%".$pesquisa."%'";
		else $sql .= ' = '.$pesquisa;
		$sql .= ";";
		//var_dump($sql); echo "<br>";

		$consulta = $GLOBALS['pdo']->query($sql);

		$registros = array();

		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			$registros[$i] = array();
			array_push($registros[$i], $linha['Codigo_Serie']);
			array_push($registros[$i], $linha['Descricao']);
		}

		return $registros;
	} catch (PDOException $e) {
		echo "Erro: ".$e->getMessage();
	}
}

function selectPDO_serie_table ($registros) {
	# $registros deve ser o retorno da função selectPDO_serie()
	# ou seja, poderia-se chamar essa função assim: prinselectPDO_serieasTable(selectPDO_serie());
	/*A função de select do PDO só retorna os valores da tabela em uma matriz
	A função printSelectTable imprime os dados da matriz em uma tabela*/
	
	echo "<table class='highlight centered responsive-table'>
	<thead class='black white-text'>
	<tr>
		<th>Código</th>
		<th>Nome</th>
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

function insertPDO_serie() {
	$stmt = $GLOBALS['pdo']->prepare("INSERT INTO ".$GLOBALS['tb_series']." (Descricao) VALUES (:Nome)");

	$stmt->bindParam(':Nome', $nome);

	$nome = $GLOBALS['ser']->getDescricao();

	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();

	//Determinar o código da série que acabou de ser registrada, para redirecionamento
	$registros = selectPDO_serie();
	$codigos = array();
	for ($i=0; $i < count($registros); $i++) { 
		array_push($codigos, $registros[$i][0]);
	}

	$codigo = $registros[count($codigos)-1][0];

	header("location:serie.php?codigo=$codigo");
}

function updatePDO_serie() {
	$stmt = $GLOBALS['pdo']->prepare("UPDATE ".$GLOBALS['tb_series']." SET Descricao = :Nome WHERE Codigo_Serie = :Codigo");

	$stmt->bindParam(':Codigo', $codigo);
	$stmt->bindParam(':Nome', $nome);
	
	$codigo = $GLOBALS['ser']->getCodigo();
	$nome = $GLOBALS['ser']->getDescricao();

	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount()."!";

	header("location:serie.php?codigo=$codigo");
}

function deletePDO_serie() {
	$stmt = $GLOBALS['pdo']->prepare("DELETE FROM ".$GLOBALS['tb_series']." WHERE Codigo_Serie = :Codigo");

	$stmt->bindParam(':Codigo', $codigo);

	$codigo = $GLOBALS['ser']->getCodigo();

	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Funções relacionadas a comandos que tratam da relação entre as tabelas série e disciplina (1:N)

function selectPDO_seriedisc($codigo) {
	try {	

		$sql = 'select s.Codigo_Serie, s.Descricao as "Serie", d.Codigo_Disciplina, d.Nome as "Disciplina"
			FROM serie s, disciplinas d
			WHERE s.Codigo_Serie = d.Serie_Codigo_Serie
			AND s.Codigo_Serie = '.$codigo;

		$consulta = $GLOBALS['pdo']->query($sql);

		$registros = array();

		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			$registros[$i] = array();
			array_push($registros[$i], $linha['Codigo_Disciplina']);
			array_push($registros[$i], $linha['Disciplina']);
			array_push($registros[$i], $linha['Codigo_Serie']);
			array_push($registros[$i], $linha['Serie']);
		}

		return $registros;

	} catch (PDOException $e) {
		echo "Erro: ".$e->getMessage();
	}

}

function selectPDO_seriedisc_table ($registros) {
	
	echo "<table class='highlight centered responsive-table' border='5'>
	<thead class='black white-text'>
	<tr>
		<th>ID Disciplina</th>
		<th>Disciplina</th>
		<th>ID Série</th>
		<th>Série</th>
	</tr>
	</thead>
	<tdbody>";

	for ($i=0; $i < count($registros); $i++) {
		echo "<tr>";
		for ($j=0; $j < count($registros[$i]); $j++) { 
			echo "<td>".$registros[$i][$j]."</td>";
		}
		echo "<td><a href=\"disciplina_editar.php?codigo=".$registros[$i][3]."\">Editar</a></td>";
		echo "<tr>";
	}
	echo "</tbody>
	</table>";

}

?>