<?php
//$Id: banques.php 38 2010-08-30 23:45:19Z pfrancois $
class banques extends items {
	/**
	 * @const la chaine qui permet une iteration facile
	 */
	protected $_xpath = '//Banques/Detail_des_banques/Banque' ;
	public $nom_classe = __class__ ;
	/**
	 * renvoi le tiers dont on donne l'id
	 *
	 * @param integer $id id de la Banque demandée
	 * @return banque
	 * @throws exception_not_exist si l'id n'existe pas
	 * @throws exception_parametre_invalide si $id n'est integer
	 */
	public function get_by_id($id) {
		global $gsb_xml ;
		try {
			if (is_numeric((string)$id)) {
				$id=(int)$id;
				$r = $gsb_xml->xpath_uniq("//Banque[@No='$id']") ;
			} else {
				throw new exception_parametre_invalide('$id') ;
			}
		}
		catch (Exception_no_reponse $except) {
			throw new exception_not_exist("banque", $id) ;
		}
		return new banque($r) ;
	}

	/**
	 * permet d'avoir le prochain id disponible
	 *
	 * @return int le prochain id
	 */
	public function get_next() {
		$nb = 0 ;
		foreach ($this->iter() as $banque) {
			if ($banque->get_id() > $nb) {
				$nb = $banque->get_id() ;
			}
		}
		return $nb + 1 ;
	}
}