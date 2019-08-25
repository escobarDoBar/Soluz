<?php

require_once "autoload.php";

class QuestaoAlternativa extends AbsQuestao implements ISetRespostaQuestao {
    private $alternativas = array();
    private $respostas = array();

    public function setAlternativa($alternativa){
		if($alternativa instanceof Alternativa) array_push($this->alternativas, $alternativa); }
    public function getAlternativa(){return $this->alternativa; }

    public function setResposta($resposta){
        if($resposta instanceof RespostaQAlternativa) array_push($this->respostas, $resposta); }
    public function getRespostas(){return $this->respostas;}
    
    function __toString(){
        $txt = "<div class='questao'>[Questão]".parent::__toString();
        $txt .= "{Alternativas}";
        $txt .= "<ol>"; #Lista de alternativas
        for ($i=0; $i < count($this->alternativas); $i++) { 
            $txt .= "<li>".$this->alternativas[$i]."</li>";
        }
        $txt .= "</ol>";
        $txt .= "{Respostas}";
        $txt .= "<ul>";
        for ($i=0; $i < count($this->respostas); $i++) { 
            $txt .= "<li>".$this->respostas[$i]."</li>";
        }
        $txt .= "</ul>";

        $txt .= "</div>";

        return $txt;
    }

    #### MÉTODOS ###################################################################################

    #embaralhar alternativas
}

?>