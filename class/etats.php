<?php

class etats extends items {
    /**
     * @const la chaine qui permet une iteration facile
     */
    protected $_xpath = '//Etat' ;
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
            if (is_numeric($id)) {
                $r = $gsb_xml->xpath_uniq("//Etat/No[.='$id']/..") ;
            } else {
                throw new exception_parametre_invalide('$id') ;
            }
        }
        catch (Exception_no_reponse $except) {
            throw new exception_not_exist("etat", $id) ;
        }
        return new etat($r) ;
    }
    /**
     * renvoi l'id du compte dont on a donné le nom
     *
     * @param string $nom nom du compte cherche
     * @return integer
     * @throws exception_not_exist si le nom ne renvoit rien
     * @assert
     */
    public function get_id_by_name($nom) {
        global $gsb_xml ;
        try {
            $r = $gsb_xml->xpath_uniq("//Etat/Nom[.='$nom']/..") ;
        }
        catch (Exception_no_reponse $except) {
            throw new exception_not_exist("etat", $nom) ;
        }
        return (int)$r->No ;
    }
    /**
     * permet d'avoir le prochain id disponible
     *
     * @return int le prochain id
     */
    public function get_next() {
        global $gsb_xml ;
        $r = $gsb_xml->xpath_iter("//Etat") ;
        $max = 0 ;
        foreach ($r as $element) {
            if ((int)$element->No > $max) {
                $max = (int)$element->No ;
            }
        }
        return $max + 1 ;
    }
    public function iter() {
        global $gsb_xml ;
        return $gsb_xml->iter_class("//Etat", 'etat') ;
    }
}