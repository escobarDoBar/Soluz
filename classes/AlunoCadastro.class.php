<?php
	
	require_once "autoload.php";

	/* Classe para pré-cadastro

	O atributo "cadastrado" deverá ter dois estados: 0 ou 1 

	Quando o aluno se cadastrar numa disciplina, o objeto "Aluno" 
	deverá ser registrado como atributo no objeto "AlunoCadastro"
	com o atributo "cadastrado" com valor 0.
	Somente o professor poderá mudar esse valor para 1.
	Somente quando o valor for igual a 1 o aluno terá acesso a disciplina.

	O aluno terá um cadastro (objeto "AlunoCadastro")
	para cada disciplina em que tiver se registrado */

	class AlunoCadastro {
		private $cadastrado;
		private $aluno;

		function __construct() {
			$aluno = new Aluno;
		}

		public function setCadastrado($cadastrado){$this->cadastrado = $cadastrado;}
		public function getCadastrado(){return $this->cadastrado;}

		public function setAluno($aluno){
			if($aluno instanceof Aluno) $this->aluno = $aluno; }
		public function getAluno(){return $this->aluno;}

		public function __toString(){
			return $this->aluno." | Cadastrado: ".$this->cadastrado;
		}

	}
?>