<?php  /* coding: utf-8 */ 

class tier extends item {
    /**
     * constructeur
     * @param SimpleXMLElement $c la partie du xml concernant le tiers
     * @param bool $nouvelle si c'est un nouveau tiers
     * @param int $id l'id du nouveau tiers
	 * @construct
     */
    public function __construct(SimpleXMLElement $c, $nouvelle = false, $id = null) {
        parent::__construct($c) ;
        global $gsb_tiers ;
        if ($nouvelle) {
            if (is_null($id)) {
                $id = $gsb_tiers->get_next() ;
            }
            $this->_dom->setAttributeNode(new DOMAttr('No', $id)) ;
            $this->_dom->setAttributeNode(new DOMAttr('Nom', "")) ;
            $this->_dom->setAttributeNode(new DOMAttr('Informations', '')) ;
            $this->_dom->setAttributeNode(new DOMAttr('Liaison', '0')) ;
        }
    }
    /**
     * tier::get_id()
     *
     * @return int
     */
    public function get_id() {
        return (int)$this->_item_xml['No'] ;
    }

    /**
     * tier::set_nom()
     *
     * @param string $nom
     * @return void
     * @throws exception_parametre_invalide si le nom est vide
     */
    public function set_nom($nom) {
        if (empty($nom)) {
            throw new exception_parametre_invalide('le nom ne peut etre vide') ;
        }
        $this->_item_xml['Nom']=$nom ;
    }
    /**
     * tier::delete()
     * efface le tier
     * @return void
     * @see class/item::delete()
     * @throws exception_integrite_referentielle si le tier est utilise dans une operation ou une echeance
     */
    public function delete() {
        global $gsb_xml ;
        global $gsb_tiers ;
        //verification que le tiers existe
        $id = $this->get_id() ;
        //verification que le tiers n'a pas d'operation reliés
        try {
            $q = $gsb_xml->xpath_iter("//Operation[@T='$id']") ;
            $q = $q[0] ;
            throw new exception_integrite_referentielle('tiers', $id, 'operation', $q['No']) ;
        }
        catch (Exception_no_reponse $except) {
        	//verification qu'il n'y a pas d'echeances relies
	        try {
	            $q = $gsb_xml->xpath_iter("//Echeance[@Tiers='$id']") ;
	            $q = $q[0] ;
	            throw new exception_integrite_referentielle('tiers', $id, 'echeance', $q['No']) ;
	        }
	        catch (Exception_no_reponse $except) {
	            $this->_dom->parentNode->removeChild($this->_dom) ;
	            $nb = 0 ;
	            $nb_next = 0 ;
        	    foreach ($gsb_xml->xpath_iter('//Detail_des_tiers/Tiers') as $tier) {
                    $nb++ ;
                    if ($nb_next < $tier['No']) {
                        $nb_next = (int)$tier['No'] ;
                    }
                }
	            //numero du plus grand tiers
	            if ($id >= (($gsb_tiers->get_next()) - 1)) {
	                //recuperation du dernier numero du tiers
	                if ($id > $nb_next) {
	                    $gsb_xml->get_xml()->Tiers->Generalites->No_dernier_tiers = $nb_next ;
	                }
	            }
	            //nb de tiers
	            $gsb_xml->get_xml()->Tiers->Generalites->Nb_tiers = $nb ;
	        }
	    }
    }
}