<?php /* coding: utf-8 */

require_once('header.php');
//----------------gestion des variables d'entree-----------------
$ope_destination_id = util::get_page_param('ope_id');

if ($ope_destination_id === "") {//nouvelle operation
	$tpl->assign('type_action','new');
	$tpl->assign('cpt_origine_id',CPT_VIREMENT);
	$cpt_destination_id = util::get_page_param('cpt_id');
	$tpl->assign('cpt_destination_id',$cpt_destination_id);
	$tpl->assign('cpt_nom_destination' ,$gsb_comptes->get_by_id($cpt_destination_id)->get_nom());
	$ope_destination_id=$gsb_operations->get_next();
	$tpl->assign('ope_date_ope',time());//on prend le timestamp de now
	$tpl->assign('ope_destination_id',$ope_destination_id);
	$tpl->assign('ope_origine_id',$ope_destination_id+1);
	$tpl->assign('ope_value','');
}
else {//edition
	$tpl->assign('type_action','edit');
	try {
	$operation=$gsb_operations->get_by_id($ope_destination_id);
	// operation jumelle
	$jumelle=$operation->get_operation_contrepartie();
	} catch (Exception_not_exist $except) {
		$tpl->critic("opération ".$except->id." inconnue",'./operations.php?cpt_id='.$cpt_destination_id);
	}
	if ($operation->get_montant()>0) {
		// on inverse les deux car normalement c'est le negatif en premier
		$transfert=$jumelle;
		$jumelle=$operation;
		$operation=$transfert;
		unset($transfert);
	}
	$tpl->assign('cpt_nom_origine' ,$operation->get_compte()->get_nom());
	$tpl->assign('ope_origine_id',$operation->get_id());
	$tpl->assign('cpt_origine_id',$operation->get_compte()->get_id());
	$tpl->assign('ope_destination_id',$jumelle->get_id());
	$tpl->assign('cpt_destination_id',$jumelle->get_compte()->get_id());

	//date (il y a une seule date)
	$tpl->assign('ope_date_ope',$operation->get_date());

	//montant
	$tpl->assign('ope_value',-1*$operation->get_montant());

}
//liste des comptes
foreach ($gsb_comptes->iter('all') as $compte) {
	if (!$compte->is_cloture()){
		$tpl->append('comptes', array("id" => $compte->get_id(),
									 "nom" => $compte->get_nom()
					)
		);
	}
}
$ua=$_SERVER['HTTP_USER_AGENT'];
if (!stripos($_SERVER['HTTP_USER_AGENT'], 'n95') && !stripos($_SERVER['HTTP_USER_AGENT'], 'iphone')) {
	$tpl->display('virement.smarty');
} else {
	$tpl->display('virement_n95.smarty');
}

?>
