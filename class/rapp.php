<?php  /* coding: utf-8 */ 

class rapp extends item {
    const POINTEE = 1 ;
    const RAPPROCHEE = 2 ;
    const RIEN = 0 ;

    public function __construct(SimpleXMLElement $c, $nouvelle = false, $id = null) {
        global $gsb_rapps ;
        parent::__construct($c) ;
        if ($nouvelle) {
            if (is_null($id)) {
                $id = $gsb_rapps->get_next() ;
            }
            if (is_numeric($id)) {
                $id = (int)$id ;
                try {
                    $gsb_rapps->get_by_id($id) ;
                    throw new exception_index('rapp', $id) ;
                }
                catch (exception_not_exist $except) {
                    $this->_dom->setAttributeNode(new DOMAttr('No', $id)) ;
                    $this->_dom->setAttributeNode(new DOMAttr('Nom', date("c"))) ;
                }
            } else {
                throw new exception_parametre_invalide("l'id '$id' n'est pas valide") ;
            }

        }
    }
}