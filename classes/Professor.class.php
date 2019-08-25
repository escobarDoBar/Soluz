<?php

require_once "autoload.php";

class Professor extends AbsUsuario implements IGetValoresEmVetorPDO {
	private $avaliacoes = array();
	private $questoes = array();

	public function setAvaliacao($avaliacao){
		if($avaliacao instanceof Avaliacao) array_push($this->avaliacoes,$avaliacao); }  
	public function getAvaliacoes(){return $this->avaliacoes; }

	public function setQuestao($questao){
		if ($questao instanceof QuestaoDiscursiva || $questao instanceof QuestaoAlternativa)
			array_push($this->questoes, $questao); }  
	public function getQuestoes(){return $this->questoes; }

	function __toString(){
		$txt = "<div class='professor'>[Professor]".parent::__toString();
		$txt .= "{Avaliações}";
		$txt .= "<ul>";
		for ($i=0; $i < count($this->avaliacoes); $i++) { 
			$txt .= "<li>".$this->avaliacoes[$i]."</li>"; }
		$txt .= "</ul>";
		$txt .= "{Questões}";
		$txt .= "<ul>";
		for ($i=0; $i < count($this->questoes); $i++) { 
			$txt .= "<li>".$this->questoes[$i]."</li>"; }
		$txt .= "</ul>";
		$txt .= "</div>";

		return $txt;
	}
 
	#### MÉTODOS #######################################################################################

	public function getValoresEmVetorPDO() {
		// OBS: TEM QUE SER NA MESMA ORDEM DOS CAMPOS NA TABELA DO BD
		// É usado na página do PDO -- possibilita que ela seja genérica/universal
		$vetor = array();
		array_push($vetor, parent::getMatricula());
		array_push($vetor, parent::getNome());
		array_push($vetor, parent::getDataNascimento());
		array_push($vetor, parent::getUltimoLogin());
		array_push($vetor, parent::getSenha());
		return $vetor;
	}
	
}

?>