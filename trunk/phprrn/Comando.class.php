<?php
/**
 * Arquivo de Configuração da Classe Comando
 * @author informatica <informatica@prrn.mpf.gov.br>
 * @version 1.0
 * @package Persistencia
 */
/**
 * classe Comando
 * Utilizada para executar instruções SQL em um Banco de Dados que NÃO retornam conjunto de dados.
 * Como por exemplo INSERT, UPDATES, DELETES, execução de StoreProcedures (que não retornam conjunto
 * de dados e etc..)
 */
class Comando {
	/**
	 * Variavel que armazena a string de consulta a ser utilizada no Banco de Dados
	 * @access private;
	 * @var string Ultimo Comando SQL usado
	 */
	private $stringSQL;
	/**
	 * Variavel que armazena a conexao ao Banco de Dados
	 * @access private;
	 * @var PDO - Conexao com o Banco de Dados
	 */
	 private $conexao = null;
	/**
	 * Construtor da Classe
	 * @param object $conexao Objeto do tipo MConexao
	 * @param string $sql comando SQL a ser executado
	 */
	public function Comando($conexao=null, $sql=null) {
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
	}
	/**
	 * Método que executa a Instrução SQL e retorna a quantidade de registros afetados.
	 */
	public function executa(){
		$ret = $this->conexao->exec($this->stringSQL);
		if(!$ret){
			$erro = ":Problemas na Execucao do Comando SQL!";
			die(__FILE__ . ":" . __LINE__ . $erro);
		}
		return $ret;
	}
}
//include_once "config.php";
//$cnxIntra = new Conexao("mysql", "localhost","test","root","");
//$sql = "INSERT INTO `TIPO_AMB` (`ID`, `DESCRICAO`) VALUES
//  (1,'Honorários Médicos'),
//  (2,'Consultas'),
//  (3,'Exames e Outros'),
//  (4,'m² Filme')";
//$queryIntra = new Comando($cnxIntra, $sql);
//echo $queryIntra->executa();
?>
