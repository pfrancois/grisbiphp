<?php  /* coding: utf-8 */   /* coding: utf-8 */ 
/**
 * class echeance
 * @author francois
 *
 */
class echeance extends item {
	/**
	* verifie si les differentes echeances ne sont pas echus
	* @return bool
	*/
	public function verif_echus(){
		$date_ech=util::datefr2time($this->_item_xml['Date']);
		if ($date_ech<time()) {
			if (datefr2time($this->_item_xml['Date_limite'])>time()){
				return true;
			}
		}
	}
	
	public function enregistre_echus(){
		global $gsb_comptes;
		global $gsb_categories;
		global $gsb_tiers;
		global $gdb_exercices;
		//on ajoute l'operation au compte
		$compte=$gsb_comptes->get_by_id((int)$this->_item_xml['Compte']);
		$operation=$compte->new_operation();
		$operation->set_date($date_ech);
		$categorie=$gsb_categories->get_by_id((int)$this->_item_xml['Categorie']);
		$operation->set_categorie($categorie);
		if ($categorie->has_sub()){
			$operation->set_scat($categorie->get_sub_by_id((int)$this->_item_xml['Sous-categorie']));
		}
		$ope_montant=util::fr2cent((string)$this->_item_xml['Montant']);
		$operation->set_montant($ope_montant);
//TODO gestion des devises a revoir
		$moyen=$compte->get_moyen_by_id((int)$this->_item_xml['Type']);
		$operation->set_moyen($moyen);
		if ($moyen->has_numerotation_auto()){
			$operation->set_num_chq((int)$moyen->get_entree_comp()+1);
		}else {
			$operation->set_num_chq((string)$this->_item_xml['Contenu_du_type']);
		}
		$exercice=$gsb_exercices->get_by_id($this->_item_xml['Exercice']);
		$cpt_jumelle=$gsb_comptes->get_by_id($this->_item_xml['Virement_compte']);

		$operation->set_exercice($exercice);
		$operation->set_notes((string)$this->_item_xml['Notes']);
		$tier=$gsb_tiers->get_by_id((string)$this->_item_xml['Tiers']);
		$operation->set_tiers($tier);

		//on met la nouvelle date de l'echeance
		$periode=(int)$this->_item_xml['Periodicite'];
		if ($periode==0){//une seule fois
			$this->delete();
		}
		if ($periode==1){//hebdo
			$this->_item_xml['Date']=date('j/n/Y', util::add_date($date_ech,7,0,0));
		}
		if ($periode==2){//mensuel
			$this->_item_xml['Date']=date('j/n/Y', util::add_date($date_ech,0,1,0));
		}
		if ($periode==3){//annuel
			$this->_item_xml['Date']=date('j/n/Y', util::add_date($date_ech,0,0,1));
		}
		if ($periode==4){//perso
			if ($this->_item_xml['Intervalle_periodicite']==0){
				$this->_item_xml['Date']=date('j/n/Y', util::add_date($date_ech,(int)$this->_item_xml['Intervalle_periodicite'],0,0));
			}
			if ($this->_item_xml['Intervalle_periodicite']==1){
				$this->_item_xml['Date']=date('j/n/Y', util::add_date($date_ech,0,(int)$this->_item_xml['Intervalle_periodicite'],0));
			}
			if ($this->_item_xml['Intervalle_periodicite']==2){
				$this->_item_xml['Date']=date('j/n/Y', util::add_date($date_ech,0,0,(int)$this->_item_xml['Intervalle_periodicite']));
			}

	}
}