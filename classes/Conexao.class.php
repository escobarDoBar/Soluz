<?php
/**
 * Classe de conexão ao banco de dados usando PDO no padrão Singleton.
 * Modo de Usar:
 * $db = Database::getInstance();
 * E agora use as funções do PDO (prepare, query, exec) em cima da variável $db.
 */

class Conexao {
	protected static $instance;

	private function __construct ()
	{
		$db_host	=	"localhost";
		$db_name	=	"prove_sistema_avaliacao";
		$db_user	=	"root";
		$db_pass	=	"";
		$db_driver	=	"mysql";

		try	{
			self::$instance = new PDO("$db_driver:host=$db_host; dbname=$db_name", $db_user, $db_pass);
			self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			self::$instance->exec('SET NAMES utf8');
		}
		catch (PDOException $e)	{
			echo "Erro: ".$e->getMessage();
		}
	}

	public static function getInstance() {
		if(!self::$instance) {			
			new Conexao();
		}
		return self::$instance;
	}
}
?>