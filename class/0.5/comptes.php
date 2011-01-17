<?php /* coding: utf-8 */

/**
 * class d'abstraction de l'ensemble compte
 */
class comptes extends items {
	/**
	 * @var la chaine qui permet une iteration facile
	 */
	protected $_xpath = '//Compte';
	public $nom_classe = __class__;
	protected $_categories;
	/**
	 * renvoi le compte dont on donne l'id
	 *
	 * @param integer $id id du compte demandé
	 * @return compte
	 * @throws exception_not_exist si l'id n'existe pas
	 * @throws exception_parametre_invalide si $id n'est integer
	 */
	public function get_by_id($id) {
		global $gsb_xml;
		try {
			if (is_numeric($id)) {
				$r = $gsb_xml->xpath_uniq("//Compte/Details/No_de_compte[.='$id']/../..");
			} else {
				throw new exception_parametre_invalide('$id');
			}
		}
		catch (Exception_no_reponse $except) {
			throw new exception_not_exist("compte", $id);
		}
		return new compte($r);
	}

	/**
	 * renvoi l'id du compte dont on a donné le nom
	 *
	 * @param string $nom nom du compte cherche
	 * @return integer
	 * @throws exception_not_exist si le nom ne renvoit rien
	 */
	public function get_id_by_name($nom) {
		global $gsb_xml;
		try {
			$r = $gsb_xml->xpath_uniq("//Compte/Details/Nom[.='$nom']/../No_de_compte");
		}
		catch (Exception_no_reponse $except) {
			throw new exception_not_exist("compte", $nom);
		}
		return (int)$r;
	}

	/**
	 * permet d'avoir le prochain id disponible
	 *
	 * @return int le prochain id
	 */
	public function get_next() {
		global $gsb_xml;
		$r = $gsb_xml->xpath_iter("//Compte/Details/No_de_compte");
		$max = 0;
		foreach ($r as $ope) {
			if ((int)$ope > $max) {
				$max = (int)$ope;
			}
		}
		return $max + 1;
	}

	/**
	 * renvoie le compte courant
	 *
	 * @return integer renvoie le compte courant
	 */
	public function get_compte_courant() {
		global $gsb_xml;
		return (int)$gsb_xml->xpath_uniq("//Comptes/Generalites/Compte_courant");
	}
	/**
	 * change le compte courant
	 *
	 * @param integer $id id du compte a mettre en compte courant
	 * @throws exception_parametre_invalide si $id n'est pas int
	 * @throws exception_not_exist si l'id n'existe pas
	 */

	public function set_compte_courant($id) {
		global $gsb_xml;
		if (is_numeric($id)) {
			$r = $gsb_xml->xpath_uniq("//Comptes/Generalites");
			try {
				$this->get_by_id($id);
				$r->Compte_courant = $id;
			}
			catch (Exception_not_exist $except) {
				throw new exception_not_exist("compte", $id);
			}
		} else {
			throw new exception_parametre_invalide('$id:' . $id);
		}
	}
	/**
	 * renvoie l'ensemble des comptes
	 * @param array $type_inclus see compte
	 * @see class/items::iter()
	 * @return compte
	 */
	public function iter($type_inclus = array(compte::T_BANCAIRE, compte::T_ESPECE)) {
		global $gsb_xml;
		$cpts = $gsb_xml->xpath_iter("//Compte");
		if ($type_inclus==='all'){
			$type_inclus=array(compte::T_ACTIF,compte::T_BANCAIRE,compte::T_ESPECE,compte::T_PASSIF);
		}
		$r = array();
		foreach ($cpts as $c) {
			if (in_array((int)$c->Details->Type_de_compte, $type_inclus)) {
				$r[] = new compte($c);
			}
		}
		return $r;
	}
}