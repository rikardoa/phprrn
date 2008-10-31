<?php
/**
 * Arquivo de Definição da Classe Modelo
 * LEIA!
 * 1) classe Modelo (Model ou o M do MVC)
 * É a representação específica da INFORMAÇÃO em que a aplicação opera. Por exemplo,
 * aluno, professor e turma fazem parte do domínio de um sistema acadêmico. As
 * informações específicas de aluno, professor e turma são dispostas em Modelos.
 * É comum haver confusão pensando que Modelo é um outro nome para a CAMADA DE DOMÍNIO.
 * A LÓGICA DE DOMÍNIO adiciona sentido aos dados (por exemplo, calcular se hoje é
 * aniversário do usuário, ou calcular o total de impostos e fretes sobre um determinado
 * carrinho de compras).
 *
 * 2) Modelo e Persistência
 * Muitas aplicações usam um mecanismo de armazenamento persistente (como banco de dados)
 * para armazenar dados. MVC não cita especificamente a camada para acesso aos dados,
 * porque subentende-se que estes métodos estariam encapsulados pelo Modelo. Contudo,
 * há sempre a possibilidade que um modelo não seja persistente, por isso separamos a
 * camada de persistência.
 *
 * 3) Problemas com a Persistência
 * Há ainda a necessidade de verificar a integridade dos dados nos relacionamentos, bem
 * como o mapeamento objeto relacional. Outros frameworks tem utilizado soluções baseadas
 * em XML, YML e etc... Nos optamos por deixar que o usuário cuide disso. O motivo é
 * simples: Se formos tratar desse assunto, a curva de aprendizagem sobe muito, pois o
 * programador terá que aprender várias palavras chave do framework.
 *
 * 4) Camada de Persistência
 * A persistência será feita por um Objeto de acesso a Dados (OAD). Em outras palavras,
 * você instanciará um objeto do tipo OAD, e nesse objeto você vai informar uma conexao
 * e um objeto modelo. O objeto OAD poderá salvar, recuperar e excluir o modelo do banco de
 * dados. O OAD (Objeto de Acesso a Dados) implementa o CRUD padrão. Pode ser necessário
 * criar uma classe que herde do OAD para realizar os ajustes do CRUD, para por exemplo
 * inserir em outras tabelas ou utilizar outros objetos de acesso a dados. Leia mais na
 * documentação do OAD.
 *
 * 5) Uso
 * Todos os modelos que você precisar para a sua aplicação, você criará a partir dessa
 * classe, de forma que uma Modelo Professor deverár extender a classe modelo. É importante
 * que todos os atributos sejam protected. Dessa forma eles não serão lidos diretamente.
 * A classe Modelo cria gets e sets mágicos, e caso você precise implementar alguma regra
 * no model para get e set basta criar uma função contendo get/set+nome do atributo. Por
 * exemplo, para o atributo $nome, crie funções getnome ou setnome($valor)...
 * Outro ponto importante é que a classe não reconhecerá como atributo do modelo um campo
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
		foreach ($atb as $nome => $valor) {
			if (substr($nome, 0, 1) != "_") {
				$atrs[] = $nome;
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
	 * Método _getArray
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
	 * Método _setAtributos
	 * Preenche o modelo utilizando um array. Verifica se as chaves do array
	 * enviado são atributos da classe.
	 * @return boolean verdadeiro se foi possível preencher os atributos.
	 */
	public function _setArray($array){
		foreach ($array as $atr => $valor) {
			if (substr($atr, 0, 1) != "_") {
				if(in_array($atr,$this->_atributos)){
					$this->$atr = $valor;
				} else {
					// TODO: Deve-se Lançar uma exceção aqui
					// Algo como return new TErroFatal("04", get_class($this) ,"O atributo << " . $atributo . " >> n&atilde;o foi encontrado na classe (ou nas superclasses de " . get_class($this),"Verifique se voc&ecirc; escreveu corretamente o nome do atributo da classe.");
					die("[set]Array $atr não encontrado no objeto " . get_class($this));
					return false;
				}
			}
		}
		return true;
	}
}
?>