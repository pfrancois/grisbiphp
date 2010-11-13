<?php

//Id of last commit: $Id: item.php 45 2010-09-20 03:12:17Z pfrancois $
abstract class item {
    const NOUVELLE = true ;
    const ANCIENNE = false ;
    /**
     * @var $c_xml SimpleXMLElement le compte simplexml
     * @todo remettre en protected
     */
     public $_item_xml ;
    protected $_dom ;
    /**
     *
     * @param SimpleXMLElement $c element de l'item a construire
     */
    public function __construct(SimpleXMLElement $c) {
        $this->_item_xml = $c ;
        $this->_dom = dom_import_simplexml($c) ;
    }

    /**
     * @return int id de la categorie
     */
    public function get_id() {
        return (int)$this->_item_xml['No'] ;
    }
    /**
     * item::get_nom()
     *
     * @return string
     */
    public function get_nom() {
        return (string )$this->_item_xml['Nom'] ;
    }
    /**
     * @return null
     * @param string $nom le nom a modifier
     */
    public function set_nom($nom) {
        if (empty($nom)) {
            throw new exception_parametre_invalide('le nom ne peut etre vide') ;
        }
        $this->_dom->setAttributeNode(new DOMAttr('Nom', "$nom")) ;
    }
    public function delete() {
        // @codeCoverageIgnoreStart
        throw new BadMethodCallException("implementation non effectue") ;
        // @codeCoverageIgnoreEnd
    }
    public function __toString() {
        return get_class($this) . ' n ' . $this->get_id() ;
    }
}
