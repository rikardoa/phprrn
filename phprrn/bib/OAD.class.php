<?php
/**
 * Arquivo de Defini��o da Classe OAD
 * LEIA!
 * 1) classe OAD
 * � utilizada para implementar a persist�ncia de modelos. A classe recebe um objeto
 * do tipo conexao com o banco de dados e um modelo. Utilizando os m�todos da classe
 * OAD � poss�vel realizar o CRUD - Create, Retrieve Update e Delete. De forma mais
 * simples a classe OAD far� a persist�ncia dos models, criando m�todos autom�ticos
 * para inser��o, atualiza��o, dele��o e sele��o.
 *
 * 2) Herdando um OAD
 * As vezes a inser��o simples n�o funciona, pois h� mais coisas para fazer na inser��o,
 * atualiza��o e dele��o. Nesse caso, � poss�vel criar uma especializa��o de um OAD para
 * tratar especificamente da persist�ncia de um modelo. E a� ser� poss�vel modificar o
 * nome da tabela (que por padr�o � o nome da classe Modelo), a chave prim�ria (que por
 * padr�o � considerado o primeiro atributo do Modelo), ou seja, se voc� precisar alterar
 * qualquer um desses padr�es basta sobrescrever os m�todos _getChavePrimaria() e
 * _getEntidade(). O mesmo pode ser feito para alterar os m�todos que retornam os comandos
 * SQL padr�o. Os m�todos que retornam o SQL s�o: SQLinsert(), SQLupdate, SQLdelete,
 * SQLselect, al�m disso voc� poder� sobrescrever os m�todos insert(), update(), delete(),
 * select(), salvar() e criar outros que sejam necess�rios.
 */
 /**
 * @author Alan em 20/08/2008
 * @version 1.0
 * @package Persistencia
 *
 */
class OAD {
	/**
	 * Variavel que guarda o modelo a ser persistido
	 * @access protected
	 * @var Modelo objeto
	 */
	protected $_model;
	protected $_conexao;
    public function OAD(Conexao $c, Modelo $m) {
    	$this->_model = $m;
    	$this->_conexao = $c;
    }
	/**
	 * Essa fun��o dever� ser sobrescrita quando a chave prim�ria
	 * n�o estiver declarada como primeiro atributo, ou quando a chave
	 * for composta. Nesse caso, o retorno sera o nome dos campos
	 * separados por virgula.
	 */
	public function _getCampoChavePrimaria(){
		$atrs	= $this->_model->_getAtributos();
		return $atrs[0];
	}
	/**
	 * Essa fun��o retorna o valor da chave prim�ria
	 * @return mixed;
	 */
	public function _getValorChavePrimaria(){
		$cp = $this->_getCampoChavePrimaria();
		$ret = $this->_model->$cp;
		return  "$ret";
	}
	/**
	 * Essa fun��o dever� ser sobrescrita quando o nome da tabela for
	 * diferente do nome da classe. Nesse caso, devera retornar o nome da
	 * tabela do banco de dados.
	 */
	public function _getEntidade(){
		$tabela = get_class($this->_model);
		return $tabela;
	}

    public function salvar(){
    	// Pega o campo chave Prim�ria
    	$campoChave = $this->_getCampoChavePrimaria();
    	$valorChave = $this->_getValorChavePrimaria();
    	if($valorChave == null){
    		$this->insert();
    	} else
    		{
    			$this->update();
    		}
    }
    public function retiraUltVirgula($sql){
    	return $sql = substr($sql, 0,strlen($sql)-2);
    }
    public function SQLselect(){

		$arr = $this->_model->_getAtributos();
		$cp  = $this->_getCampoChavePrimaria();
		foreach($arr as $atb){
			$sqlAtb .= "$atb, ";
		}
		$sqlAtb = $this->retiraUltVirgula($sqlAtb);
		$tbl = $this->_getEntidade();
		$sql = "SELECT $sqlAtb FROM $tbl ";
		$sql.= "WHERE $cp = :$cp;";
		return $sql;
    }

