<?php /* coding: utf-8 */

require_once 'header.php' ;
$cpt_id = (int)util::get_page_param('cpt_id') ;
$cpt_id =6;
//si pas de compte defini, on revient vers compte.php
if (empty($cpt_id) &&  $cpt_id!==0){
	util::redirection_header("compte.php");
	exit();
}
//on recupere la liste des operation du compte
$compte = $gsb_comptes->get_by_id($cpt_id) ;
$tpl->assign('compte_nom', $compte->get_nom()) ;
$tpl->assign('cpt_id', $cpt_id) ;
//TODO gestion des operations ventilee
$nbtitres = 0 ;
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
	if (!isset($titre[$id])){
		$titre[$id]=array();
		$titre[$id]['nom']=$nom;
		$titre[$id]['montant']=0;
	}
	var_dump($id);
	$categorie=$operation->get_categorie();
	if (!is_null($categorie)){
		if ($categorie->get_id() == PMVALUES){//on ajoute aux plus ou moins values
				$titres[$id]['montant']=$titres[$id]['montant']+$operation->get_montant()/100;
		}elseif ($categorie->get_id() == OSTITRES){//on gere les achats et ventes
				$titres[$id]['montant']=$titres[$id]['montant']+$operation->get_montant()/100;
		}else {
			continue;//ce n'est pas une operation sur titre
		}
	} else {
		continue;
	}
}
$tpl->assign('titres',$titres);
$tpl->assign('solde_compte', ($compte->get_solde_courant()/100)) ;
$tpl->assign('devise',$compte->get_devise()->get_isocode());
//var_dump($tpl->_tpl_vars);
//afichage final
if ($compte->is_cloture()){
	}else {
	//$tpl->display('cpt_placement.smarty') ;
}
