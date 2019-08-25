<?php

# Resposta de apenas uma alternativa
# Ou seja, se o aluno marcou ela como verdadeira ou como falsa
# (nas questões de única escolha, apenas uma é verdadeira
# enquanto todas as outras automaticamente são falsas -- isso é feito usando "radio"
# em vez de checkbox (o que será usado nas V ou F))

# O "1" no nome da classe se refere a isso: ser registrada como objeto
# apenas a resposta de uma alternativa

require_once "autoload.php";

class Resposta1Alternativa extends AbsCodigo {
    private $resposta;

    public function setResposta($resposta){$this->resposta=$resposta;}
    public function getResposta(){return $this->resposta;}

    function __toString(){
        return "<div class='resposta-1alternativa'>[Resposta da alternativa] ".parent::__toString()." | Resposta: ".$this->resposta."</div>";
    }

}

?>