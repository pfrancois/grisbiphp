<?php /* coding: utf-8 */ 

class sib extends subitem {
	/**
	 * @return ib
	 */
    public function get_mere() {
        global $gsb_xml ;
        return new ib($gsb_xml->xpath_uniq('..', $this->_item_xml)) ;
    }
    
    /**
     * @return string
     */
    public function __toString() {
        return "sib #" . $this->get_id() . " '" . $this->get_nom() . "' de l'ib #" . $this->get_mere()->get_id() . " '" . $this->get_mere()->get_nom() . "'" ;
    }
}