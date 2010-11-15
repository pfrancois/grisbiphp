<?php  /* coding: utf-8 */ 

class etat extends item {
    function get_id() {
        return (int)$this->_item_xml->No ;
    }
    function get_nom() {
        return (string )$this->_item_xml->Nom ;
    }
}