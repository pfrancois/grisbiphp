<?php /* coding: utf-8 */ 

class exercice extends item {
    protected $_xpath = './exercice' ;
    /**
     * @return int timestamp
     */
    public function get_date_debut() {
        $date = (string )$this->_item_xml['Date_debut'] ;
        return util::datefr2time($date) ;
    }
    /**
     * @return int le timestamp
     */
    public function get_date_fin() {
        $date = (string )$this->_item_xml['Date_fin'] ;
        return util::datefr2time($date) ;
    }
    /**
     * @return bool
     */
    public function is_affiche() {
        $r = (int)$this->_item_xml['Affiche'] ;
        if ($r === 1) {
            return true ;
        } else {
            return false ;
        }
    }
}