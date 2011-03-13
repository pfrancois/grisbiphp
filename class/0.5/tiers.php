<?php /* coding: utf-8 */

class tiers extends items {
	/**
	 * @var la chaine qui permet une iteration facile
	 */
	protected $_xpath = '//Detail_des_tiers/Tiers';
	/**
	 * @ var le nom de la classe car pas de late state binding
	 */
	public $nom_classe = __class__;
	/**
	 * renvoi le tiers dont on donne l'id
	 *
	 * @param integer $id id du tiers demandee
	 * @throws exception_not_exist si l'id n'existe pas
	 * @throws exception_parametre_invalide si $id n'est integer
	 */
	public function get_by_id($id) {
		global $gsb_xml;
		try {
			if (is_numeric((string)$id)) {
				$id=(int)$id;
				$req = $gsb_xml->xpath_uniq("//Detail_des_tiers/Tiers[@No='$id']");
			} else {
				throw new exception_parametre_invalide("$id");
			}
		}
		catch (Exception_no_reponse $except) {
            if ($id==0){
                return null;
            } else {
                throw new exception_not_exist("tiers", $id);
            }
		}
		return new tier($req);
	}

	/**
	 * permet d'avoir le prochain id disponible
	 *
	 * @return int le prochain id
	 */
	public function get_next() {
		global $gsb_xml;
		$req = (int)$gsb_xml->get_xml()->Tiers->Generalites->No_dernier_tiers;
		return $req + 1;
	}

	/**
	 * retourne un tier si on lui donne le nom
	 *
	 * @param string $name le nom du tiers a chercher
	 * @return tier
	 */
	public function get_id_by_name($name) {
		global $gsb_xml;
		try {
			$r = $gsb_xml->xpath_uniq("//Detail_des_tiers/Tiers[@Nom='$name']");
		}
		catch (Exception_no_reponse $except) {
			throw new exception_not_exist("tiers", $name);
		}
		return new tier($r);
	}

	/**
	 * renvoie un nouveau tiers
	 * attention, il faut en remplir les proprietes
	 * @param integer $id si non NULL, il renvoit le nouveau tiers de l'id
	 * @return tier
	 */
	public function new_tier($id = NULL) {
		global $gsb_xml;
		$n=$gsb_xml->get_xml()->Tiers->Detail_des_tiers;
		$n = $n->addChild("Tiers");
		$t = new tier($n, item::NOUVELLE, $id);
		return $t;
	}
}
