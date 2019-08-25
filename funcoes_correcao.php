<?php
	require_once "autoload.php";

	$pdo = Conexao::getInstance();

	function notasDisciplinaTodos_view($cod_disciplina) {
		$notas = notasDisciplinaTodos($cod_disciplina);
		for ($i=0; $i < count($notas); $i++) {
			for ($j=0; $j < count($notas[$i]); $j++) {
				echo $notas[$i][$j]." - ";
			}
			echo "<br/>";
		}
	}

	function notasDisciplinaTodos($cod_disciplina) { // O MÁXIMO QUE O PROFESSOR VERÁ
		// gera uma matriz que tem as notas de todos os alunos da disciplina

		// consulta quais os alunos que fazem parte da disciplina
		$query = "SELECT Alunos_Matricula FROM Disciplina_has_Alunos WHERE Disciplina_Codigo_Disciplina = ".$cod_disciplina;
		$consulta = $GLOBALS['pdo']->query($query);
		$matriculas = array();
		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			array_push($matriculas, $linha['Alunos_Matricula']);
		}

		$notas_alunos = array();
		for ($i=0; $i < count($matriculas); $i++) {
			$notas_alunos[$i] = array();
			$notas_alunos[$i][0] = $matriculas[$i];

			$notas = notasDisciplinaAluno($cod_disciplina, $matriculas[$i]);
			for ($j=0; $j < count($notas); $j++) {
				array_push($notas_alunos[$i], $notas[$j]);
			}
		}

		return $notas_alunos;
	}

	function notaTodasAvalProf_view($cod_avaliacao) {
		$notas = notaTodasAvalProf($cod_avaliacao);
		echo "<table class='centered highlight table-responsive'>";
			echo "<thead>";
				echo "<tr>";
					echo "<th>Matrícula</th>";
					echo "<th>Nome</th>";
					echo "<th>Nota</th>";
				echo "</tr>";
			echo "</thead>";
			echo "<tbody>";
			for ($i=0; $i < count($notas); $i++) {
				$sql = "SELECT Nome FROM Alunos WHERE Matricula = '".$notas[$i][0]."'";
				$query = $GLOBALS['pdo']->query($sql);
				$row = $query->fetch(PDO::FETCH_ASSOC);
				$nome = $row['Nome'];

				$cor = $notas[$i][1] >= 7 ? ' green lighten-2 ' : ' red lighten-2 ';

				echo "<tr>";
					echo "<td>".$notas[$i][0]."</td>";
					echo "<td>".$nome."</td>";
					echo "<td class='".$cor."'>".$notas[$i][1]."</td>";
				echo "</tr>";
			}
			echo "</tbody>";
		echo "</table>";
	}

	function notaTodasAvalProf($cod_avaliacao) {
		// Para que o professor veja a nota de uma avaliação de todos os alunos

		// Pega o código da disciplina da qual a avaliação informada faz parte
		$sql = "SELECT Disciplina_Codigo_Disciplina FROM Avaliacoes WHERE Codigo_Avaliacao = ".$cod_avaliacao;
		$query = $GLOBALS['pdo']->query($sql);
		$row = $query->fetch(PDO::FETCH_ASSOC);
		$cod_disciplina = $row['Disciplina_Codigo_Disciplina'];

		// Pega a matrícula de todos os alunos que fazem parte da disciplina
		$sql = "SELECT Alunos_Matricula FROM Disciplina_has_Alunos WHERE Disciplina_Codigo_Disciplina = ".$cod_disciplina;
		$query = $GLOBALS['pdo']->query($sql);
		$matriculas = array();
		for ($i=0; $row = $query->fetch(PDO::FETCH_ASSOC); $i++) {
			array_push($matriculas, $row['Alunos_Matricula']);
		}

		$notas = array();
		for ($i=0; $i < count($matriculas); $i++) {
			$notas[$i] = array();
			array_push ($notas[$i], $matriculas[$i]);
			array_push ($notas[$i], notaAvaliacao($cod_avaliacao, $matriculas[$i]));
		}

		return $notas;

	}

	function notaTodasDiscAluno($matricula) { // O MÁXIMO QUE O ALUNO VERÁ
		// gera uma matriz com as notas de um aluno de todas as disciplinas em que está

		// consulta de que disciplinas o aluno faz parte
		$query = "SELECT Disciplina_Codigo_Disciplina FROM Disciplina_has_Alunos WHERE Alunos_Matricula = '".$matricula."'";
		$consulta = $GLOBALS['pdo']->query($query);
		$cod_disciplinas = array();
		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			array_push($cod_disciplinas, $linha['Disciplina_Codigo_Disciplina']);
		}

		$notas_aluno = array();
		for ($i=0; $i < count($cod_disciplinas); $i++) {
			$notas_aluno[$i] = array();
			$notas_aluno[$i][0] = $cod_disciplinas[$i];

			$notas_disciplina =  notasDisciplinaAluno($cod_disciplinas[$i], $matricula);
			for ($j=0; $j < count($notas_disciplina); $j++) {
				array_push($notas_aluno[$i], $notas_disciplina[$j]);
			}
		}

		return $notas_aluno;


	}

	function mediaDisciplinaAluno($cod_disciplina, $matricula) {
		$notas = notasDisciplinaAluno($cod_disciplina, $matricula);
		return round((array_sum($notas) / count($notas)), 1);
	}

	function notasDisciplinaAluno($cod_disciplina, $matricula) {
		// gera um array que tem todas as notas de um aluno na disciplina

		// consulta os códigos das avaliações que são da disciplina
		$query = "SELECT Codigo_Avaliacao FROM Avaliacoes WHERE Disciplina_Codigo_Disciplina = ".$cod_disciplina." ORDER BY Codigo_Avaliacao";
		$consulta = $GLOBALS['pdo']->query($query);
		$cod_avaliacoes = array();
		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			array_push($cod_avaliacoes, $linha['Codigo_Avaliacao']);
		}

		$notas = array();
		for ($i=0; $i < count($cod_avaliacoes); $i++) {
			array_push($notas, notaAvaliacao($cod_avaliacoes[$i], $matricula));
		}

		return $notas;
	}

	function notaAvaliacao($cod_avaliacao, $matricula) {
		$correcao = correcaoAvaliacao($cod_avaliacao, $matricula);

		$qtd_questoes = count($correcao);
		if($qtd_questoes > 0) {
			$valor_questao = 10 / $qtd_questoes;

			$nota = 0;
			for ($i=0; $i < $qtd_questoes; $i++) {
				$nota += $correcao[$i][1];
			}

			$nota *= $valor_questao;

			return round($nota,1);
		}
	}

	function correcaoAvaliacao($cod_avaliacao, $matricula) {
		// consulta os códigos das questões da prova
		$query = "SELECT Codigo_Questao FROM Questao Q, Questoes_has_Avaliacoes QA WHERE QA.Questoes_Codigo_Questao = Q.Codigo_Questao AND QA.Avaliacoes_Codigo_Avaliacao = ".$cod_avaliacao;
		$consulta = $GLOBALS['pdo']->query($query);
		$cod_questoes = array();
		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			array_push($cod_questoes, $linha['Codigo_Questao']);
		}

		$qtd_questoes = count($cod_questoes);

		// determina a pontuação que o aluno atingiu na prova
		$correcao = array();
		for ($i=0; $i < $qtd_questoes; $i++) {
			$correcao[$i] = array();
			$correcao[$i][0] = $cod_questoes[$i];
			$correcao[$i][1] = correcaoResposta($cod_questoes[$i], $matricula);
		}

		return $correcao;
	}

	function correcaoResposta($cod_questao, $matricula) {
		// consulta o tipo da questão
		$query = "SELECT Tipo_Codigo FROM Questao WHERE Codigo_Questao = ".$cod_questao;
		$consulta = $GLOBALS['pdo']->query($query);
		$linha = $consulta->fetch(PDO::FETCH_ASSOC);
		$tipo_codigo = $linha['Tipo_Codigo'];

		switch ($tipo_codigo) {
			case 1:
				return correcaoResposta_disc($cod_questao, $matricula);
				// retorna 0 (errada), 0.5 (meio certo) ou 1 (certo)
				// no banco de dados é 0errada 1meio 2certo
				// então a função retorna o valor do banco dividido por 2
				break;
			case 2:
				return correcaoResposta_unica($cod_questao, $matricula);
				// retorna 0 (errada) ou 1 (correta)
				break;
			case 3:
				return correcaoResposta_VouF($cod_questao, $matricula);
				// retorna a razão de qtd. alternativas acertadas sobre qtd. alternativas da questao
				// ou seja, se o usuário acertou 3 de 5 alternativas, o retorno é 0.6
				// assim, o valor máximo que a função retorna é 1 (5 acertadas sobre 5 alternativas -- todas)
				// o resultado passa pela função round() com precisão 1 (retorna com um número após a vírgula)
				break;
		}
	}

	function correcaoResposta_disc($cod_questao, $matricula) {
		// consulta o código da resposta que o usuário deu
		$query0 = "SELECT Codigo_Discursiva FROM Discursiva WHERE Alunos_Matricula = '".$matricula."' AND Questao_Codigo = ".$cod_questao;
		//var_dump($query0);
		$consulta = $GLOBALS['pdo']->query($query0);
		$linha = $consulta->fetch(PDO::FETCH_ASSOC);
		$cod_resposta = $linha['Codigo_Discursiva'];

		if(isset($cod_resposta)) {
			// consulta a correção da resposta que o usuário deu
			$query = "SELECT Correta FROM Discursiva WHERE Codigo_Discursiva = ".$cod_resposta.";";
			//var_dump($query);
			$consulta = $GLOBALS['pdo']->query($query);
			$linha = $consulta->fetch(PDO::FETCH_ASSOC);
			return ($linha['Correta'] / 2);
		} else {
			return 0;
		}
	}

	function correcaoResposta_unica($cod_questao, $matricula) {
		// consulta qual é a alternativa correta
		$query1 = "SELECT Codigo_Alternativa FROM Alternativa WHERE Correta = 1 AND Questao_Codigo = ".$cod_questao;
		$consulta = $GLOBALS['pdo']->query($query1);
		$linha = $consulta->fetch(PDO::FETCH_ASSOC);
		$alt_correta = $linha['Codigo_Alternativa'];

		// consulta se o aluno selecionou a alt_correta (a consulta retorna 1) ou se não selecionou (retorna 0) e a função já retorna isso
		$query2 = "SELECT Resposta FROM Resposta_Alternativa WHERE Alternativa_Alternativa_Codigo = ".$alt_correta." AND Alunos_Matricula = '".$matricula."'";
		//var_dump($query2);
		$consulta = $GLOBALS['pdo']->query($query2);
		$linha = $consulta->fetch(PDO::FETCH_ASSOC);
		return $linha['Resposta'];
	}

	function correcaoResposta_VouF($cod_questao, $matricula) {
		// OBS: AQUI A ORDEM É IMPORTANTE
		// consulta o conjunto de alternativas corretas e gera um array a partir disso
		// consulta, junto, quais são os códigos das alternativas, que são usados no array depois desse
		$query1 = "SELECT Codigo_Alternativa, Correta FROM Alternativa WHERE Questao_Codigo = ".$cod_questao." ORDER BY Codigo_Alternativa";

		$alt_corretas = array();
		$cod_alts = array();

		$consulta = $GLOBALS['pdo']->query($query1);
		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			array_push($alt_corretas, $linha['Correta']);
			array_push($cod_alts, $linha['Codigo_Alternativa']);
		}
		$qtd_alt = count($alt_corretas);

		// determina que valor os campos Alternativa_Alternativa_Codigo devem ter, em intervalo
		$inicial = $cod_alts[0];
		$final = $cod_alts[($qtd_alt-1)];
		// consulta o conjunto de respostas do usuário e gera um array a partir disso
		$query2 = "SELECT Resposta FROM Resposta_Alternativa WHERE Alternativa_Alternativa_Codigo >= ".$inicial." AND Alternativa_Alternativa_Codigo <= ".$final." AND Alunos_Matricula = '".$matricula."' ORDER BY Alternativa_Alternativa_Codigo";
		$consulta = $GLOBALS['pdo']->query($query2);
		$alt_respostas = array();
		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			array_push($alt_respostas, $linha['Resposta']);
		}

		if(count($alt_respostas) == count($alt_corretas)) {

			$qtd_acertos = 0;
			for ($i=0; $i < $qtd_alt; $i++) {
				if ($alt_corretas[$i] == $alt_respostas[$i]) {
					$qtd_acertos++;
				}
			}

			$correcao = $qtd_acertos / $qtd_alt;
			return round($correcao,2);
		} else {
			return 0;
		}
	}



