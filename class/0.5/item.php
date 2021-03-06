<?php /* coding: utf-8 */

abstract class item{
	const NOUVELLE = true;
	const ANCIENNE = false;
	/**
	 * @var $_item_xml SimpleXMLElement le compte simplexml
	 */
	protected $_item_xml;
	/**
	 * @var $_dom SimpleXMLElement le compte simplexml
	 */
	protected $_dom;
	/**
	 *
	 * @param SimpleXMLElement $c element de l'item a construire
	 */
	public function __construct(SimpleXMLElement $c) {
		$this->_item_xml = $c;
		$this->_dom = dom_import_simplexml($c);
	}

	/**
	 * @return int id de la categorie
	 */
	public function get_id() {
		return (int)$this->_item_xml['No'];
	}
	/**
	 * item::get_nom()
	 *
	 * @return string
	 */
	public function get_nom() {
		return (string )$this->_item_xml['Nom'];
	}
	/**
	 * @return null
	 * @param string $nom le nom a modifier
	 */
	public function set_nom($nom) {
		if (empty($nom)) {
			throw new exception_parametre_invalide('le nom ne peut etre vide');
		}
		$this->_dom->setAttributeNode(new DOMAttr('Nom', "$nom"));
	}
	public function delete() {
		// @codeCoverageIgnoreStart
		throw new BadMethodCallException("implementation non effectue");
		// @codeCoverageIgnoreEnd
	}
	public function __toString() {
		return get_class($this) . ' n ' . $this->get_id();
	}
	/**
	 * @return simplexml
	 */
	public function get_xml(){
		return $this->_item_xml;
	}
}
