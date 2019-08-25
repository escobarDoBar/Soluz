<?php

require_once "autoload.php";

abstract class AbsCodigoDescricao extends AbsCodigo {

	private $descricao;



	public function setDescricao($descricao) {
		$this->descricao=$descricao;
	}
	public function getDescricao() {
		return $this->descricao;
	}



	public function __toString() {
		return parent::__toString()." | "."Descrição: ".$this->descricao;
	}
	
}
?>