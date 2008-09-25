<?php
/**
 * Arquivo de Definição da Classe Modelo
 * @author Alan em 20/08/2008
 * @version 1.0
 * @package Persistencia
 */
 /**
 * classe Modelo
 * As classes que representam o Model do MVC.
 * Essa classe dispensa o uso de get/set. Caso seja necessário implementar
 * os métodos get/set são chamados automaticamente.
 */
abstract class Modelo {
	/**
	 * Variavel que guarda um Array contendo todos os atributos da classe
	 * @access protected
	 * @var array String
	 */
	protected $_atributos = null;
	/**
	 * Variavel que guarda um Array contendo todos os métodos da classe
	 * @access protected
	 * @var array String
	 */
	protected $_metodos = null;
	/**
	 * Construtor da Classe
	 */
	public final function Modelo(){
		$this->_atributos 	= $this->_getArrayAtributos();
		$this->_metodos 	= $this->_getArrayMetodos();
	}
	/**
	 * @access protected
	 * @return array string - Array contendo todos os atributos da classe (não iniciados por _(underline)
	 */
	private final function _getArrayAtributos() {
		$atb = get_object_vars($this);
		foreach ($atb as $name => $value) {
			if (substr($name, 0, 1) != "_") {
				$atrs[] = $name;
			}
		}
		return $atrs;
	}
	/**
	 * @access protected
	 * @return array string - Array contendo todos os métodos da classe (não iniciados por _(underline)
	 */
	private final function _getArrayMetodos() {
		foreach (get_class_methods(get_class($this)) as $metodo) {
			// Verifica se o atributo começa com _ (Nesse caso desprezamos por considera-lo uma
			// propriedade interna da classe e não um campo do banco de dados
			if (substr($metodo, 0, 1) != "_") {
				$mtds[] = $metodo;
			}
		}
		return $mtds;
	}
	/**
	 * Método Mágico __set.
	 * Permite alterar atributos da classe.
	 * Sempre que houver um método set[NomeAtributo], esse método será chamado.
	 * Note que para chamar o método basta instanciar uma classe e tentar alterar o
	 * valor do atributo diretamente.
	 * Ex:
	 * $a = new Classe();
	 * $a->Atributo = 10;
	 * Aqui a classe irá verificar se existe um método setAtributo.
	 * Se houver, esse método será chamado e o valor será passado ao método.
	 * Caso contrário, o Atributo será modificado diretamente.
	 * @param string Nome do Atributo
	 * @param string Valor do Atributo
	 */
	public final function __set($atributo, $valor) {
		// Crio o nome do Método que estamos buscando.
		$metodo = "set$atributo";
		// Verifica se o método existe
		if (in_array($metodo, $this->_metodos)){
			//Se existir utiliza o método para setar as variáveis.
			$this->$metodo($valor);
		} elseif (in_array($atributo, $this->_atributos)) {
			$this->$atributo = $valor;
			return true;
		} else {
			// TODO: Deve-se Lançar uma exceção aqui
			// Algo como return new TErroFatal("04", get_class($this) ,"O atributo << " . $atributo . " >> n&atilde;o foi encontrado na classe (ou nas superclasses de " . get_class($this),"Verifique se voc&ecirc; escreveu corretamente o nome do atributo da classe.");
			die("[set]Atributo $atributo não encontrado!");
		}
	}
	/**
	 * Método Mágico __get.
	 * Permite ler atributos da classe.
	 * Sempre que houver um método get[NomeAtributo], esse método será chamado.
	 * Note que para chamar o método basta instanciar uma classe e tentar ler o
	 * valor do atributo diretamente.
	 * Ex:
	 * $a = new Classe();
	 * echo $a->Atributo;
	 * Aqui a classe irá verificar se existe um método getAtributo.
	 * Se houver, esse método será chamado e o valor retornado será passado pelo método.
	 * Caso contrário, o Atributo será retornado diretamente.
	 * @param string Nome do Atributo
	 * @return mixed O valor armazenado no atributo
	 */
	public final function __get($atributo) {
		// Crio o nome do Método que estamos buscando.
		$metodo = "get$atributo";
		// Verifica se o método existe
		if (in_array($metodo, $this->_metodos)){
			//Se existir utiliza o método para pegar as variáveis.
			return $this->$metodo();
		} elseif (in_array($atributo, $this->_atributos)) {
			return $this->$atributo;
		} else {
			// TODO: Deve-se Lançar uma exceção aqui
			// Algo como return new TErroFatal("04", get_class($this) ,"O atributo << " . $atributo . " >> n&atilde;o foi encontrado na classe (ou nas superclasses de " . get_class($this),"Verifique se voc&ecirc; escreveu corretamente o nome do atributo da classe.");
			die("[get]Atributo $atributo não encontrado!");
		}
	}
	/**
	 * Método _getValores.
	 * Retorna um array com todos os valores para cada Atributo da Classe.
	 * Note que atributos iniciados por _ [underline] não são levados em
	 * consideração
	 * @return array string Contendo os valores de todos os atributos do objeto
	 */
	public function _getValores(){
		$atrs = $this->_getAtributos();
		foreach($atrs as $atr){
			$ret[] = $this->$atr;
		}
		return $ret;
	}
	/**
	 * Método _getAtributos
	 * Retorna um array com todos os atributos da Classe.
	 * Note que atributos iniciados por _ [underline] não são levados em
	 * consideração
	 * @return array string Contendo o nome de todos os atributos do objeto
	 */
	public function _getAtributos(){
		return $this->_atributos;
	}
	/**
	 * Método _getAtributos
	 * Retorna um array com todos os atributos e valores da Classe.
	 * @return array string Contendo o nome de todos os atributos do objeto
	 */
	public function _getArray(){
		$atb = get_object_vars($this);
		foreach ($atb as $name => $value) {
			if (substr($name, 0, 1) != "_") {
				$atrs[$name] = $value;
			}
		}
		return $atrs;
	}
}
?>