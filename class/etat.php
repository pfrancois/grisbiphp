<?php /* coding: utf-8 */ 

class etat extends item {
	/**
     * @return int id de la categorie
     */
    function get_id() {
        return (int)$this->_item_xml->No ;
    }
    /**
     * item::get_nom()
     *
     * @return string
     */
    function get_nom() {
        return (string)$this->_item_xml->Nom ;
    }
}