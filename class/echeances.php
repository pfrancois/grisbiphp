<?php  /* coding: utf-8 */ 

class echeances extends items {
	/**
	 * @const la chaine qui permet une iteration facile
	 */
	protected $_xpath = '//Echeance' ;
	public $nom_classe = __class__ ;
	/**
	 * renvoi le tiers dont on donne l'id
	 *
	 * @param integer $id id de l'echeance demandée
	 * @return echeance
	 * @throws exception_not_exist si l'id n'existe pas
	 * @throws exception_parametre_invalide si $id n'est integer
	 */
	public function get_by_id($id) {
		global $gsb_xml ;
		try {
			if (is_numeric((string)$id)) {
				$id=(int)$id;
				$r = $gsb_xml->xpath_uniq("//Echeance[@No='$id']") ;
			} else {
				throw new exception_parametre_invalide('$id') ;
			}
		}
		catch (Exception_no_reponse $except) {
			throw new exception_not_exist("echeances", $id) ;
		}
		return new echeance($r) ;
	}

	/**
	 * permet d'avoir le prochain id disponible
	 *
	 * @return int le prochain id
	 */
	public function get_next() {
		global $gsb_xml ;
		(int)$r = $gsb_xml->xpath_uniq('//Echeances/Generalites/No_derniere_echeance') ;
		return $r + 1 ;
	}
}