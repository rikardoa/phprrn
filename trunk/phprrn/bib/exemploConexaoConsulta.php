<?php
/**
 * Arquivo de Exemplo do Uso da Classe Conexao e Consulta
 */
include_once "config.php";

class Tipo_Amb extends Modelo {
	protected $id;
	protected $Descricao;
}
// Você pode extender o OAD para implementar insert, updates e deletes personalizados
class OADTipo_Amb extends OAD {
	//Optamos por não sobrescrever nenhum dos métodos.
	//Se não for sobrescrito, o OAD faz select,insert,update e delete automaticamente.
}
$cnxIntra = new Conexao("mysql", "localhost","test","root","");
$a = new Tipo_Amb();
$a->Descricao = "Teste de Funcionamento";

$o = new OADTipo_Amb($cnxIntra, $a);
$o->insert();
echo $a->id;
die();
$a->Descricao = "Descricao Atualizada";
$o->select(10);
echo $a->Descricao;

die();


$sql = "INSERT INTO `TIPO_AMB` (`ID`, `DESCRICAO`) VALUES (:id, :valor)";
$queryIntra = new ComandoPreparado($cnxIntra, $sql);
$queryIntra->liga("id", null);
$queryIntra->liga("valor", "UNIMED");
echo $queryIntra->executa(); // Retorna 4 (Que é a quantidade de Registros afetados)!













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