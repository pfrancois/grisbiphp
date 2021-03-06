<?php /* coding: utf-8 */

require_once 'header.php';
$cpt_id = (int)util::get_page_param('cpt_id');

//si pas de compte defini, on prend le compte par defaut
if (empty($cpt_id) && $cpt_id!==0){
	$cpt_id = $gsb_comptes->get_compte_courant();
}
//on recupere la liste des operation du compte
$compte = $gsb_comptes->get_by_id($cpt_id);
$tpl->assign('compte_nom', $compte->get_nom());
$tpl->assign('cpt_id', $cpt_id);
//TODO gestion des operations ventilee
$nbfiltre_date = 0;
$nb_rapp = 0;
//ne fait apparaitre que les operations non rapprochees.
//pour les operations ventilees, cela fait apparaitres les operation filles mais pas la mere
foreach ($compte->iter_operations() as $operation) {
	$pointe = $operation->get_statut_pointage();
	if (($operation->is_ventilee() === false) && ($pointe == rapp::RIEN || $pointe == rapp::POINTEE)) {
		$date = $operation->get_date();
		$date_limite = time() - 86400 * NB_JOURS_AFF; //on prend NB_AFF jours avant aujourd'hui
		if (($date > $date_limite)) {
			$key = $date . '_' . $operation->get_id();
			$operations_apres_filtre[$key] = $operation;
		} else {
			$nbfiltre_date++;
		}
	} else {
			$nb_rapp++;
	}
}

$tpl->assign('nbfiltre', $nbfiltre_date);
$tpl->assign('nbrapp',$nb_rapp);
$i = 0;
//si au moins une operation on remplit le tableau
if (isset($operations_apres_filtre)) {
	krsort($operations_apres_filtre, SORT_STRING);
	foreach ($operations_apres_filtre as $key => $operation) {
		$ope_item = array();
		$ope_item['date'] = $operation->get_date();
		$categorie=$operation->get_categorie();
		if (!is_null($categorie)){
			$ope_item['cat_name']= $categorie->get_nom();
		} else {
			$ope_item['cat_name']='N/D';
		}
		if (!is_null($operation->get_tiers())){
			$ope_item['tiers_name']= $operation->get_tiers()->get_nom();
		}else {
			$ope_item['tiers_name']= '';
		}
		if ($operation->is_virement()) {
			if ($operation->get_montant() < 0) {
				$ope_item['tiers_name'] = $operation->get_compte()->get_nom()." => ".$operation->get_cpt_contrepartie()->get_nom();
			} else {
				$ope_item['tiers_name'] = $operation->get_cpt_contrepartie()->get_nom()." => ".$operation->get_compte()->get_nom();
			}
			$ope_item['cat_name'] = "virement";
		}

		$value = $operation->get_montant();
		$ope_item['virement'] =$operation->is_virement();
		if ($value < 0) {
			$ope_item['debit'] = $value/100;
			$ope_item['credit'] = "";
			$ope_item['class2'] = "debitElement";
		} else {
			$ope_item['credit'] = $value/100;
			$ope_item['debit'] = "";
			$ope_item['class2'] = "creditElement";
		}
		$ope_item['id'] = $operation->get_id();
		$tpl->append('opes', $ope_item);
		$i++;
		unset($ope);
	}
}
$tpl->assign('solde_compte', ($compte->get_solde_courant()/100));
$tpl->assign('devise',$compte->get_devise()->get_isocode());
//gestion des écheances
$nb_ope_echus=0;
foreach($gsb_echeances->iter() as $ech){
    if ($ech->verif_echus() && ($ech->get_compte()->get_id()===$cpt_id)){
        $nb_ope_echus++;
    }
}
$tpl->assign('nb_ope_echus',$nb_ope_echus);
//afichage final
if ($compte->is_cloture()){
	$tpl->assign("cloture",true);
	}
$tpl->display('operations.smarty');
