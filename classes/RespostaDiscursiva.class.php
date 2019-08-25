<?php

require_once "autoload.php";

class RespostaDiscursiva extends AbsCodigo {
    private $resposta; # Resposta discursiva do aluno -- o que ele escreveu como resposta
    private $correcao; # Comentário do professor sobre a resposta: porque errou, o que faltou, se foi boa etc.
    private $pontuacao; # Pontuação que professor dá
    # A pontuação das questões alternativas é feita automaticamente pelo código -- não existe como atributo

    public function setResposta($resposta){$this->resposta=$resposta;}
    public function getResposta(){return $this->resposta;}

    public function setCorrecao($correcao){$this->correcao=$correcao;}
    public function getCorrecao(){return $this->correcao;}

    public function setPontuacao($pontuacao){$this->pontuacao=$pontuacao;}
    public function getPontuacao(){return $this->pontuacao;}

    function __toString() {
        $txt = "<div class='resposta'>[Resposta]".parent::__toString();
        $txt .= " | Pontuação: ".$this->pontuacao;
        $txt .= "<br> | Resposta do aluno: ".$this->resposta;
        $txt .= "<br> | Correção do professor: ".$this->correcao;
        $txt .= "</div>";

        return $txt;
    }

}

?>