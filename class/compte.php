<?php

//$Id: compte.php 45 2010-09-20 03:12:17Z pfrancois $
/**
 * class compte
 * @author francois
 *
 */
class compte extends item {
    const T_BANCAIRE = 0;
    const T_ESPECE = 1;
    const T_PASSIF = 2;
    const T_ACTIF = 3;
    /**
     * retourne l'id du compte
     *
     * @return integer
     */
    public function get_id() {
        return (int) $this->_item_xml->Details->No_de_compte;
    }

    /**
     * compte::get_nom()
     *
     * @return string
     */
    public function get_nom() {
        return (string) $this->_item_xml->Details->Nom;
    }

    /**
     * renvoie le nombre d'operation contenu dans ce compte.
     *
     * attention, il y a possibilité d'erreur car il n'y a pas comptage.
     *
     * @return integer
     */
    public function get_nb_ope() {
        return (int) $this->_item_xml->Details->Nb_operations;
    }

    /**
     * renvoie le solde courant en centime de ce compte
     *
     * @return integer
     */
    public function get_solde_courant() {
        return util::fr2cent($this->_item_xml->Details->Solde_courant);
    }

    /**
     * verifie si ce compte est cloture.
     *
     * @return bool
     */
    public function is_cloture() {
        $r = (int) $this->_item_xml->Details->Compte_cloture;
        if ($r === 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return integer
     */
    public function get_type_compte() {
        $a = (int) $this->_item_xml->Details->Type_de_compte;
        return $a;
    }

    /**
     * compte::get_next_moyen()
     *
     * @return integer
     */
    public function get_next_moyen() {
        return (int) $this->_item_xml->Details->Nombre_de_types + 1;
    }

    /**
     * @return moyen
     */
    public function get_moyen_debit_defaut() {
        $id = (int) $this->_item_xml->Details->Type_defaut_debit;
        return $this->get_moyen_by_id($id);
    }

    /**
     * @return moyen
     */
    public function get_moyen_credit_defaut() {
        $id = (int) $this->_item_xml->Details->Type_defaut_credit;
        return $this->get_moyen_by_id($id);
    }

    /**
     * compte::get_moyen_by_id()
     *
     * @param integer $id
     * @return moyen
     */
    public function get_moyen_by_id($id) {
        global $gsb_xml;
        $id = (int) $id;
        $xpath = "Detail_de_Types/Type[@No='$id']";
        try {
            $r = $gsb_xml->xpath_uniq($xpath, $this->_item_xml);
            return new moyen($r);
        } catch (Exception_no_reponse $except) {
            if ($id != 0) {
                throw new Exception_no_reponse($except->message);
            } else {
                return null;
            }
        }
    }

    /**
     * compte::get_moyen_by_name()
     *
     * @param string $name
     * @return moyen
     */
    public function get_moyen_by_name($name) {
        global $gsb_xml;
        $name = (string) $name;
        $xpath = "Detail_de_Types/Type[@Nom='$name']";
        $req = $gsb_xml->xpath_uniq($xpath, $this->_item_xml);
        return new moyen($req);
    }

    public function get_devise() {
        global $gsb_xml;
        global $gsb_devises;
        return $gsb_devises->get_by_id((int) $this->_item_xml->Details->Devise);
    }

    //---------------------------SETTERS
    /**
     * modifie le nom du compte
     *
     * @param string $nom
     * @throws exception_index si on met un nom qui existe deja
     * @throws exception_parametre_invalide si on met un nom vide
     * @return void
     */
    public function set_nom($nom) {
        global $gsb_comptes;
        $nom = (string) $nom;
        try {
            $gsb_comptes->get_id_by_name($nom);
            // normalement il doit y avoir une exception car le nom ne doit pas exister
            throw new exception_index('compte', $nom);
        } catch (exception_not_exist $except) {
            if ($nom <> '' && !empty($nom)) {
                $this->_item_xml->Details->Nom = $nom;
            } else {
                throw new exception_parametre_invalide("compte $nom");
            }
        }
    }

    public function set_cloture() {
        $this->_item_xml->Details->Compte_cloture = 1;
    }

    /**
     * @return void
     */
    public function set_non_cloture() {
        $this->_item_xml->Details->Compte_cloture = 0;
    }

    /**
     * @return void
     * @throws exception_parametre_invalide si pas nombre entier
     * @param int $solde en centimes
     */
    public function set_solde_courant($solde) {
        if (is_numeric($solde)) {
            $this->_item_xml->Details->Solde_courant = util::cent2fr($solde);
        } else {
            throw new exception_parametre_invalide('$solde doit etre un nombre entier pour modifier le solde car on raisonne en centimes');
        }
    }

//AJOUTEURS
    /**
     * @param int $id
     * @return operation
     * @throws exception_index
     */
    public function new_operation($id = null) {
        global $gsb_xml;
        global $gsb_operations;
        try {
        	if (empty($id)){
				throw new exception_not_exist('operation',0);
            }else {
            	$gsb_operations->get_by_id($id);
            	throw new exception_index('operation', $id);
            }
        } catch (exception_not_exist $except) {
            $n = $this->_item_xml->Detail_des_operations->addChild("Operation");
            $op = new operation($n, item::NOUVELLE, $id);
            $this->_item_xml->Details->Nb_operations = count($this->_item_xml->Detail_des_operations->Operation);
        }
        return $op;
    }


    //ITERATEURS
    public function iter_operations() {
        global $gsb_xml;
        return $gsb_xml->iter_class("//Compte/Details/No_de_compte[.='" . $this->get_id
                () . "']/../../Detail_des_operations/Operation", 'operation');
    }

    public function iter_moyens() {
        global $gsb_xml;
        return $gsb_xml->iter_class("//Compte/Details/No_de_compte[.='" . $this->get_id
                () . "']/../../Detail_de_Types/Type", 'moyen');
    }

}
