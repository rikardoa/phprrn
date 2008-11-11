<?php
/**
 * Arquivo de Configuração da Classe ConsultaPreparada
 * @author informatica <informatica@prrn.mpf.gov.br>
 * @version 1.0
 * @package Persistencia
 */
/**
 * classe ConsultaPreparada
 * Utilizada para executar consultas preparada para um Banco de Dados que retornam conjunto de dados.
 * Utilizar apenas nas ações no banco que não retornam conjunto de dados, como por exemplo INSERT,
 * UPDATES, DELETES, execução de StoreProcedures (que não retornam conjunto de dados e etc..).
 * No caso de INSERT, UPDATES e etc.. utilize a classe Comando ou ComandoPreparado.
 */
class ConsultaPreparada extends Consulta implements iPreparado {
	public function ConsultaPreparada($conexao=null, $sql=null) {
		parent::Consulta($conexao, $sql);
	}
	public function liga($campo, $valor){
		$this->resultado->bindParam(":" . $campo, $valor);
	}
}
//include_once "config.php";
//$cnxIntra = new Conexao("mysql", "localhost","test","root","");
//$cnxIntra->getConexao();
//$queryIntra = new ConsultaPreparada($cnxIntra, "SELECT * FROM pessoafisica WHERE NOME LIKE :nome OR ENDERECO LIKE :nome");
//$queryIntra->liga("nome","%jan%");
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