<?php
/**
 * Arquivo de Configura��o da Classe Comando
 * @author informatica <informatica@prrn.mpf.gov.br>
 * @version 1.0
 * @package Persistencia
 */
/**
 * classe Comando
 * Utilizada para executar instru��es SQL em um Banco de Dados que N�O retornam conjunto de dados.
 * Como por exemplo INSERT, UPDATES, DELETES, execu��o de StoreProcedures (que n�o retornam conjunto
 * de dados e etc..)
 */
class Comando {
	/**
	 * Variavel que armazena a string de consulta a ser utilizada no Banco de Dados
	 * @access protected;
	 * @var string Ultimo Comando SQL usado
	 */
	protected $stringSQL;
	/**
	 * Variavel que armazena a conexao ao Banco de Dados
	 * @access protected;
	 * @var PDO - Conexao com o Banco de Dados
	 */
	protected $conexao = null;
	/**
	 * Variavel que armazena o conjunto de resultados
	 * @access protected;
	 * @var array array contendo o conjunto de resultados
	 */
	protected $resultado = null;
	/**
	 * Construtor da Classe
	 * @param object $conexao Objeto do tipo MConexao
	 * @param string $sql comando SQL a ser executado
	 */
	public function Comando($conexao=null, $sql=null) {
		if(!is_a($conexao, "Conexao")){
			$erro = ":A Conexao n�o foi informada ou o 1� parametro n�o � uma Conexao!";
			die(__FILE__ . ":" . __LINE__ . $erro);
		} else {
			$this->conexao	= $conexao->getConexao();
		}
		if(empty($sql)){
			$erro = ":� preciso informar uma String de Consulta ao Banco de Dados!";
			die(__FILE__ . ":" . __LINE__ . $erro);

		}
		$this->stringSQL= $sql;
		$this->resultado= $this->conexao->prepare($this->stringSQL);
	}
	/**
	 * M�todo que executa a Instru��o SQL e retorna a quantidade de registros afetados.
	 */
	public function executa(){
		$ret = $this->resultado->execute();
		if(!$ret){
			$erro = ":Problemas na Execucao do Comando SQL!";
			die(__FILE__ . ":" . __LINE__ . $erro);
		}
		return $this->resultado->rowCount();
	}
	/**
	 * M�todo que retorna o �ltimo ID inserido.
	 */
	public function getUltimoId(){
		return $this->conexao->lastInsertId();
	}
}
//include_once "config.php";
//$cnxIntra = new Conexao("mysql", "localhost","test","root","");
//$sql = "INSERT INTO `TIPO_AMB` (`ID`, `DESCRICAO`) VALUES
//  (1,'Honor�rios M�dicos'),
//  (2,'Consultas'),
//  (3,'Exames e Outros'),
//  (4,'m� Filme')";
//$queryIntra = new Comando($cnxIntra, $sql);
//echo $queryIntra->executa(); // Retorna 4 (Que � a quantidade de Registros afetados)!
?>
