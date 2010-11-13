<?php
//$Id: sib.php 35 2010-08-22 23:08:05Z pfrancois $
class sib extends subitem {
    public function get_mere() {
        global $gsb_xml ;
        return new ib($gsb_xml->xpath_uniq('..', $this->_item_xml)) ;
    }
    public function __toString() {
        return "sib #" . $this->get_id() . " '" . $this->get_nom() . "' de l'ib #" . $this->get_mere()->get_id() .
            " '" . $this->get_mere()->get_nom() . "'" ;
    }
}
