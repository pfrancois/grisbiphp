<?php
//$Id: rapps.php 38 2010-08-30 23:45:19Z pfrancois $
class rapps extends items {
    /**
     * @const la chaine qui permet une iteration facile
     */
    protected $_xpath = '//Rapprochement' ;
    public $nom_classe = __class__ ;
    /**
     * renvoi l'operation dont on donne l'id
     *
     * @param integer $id id du rapprochement demandée
     * @return rapp
     * @throws exception_not_exist si l'id n'existe pas
     * @throws exception_parametre_invalide si $id n'est integer
     */
    public function get_by_id($id) {
        global $gsb_xml ;
        try {
			if (is_numeric((string)$id)) {
				$id=(int)$id;
                $r = $gsb_xml->xpath_uniq("//Rapprochement[@No='$id']") ;
            } else {
                throw new exception_parametre_invalide('$id') ;
            }
        }
        catch (Exception_no_reponse $except) {
            throw new exception_not_exist("rapprochement", $id) ;
        }
        return new rapp($r) ;
    }
    /**
     * permet d'avoir le prochain id disponible
     *
     * @return int le prochain id
     */
    public function get_next() {
        global $gsb_xml ;
        $r = $gsb_xml->xpath_iter("//Rapprochement/attribute::No") ;
        $max = 0 ;
        foreach ($r as $element) {
            if ((int)$element['No'] > $max) {
                $max = (int)$element['No'] ;
            }
        }
        return $max + 1 ;
    }
    public function iter() {
        global $gsb_xml ;
        return $gsb_xml->iter_class("//Rapprochement", 'rapp') ;
    }
    public function new_rapp($id = null) {
        global $gsb_xml ;
        $noeud = $gsb_xml->get_xml()->Rapprochements->Detail_des_rapprochements ;
        $noeud = $noeud->addChild("Rapprochement") ;
        $final = new rapp($noeud, item::NOUVELLE, $id) ;
        return $final ;
    }
}