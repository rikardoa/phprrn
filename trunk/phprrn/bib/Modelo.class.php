<?php
/**
 * Arquivo de Defini��o da Classe Modelo
 * LEIA!
 * 1) classe Modelo (Model ou o M do MVC)
 * � a representa��o espec�fica da INFORMA��O em que a aplica��o opera. Por exemplo,
 * aluno, professor e turma fazem parte do dom�nio de um sistema acad�mico. As
 * informa��es espec�ficas de aluno, professor e turma s�o dispostas em Modelos.
 * � comum haver confus�o pensando que Modelo � um outro nome para a CAMADA DE DOM�NIO.
 * A L�GICA DE DOM�NIO adiciona sentido aos dados (por exemplo, calcular se hoje �
 * anivers�rio do usu�rio, ou calcular o total de impostos e fretes sobre um determinado
 * carrinho de compras).
 *
 * 2) Modelo e Persist�ncia
 * Muitas aplica��es usam um mecanismo de armazenamento persistente (como banco de dados)
 * para armazenar dados. MVC n�o cita especificamente a camada para acesso aos dados,
 * porque subentende-se que estes m�todos estariam encapsulados pelo Modelo. Contudo,
 * h� sempre a possibilidade que um modelo n�o seja persistente, por isso separamos a
 * camada de persist�ncia.
 *
 * 3) Problemas com a Persist�ncia
 * H� ainda a necessidade de verificar a integridade dos dados nos relacionamentos, bem
 * como o mapeamento objeto relacional. Outros frameworks tem utilizado solu��es baseadas
 * em XML, YML e etc... Nos optamos por deixar que o usu�rio cuide disso. O motivo �
 * simples: Se formos tratar desse assunto, a curva de aprendizagem sobe muito, pois o
 * programador ter� que aprender v�rias palavras chave do framework.
 *
 * 4) Camada de Persist�ncia
 * A persist�ncia ser� feita por um Objeto de acesso a Dados (OAD). Em outras palavras,
 * voc� instanciar� um objeto do tipo OAD, e nesse objeto voc� vai informar uma conexao
 * e um objeto modelo. O objeto OAD poder� salvar, recuperar e excluir o modelo do banco de
 * dados. O OAD (Objeto de Acesso a Dados) implementa o CRUD padr�o. Pode ser necess�rio
 * criar uma classe que herde do OAD para realizar os ajustes do CRUD, para por exemplo
 * inserir em outras tabelas ou utilizar outros objetos de acesso a dados. Leia mais na
 * documenta��o do OAD.
 *
 * 5) Uso
 * Todos os modelos que voc� precisar para a sua aplica��o, voc� criar� a partir dessa
 * classe, de forma que uma Modelo Professor dever�r extender a classe modelo. � importante
 * que todos os atributos sejam protected. Dessa forma eles n�o ser�o lidos diretamente.
 * A classe Modelo cria gets e sets m�gicos, e caso voc� precise implementar alguma regra
 * no model para get e set basta criar uma fun��o contendo get/set+nome do atributo. Por
 * exemplo, para o atributo $nome, crie fun��es getnome ou setnome($valor)...
 * Outro ponto importante � que a classe n�o reconhecer� como atributo do modelo um campo
 * iniciado por underline "_nome".
 */
 /**
 * @author Alan em 20/08/2008
 * @version 1.0
 * @package Persistencia
 *
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
		foreach ($atb as $nome => $valor) {
			if (substr($nome, 0, 1) != "_") {
				$atrs[] = $nome;
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
	 * M�todo _getArray
	 * Retorna um array com todos os atributos e valores da Classe.
	 * @return array string Contendo o nome de todos os atributos do objeto
	 */
	public function _getArray(){
		$atb = get_object_vars($this);
		foreach ($atb as $nome => $valor) {
			if (substr($nome, 0, 1) != "_") {
				$atrs[$nome] = $valor;
			}
		}
		return $atrs;
	}
	/**
	 * M�todo _setAtributos
	 * Preenche o modelo utilizando um array. Verifica se as chaves do array
	 * enviado s�o atributos da classe.
	 * @return boolean verdadeiro se foi poss�vel preencher os atributos.
	 */
	public function _setArray($array){
		foreach ($array as $atr => $valor) {
			if (substr($atr, 0, 1) != "_") {
				if(in_array($atr,$this->_atributos)){
					$this->$atr = $valor;
				} else {
					// TODO: Deve-se Lan�ar uma exce��o aqui
					// Algo como return new TErroFatal("04", get_class($this) ,"O atributo << " . $atributo . " >> n&atilde;o foi encontrado na classe (ou nas superclasses de " . get_class($this),"Verifique se voc&ecirc; escreveu corretamente o nome do atributo da classe.");
					die("[set]Array $atr n�o encontrado no objeto " . get_class($this));
					return false;
				}
			}
		}
		return true;
	}
}
?>