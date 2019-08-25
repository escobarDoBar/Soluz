<?php

require_once "autoload.php";

class Escola extends AbsCodigoDescricao {
    private $series = array();

    public function setSerie($serie){
        if($serie instanceof Serie) array_push($this->series, $serie); }
    public function getSerie(){return $this->series;}

    public function __toString() {
        $txt = parent::__toString();
        $txt .= " | {Séries}: ";
        for ($i=0; $i < count($this->series); $i++) { 
            $txt .= $this->series[$i].", "; }
        return $txt;
    }
}

?>