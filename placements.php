<?php /* coding: utf-8 */

require_once 'header.php';
$cpt_id = (int)util::get_page_param('cpt_id');
//si pas de compte defini, on revient vers compte.php
if (empty($cpt_id) && $cpt_id!==0){
	util::redirection_header("comptes.php");
	exit();
}
//on recupere la liste des operation du compte
$compte = $gsb_comptes->get_by_id($cpt_id);
$tpl->assign('compte_nom', $compte->get_nom());
$tpl->assign('cpt_id', $cpt_id);
if ($compte->get_type_compte() != compte::T_ACTIF){
	util::redirection_header("operations.php?cpt_id=$cpt_id");
	exit();
}
$titres=array();
foreach ($compte->iter_operations() as $operation) {
	if (stripos($operation->get_tiers()->get_nom(),"titre_")===FALSE){
		// ce n'est pas un titre
		continue;
	}
	if ($operation->is_ventilee()){
		continue;//c'est une operation qui est mere d'autres
	}
	//verifie que le compte est bien un compte d'actif (ou de placement)
	if ($operation->get_compte()->get_type_compte()!=compte::T_ACTIF){
		continue;
	}
	$nom=substr($operation->get_tiers()->get_nom(),7);
	$id=$operation->get_tiers()->get_id();
	//var_dump($id);
	if (!isset($titres[$id])){
		$titres[$id]=array();
		$titres[$id]['id']=$id;
		$titres[$id]['nom']=$nom;
		$titres[$id]['pmv']=0;
		$titres[$id]['mise']=0;
	}
	$categorie=$operation->get_categorie();
	if (!is_null($categorie)){
		if ($categorie->get_id() == PMVALUES){//on ajoute aux plus ou moins values
			$titres[$id]['pmv']=$titres[$id]['pmv']+$operation->get_montant()/100;
		}elseif ($categorie->get_id() == OSTITRES){//on gere les achats et ventes
			$titres[$id]['mise']=$titres[$id]['mise']+$operation->get_montant()/100;
		}else {
			continue;//ce n'est pas une operation sur titre
		}
	} else {
		continue;
	}
}
$total_titres=0;
foreach($titres as $titre){
	$total_titres=$total_titres+$titre['pmv']+$titre['mise'];
}
$tpl->assign('titres',$titres);
$tpl->assign('espece',($compte->get_solde_courant()/100)-$total_titres);
$tpl->assign('solde_compte', ($compte->get_solde_courant()/100));
$tpl->assign('devise',$compte->get_devise()->get_isocode());
//afichage final
	$tpl->display('cpt_placement.smarty');