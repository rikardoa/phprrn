<?php
/**
 * Arquivo de Configura��o da Classe TConexao
 * @author Alan Gustavo Santana Ribeiro <alan@prrn.mpf.gov.br> em 18/08/2008
 * @version 1.0
 * @package Persistencia
 */
/**
 * classe gcConexao
 * Gerencia as conex�es com banco de dados
 */
class Conexao {
	/**
	 * Variavel que guarda o tipo de banco de dados a ser utilizado na conexao.
	 * Os tipos previstos s�o:
	 * pgsql - PostgreSQL
	 * mysql - MySQL
	 * sqlite- SQLite
	 * ibase - InterBase
	 * oci8	 - Oracle
	 * mssql - Microsoft SQL
	 * IMPORTANTE: Para que os tipos funcionem � preciso que o pdo PHP Data Object
	 * e o respectivo driver pdo_<driver> esteja devidamente instalado.
	 * Exemplo: Para utilizar com o oracle � necess�rio que o pdo_oci8 esteja
	 * instalado e funcionando. Verifique na documenta��o do PHP.
	 * @access private
	 * @var string tipo do banco de dados
	 */
	private $tipo;
	/**
	 * @access private
	 * @var string endere�o ou o nome do servidor de banco de dados
	 */
	private $servidor;
	/**
	 * @access private;
	 * @var string nome do banco de dados, base de dados, servi�o ou schema
	 */
	private $base;
	/**
	 * @access private;
	 * @var string nome do usu�rio do banco de dados
	 */
	private $usuario;
	/**
	 * @access private;
	 * @var string senha do usu�rio do banco de dados
	 */
	private $senha;
	/**
	 * @access private;
	 * @var int porta utilizada para o Banco de Dados
	 */
	private $porta;
	/**
	 * @access private;
	 * @var object Objeto do pdo_mysql
	 */
	private $conexao = null;
	/**
	 * Construtor da Classe
	 * @param string $tipo tipo do banco de dados
	 * @param string $servidor endere�o ou o nome do servidor de banco de dados
	 * @param string $base nome do banco de dados, base de dados, servi�o ou schema
	 * @param string $usuario nome do usu�rio do banco de dados utilizado na conexao
	 * @param string $senha senha do usu�rio do banco de dados utilizado na conexao
	 */
	public function __construct($tipo, $servidor, $base, $usuario, $senha, $porta = false) {
		$this->tipo = $tipo;
		$this->servidor = $servidor;
		$this->base = $base;
		$this->usuario = $usuario;
		$this->senha = $senha;
		$this->porta = $porta;
	}
	/**
	 *  Cria a conex�o e retorna um objeto do tipo PDO
	 */
	public function getConexao() {
		if ($this->conexao == null) {
			switch ($this->tipo) {
				case 'pgsql' :
					$this->conexao = new PDO("pgsql:dbname={$this->base};user={$this->usuario}; password={$this->senha};host=$this->servidor");
					break;
				case 'mysql' :
					$this->conexao = new PDO("mysql:dbname=$this->base;host=$this->servidor", $this->usuario, $this->senha);
					break;
				case 'sqlite' :
					$this->conexao = new PDO("sqlite:{$this->base}");
					break;
				case 'ibase' :
					$this->conexao = new PDO("firebird:dbname={$this->base}", $this->usuario, $this->senha);
					break;
				case 'oci8' :
					$this->conexao = new PDO("oci:dbname={$this->base}", $this->usuario, $this->senha);
					break;
				case 'mssql' :
					$this->conexao = new PDO("mssql:host={$this->servidor},1433;dbname={$this->base}", $this->usuario, $this->senha);
					break;
			}

			// define para que o PDO lance exce��es na ocorr�ncia de erros
			$this->conexao->setAttribute(PDO :: ATTR_ERRMODE, PDO :: ERRMODE_EXCEPTION);
		}
		// retorna o objeto instanciado.
		return $this->conexao;
	}
}
?>