    public function SQLinsert(){

		$arr = $this->_model->_getAtributos();
		foreach($arr as $atb){
			$sqlAtb .= "$atb, ";
			$sqlVal .= ":$atb, ";
		}
		$tbl = $this->_getEntidade();
		$sql = "INSERT INTO $tbl( ";
		$sql.= $this->retiraUltVirgula($sqlAtb) . ") ";
		$sql.= "VALUES (" . $this->retiraUltVirgula($sqlVal) . ") ";
		return $sql;
    }
    public function SQLupdate(){
    	// Pega o Array de Atributos da Classe
		$arr = $this->_model->_getAtributos();
		// Pega o nome da Tabela do Banco de Dados
		$tbl = $this->_getEntidade();
		$cp  = $this->_getCampoChavePrimaria();
		$sql = "UPDATE $tbl SET ";
		foreach($arr as $atb){
			$sql .= "$atb = :$atb, ";
		}
		// Retiro a �ltima virgula do SQL
		$sql = $this->retiraUltVirgula($sql) . " ";
		// Acrescento a cl�usula WHERE
		$sql.= "WHERE $cp = :$cp;";
		return $sql;
    }
    public function SQLdelete(){
		// Pega o nome da Tabela do Banco de Dados
		$tbl = $this->_getEntidade();
		$cp  = $this->_getCampoChavePrimaria();
		$sql = "DELETE FROM $tbl ";
		$sql.= "WHERE $cp = :$cp;";
		return $sql;
    }
    public function select($id){
    	$sql 	= $this->SQLselect();
		$con	= new ConsultaPreparada($this->_conexao, $sql);
		$cp		= $this->_getCampoChavePrimaria();
		$vp		= empty($id)?$this->_getValorChavePrimaria():$id;
		$ar[$cp]=$vp;
		$this->liga($con, $ar);
		foreach($con->getResultados()as $linha){
			foreach($linha as $campo=>$valor){
				$this->_model->$campo = $valor;
			}
		}
		return $this->_model;

    }
    public function insert(){
    	$sql 	= $this->SQLinsert();
		$comm	= new ComandoPreparado($this->_conexao, $sql);
		$cp 	= $this->_getCampoChavePrimaria();
		$this->liga($comm);
		// Registros Afetados
		$ra = $comm->executa();
		$this->_model->$cp = $comm->getUltimoId();
		return $ra;

    }
    public function update(){
    	$sql 	= $this->SQLupdate();
		$comm	= new ComandoPreparado($this->_conexao, $sql);
		$this->liga($comm);
		return $comm->executa();
    }
    public function delete(){
    	$sql 	= $this->SQLdelete();
		$comm	= new ComandoPreparado($this->_conexao, $sql);
		$cp		= $this->_getCampoChavePrimaria();
		$vp		= $this->_getValorChavePrimaria();
		$ar[$cp]=$vp;
		$this->liga($comm, $ar);
		return $comm->executa();
    }


    public function liga(iPreparado $comando, $arr = null) {
		if($arr == null){
			$arr = $this->_model->_getArray();
		}
    	foreach($arr as $atb=>$val) {
    		$comando->liga($atb, $val);
    	}
    }
}
$a = new OAD("asd","adsfd");
//include_once "config.php";
//
//class Tipo_Amb extends Modelo {
//	protected $id;
//	protected $Descricao;
//}
//// Voc� pode extender o OAD para implementar insert, updates e deletes personalizados
//class OADTipo_Amb extends OAD {
//	//Optamos por n�o sobrescrever nenhum dos m�todos.
//	//Se n�o for sobrescrito, o OAD faz select,insert,update e delete automaticamente.
//}
//$cnxIntra = new Conexao("mysql", "localhost","test","root","");
//// Crio um objeto Modelo
//$a = new Tipo_Amb();
//// Voc� pode preencher os campos
//$a->Descricao = "Descri��o Qualquer";
//// Ent�o voc� cria o Objeto de Acesso a dados
//$o = new OADTipo_Amb($cnxIntra, $a);
//// Faz a Inser��o dos dados
//$o->insert();
//// Agora o Model j� tem um Id no banco.
//echo "Id = " . $a->id . "<br>";
//echo "Descricao = " . $a->Descricao . "<br>";
//// Vamos criar um outro model;
//$b = new Tipo_Amb();
//// Dessa vez eu Criei o Objeto a dados padr�o
//$c = new OAD($cnxIntra, $b);
//// Preencho todos os atributos do novo Modelo (passando ao select o valor da chave prim�ria);
//$c->select($a->id);
//echo "Descricao de B = " . $b->Descricao . "<br>";
//$b->Descricao = "Descricao Atualizada!";
//$c->update();
//echo "Descricao de B = " . $b->Descricao . "<br>";
//$c->delete();

?>