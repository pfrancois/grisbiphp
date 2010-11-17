<?php /* coding: utf-8 */

class devises extends items {
	/**
	 * @const la chaine qui permet une iteration facile
	 */
	protected $_xpath = '//Devises/Detail_des_devises/Devise' ;
	public $nom_classe = __class__ ;
	/**
	 * renvoi le tiers dont on donne l'id
	 *
	 * @param integer $id id de la devise demand�e
	 * @return devise
	 * @throws exception_not_exist si l'id n'existe pas
	 * @throws exception_parametre_invalide si $id n'est integer
	 */
	public function get_by_id($id) {
		global $gsb_xml ;
		try {
			if (is_numeric((string)$id)) {
				$id=(int)$id;
				$r = $gsb_xml->xpath_uniq("//Devise[@No='$id']") ;
			} else {
				throw new exception_parametre_invalide('$id') ;
			}
		}
		catch (Exception_no_reponse $except) {
			throw new exception_not_exist("devises", $id) ;
		}
		return new devise($r) ;
	}

	/**
	 * permet d'avoir le prochain id disponible
	 *
	 * @return int le prochain id
	 */
	public function get_next() {
		global $gsb_xml ;
		(int)$r = $gsb_xml->xpath_uniq('//Devises/Generalites/No_derniere_devise') ;
		return $r + 1 ;
	}
}