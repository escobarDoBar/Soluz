<?php

require_once "autoload.php";

class Alternativa extends AbsCodigoDescricao {
	private $correta; # Atributo binário (é a alternativa correta ou não)
	private $respostas = array(); # Resposta de cada aluno para cada alternativa de uma questão -- não para a questão como um todo

	public function setCorreta($correta){$this->correta=$correta;}
	public function getCorreta(){return $this->correta;}

	public function setResposta($resposta){
		if($resposta instanceof Resposta1Alternativa) array_push($this->respostas, $resposta);}
	public function getRespostas(){return $this->respostas;}

	public function __toString(){
		$txt = parent::__toString();
		$txt .= " | Correta: ".$this->correta;
		$txt .= " <br>| {Respostas} ";
		$txt .= "<ul>";
		for ($i=0; $i < count($this->respostas); $i++) { 
			$txt .= "<li>".$this->respostas[$i]."</li>";
		}
		$txt .= "</ul>";

		return $txt;
		
	}
}

?>