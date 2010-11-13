<?php
//$Id: etat.php 35 2010-08-22 23:08:05Z pfrancois $
class etat extends item {
    function get_id() {
        return (int)$this->_item_xml->No ;
    }
    function get_nom() {
        return (string )$this->_item_xml->Nom ;
    }
}
