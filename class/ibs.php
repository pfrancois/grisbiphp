<?php  /* coding: utf-8 */ 

class ibs extends items {
	/**
	 * @const la chaine qui permet une iteration facile
	 */
	protected $_xpath = '//Imputation' ;
	public $nom_classe = __class__ ;
	/**
	 * renvoi le tiers dont on donne l'id
	 *
	 * @param integer $id id de l'imputation demandée
	 * @return ibs
	 * @throws exception_not_exist si l'id n'existe pas
	 * @throws exception_parametre_invalide si $id n'est integer
	 */
	public function get_by_id($id) {
		global $gsb_xml ;
		try {
			if (is_numeric($id)) {
				$r = $gsb_xml->xpath_uniq("//Imputation[@No='$id']") ;
			} else {
				throw new exception_parametre_invalide('$id') ;
			}
		}
		catch (Exception_no_reponse $except) {
			throw new exception_not_exist("imputations", $id) ;
		}
		return new ib($r) ;
	}

	/**
	 * permet d'avoir le prochain id disponible
	 *
	 * @return int le prochain id
	 */
	public function get_next() {
		global $gsb_xml ;
		(int)$r = $gsb_xml->xpath_uniq('//Imputations/Generalites/No_derniere_imputation') ;
		return $r + 1 ;
	}
	/**
	 * renvoi l'id de l'ib dont on a donné le nom
	 *
	 * @param string $nom nom du compte cherche
	 * @return integer
	 * @throws exception_not_exist si le nom ne renvoit rien
	 * @assert
	 */
	public function get_id_by_name($nom) {
		global $gsb_xml ;
		try {
			$r = $gsb_xml->xpath_uniq("//Imputation[@Nom='$nom']") ;
		}
		catch (Exception_no_reponse $except) {
			throw new exception_not_exist("ib", $nom) ;
		}
		return (int)$r['No'] ;
	}
}