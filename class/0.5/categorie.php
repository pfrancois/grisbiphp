<?php /* coding: utf-8 */

class categorie extends subitems {
	const DEBIT = 1;
	const CREDIT = 0;
	const SPECIAL = 2;
	protected $_xpath = './Sous-categorie';
	protected $xpath_next = "No_derniere_sous_cagegorie";
	public $nom_sub_classe = "scat";

	/**
	 * @return integer le type de la categorie
	 */
	public function get_type() {
		return (int)$this->_item_xml['Type'];
	}

	/**
	 * @param integer $type le type de la categorie
	 * @throws exception_parametre_invalide si $type n'est pas int
	 */
	public function set_type($type) {
		if ($type === 0 || $type === 1 || $type === 2) {
			$this->_item_xml['Type']=$type;
		} else {
			throw new exception_parametre_invalide("'$type' paramètre invalide. il doit être 0, 1 ou 2");
		}
	}

	public function delete() {
		global $gsb_xml;
		global $gsb_categories;
		//verification que la categorie n'existe pas dans une operation
		try {
			$id = $this->get_id();
			$q = $gsb_xml->xpath_iter("//Operation[@C='$id']");
			$q = $q[0];
			throw new exception_integrite_referentielle('categorie', $this->get_id(),'operation', $q['No']);
		}
		catch (Exception_no_reponse $except) {
			$this->_dom->parentNode->removeChild($this->_dom);
			//recuperation du dernier numero
			$nb = 0;
			$nb_next = 0;
			foreach ($gsb_categories->iter() as $cat) {
				$nb++;
				if ($cat->get_id() >= $nb_next) {
					$nb_next = $cat->get_id();
				}
			}
			if ($id > $nb_next) {
				$gsb_xml->get_xml()->Categories->Generalites->No_derniere_categorie = $nb_next;
			}
			//nb de categories
			$gsb_xml->get_xml()->Categories->Generalites->Nb_categories = $nb;
		}
	}

	/**
	 * categorie::get_sub_by_id()
	 *
	 * @param int $id
	 * @return item le sous item demande
	 */
	public function get_sub_by_id($id) {
		global $gsb_xml;
		try {
			if (is_numeric($id)) {
				$r = $gsb_xml->xpath_uniq($this->_xpath . "[@No='$id']", $this->_item_xml);
			} else {
				throw new exception_parametre_invalide('$id');
			}
		}
		catch (Exception_no_reponse $except) {
			throw new exception_not_exist($this->nom_sub_classe, $id);
		}
		return new scat($r);
	}
	/**
	 * categorie::get_sub_by_name()
	 *
	 * @param string $name
	 * @return item le sous item demande
	 */
	public function get_sub_by_name($name) {
		global $gsb_xml;
		try {
			$r = $gsb_xml->xpath_uniq($this->_xpath . "[@Nom='$name']", $this->_item_xml);
		}
		catch (Exception_no_reponse $except) {
			throw new exception_not_exist($this->nom_sub_classe, $name);
		}
		return new scat($r);
	}
}
