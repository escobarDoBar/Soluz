<?php

require_once "autoload.php";

class QuestaoDiscursiva extends AbsQuestao implements ISetRespostaQuestao {  
    private $respostas = array();

    public function setResposta($resposta){
        if($resposta instanceof RespostaDiscursiva) array_push($this->respostas,$resposta); }
    public function getRespostas(){return $this->respostas;}

    public function __toString(){
        $txt = "<div class='questao'>[Questão]".parent::__toString();
        $txt .= "<br>{Respostas}";
        $txt .= "<ul>";
        for ($i=0; $i < count($this->respostas); $i++) { 
            $txt .= "<li>".$this->respostas[$i]."</li>";
        }
        $txt .= "</ul>";
        $txt .= "</div>";

        return $txt;
    }
}

?>