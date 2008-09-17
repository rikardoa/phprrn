<?php
/**
 * O que щ o Arquivo. Ex:Arquivo de Configuraчуo da Classe TConexao
 * @author Alan Gustavo Santana Ribeiro <alan@prrn.mpf.gov.br> em 17/09/2008
 * @version 1.0
 * @package
 */
$CAMINHO 	= str_replace("config.php","",__FILE__);
$DS			= DIRECTORY_SEPARATOR;
function __autoload($classe)
{
	// Aqui armazeno o caminho de config.php (retirando o nome do arquivo).
	// Retorna algo como C:\xampp\htdocs\Orion\asspa\gc\
	$CAMINHO 	= str_replace("config.php","",__FILE__);
	$DS			= DIRECTORY_SEPARATOR;
	// Essa funчуo procura a classe no caminho especificado.
    if (file_exists("$CAMINHO$classe.class.php"))
    {
        include_once "$CAMINHO$classe.class.php";
    }
}
?>