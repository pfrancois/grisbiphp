<?php
//$Id: action.php 47 2010-09-21 23:19:02Z pfrancois $
require_once('header.php');
//----------------gestion des variables d'entree-----------------
$action = util::get_page_param( 'action' );
$cpt_id =( integer ) util::get_page_param('cpt_id');
$ope_id =( integer ) util::get_page_param('ope_id');
//verification de la date
try {
	$ope_date=util::datefr2time(util::get_page_param( 'ope_date_ope' ));
} catch (InvalidArgumentException $except) {
	$tpl->critic("la date n'est pas valide", "operations.php?cpt_id=$cpt_id");
}

$tiers_id=(integer) util::get_page_param('tiers');
$tiers_nom=util::get_page_param( 'nom_nx_tiers' );
$cat__et_scat_id = explode( ':', util::get_page_param( 'cat' ) );
$cat_id=trim($cat__et_scat_id[0]);
$scat_id=trim($cat__et_scat_id[1]);
$ope_montant=util::get_page_param('ope_montant');
$ope_num_chq=util::get_page_param( 'numero_chq' );
$ope_notes=util::get_page_param( 'rem' );
$ope_moyen=(integer)util::get_page_param('moyen');
//---------------------------gestion du processus---------------
//creation du nouveau tiers au besoin
if (  $tiers_id== -1 ) {
	if ( $tiers_nom != "" ) {
		try {
			$tier=$gsb_tiers->new_tier();
			$tier->set_nom($tiers_nom);
			$gsb_xml->save();
			$tpl->append("resultat","nouveau tiers, ok");
		} catch (exception_index $except) {
			$tpl->critic("erreur dans la creation du tiers" , "operations.php?cpt_id=$cpt_id");
		} catch (Exception $except) {
			$tpl->critic("retour vers le compte car" , "operations.php?cpt_id=$cpt_id");
		}
	} else {
		$tpl->critic("il faut mettre un nom dans le nouveau tiers" , "operations.php?cpt_id=$cpt_id");
	}
}else{
	$tier=$gsb_tiers->get_by_id($tiers_id);
}
$categorie = $gsb_categories->get_by_id($cat_id);

if ($action=="edit"){
	$operation = $gsb_operations->get_by_id($ope_id);
	if (($operation->is_ventilee()) || ($operation->is_ventilation()) || ($operation->get_statut_pointage()==rapp::RAPPROCHEE)) {
		//pas d'edition d'une ope ventilee, si va=0, cela veut dire pas d'ope mere, on ne modifie que les ope non rappro
		$tpl->critic("impossible d'&eacute;diter une operation point&eacute;e ou rapproch&eacute;e".PHP_EOL."impossible d'editer une operation ventil&eacute;e" ,
		 "operations.php?cpt_id=$cpt_id");
	}
	$tpl->assign('titre',"op&eacute;ration modifi&eacute;e");
} elseif ($action=="new"){
	try{
		$compte=$gsb_comptes->get_by_id($cpt_id);
		$operation=$compte->new_operation($ope_id);
	} catch (Exception $except) {
		if (DEBUG){
		$tpl->critic(get_class($except) . " '{$except->message}' in {$except->file}({$except->line})\n {$except->getTraceAsString()}" , "operations.php?cpt_id=$cpt_id");
		}else {
			$tpl->critic( "{$except->message}" , "operations.php?cpt_id=$cpt_id");
		}
	}
	$tpl->assign('titre',"op&eacute;ration ajout&eacute;e");
}else {
	$tpl->critic('il faut choisir soit edit ou new' , "operations.php?cpt_id=$cpt_id");
}
try{
	$operation->set_date($ope_date);
	$operation->set_categorie($categorie);
	if ($categorie->has_sub()){
		$operation->set_scat($categorie->get_sub_by_id($scat_id));
	}
	//la categorie est une depense donc par construction, c'est negatif
	if ( $categorie->get_type() == 1 ) {
		//c'est une depense a ce moment la
		$ope_montant = $ope_montant * -1;
	}
	$operation->set_montant($ope_montant);
	//todo:reflechir si on met une verification pour le numero

	if(!empty($ope_moyen)){
		$operation->set_moyen($operation->get_compte()->get_moyen_by_id($ope_moyen));
	}
	$operation->set_num_chq($ope_num_chq);
	$operation->set_notes($ope_notes);
	$operation->set_tiers($tier);
	$gsb_xml->save();
	$tpl->append("resultats",$action." operation $ope_id, ok");
} catch (Exception $except) {
	if (DEBUG){
	$tpl->critic( get_class($except) . " '{$except->message}' in {$except->file}({$except->line})\n {$except->getTraceAsString()}" , "operations.php?cpt_id=$cpt_id");
	}else {
		$tpl->critic("{$except->message}" , "operations.php?cpt_id=$cpt_id");
	}
}
if (DEBUG){
	$tpl->assign("nom_classe_css","progress");
	$tpl->assign('lien',"operations.php?cpt_id=$cpt_id");
	$tpl->display('resultats.smarty');
} else {
	util::redirection_header("operations.php?cpt_id=$cpt_id");
}

