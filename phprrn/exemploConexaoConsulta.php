<?php
/**
 * Arquivo de Exemplo do Uso da Classe Conexao e Consulta
 */
include_once "config.php";
$cnxIntra = new Conexao("mysql", "localhost","test","root","");
$cnxIntra->getConexao();
$queryIntra = new ConsultaPreparada($cnxIntra, "SELECT * FROM pessoafisica WHERE NOME LIKE :nome OR ENDERECO LIKE :nome");
$queryIntra->liga("nome","%jan%");
echo "<br>Numero de Registros:" . $queryIntra->getQtdeLinhas();
echo "<br>Numero de Campos:" . $queryIntra->getQtdeCampos();
echo "<br>Cabecalho:<br>";
foreach($queryIntra->getCampos() as $Campo){
	echo $Campo . "<br>";
}
foreach($queryIntra->getResultados()as $linha){
	foreach($linha as $campo=>$valor){
		echo "$campo = $valor";
	}
	echo "<br>";
}












//include_once "config.php";
//$cnxIntra = new Conexao("mysql", "localhost","test","root","");
//$cnxIntra->getConexao();
//$queryIntra = new ConsultaPreparada($cnxIntra, "SELECT NOME, IRA FROM CONCURSO_ESTAGIARIO WHERE NOME LIKE :nome");
//$queryIntra->liga("nome", "%ALAN%");
//
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