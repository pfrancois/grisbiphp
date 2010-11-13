<?php
//$Id: operations.php 38 2010-08-30 23:45:19Z pfrancois $
/**
 * class d'abstraction de l'ensemble compte
 */
class operations extends items {
    /**
     * @const la chaine qui permet une iteration facile
     */
    protected $_xpath = '//Operation' ;
    public $nom_classe = __class__ ;

    /**
     * renvoi l'operation dont on donne l'id
     *
     * @param integer $id id de l'operation demandée
     * @throws exception_not_exist si l'id n'existe pas
     * @throws exception_parametre_invalide si $id n'est integer
     * @return operation
     */
    public function get_by_id($id) {
        global $gsb_xml ;
        try {
			if (is_numeric((string)$id)) {
				$id=(int)$id;
                $req = $gsb_xml->xpath_uniq("//Operation[@No='$id']") ;
            } else {
                throw new exception_parametre_invalide('$id') ;
            }
        }
        catch (Exception_no_reponse $except) {
            throw new exception_not_exist("operation", $id) ;
        }
        return new operation($req, item::ANCIENNE, $id) ;
    }

    /**
     * permet d'avoir le prochain id disponible
     *
     * @return int le prochain id
     */
    public function get_next() {
        global $gsb_xml ;
        $r = (int)$gsb_xml->xpath_uniq('//Generalites/Numero_derniere_operation') ;
        return $r + 1 ;
    }
}