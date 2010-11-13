<?php
//$Id: items.php 35 2010-08-22 23:08:05Z pfrancois $
/**
 * class abstraite qui gere les ensembles d'items
 *
 * @version 1.1.0
 */
abstract class items implements Countable {
	/**
	 * @var la chaine qui permet une iteration facile
	 */
	protected $_xpath = '//invalide' ;
	public $nom_classe = __class__ ;
	/**
	 * squelettes de fonction
	 */
	abstract public function get_next() ;
	abstract public function get_by_id($id) ;

	/**
	 * fonction afin d'implementer interface countable
	 */
	public function count() {
		global $gsb_xml ;
		$r = $gsb_xml->xpath_iter($this->_xpath) ;
		return count($r) ;
	}

	/**
	 * items::iter()
	 *
	 * @return mixed
	 */
	public function iter() {
		global $gsb_xml ;
		$nom_super_classe = substr($this->nom_classe, 0, strlen($this->nom_classe) - 1) ;
		return $gsb_xml->iter_class($this->_xpath, $nom_super_classe) ;
	}
}
