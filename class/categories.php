<?php /* coding: utf-8 */ 

class categories extends items {
    /**
     * @var la chaine qui permet une iteration facile
     */
    protected $_xpath = '//Detail_des_categories/Categorie' ;
    public $nom_classe = __class__ ;

    /**
     * renvoi la categorie dont on donne l'id
     *
     * @param integer $id id de la categorie demand�e
     * @return categorie
     * @throws exception_not_exist si l'id n'existe pas
     * @throws exception_parametre_invalide si $id n'est integer
     */
    public function get_by_id($id) {
        global $gsb_xml ;
        try {
			if (is_numeric((string)$id)) {
				$id=(int)$id;
                $r = $gsb_xml->xpath_uniq("//Detail_des_categories/Categorie[@No='$id']") ;
            } else {
                throw new exception_parametre_invalide('$id') ;
            }
        }
        catch (Exception_no_reponse $except) {
            throw new exception_not_exist("categorie", $id) ;
        }
        return new categorie($r) ;
    }
    /**
     * renvoi l'id du compte dont on a donn� le nom
     *
     * @param string $nom nom du compte cherche
     * @return integer
     * @throws exception_not_exist si le nom ne renvoit rien
     * @assert
     */
    public function get_id_by_name($nom) {
        global $gsb_xml ;
        try {
            $r = $gsb_xml->xpath_uniq("//Categorie[@Nom='$nom']") ;
        }
        catch (Exception_no_reponse $except) {
            throw new exception_not_exist("categorie", $nom) ;
        }
        return (int)$r['No'] ;
    }
    /**
     * permet d'avoir le prochain id disponible
     *
     * @return {integer} le prochain id
     */
    public function get_next() {
        global $gsb_xml ;
        (int)$r = $gsb_xml->xpath_uniq('//Categories/Generalites/No_derniere_categorie') ;
        return $r + 1 ;
    }

    public function count() {
        global $gsb_xml ;
        return (int)$gsb_xml->xpath_uniq('//Categories/Generalites/Nb_categories') ;
    }

}