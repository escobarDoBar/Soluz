<?php

require_once "autoload.php";

class Aluno extends AbsUsuario {
    private $respostas = array();

    public function setResposta($resposta){
        if($resposta instanceof RespostaQAlternativa || $resposta instanceof RespostaDiscursiva)
            array_push($this->respostas,$resposta); }
    public function getRespostas(){return $this->respostas;}

    function __toString(){
        $txt = "<div class='aluno'>[Aluno]".parent::__toString();
        $txt .= "<br>{Respostas}";
        $txt .= "<ul>";
        for ($i=0; $i < count($this->respostas); $i++) { 
            $txt .= "<li>".$this->respostas[$i]."</li>"; }
        $txt .= "</ul>";
        $txt .= "</div>";

        return $txt;
    }
}

?>