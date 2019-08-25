<?php

require_once "autoload.php";

class Disciplina extends AbsCodigoDescricao {
	private $professores = array();
	private $alunos = array();
	private $avaliacoes = array();

	function __construct() {
		$professor = new Professor;
		$aluno = new Aluno;
		$avaliacao = new Avaliacao;
	}

	public function setProfessor($professor){
		if ($professor instanceof Professor) array_push($this->professores, $professor); }
	public function getProfessor(){return $this->professor;}

	public function setAluno($aluno){
		if ($aluno instanceof AlunoCadastro) array_push($this->alunos, $aluno); }
	public function getAluno(){return $this->aluno;}

	public function setAvaliacao($avaliacao){
		if ($avaliacao instanceof Avaliacao) array_push($this->avaliacoes, $avaliacao); }
	public function getAvaliacao(){return $this->avaliacao;}


	public function __toString() {
		$txt = "<div class='disciplina'>[Disciplina]".parent::__toString();
		$txt .= "<dl>";
		$txt .= "<dt>{Professores}</dt>";
		$txt .= "<dd> <dl>"; # Lista dentro de uma lista (lista de professores dentro da lista de atributos da disciplina)
		for ($i=0; $i < count($this->professores); $i++) { 
			$txt .= "<dt>Professor</dt> <dd>".$this->professores[$i]."</dd>"; }
		$txt .= "</dl> </dd>"; # Fim da lista de professores

		$txt .= "<dt>{Alunos}</dt>";
		$txt .= "<dd> <dl>"; # Lista dentro de uma lista (lista de alunos dentro da lista de atributos da disciplina)
		for ($i=0; $i < count($this->alunos); $i++) { 
			$txt .= "<dt>Aluno</dt> <dd>".$this->alunos[$i]."</dd>"; }
		$txt .= "</dl> </dd>";

		$txt .= "<dt>{Avaliações}</dt>";
		$txt .= "<dd><dl>";
		for ($i=0; $i < count($this->avaliacoes); $i++) { 
			$txt .= "<dt>Avaliação</dt> <dd>".$this->avaliacoes[$i]."</dd>"; }
		$txt .= "</dl></dd>";

		$txt .= "</div>";
		return $txt;
	}
}

?>