//// Funções que criam a visualização das respostas ////////////

	function correcaoAvaliacao_view($cod_avaliacao, $matricula) {
		// consulta os códigos das questões da prova
		$query = "SELECT Codigo_Questao, Texto, Enunciado, Tipo_Codigo FROM Questao Q, Questoes_has_Avaliacoes QA WHERE QA.Questoes_Codigo_Questao = Q.Codigo_Questao AND QA.Avaliacoes_Codigo_Avaliacao = ".$cod_avaliacao." ORDER BY Codigo_Questao";
		$consulta = $GLOBALS['pdo']->query($query);
		$questoes = array();
		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			$questoes[$i] = array();
			array_push($questoes[$i], $linha['Codigo_Questao']);
			array_push($questoes[$i], $linha['Texto']);
			array_push($questoes[$i], $linha['Enunciado']);
			array_push($questoes[$i], $linha['Tipo_Codigo']);
		}

		$qtd_questoes = count($questoes);
		$valor_questao = round((10 / $qtd_questoes),2);

		// determina a pontuação que o aluno atingiu na prova
		$correcao = array();
		for ($i=0; $i < $qtd_questoes; $i++) {
			$correcao[$i] = correcaoResposta($questoes[$i][0], $matricula);
			$nota_questao[$i] = round(($correcao[$i] * $valor_questao), 2);
		}

		for ($i=0; $i < count($questoes); $i++) {
			$noQuestao = $i+1;
			echo "<div class='card-panel'>";
				echo "<p><b>".$noQuestao.")</b></p>";
				if(isset($questoes[$i][1])) { echo "<p><b>Texto:</b> ".$questoes[$i][1]."</p>"; }
				echo "<p><b>Enunciado: </b> ".$questoes[$i][2]."</p>";
			if(jaRespondeu($questoes[$i][0], $questoes[$i][3], $matricula)) {
				correcaoResposta_view($questoes[$i][0], $matricula);
				echo "<p><b>Pontuação do aluno: </b> ".$nota_questao[$i]." de ".$valor_questao."</p>";
			} else {
				echo "<div class='row'><b class='col red-text text-darken-2'>O aluno não respondeu a questão</b></div>";
				echo "<p><b>Pontuação do aluno: </b> 0 de ".$valor_questao."</p>";
			}
			echo "</div>";
		}
	}

	function correcaoResposta_view($cod_questao, $matricula) {
		// consulta o tipo da questão
		$query = "SELECT Tipo_Codigo FROM Questao WHERE Codigo_Questao = ".$cod_questao;
		$consulta = $GLOBALS['pdo']->query($query);
		$linha = $consulta->fetch(PDO::FETCH_ASSOC);
		$tipo_codigo = $linha['Tipo_Codigo'];

		switch ($tipo_codigo) {
			case 1:
				return correcaoResposta_view_disc($cod_questao, $matricula);
				// retorna 0 (errada), 0.5 (meio certo) ou 1 (certo)
				// no banco de dados é 0errada 1meio 2certo
				// então a função retorna o valor do banco dividido por 2
				break;
			case 2:
				return correcaoResposta_view_unica($cod_questao, $matricula);
				// retorna 0 (errada) ou 1 (correta)
				break;
			case 3:
				return correcaoResposta_view_VouF($cod_questao, $matricula);
				// retorna a razão de qtd. alternativas acertadas sobre qtd. alternativas da questao
				// ou seja, se o usuário acertou 3 de 5 alternativas, o retorno é 0.6
				// assim, o valor máximo que a função retorna é 1 (5 acertadas sobre 5 alternativas -- todas)
				// o resultado passa pela função round() com precisão 1 (retorna com um número após a vírgula)
				break;
		}
	}

	function correcaoResposta_view_VouF($cod_questao, $matricula) {
		// OBS: AQUI A ORDEM É IMPORTANTE
		// consulta o conjunto de alternativas corretas e gera um array a partir disso
		// consulta, junto, quais são os códigos das alternativas, que são usados no array depois desse
		$query1 = "SELECT Codigo_Alternativa, Descricao, Correta FROM Alternativa WHERE Questao_Codigo = ".$cod_questao." ORDER BY Codigo_Alternativa";

		$alt_corretas = array();
		$descricoes = array();
		$cod_alts = array();

		$consulta = $GLOBALS['pdo']->query($query1);
		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			array_push($alt_corretas, $linha['Correta']);
			array_push($descricoes, $linha['Descricao']);
			array_push($cod_alts, $linha['Codigo_Alternativa']);
		}
		$qtd_alt = count($alt_corretas);

		// determina que valor os campos Alternativa_Alternativa_Codigo devem ter, em intervalo
		$inicial = $cod_alts[0];
		$final = $cod_alts[($qtd_alt-1)];
		// consulta o conjunto de respostas do usuário e gera um array a partir disso
		$query2 = "SELECT Resposta FROM Resposta_Alternativa WHERE Alternativa_Alternativa_Codigo >= ".$inicial." AND Alternativa_Alternativa_Codigo <= ".$final." AND Alunos_Matricula = '".$matricula."' ORDER BY Alternativa_Alternativa_Codigo";
		$consulta = $GLOBALS['pdo']->query($query2);
		$alt_respostas = array();
		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			array_push($alt_respostas, $linha['Resposta']);
		}

		if(count($alt_respostas) == count($alt_corretas)) {

			$qtd_acertos = 0;
			for ($i=0; $i < $qtd_alt; $i++) {
				if ($alt_corretas[$i] == $alt_respostas[$i]) {
					$qtd_acertos++;
				}
			}

			$correcao = $qtd_acertos / $qtd_alt;
			$correcao = round($correcao,2);

			// VISUALIZAÇÃO
			echo "<div class='row'>
				<div class='col s7 m8 l9'>";
			for ($i=0; $i < count($descricoes); $i++) {
				if($alt_respostas[$i] == '1') $checked = ' checked ';
				else $checked = ' ';

				if($alt_respostas[$i] == $alt_corretas[$i]) {
					$txt0 = ' Certo ';
					$cor0 = ' green-text ';
				} else {
					$txt0 = ' Errado ';
					$cor0 = ' red-text ';
				}

				echo "<p>
					<label>
						<input type='checkbox' ".$checked." disabled/>
						<span>".$descricoes[$i]."</span>
						<span class='".$cor0."'>".$txt0."</span>
					</label>
				</p>";
			}
			echo "</div>";

			if ($correcao == 1) { $cor = ' green darken-2 '; $icon = ' done '; }
			else if ($correcao == 0) { $cor = ' red darken-2 '; $icon = ' close '; }
			else if ($correcao > 0 && $correcao < 1) { $cor = 'yellow darken-3'; $icon = ' waves '; }
			$txt = $qtd_acertos.' de '.$qtd_alt;

			echo "<div class='col s5 m4 l3 center-align'>";
			echo "<button class='".$cor." btn'><i class='material-icons left'>".$icon."</i>".$txt."</button>";
			echo "</div>
			</div>";

		} else {
			return 0;
		}
	}

	function correcaoResposta_view_unica($cod_questao, $matricula) {
		$query1 = "SELECT Codigo_Alternativa, Descricao, Correta FROM Alternativa WHERE Questao_Codigo = ".$cod_questao." ORDER BY Codigo_Alternativa";
		$consulta = $GLOBALS['pdo']->query($query1);

		$cod_alts = array();
		$desc_alts = array();
		for ($i=0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			array_push($cod_alts, $linha['Codigo_Alternativa']);
			array_push($desc_alts, $linha['Descricao']);
			if ($linha['Correta'] == 1) $correta = $linha['Codigo_Alternativa'];
		}

		$query2 = "SELECT Alternativa_Alternativa_Codigo, Resposta FROM Resposta_Alternativa WHERE Alternativa_Alternativa_Codigo >= ".$cod_alts[0]." AND Alternativa_Alternativa_Codigo <= ".$cod_alts[(count($cod_alts)-1)]." AND Alunos_Matricula = '".$matricula."' ORDER BY Alternativa_Alternativa_Codigo";
		$consulta = $GLOBALS['pdo']->query($query2);
		$alts_res = array();
		for ($i=0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			if($linha['Resposta'] == 1) $resposta = $linha['Alternativa_Alternativa_Codigo'];
		}

		if($correta == $resposta) {
			$correcao = 1;
		} else {
			$correcao = 0;
		}

		//// VISUALIZAÇAO ////////////////////

		echo "<div class='row'>
			<div class='col s7 m8 l9'>";

			for ($i=0; $i < count($cod_alts); $i++) {
				$span = '';
				if($cod_alts[$i] == $resposta) {
					$checked = ' checked ';
					if($correcao == 1) { $span = "<span class='green-text'>Certo</span>"; }
					else if ($correcao == 0) { $span = "<span class='red-text'>Errado</span>";  }
				}
				else {
					$checked = '';
					if($cod_alts[$i] == $correta) $span = "<span class='yellow-text text-darken-3'>Correção</span>";
				}

				echo "<p>
					<label>
						<input type='radio' ".$checked." disabled>
						<span>".$desc_alts[$i]."</span>
						".$span."
					</label>
				</p>";
			}
		echo "</div>";
		echo "<div class='col s5 m4 l3 center-align'>";
			if ($correcao == 1) {
				$txt = " Certo ";
				$cor = " green darken-2 ";
				$ico = " done ";
			} else {
				$txt = " Errado ";
				$cor = " red darken-2 ";
				$ico = " close ";
			}

			echo "<button class='btn ".$cor."'><i class='material-icons left'>".$ico."</i>".$txt."</button>";

		echo "</div>
		</div>";
	}

	function correcaoResposta_view_disc($cod_questao, $matricula) {
		// consulta o código da resposta que o usuário deu
		$query0 = "SELECT Codigo_Discursiva FROM Discursiva WHERE Alunos_Matricula = '".$matricula."' AND Questao_Codigo = ".$cod_questao;
		//var_dump($query0);
		$consulta = $GLOBALS['pdo']->query($query0);
		$linha = $consulta->fetch(PDO::FETCH_ASSOC);
		$cod_resposta = $linha['Codigo_Discursiva'];

		if(isset($cod_resposta)) {
			// consulta a correção da resposta que o usuário deu
			$query = "SELECT Resposta, Correta FROM Discursiva WHERE Codigo_Discursiva = ".$cod_resposta.";";
			//echo $query;
			//var_dump($query);
			$consulta = $GLOBALS['pdo']->query($query);
			$linha = $consulta->fetch(PDO::FETCH_ASSOC);

			echo "<div class='row'>
			<div class='col s7 m8 l9'>";
				echo "<p><b>Resposta: </b> ".$linha['Resposta']."</p>";
			echo "</div>";

			$correcao = $linha['Correta'];

			if(!isset($linha['Correta'])) $correcao = -1;


			switch ($correcao) {
				case -1: $cor = ' grey darken-2 '; $icon = ' help_outline ' ; $txt = ' Sem correção '; break;
				case 0: $cor = ' red darken-2 '; $icon=' close '; $txt = 'Errado'; break;
				case 1: $cor = ' yellow darken-3 '; $icon = ' waves '; $txt = 'Meio certo'; break;
				case 2: $cor = ' green darken-2 '; $icon = ' done '; $txt = 'Certo'; break;
			}

			echo "<div class='col s5 m4 l3 center-align'>";
				echo "<button class='".$cor." btn'><i class='material-icons left'>".$icon."</i>".$txt."</button>";
			echo "</div>
			</div>";

		} else {
			return 0;
		}
	}



	function jaRespondeu ($cod_questao, $tipo_questao, $matricula) {

	if ($tipo_questao == 1) {
		$query = "SELECT count(Alunos_Matricula) as 'Respondeu' FROM Discursiva WHERE Alunos_Matricula = '".$matricula."' AND Questao_Codigo = ".$cod_questao;
	} else {
		$query = "SELECT count(RA.Alunos_Matricula) as 'Respondeu' FROM Resposta_Alternativa RA, Alternativa A
		WHERE Alunos_Matricula = '".$matricula."'
		AND RA.Alternativa_Alternativa_Codigo = A.Codigo_Alternativa
		AND A.Questao_Codigo = ".$cod_questao;
	}

	$consulta = $GLOBALS['pdo']->query($query);

		$linha = $consulta->fetch(PDO::FETCH_ASSOC);
		$respondeu = $linha['Respondeu'];

		if ($respondeu > 0) return true;
		else return false;
	}
?>
