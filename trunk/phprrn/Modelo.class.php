<?php
/**
 * Arquivo de Defini��o da Classe Modelo
 * @author Alan em 20/08/2008
 * @version 1.0
 * @package Persistencia
 */
 /**
 * classe Modelo
 * As classes que representam o Model do MVC.
 * Essa classe dispensa o uso de get/set. Caso seja necess�rio implementar
 * os m�todos get/set s�o chamados automaticamente.
 */
abstract class Modelo {
	/**
	 * Variavel que guarda um Array contendo todos os atributos da classe
	 * @access protected
	 * @var array String
	 */
	protected $_atributos = null;
	/**
	 * Variavel que guarda um Array contendo todos os m�todos da classe
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
	 * @return array string - Array contendo todos os atributos da classe (n�o iniciados por _(underline)
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
	 * @return array string - Array contendo todos os m�todos da classe (n�o iniciados por _(underline)
	 */
	private final function _getArrayMetodos() {
		foreach (get_class_methods(get_class($this)) as $metodo) {
			// Verifica se o atributo come�a com _ (Nesse caso desprezamos por considera-lo uma
			// propriedade interna da classe e n�o um campo do banco de dados
			if (substr($metodo, 0, 1) != "_") {
				$mtds[] = $metodo;
			}
		}
		return $mtds;
	}
	/**
	 * M�todo M�gico __set.
	 * Permite alterar atributos da classe.
	 * Sempre que houver um m�todo set[NomeAtributo], esse m�todo ser� chamado.
	 * Note que para chamar o m�todo basta instanciar uma classe e tentar alterar o
	 * valor do atributo diretamente.
	 * Ex:
	 * $a = new Classe();
	 * $a->Atributo = 10;
	 * Aqui a classe ir� verificar se existe um m�todo setAtributo.
	 * Se houver, esse m�todo ser� chamado e o valor ser� passado ao m�todo.
	 * Caso contr�rio, o Atributo ser� modificado diretamente.
	 * @param string Nome do Atributo
	 * @param string Valor do Atributo
	 */
	public final function __set($atributo, $valor) {
		// Crio o nome do M�todo que estamos buscando.
		$metodo = "set$atributo";
		// Verifica se o m�todo existe
		if (in_array($metodo, $this->_metodos)){
			//Se existir utiliza o m�todo para setar as vari�veis.
			$this->$metodo($valor);
		} elseif (in_array($atributo, $this->_atributos)) {
			$this->$atributo = $valor;
			return true;
		} else {
			// TODO: Deve-se Lan�ar uma exce��o aqui
			// Algo como return new TErroFatal("04", get_class($this) ,"O atributo << " . $atributo . " >> n&atilde;o foi encontrado na classe (ou nas superclasses de " . get_class($this),"Verifique se voc&ecirc; escreveu corretamente o nome do atributo da classe.");
			die("[set]Atributo $atributo n�o encontrado!");
		}
	}
	/**
	 * M�todo M�gico __get.
	 * Permite ler atributos da classe.
	 * Sempre que houver um m�todo get[NomeAtributo], esse m�todo ser� chamado.
	 * Note que para chamar o m�todo basta instanciar uma classe e tentar ler o
	 * valor do atributo diretamente.
	 * Ex:
	 * $a = new Classe();
	 * echo $a->Atributo;
	 * Aqui a classe ir� verificar se existe um m�todo getAtributo.
	 * Se houver, esse m�todo ser� chamado e o valor retornado ser� passado pelo m�todo.
	 * Caso contr�rio, o Atributo ser� retornado diretamente.
	 * @param string Nome do Atributo
	 * @return mixed O valor armazenado no atributo
	 */
	public final function __get($atributo) {
		// Crio o nome do M�todo que estamos buscando.
		$metodo = "get$atributo";
		// Verifica se o m�todo existe
		if (in_array($metodo, $this->_metodos)){
			//Se existir utiliza o m�todo para pegar as vari�veis.
			return $this->$metodo();
		} elseif (in_array($atributo, $this->_atributos)) {
			return $this->$atributo;
		} else {
			// TODO: Deve-se Lan�ar uma exce��o aqui
			// Algo como return new TErroFatal("04", get_class($this) ,"O atributo << " . $atributo . " >> n&atilde;o foi encontrado na classe (ou nas superclasses de " . get_class($this),"Verifique se voc&ecirc; escreveu corretamente o nome do atributo da classe.");
			die("[get]Atributo $atributo n�o encontrado!");
		}
	}
	/**
	 * M�todo _getValores.
	 * Retorna um array com todos os valores para cada Atributo da Classe.
	 * Note que atributos iniciados por _ [underline] n�o s�o levados em
	 * considera��o
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
	 * M�todo _getAtributos
	 * Retorna um array com todos os atributos da Classe.
	 * Note que atributos iniciados por _ [underline] n�o s�o levados em
	 * considera��o
	 * @return array string Contendo o nome de todos os atributos do objeto
	 */
	public function _getAtributos(){
		return $this->_atributos;
	}
	/**
	 * M�todo _getAtributos
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