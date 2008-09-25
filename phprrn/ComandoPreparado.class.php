<?php
/**
 * Arquivo de Configuração da Classe ComandoPreparado
 * @author informatica <informatica@prrn.mpf.gov.br>
 * @version 1.0
 * @package Persistencia
 */
/**
 * classe ComandoPreparado
 * Utilizada para executar instruções SQL em um Banco de Dados que NÃO retornam conjunto de dados.
 * Como por exemplo INSERT, UPDATES, DELETES, execução de StoreProcedures (que não retornam conjunto
 * de dados e etc..)
 */
class ComandoPreparado extends Comando implements iPreparado {
	public function ComandoPreparado($conexao=null, $sql=null) {
		parent::Comando($conexao, $sql);
	}
	public function liga($campo, $valor){
		$this->resultado->bindParam(":" . $campo, $valor);
	}

}
//include_once "config.php";
//$cnxIntra = new Conexao("mysql", "localhost","test","root","");
//$sql = "INSERT INTO `TIPO_AMB` (`ID`, `DESCRICAO`) VALUES (:id, :valor)";
//$queryIntra = new ComandoPreparado($cnxIntra, $sql);
//$queryIntra->liga("id", null);
//$queryIntra->liga("valor", "UNIMED");
//echo $queryIntra->executa(); // Retorna 4 (Que é a quantidade de Registros afetados)!

?>

