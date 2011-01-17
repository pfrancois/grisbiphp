<?php /* coding: utf-8 */

class moyen extends subitem {
	const NEUTRE = 0;
	const DEBIT = 1;
	const CREDIT = 2;


	/**
	 * @return compte le compte mere
	 */
	public function get_mere() {
		global $gsb_xml;
		return new compte($gsb_xml->xpath_uniq('../..', $this->_item_xml));
	}

	/**
	 * @return int (retourne le signe du moyen (0,1 ou 2) cf constante de la classe
	 */
	public function get_signe() {
		return (int)$this->_item_xml['Signe'];
	}

	/**
	 * change le signe par defaut du moyen. les types sont des const de classes.
	 *
	 * @param integer $type le type de la categorie
	 * @throws exception_parametre_invalide si $type n'est pas int
	 * @return void
	 */
	public function set_signe($type) {
		if ($type === 0 || $type === 1 || $type === 2) {
			$this->_item_xml['Signe']=$type;
		} else {
			throw new exception_parametre_invalide("'$type' parametre invalide. il doit etre 0, 1 ou 2");
		}
	}

	/**
	@return bool
	*/
	public function has_numerotation_auto() {
		$r = (string )$this->_item_xml['Numerotation_auto'];
		if ($r === '1') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param $etat
	 * @return void
	 */
	public function set_numerotation_auto($etat) {
		if ($etat === true || $etat === false) {
			if ($etat == true) {
				$etat = 1;
			} else {
				$etat = 0;
			}
			$this->_item_xml['Numerotation_auto']=$etat;
		} else {
			throw new exception_parametre_invalide("'$etat' parametre invalide. il doit etre soit true soit false");
		}
	}
	/**
	 * question si a le numero complementaire
	 * @return bool
	 */
	public function has_entree_compl() {
		$r = (string )$this->_item_xml['Affiche_entree'];
		if ($r === '1') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @return string
	 */
	public function get_entree_comp() {
		return (string )$this->_item_xml['No_en_cours'];
	}

	/**
	@param string $entree
	@return void
	*/
	public function set_entree_comp($entree) {
		$this->_item_xml['Affiche_entree']=1;
		$id = (string )$entree;
		$this->_item_xml['No_en_cours']=$entree;
	}

	/**
	 * @return void
	 */
	public function efface_entree_comp() {
		$this->_item_xml['Affiche_entree']=0;
		$this->_item_xml['No_en_cours']="";
	}

	/**
	 * @return int
	 */
	public function get_next_num() {
		if ($this->has_entree_compl()){
			if ($this->has_numerotation_auto()){
				return ((int)$this->get_entree_comp())+1;
			}else {
				return $this->get_entree_comp();
			}
		}else {
			return "";
		}
	}
}