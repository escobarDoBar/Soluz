<?php

require_once "autoload.php";

class Avaliacao extends AbsCodigo {

	private $codigo_avaliacao;
	private $conteudo;
	private $dataInicio;
	private $dataFim;
	private $peso;
	private $questoes = array();
	private $embaralhar; # binário: embaralhar ou não
	# embaralha não só a ordem das questões
	# como a ordem das alternativas de cada questão também
	
	public function setCodigo_Avaliacao($codigo_avaliacao){$this->codigo_avaliacao = $codigo_avaliacao;}
	public function getCodigo_Avaliacao(){return $this->codigo_avaliacao;}

	public function setConteudo($conteudo){$this->conteudo = $conteudo;}
	public function getConteudo(){return $this->conteudo;}

	public function setDataInicio($dataInicio){$this->dataInicio = $dataInicio;}
	public function getDataInicio(){return $this->dataInicio;}

	public function setDataFim($dataFim){$this->dataFim = $dataFim;}
	public function getDataFim(){return $this->dataFim;}

	public function setQuestao($questao){
		if($questao instanceof QuestaoAlternativa || $questao instanceof QuestaoDiscursiva)
			array_push($this->questoes, $questao); }
	public function getQuestao(){return $this->questao; }

	public function setPeso($peso){$this->peso = $peso; }
	public function getPeso(){return $this->peso; }
	
	public function setEmbaralhar($embaralhar){$this->embaralhar = $embaralhar; }
    public function getEmbaralhar(){return $this->embaralhar; }

	public function __toString(){
		$txt = "<div class='avaliacao'>[Avaliação]".parent::__toString();
		$txt .= " | Peso: ".$this->peso;
		$txt .= " | Conteúdo: ".$this->conteudo;
		$txt .= " | Data início: ".$this->dataInicio;
		$txt .= " | Data fim: ".$this->dataFim;
		$txt .= " | Embaralhar: ".$this->embaralhar;
		$txt .= "<br> | {Questões}: ";
		$txt .= "<ol>"; # Lista de questões
		for ($i=0; $i < count($this->questoes); $i++) { 
			$txt .= "<li>".$this->questoes[$i]."</li><br>";
		}
		$txt .= "</ol>"; # Fim lista de questões
		$txt .= "</div>";
		return $txt;
	}
}

?>