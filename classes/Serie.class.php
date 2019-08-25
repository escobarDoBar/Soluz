<?php

require_once "autoload.php";

class Serie extends AbsCodigoDescricao {
	private $disciplinas = array();

	public function setDisciplina($disciplina) {
		if($disciplina instanceof Disciplina) {
			array_push($this->disciplinas, $disciplina);
		}
	}

	public function getDisciplinas() {
		return $this->disciplinas;
	}
}

?>