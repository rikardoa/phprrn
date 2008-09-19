<?php
/**
 * Arquivo de Configuração da Classe Consulta
 * @author informatica <informatica@prrn.mpf.gov.br>
 * @version 1.0
 * @package Persistencia
 */
/**
 * classe Consulta
 * Utilizada para executar consultas SQL em um Banco de Dados que retornam conjunto de dados.
 * Para as ações no banco que não retornam conjunto de dados, como por exemplo INSERT, UPDATES
 * DELETES, execução de StoreProcedures (que não retornam conjunto de dados e etc..). Para esse
 * caso utilize a classe Comando
 */
class Consulta {
	/**
	 * Variavel que armazena a string de consulta a ser utilizada no Banco de Dados
	 * @access protected;
	 * @var string Ultimo Comando SQL usado
	 */
	protected $stringSQL;
	/**
	 * Variavel que armazena o conjunto de resultados
	 * @access protected;
	 * @var array array contendo o conjunto de resultados
	 */
	protected $resultado = null;
	/**
	 * Variavel que armazena o conjunto de campos
	 * @access protected;
	 * @var array array contendo o conjunto de campos
	 */
	protected $campos = null;
	/**
	 * Variavel que armazena a conexao ao Banco de Dados
	 * @access protected;
	 * @var PDO - Conexao com o Banco de Dados
	 */
	protected $conexao = null;
	/**
	 * Variavel que verifica se já houve a execucação da consulta
	 * @access protected;
	 * @var boolean
	 */
	protected $exec = false;
	/**
	 * Construtor da Classe
	 * @param object $conexao Objeto do tipo MConexao
	 * @param string $sql comando SQL a ser executado
	 */
	public function Consulta($conexao=null, $sql=null) {
		if(!is_a($conexao, "Conexao")){
			$erro = ":A Conexao não foi informada ou o 1º parametro não é uma Conexao!";
			die(__FILE__ . ":" . __LINE__ . $erro);
		} else {
			$this->conexao	= $conexao->getConexao();
		}
		if(empty($sql)){
			$erro = ":É preciso informar uma String de Consulta ao Banco de Dados!";
			die(__FILE__ . ":" . __LINE__ . $erro);

		}
		$this->stringSQL= $sql;
		$this->resultado= $this->conexao->prepare($this->stringSQL);
	}
	/**
	 * Método que retorna um array com os resultados retornados da consulta ao banco de dados
	 */
	public function getResultados(){
		$this->executa();
		return $this->resultado->fetchAll(PDO::FETCH_ASSOC);
	}
	/**
	 * Método que retorna um array com todos os campos retornados na consulta ao banco de dados
	 */
	public function getCampos(){
		$this->executa();
		$qtd 		= $this->getQtdeCampos();

		for($i=0; $i<$qtd; $i++){
			$campo = $this->resultado->getColumnMeta($i);
			$campos[] = $campo["name"];
		}
		return $campos;
	}
	/**
	 * Método que retorna a quantidade de Campos retornados
	 */
	public function getQtdeCampos(){
		$this->executa();
		return $this->resultado->columnCount();
	}
	/**
	 * Método que retorna a quantidade de Registros
	 */
	public function getQtdeLinhas(){
		$this->executa();
		return $this->resultado->rowCount();
	}
	/**
	 * Método que executa a consulta
	 */
	public function executa(){
		if(!$this->exec){
			$this->resultado->execute();
			$this->exec = true;
		}
	}
}
//include_once "config.php";
//$cnxIntra = new Conexao("mysql", "host","base","usuario","senha");
//$cnxIntra->getConexao();
//$queryIntra = new Consulta($cnxIntra, "SELECT NOME, IRA FROM CONCURSO_ESTAGIARIO WHERE NOME LIKE '%RIBEIRO%'");
//echo "<br>Numero de Registros:" . $queryIntra->getQtdeLinhas();
//echo "<br>Numero de Campos:" . $queryIntra->getQtdeCampos();
//echo "<br>Cabecalho:<br>";
//foreach($queryIntra->getCampos() as $Campo){
//	echo $Campo . "<br>";
//}
//foreach($queryIntra->getResultados()as $linha){
//	foreach($linha as $campo=>$valor){
//		echo "$campo = $valor";
//	}
//	echo "<br>";
//}
?>
