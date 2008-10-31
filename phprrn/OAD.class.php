<?php
/**
 * Arquivo de Definição da Classe OAD
 * LEIA!
 * 1) classe OAD
 * É utilizada para implementar a persistência de modelos. A classe recebe um objeto
 * do tipo conexao com o banco de dados e um modelo. Utilizando os métodos da classe
 * OAD é possível realizar o CRUD - Create, Retrieve Update e Delete. De forma mais
 * simples a classe OAD fará a persistência dos models, criando métodos automáticos
 * para inserção, atualização, deleção e seleção.
 *
 * 2) Herdando um OAD
 * As vezes a inserção simples não funciona, pois há mais coisas para fazer na inserção,
 * atualização e deleção. Nesse caso, é possível criar uma especialização de um OAD para
 * tratar especificamente da persistência de um modelo. E aí será possível modificar o
 * nome da tabela (que por padrão é o nome da classe Modelo), a chave primária (que por
 * padrão é considerado o primeiro atributo do Modelo), ou seja, se você precisar alterar
 * qualquer um desses padrões basta sobrescrever os métodos _getChavePrimaria() e
 * _getEntidade(). O mesmo pode ser feito para alterar os métodos que retornam os comandos
 * SQL padrão. Os métodos que retornam o SQL são: SQLinsert(), SQLupdate, SQLdelete,
 * SQLselect, além disso você poderá sobrescrever os métodos insert(), update(), delete(),
 * select(), salvar() e criar outros que sejam necessários.
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
	 * Essa função deverá ser sobrescrita quando a chave primária
	 * não estiver declarada como primeiro atributo, ou quando a chave
	 * for composta. Nesse caso, o retorno sera o nome dos campos
	 * separados por virgula.
	 */
	public function _getCampoChavePrimaria(){
		$atrs	= $this->_model->_getAtributos();
		return $atrs[0];
	}
	/**
	 * Essa função retorna o valor da chave primária
	 * @return mixed;
	 */
	public function _getValorChavePrimaria(){
		$cp = $this->_getCampoChavePrimaria();
		$ret = $this->_model->$cp;
		return  "$ret";
	}
	/**
	 * Essa função deverá ser sobrescrita quando o nome da tabela for
	 * diferente do nome da classe. Nesse caso, devera retornar o nome da
	 * tabela do banco de dados.
	 */
	public function _getEntidade(){
		$tabela = get_class($this->_model);
		return $tabela;
	}

    public function salvar(){
    	// Pega o campo chave Primária
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
		// Retiro a última virgula do SQL
		$sql = $this->retiraUltVirgula($sql) . " ";
		// Acrescento a cláusula WHERE
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
//// Você pode extender o OAD para implementar insert, updates e deletes personalizados
//class OADTipo_Amb extends OAD {
//	//Optamos por não sobrescrever nenhum dos métodos.
//	//Se não for sobrescrito, o OAD faz select,insert,update e delete automaticamente.
//}
//$cnxIntra = new Conexao("mysql", "localhost","test","root","");
//// Crio um objeto Modelo
//$a = new Tipo_Amb();
//// Você pode preencher os campos
//$a->Descricao = "Descrição Qualquer";
//// Então você cria o Objeto de Acesso a dados
//$o = new OADTipo_Amb($cnxIntra, $a);
//// Faz a Inserção dos dados
//$o->insert();
//// Agora o Model já tem um Id no banco.
//echo "Id = " . $a->id . "<br>";
//echo "Descricao = " . $a->Descricao . "<br>";
//// Vamos criar um outro model;
//$b = new Tipo_Amb();
//// Dessa vez eu Criei o Objeto a dados padrão
//$c = new OAD($cnxIntra, $b);
//// Preencho todos os atributos do novo Modelo (passando ao select o valor da chave primária);
//$c->select($a->id);
//echo "Descricao de B = " . $b->Descricao . "<br>";
//$b->Descricao = "Descricao Atualizada!";
//$c->update();
//echo "Descricao de B = " . $b->Descricao . "<br>";
//$c->delete();

?>