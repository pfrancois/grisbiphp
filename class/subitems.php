<?php

//Id of last commit: $Id: subitems.php 35 2010-08-22 23:08:05Z pfrancois $
abstract class subitems extends item implements Countable {
	/**
	 * @var string le chemin xpath pour atteindre la collection
	 */
	protected $_xpath = '//invalide' ;
	public $nom_sub_classe = 'invalide' ;
	protected $xpath_next = "" ;
	abstract public function get_sub_by_id($id) ;
	abstract public function get_sub_by_name($name) ;

	/**
	 * fonction afin d'implementer interface countable
	 */
	public function count() {
		global $gsb_xml ;
		try {
			$r = $gsb_xml->xpath_iter($this->_xpath, $this->_item_xml) ;
			return count($r) ;
		}
		catch (Exception_no_reponse $except) {
			return 0 ;
		}
	}

	/**
	 * subitems::iter_sub()
	 *
	 * @return array of SimpleXMLElement
	 */
	public function iter_sub() {
		global $gsb_xml ;
		$c = $this->nom_sub_classe ;
		return $gsb_xml->iter_class($this->_xpath, $c, $this->_item_xml) ;
	}

	/**
	 * subitems::has_sub()
	 *
	 * @return boolean
	 */
	public function has_sub() {
		global $gsb_xml ;
		if (count($this) == 0) {
			return false ;
		} else {
			return true ;
		}
	}
	/**
	 * @return int l'id de la prochaine sous categorie
	 */
	public function get_next_sub() {
		return (int)$this->_item_xml[$this->xpath_next] + 1 ;
	}
}
