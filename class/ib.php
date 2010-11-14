<?php  /* coding: utf-8 */ 

class ib extends subitems {
    const DEBIT = categorie::DEBIT ;
    const CREDIT = categorie::CREDIT ;
    const SPECIAL = categorie::SPECIAL ;
    protected $_xpath = './Sous-imputation' ;
    public $nom_sub_classe = "sib" ;
    protected $xpath_next = "No_derniere_sous_imputation" ;
    /**
     * ib::delete()
     *
     * @return void
     */
    public function delete() {
        global $gsb_xml ;
        global $gsb_ibs ;
        //verification que la ib n'existe pas
        try {
            $id = $this->get_id() ;
            $q = $gsb_xml->xpath_iter("//Operation[@I='$id']") ;
            $q = $q[0] ;
            throw new exception_integrite_referentielle('ib', $this->get_id(), 'operation',
                $q['No']) ;
        }
        catch (Exception_no_reponse $except) {
            $this->_dom->parentNode->removeChild($this->_dom) ;
            //recuperation du dernier numero
            $nb = 0 ;
            $nb_next = 0 ;
            foreach ($gsb_ibs->iter() as $ib) {
                $nb++ ;
                if ($ib->get_id() >= $nb_next) {
                    $nb_next = $ib->get_id() ;
                }
            }
            if ($id > $nb_next) {
                $gsb_xml->get_xml()->Imputations->Generalites->No_derniere_imputation = $nb_next ;
            }
            //nb de ibs
            $gsb_xml->get_xml()->Imputations->Generalites->Nb_imputation = $nb ;
        }
    }
    /**
     * @return int le type de la categorie
     */
    public function get_type() {
        return (int)$this->_item_xml['Type'] ;
    }
    /**
     * @param integer $type le type de la categorie
     */
    public function set_type($type) {
        if ($type === 0 || $type === 1 || $type === 2) {
            $this->_dom->setAttributeNode(new DOMAttr('Type', "$type")) ;
        } else {
            throw new exception_parametre_invalide("'$type' parametre invalide. il doit etre 0, 1 ou 2") ;
        }
    }
    /**
     * ib::get_sub_by_id()
     *
     * @param int $id
     * @return item le sous item demande
     */
    public function get_sub_by_id($id) {
        global $gsb_xml ;
        try {
            if (is_numeric($id)) {
                $r = $gsb_xml->xpath_uniq($this->_xpath . "[@No='$id']", $this->_item_xml) ;
            } else {
                throw new exception_parametre_invalide('$id') ;
            }
        }
        catch (Exception_no_reponse $except) {
            throw new exception_not_exist($this->nom_sub_classe, $id) ;
        }
        return new sib($r) ;
    }
     /**
     * ib::get_sub_by_name()
     *
     * @param string $name
     * @return item le sous item demande
     */
    public function get_sub_by_name($name) {
        global $gsb_xml ;
        try {
            $r = $gsb_xml->xpath_uniq($this->_xpath . "[@Nom='$name']", $this->_item_xml) ;
        }
        catch (Exception_no_reponse $except) {
            throw new exception_not_exist($this->nom_sub_classe, $name) ;
        }
        return new sib($r) ;
    }
}