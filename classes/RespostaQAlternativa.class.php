<?php

# Esta classe prescreve objetos que são respostas à questões de alternativa
# como um todo, não um objeto para cada alternativa.
# Esta classe recebe as respostas de cada alternativa num array.
# Esta classe que é recebida, junto com objetos "RespostaDiscursiva",
# nos objetos "Questao" no atributo array "respostas"

require_once "autoload.php";


class RespostaQAlternativa extends AbsCodigo {
    private $respostas = array();

    function __construct(){
        $resposta = new Resposta1Alternativa;
    }

    public function setResposta($resposta){
        if($resposta instanceof Resposta1Alternativa) array_push($this->respostas,$resposta);}
    public function getRespostas(){return $this->respostas;}

    function __toString() {
        $txt = "<div class='resposta'>[Resposta]".parent::__toString()." <br>| {Respostas}";
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