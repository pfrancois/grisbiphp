<?php  /* coding: utf-8 */

class operation extends item {
	/**
	 * @param SimpleXMLElement $c
	 * @param unknown_type $nouvelle
	 * @param unknown_type $id
	 * @throws exception_index
	 */
	public function __construct(SimpleXMLElement $c, $nouvelle = false, $id = null) {
		global $gsb_xml ;
		global $gsb_operations ;
		parent::__construct($c) ;
		if ($nouvelle) {
			if (is_null($id)) {
				$id = $gsb_operations->get_next() ;
			}
			//verification avant la creation
			try{
				$gsb_operations->get_by_id($id);
				throw new exception_index("operation",$id);
			} catch (Exception_not_exist $except) {}
			//numerotation generale
			if ($id > ($gsb_operations->get_next() - 1)) {
				//gestion des id a changer
				$gsb_xml->xpath_uniq('Generalites')->Numero_derniere_operation = $id ;
			}

			//numerotation dans le compte
			// on ajoute l'attribut comme on ne peut pas le changer
			$this->_dom->setAttributeNode(new DOMAttr('No', $id)) ;
			//pour les autres champs qui ne sont pas changés par l'application
			$this->_dom->setAttributeNode(new DOMAttr('Id', '')) ;
			$this->_dom->setAttributeNode(new DOMAttr('D', '0/0/0')) ;
			$this->set_date(time()) ; //date du jour par defaut
			$this->_dom->setAttributeNode(new DOMAttr('Db', '0/0/0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('M', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('De', '1')) ;
			$this->_dom->setAttributeNode(new DOMAttr('Rdc', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('Tc', '0,0000000')) ;
			$this->_dom->setAttributeNode(new DOMAttr('Fc', '0,0000000')) ;
			$this->_dom->setAttributeNode(new DOMAttr('T', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('C', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('Sc', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('Ov', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('N', '')) ;
			$this->_dom->setAttributeNode(new DOMAttr('Ty', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('Ct', '')) ;
			$this->_dom->setAttributeNode(new DOMAttr('P', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('A', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('R', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('E', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('I', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('Si', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('Pc', '')) ;
			$this->_dom->setAttributeNode(new DOMAttr('Ibg', '')) ;
			$this->_dom->setAttributeNode(new DOMAttr('Ro', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('Rc', '0')) ;
			$this->_dom->setAttributeNode(new DOMAttr('Va', '0')) ;
		}
	}

	/**
	 * renvoie l'id de l'opération
	 * @return int
	 */
	public function get_id() {
		return (int)$this->_item_xml['No'] ;
	}

	/**
	 * renvoi le compte de l'opération
	 * @return compte
	 */
	public function get_compte() {
		global $gsb_xml ;
		$req = $gsb_xml->xpath_uniq('./../..', $this->_item_xml) ;
		return new compte($req) ;
	}

	/**
	 * renvoie la date au format timestamp de l'opération
	 * @return string
	 */
	public function get_date() {
		$date = (string )$this->_item_xml['D'] ;
		$date = util::datefr2time($date) ;
		return $date ;
	}

	/**
	 * renvoie le montant en centime de l'opération
	 * @return int
	 */
	public function get_montant() {
		return util::fr2cent((string )$this->_item_xml['M']) ;
	}

	/**
	 * renvoie le tiers de l'opération
	 * @return tiers|null
	 */
	public function get_tiers() {
		global $gsb_tiers ;
		$t = (int)$this->_item_xml['T'] ;
		return $gsb_tiers->get_by_id($t) ;
	}

	/**
	 * renvoie la categorie
	 * @return categorie|null
	 */
	public function get_categorie() {
		global $gsb_categories ;
		$t = (int)$this->_item_xml['C'] ;
		if ($t==0){
			return null;
		}else {
			return $gsb_categories->get_by_id($t) ;
		}
	}

	/**
	 * @return scat|null
	 **/
	public function get_scat() {
		$t = (int)$this->_item_xml['Sc'] ;
		if ($t==0){
			return null;
		}else {
			return  $this->get_categorie()->get_sub_by_id($t) ;
		}
	}

	/**
	 * @return bool
	 */
	public function is_ventilee() {
		$t = (int)$this->_item_xml['Ov'] ;
		if ($t === 1) {
			return true ;
		} else {
			return false ;
		}
	}

	/**
	 * renvoie les notes
	 * attention, aucun filtre de sanitization si affichage
	 * @return string
	 */
	public function get_notes() {
		return (string )$this->_item_xml['N'] ;
	}

	/**
	 * operation::get_moyen()
	 *
	 * @return moyen
	 */
	public function get_moyen() {
		$t = (int)$this->_item_xml['Ty'] ;
		if ($t==0){
			return null;
		}else {
			return $this->get_compte()->get_moyen_by_id($t) ;
		}
	}

	/**
	 * renvoie le numero du cheque (le numero peut être uen chaine)
	 *
	 * @return string
	 */
	public function get_num_chq() {
		return (string )$this->_item_xml['Ct'] ;
	}

	/**
	 * operation::get_statut_pointage()
	 * cf les constante rapp
	 *
	 * @return int
	 */
	public function get_statut_pointage() {
		$t = (int)$this->_item_xml['P'] ;
		return $t ;
	}

	/**
	 * @return bool
	 */
	public function is_planifie() {
		$r = (int)$this->_item_xml['A'] ;
		if ($r === 1) {
			return true ;
		} else {
			return false ;
		}
	}

	/**
	 * operation::get_rapp()
	 *
	 * @return rapp
	 */
	public function get_rapp() {
		global $gsb_rapps ;
		$t = (int)$this->_item_xml['R'] ;
		return $gsb_rapps->get_by_id($t) ;
	}

	/**
	 * operation::get_exercice()
	 *
	 * @return exercice|null
	 */
	public function get_exercice() {
		global $gsb_exercices ;
		$t = (int)$this->_item_xml['E'] ;
		if ($t==0){
			return null;
		}else {
			return $gsb_exercices->get_by_id($t) ;
		}
	}

	/**
	 * operation::get_ib()
	 *
	 * @return ib
	 */
	public function get_ib() {
		global $gsb_ibs ;
		$t = (int)$this->_item_xml['I'] ;
		if ($t==0){
			return null;
		}else {
			return $gsb_ibs->get_by_id($t);
		}
	}

	/**
	 * operation::get_sib()
	 *
	 * @return sib
	 */
	public function get_sib() {
		$t = (int)$this->_item_xml['Si'] ;
		if ($t==0){
			return null;
		}else {
			return  $this->get_ib()->get_sub_by_id($t);
		}
	}

	/**
	 * operation::get_operation_contrepartie()
	 *
	 * @return operation
	 */
	public function get_operation_contrepartie() {
		global $gsb_operations ;
		$t = (int)$this->_item_xml['Ro'] ;
		return $gsb_operations->get_by_id($t) ;
	}

	/**
	 * operation::get_cpt_contrepartie()
	 *
	 * @return operation
	 */
	public function get_cpt_contrepartie() {
		global $gsb_comptes ;
		$t = (int)$this->_item_xml['Rc'] ;
		return $gsb_comptes->get_by_id($t) ;
	}

	/**
	 * @return bool
	 */
	public function is_ventilation() {
		$t = (int)$this->_item_xml['Va'] ;
		if ($t != 0) {
			return true ;
		} else {
			return false ;
		}
	}

	/**
	 * operation::is_virement()
	 *
	 * @return bool
	 */
	public function is_virement() {
		$t = (int)$this->_item_xml['Ro'] ;
		if ($t != 0) {
			return true ;
		} else {
			return false ;
		}

	}

	/**
	 * operation::get_operation_mere() dans le cadre des operations ventilees
	 *
	 * @return operation
	 */
	public function get_operation_mere() {
		global $gsb_operations ;
		$t = (int)$this->_item_xml['Va'] ;
		return $gsb_operations->get_by_id($t) ;
	}

	/** ------------------------------- SETTER -------------------------------------------------------*/

	/**
	 * @param int $id
	 * @return void
	 */
	public function set_compte($id) {
		global $gsb_xml;
		global $gsb_comptes;
		try {
			if (is_numeric($id)) {
				$compte = $gsb_comptes->get_by_id($id);
			} else {
				throw new exception_parametre_invalide('$id') ;
			}
		}
		catch (Exception_no_reponse $except) {
			throw new exception_not_exist("compte", $id) ;
		}
		$op=array(
		(string) $this->_item_xml['No'] ,
		(string) $this->_item_xml['Id'],
		(string) $this->_item_xml['D'] ,
		(string) $this->_item_xml['Db'],
		(string) $this->_item_xml['M'],
		(string) $this->_item_xml['De'],
		(string) $this->_item_xml['Rdc'],
		(string) $this->_item_xml['Tc'],
		(string) $this->_item_xml['Fc'],
		(string) $this->_item_xml['T'],
		(string) $this->_item_xml['C'],
		(string) $this->_item_xml['Sc'],
		(string) $this->_item_xml['Ov'],
		(string) $this->_item_xml['N'] ,
		(string) $this->_item_xml['Ty'],
		(string) $this->_item_xml['Ct'],
		(string) $this->_item_xml['P'],
		(string) $this->_item_xml['A'],
		(string) $this->_item_xml['R'],
		(string) $this->_item_xml['E'],
		(string) $this->_item_xml['I'],
		(string) $this->_item_xml['Si'],
		(string) $this->_item_xml['Pc'],
		(string) $this->_item_xml['Ibg'],
		(string) $this->_item_xml['Ro'],
		(string) $this->_item_xml['Rc'],
		(string) $this->_item_xml['Va']
		);
		$compte_ancien=$this->get_compte();
		$this->_dom->parentNode->removeChild($this->_dom);
		$t=$compte->get_xml()->Detail_des_operations->addChild("Operation");
		$t->AddAttribute('No', $op[0]);
		//pour les autres champs qui ne sont pas changés par l'application
		$t->AddAttribute('Id', $op[1]);
		$t->AddAttribute('D', $op[2]);
		$t->AddAttribute('Db', $op[3]);
		$t->AddAttribute('M', $op[4]);
		$t->AddAttribute('De', $op[5]);
		$t->AddAttribute('Rdc', $op[6]);
		$t->AddAttribute('Tc', $op[7]);
		$t->AddAttribute('Fc', $op[8]);
		$t->AddAttribute('T', $op[9]);
		$t->AddAttribute('C', $op[10]);
		$t->AddAttribute('Sc', $op[11]);
		$t->AddAttribute('Ov', $op[12]);
		$t->AddAttribute('N', $op[13]);
		$t->AddAttribute('Ty', $op[14]);
		$t->AddAttribute('Ct', $op[15]);
		$t->AddAttribute('P', $op[16]);
		$t->AddAttribute('A', $op[17]);
		$t->AddAttribute('R', $op[18]);
		$t->AddAttribute('E', $op[19]);
		$t->AddAttribute('I', $op[20]);
		$t->AddAttribute('Si', $op[21]);
		$t->AddAttribute('Pc', $op[22]);
		$t->AddAttribute('Ibg', $op[23]);
		$t->AddAttribute('Ro', $op[24]);
		$t->AddAttribute('Rc', $op[25]);
		$t->AddAttribute('Va', $op[26]);

		//modification des soldes
		//ancien compte
		$m=util::fr2cent($op[4])/100 ;
		$compte_ancien->set_solde_courant($compte_ancien->get_solde_courant()-$m);
		//nouveau compte
		$compte->set_solde_courant($compte->get_solde_courant()+$m);
	}

	/**
	 * @param integer $date date au format timestamp
	 * @return void
	 */
	public function set_date($date) {
		if (is_int($date)) {
			$date = date('j/n/Y', $date) ;
			$this->_item_xml['D']=$date;
		} else {
			throw new exception_parametre_invalide("la date '$date' doit être au format timestamp. il s'agit de l'opération".$this->get_id());
		}
	}

	/**
	 * @param integer $value montant de l'opération en centime
	 * @return void
	 * @throw exception_parametre_invalide si le montant n'est pas int
	 */
	public function set_montant($value){
		global $gsb_xml;
		if (is_numeric($value)) {
			$this->_item_xml['M']=util::cent2fr($value) ;
			$compte=$this->get_compte();
			$compte->set_solde_courant($value+$compte->get_solde_courant());
		} else {
			throw new exception_parametre_invalide("le montant '$value' doit être en centime. il s'agit de l'opération".$this->get_id()) ;
		}
	}

	/**
	 * change le tiers en rapport avec cette operation
	 * @param tier $id
	 * @return void
	 */
	public function set_tiers(tier $id) {
		$this->_item_xml['T']=$id->get_id();
	}

	/**
	 * si la cat n'a pas de scat, mise automatique de scat à 0
	 * @param categorie $id
	 * @return void
	 */
	public function set_categorie(categorie $id) {
		$this->_item_xml['C']=$id->get_id();
		if (!$id->has_sub()) {
			$this->_item_xml['Sc']= 0 ;
		}
	}

	/**
	 * si modification de scat et que cat est modifie en meme temps, raccourci pour les deux
	 * @param scat $id
	 * @return void
	 */
	public function set_scat(scat $id) {
		$cat = $this->get_categorie() ;
		if ($id->get_mere()->get_id() == $cat->get_id()) {
			$this->_item_xml['Sc']=$id->get_id();
		} else {
			$this->_item_xml['C']=$id->get_mere()->get_id();
			$this->_item_xml['Sc']=$id->get_id();
		}
	}

	/**
	 * pas encore fais mais normalement cela permet de gerer les operations ventilées
	 * operation::set_ventilee()
	 *
	 * @param bool $v
	 * @return void
	 */
	public function set_ventilee($v) {
		if (!is_bool($v)) {
			throw new exception_parametre_invalide('$v non bool dans opération' . $this->get_id
			()) ;
		}
		$this->_item_xml['Ov']=$v;
	}

	/**
	 * operation::set_notes()
	 *
	 * @param string $n
	 * @return void
	 */
	public function set_notes($n) {
		$this->_item_xml['N']=(string )$n ;
	}

	/**
	 * operation::set_moyen()
	 * attention verification que c'est le bon compte
	 *
	 * @param moyen $id
	 * @return void
	 * @throw exception_parametre_invalide si le moyen en fait pas parti du compte
	 */
	public function set_moyen(moyen $id) {
		$compte = $id->get_mere() ;
		if ($compte->get_id() == (int)$this->get_compte()->get_id()) {
			$this->_item_xml['Ty']=$id->get_id();
		} else {
			throw new exception_parametre_invalide("moyen invalide. vous donnez un moyen du compte " . $compte . " alors que l'opération est dans le compte" . (int)$this->get_compte()->get_id()) ;
		}
	}

	/**
	 * operation::set_num_chq()
	 *
	 * @param string $n
	 * @return void
	 */
	public function set_num_chq($n) {
		$this->_item_xml['Ct']=(string )$n ;
	}

	/**
	 * operation::set_statut_pointage()
	 *
	 * @param int $v
	 * @return void
	 * @throw exception_parametre_invalide si pas o  1 ou 2 ou rapp::
	 */
	public function set_statut_pointage($type) {
		if ($type === 0 || $type === 1 || $type === 2) {
			$this->_item_xml['P']=$type;
		} else {
			throw new exception_parametre_invalide("'$type' parametre invalide. il doit être  0, 1 ou 2") ;
		}
	}

	/**
	 * operation::set_planifie()
	 *
	 * @param bool $v
	 * @return void
	 * @throw exception_parametre_invalide  si pas bool
	 */
	public function set_planifie($v) {
		if (!is_bool($v)) {
			throw new exception_parametre_invalide('$v non bool dans operation ' . $this->get_id
			()) ;
		}
		if ($v) {
			$this->_item_xml['A']= 1 ;
		} else {
			$this->_item_xml['A']=0 ;
		}
	}

	/**
	 * met en place le numero de rapprochement (et change le statut de rapprochement)
	 *
	 * @param rapp $id
	 * @return void
	 */
	public function set_rapp(rapp $id) {
		$this->_item_xml['R']=$id->get_id();
		if ($this->get_statut_pointage() != rapp::RAPPROCHEE) {
			$this->set_statut_pointage(rapp::RAPPROCHEE) ;
		}
	}

	/**
	 * operation::set_exercice()
	 *
	 * @param int $id
	 * @return void
	 */
	public function set_exercice(exercice $id) {
		$this->_item_xml['E']=$id->get_id();
	}

	/**
	 * operation::set_ib()
	 *
	 * @param ib $id
	 * @return void
	 */
	public function set_ib(ib $id) {
		$this->_item_xml['I']=$id->get_id();
	}

	/**
	 * operation::set_sib()
	 *
	 * @param sib $id
	 * @return void
	 */
	public function set_sib(sib $id) {
		$ib = $this->get_ib() ;
		if ($id->get_mere()->get_id() == $ib->get_id()) {
			$this->_item_xml['Si']=$id->get_id();
		} else {
			$this->_item_xml['I']=$id->get_mere()->get_id();
			$this->_item_xml['Si']=$id->get_id();
		}
	}

	/**
	 * @param operation $id l'id de l'opération en contrepartie
	 */
	public function set_operation_contrepartie(operation $id) {
		$this->_item_xml['Ro']=$id->get_id(); //id ope
		$this->_item_xml['Rc']=$id->get_compte()->get_id(); //id compte
	}

	/**
	 * operation::set_ope_mere()
	 *
	 * @param operation $id
	 * @return void
	 */
	public function set_ope_mere(operation $id) {
		$this->_item_xml['Va']=$id->get_id();
	}

	/**
	 *attention, une operation n'a pas de nom
	 *@throw exception_base
	 */
	public function get_nom() {
		throw new exception_base("attention, une operation n'a pas de nom") ;
	}

	/**
	 *attention, une operation n'a pas de nom
	 *@param string $nom
	 *@throw exception_base
	 */
	public function set_nom($nom) {
		throw new exception_base("attention, une operation n'a pas de nom alors que vous voulez lui mettre comme nom '$nom'") ;
	}


	//------------------------------------ ITER ---------------------------------
	/**
	 * operation::get_operation_ventilees()
	 *
	 * @return array of operation
	 * @thrown Exception_no_reponse si pas d'operation ventilee
	 */
	public function iter_operation_ventilees() {
		global $gsb_xml ;
		if ($this->is_ventilee()) {
			$id = $this->get_id() ;
			return $gsb_xml->iter_class("//Operation[@Va='$id']", "operation") ;
		} else {
			throw new Exception_no_reponse("pas possible car $this n'est pas une operation ventilee") ;
		}
	}

	/**
	 *@param bool $controle_integrite gere l'integrité référentielle
	 */
	public function delete($controle_integrite = true) {
		global $gsb_operations ;
		global $gsb_xml ;
		$id = $this->get_id() ;
		if ($controle_integrite) {
			if ($this->is_ventilee() == true) {
				try {
					$iterateur = $this->iter_operation_ventilees() ;
					$id_fille = $iterateur[0]->get_id() ;
					throw new exception_integrite_referentielle('operation', $id, 'operation', $id_fille) ;
				}
				catch (exception_not_exist $e) {
					// @codeCoverageIgnoreStart
					throw new BadMethodCallException("cette operation " . $this->get_id() .
						" a été définie comme une ventilation mais n'a pas d'opération fille. probleme dans la structure du fichier. effacement annulé") ;
					// @codeCoverageIgnoreEnd
				}
			}
			if ($this->is_virement() == true) {
				try {
					$this->get_operation_contrepartie()->get_id() ;
					throw new exception_integrite_referentielle('operation', $this->get_id(),
						'operation', $this->get_operation_contrepartie()->get_id()) ;
				}
				catch (exception_not_exist $e) {
					// @codeCoverageIgnoreStart
					throw new BadMethodCallException("cette operation " . $this->get_id() .
						" a été définie comme une ventilation mais n'a pas d'opération en contrepartie. probleme dans l'effacement") ;
					// @codeCoverageIgnoreEnd
				}
			}
			if ($this->is_ventilation() == true) {
				$this->get_operation_mere() ;
				throw new exception_integrite_referentielle('operation', $this->get_id(),'operation', $this->get_operation_mere()->get_id()) ;
			}

		}
		$mere = $this->get_compte() ;
		$montant=$this->get_montant();
		$this->_dom->parentNode->removeChild($this->_dom) ;
		//recuperation du dernier numero
		if ($id >= (($gsb_operations->get_next()) - 1)) {
			//recuperation du dernier numero (dans tous les comptes)
			$nb_next = 0 ;
			foreach ($gsb_xml->iter_class('//Operation', 'operation') as $ope) {
				if ($ope->get_id() > $nb_next) {
					$nb_next = $ope->get_id() ;
				}
			}
			if ($id > $nb_next) {
				$gsb_xml->get_xml()->Generalites->Numero_derniere_operation = $nb_next ;
			}
		}
		//nb d'operations pour ce compte
		$nb = count($mere->iter_operations()) ;
        //gestion de id
		$req = $gsb_xml->xpath_uniq("//Compte/Details/No_de_compte[.='" . $mere->get_id() . "']/..") ;
		$req->Nb_operations = $nb ;
		//gestion des soldes
		//ancien compte
		$mere->set_solde_courant($mere->get_solde_courant()-$montant);
	}
}
