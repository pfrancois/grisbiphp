<?php  /* coding: utf-8 */ 

class scat extends subitem {
    public function get_mere() {
        global $gsb_xml ;
        return new categorie($gsb_xml->xpath_uniq('..', $this->_item_xml)) ;
    }
    public function __toString() {
        return "scat #" . $this->get_id() . " '" . $this->get_nom() .
            "' de la categorie #" . $this->get_mere()->get_id() . " '" . $this->get_mere()->get_nom() .
            "'" ;
    }
}