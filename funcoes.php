<?php
include 'conf.php';
include 'connect/connect.php';

function printHeader() {
	?>
	<!-- Image and text -->
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<a class="navbar-brand" href="#">Soluz</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarText">
			<ul class="navbar-nav mr-auto">
			</ul>
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="sobre.html">Sobre</a>
				</li>
				<li class="nav-item active">
					<a class="nav-link" href="entrar.php">Entrar</a>
				</li>
			</ul>
		</div>
	</nav>
	<?php
}

function printFooter() {
	?>
		<div class="footer-copyright">
			<div class="container">
				<span class="left" id="copyright-js"></span> &nbsp; <a target="_blank" href="https://github.com/MateuxLucax/Prove">Prove</a>
				<span class="right"><a target="_blank" href="https://opensource.org/licenses/MIT">MIT License</a></span>
			</div>
		</div>
	<?php
}

function gerarSelect($tabela, $selectName, $codigo, $value, $texto) {
	$sql = "SELECT * FROM $tabela";

	$result = mysqli_query($GLOBALS['conexao'], $sql);

	echo "<select name='$selectName'>";
	while ($row = mysqli_fetch_array($result)) {

		if ($selectName == 'Disciplina_Codigo_Disciplina')
		{
			$sql2 = "SELECT Descricao FROM Serie WHERE Codigo_Serie = ".$row["Serie_Codigo_Serie"];
			$result2 = mysqli_query($GLOBALS['conexao'], $sql2);
			$row2 = mysqli_fetch_array($result2);
			echo "<option value=".$row["$value"].">".$row2['Descricao']." - ".$row["$texto"]."</option>";
		} else
		{
			echo "<option value=".$row["$value"].">".$row["$texto"]."</option>";
		}
	}
	echo "</select>";
}

function gerarSelectMultiple($tabela, $selectName, $value, $texto) {
	$sql = "SELECT * FROM $tabela";

	$result = mysqli_query($GLOBALS['conexao'], $sql);

	echo "<select multiple name='".$selectName."[]' class='form-control'>";
	while ($row = mysqli_fetch_array($result)) {
		echo "<option value=".$row["$value"]." ".$selected.">".$row["$texto"]." (#".$row["$value"].")</option>";
	}
	echo "</select>";
}


function mostrar_questoes($questoes) {
	//$questoes deve ser o return da função "selectPDO_avalques_all"
	$ID_anterior = 0;

	/*for ($i=0; $i < count($questoes); $i++) {
		for ($j=0; $j < count($questoes[$i]); $j++) {
			echo $questoes[$i][$j]."---";
		}
		echo "<br/><br/>";
	}*/

		echo "<div class='questao'>";
			for ($i=0; $i < count($questoes); $i++) {

				if($questoes[$i][0] != $ID_anterior) {
					if($i >= 1) {
						echo "</form><form class='card-panel container' action='' method='post'>";
					} else {
						echo "<form class='card-panel container'>";
					}

					echo "<small style='color:grey'>#".$questoes[$i][0]."</small>";

					if(isset($questoes[$i][1])) echo "<p>".$questoes[$i][1]."</p>";

					echo "<p><b>".$questoes[$i][2]."</b></p>";

					if($questoes[$i][3] != 1) {
						if($questoes[$i][3] == 2) {
							$tipo = 'radio';
						} else if ($questoes[$i][3] == 3) {
							$tipo = 'checkbox';
						}

						echo "<p>
							<label>
								<input type='".$tipo."' name='questao".$questoes[$i][0]."alt' disabled/>
								<span>".$questoes[$i][6]."</span>
							</label>
						</p>";
					} else {
						echo "<textarea disabled class='materialize-textarea' name='questao".$questoes[$i][0]."txt'></textarea>";
					}

				} else {
					if($questoes[$i][3] == 2) {
						$tipo = 'radio';
					} else if ($questoes[$i][3] == 3) {
						$tipo = 'checkbox';
					}

					echo "<p>
						<label>
							<input type='".$tipo."' name='questao".$questoes[$i][0]."alt' disabled/>
							<span>".$questoes[$i][6]."</span>
						</label>
					</p>";

				}

				$ID_anterior = $questoes[$i][0];

			}
	echo "</div>";
}
?>
