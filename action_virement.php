<?php /* coding: utf-8 */

require_once 'header.php';
//----------------gestion des variables d'entree-----------------
$action = (string) util::get_page_param('action');
$ope_id_origine = (int) util::get_page_param('ope_origine_id');
$ope_id_destination = (int) util::get_page_param('ope_destination_id');
$ope_value = ((int) util::get_page_param('ope_value'));
$ope_date = util::datefr2time(util::get_page_param('ope_date_ope'));
$compte_origine=$gsb_comptes->get_by_id(util::get_page_param('cpt_origine'));
$compte_destination=$gsb_comptes->get_by_id(util::get_page_param('cpt_destination'));


if ($action=="edit") {
	try{
		$ope_orig=$gsb_operations->get_by_id($ope_id_origine);
		$ope_dest=$gsb_operations->get_by_id($ope_id_destination);
		if (($ope_orig->is_ventilation()) && (!$ope_orig->is_ventilee()) && ($ope_orig->get_statut_pointage()==rapp::RAPPROCHEE)) {
			$tpl->critic('error',"le virement de ".$compte_origine." vers ".$compte_destination." n'est pas modifiable car l'opération est soit ventilée soit rapproché dans le compte".$compte_origine,'operations.php?cpt_id='.$compte_destination->get_id(),true);
		}
		if (($ope_dest->is_ventilation()) && (!$ope_dest->is_ventilee()) && ($ope_dest->get_statut_pointage()==rapp::RAPPROCHEE)) {
			$tpl->critic('error',"le virement de ".$compte_origine." vers ".$compte_destination." n'est pas modifiable car l'opération est soit ventilée soit rapproché dans le compte".$compte_destination,'operations.php?cpt_id='.$compte_destination->get_id(),true);
		}

		if ($ope_date!=$ope_orig->get_date()){
			$ope_orig->set_date($ope_date);
			$ope_dest->set_date($ope_date);
			$tpl->ral("resultats","date modifie pour les operation $ope_id_origine et $ope_id_destination, ok");
		}

		if ($ope_value!=$ope_dest->get_montant()){
			$ope_orig->set_montant($ope_value*-1);
			$ope_dest->set_montant($ope_date);
			$tpl->ral("resultats","montant modifie pour l'operation $ope_id_origine et $ope_id_destination, ok");
		}
		if ($ope_orig->get_compte()->get_id()!=$compte_origine->get_id()){
			//modification du compte
			$ope_orig->set_compte($compte_origine);
			$tpl->ral("resultats","compte modifie pour l'operation $ope_id_origine, ok");
		}
		if ($ope_dest->get_compte()->get_id()!=$compte_destination->get_id()){
			//modification du compte
			$ope_dest->set_compte($compte_destination);
			$tpl->ral("resultats","compte modifie pour l'operation $ope_id_destination, ok");
		}
		$gsb_xml->save();
	} catch (Exception $except) {
		if (DEBUG){
			$tpl->critic(get_class($except) . " '{$except->message}' in {$except->file}({$except->line})\n {$except->getTraceAsString()}" , "operations.php?cpt_id=$compte_destination");
		}else {
			$tpl->critic("{$except->message}" , "operations.php?cpt_id=$compte_destination");
		}
	}//end try
	$tpl->assign('titre',"opérations modifiées");
}elseif ($action=="new"){
	//verification de l'integrite referentielle
	try {
		ajout_virement_simple($compte_origine,$compte_destination,$ope_date,$ope_value,$ope_id_origine,$ope_id_destination);
		$gsb_xml->save();
		$tpl->ral("resultats","ok pour les operations $ope_id_origine et $ope_id_destination");
	} catch (Exception $except) {
		if (DEBUG){
			$tpl->critic(get_class($except) . " '{$except->message}' in {$except->file}({$except->line})\n {$except->getTraceAsString()}" , "operations.php?cpt_id=$compte_destination");
		}else {
			$tpl->critic("{$except->message}" , "operations.php?cpt_id=$compte_destination");
		}
	}//end try
	$tpl->assign('titre',"opérations ajoutées");
}else{
	$tpl->critic('type d\'operation invalide, il doit etre soit "edit" soit "new"','operations.php?cpt_id ='.$compte_destination->get_id());
}

if (DEBUG){
	$tpl->assign("nom_classe_css","progress");
	$tpl->assign('lien','operations.php?cpt_id='.$compte_destination->get_id());
	$tpl->display('resultats.smarty');
} else {
	util::redirection_header('operations.php?cpt_id='.$compte_destination->get_id());
}