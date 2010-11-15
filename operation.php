<?php /* coding: utf-8 */

require_once 'header.php';
//----------------gestion des variables d'entree-----------------
$cpt_id=(int)util::get_page_param('cpt_id');
$ope_id=(int)util::get_page_param('ope_id');
if ($cpt_id === "") {
	$cpt_id=$gsb_comptes->get_compte_courant();
}
$compte=$gsb_comptes->get_by_id($cpt_id);

if ($ope_id === 0) {//nouvelle operation
	if ($compte->is_cloture()){
		$tpl->critic('attention, impossible de creer une operation dans un compte clotur&eacute;','./operations.php?cpt_id='.$cpt_id);
	} else {
		$tpl->assign('type_action', 'new');
		$ope_id=$gsb_operations->get_next();
		$moyen=$compte->get_moyen_debit_defaut();
		if (!is_null($moyen)){
			$num_chq=$moyen->get_next_num();
		}else {
			$num_chq='';
		}
		$tpl->assign('ope',array("date"=>time(),
										"tier"=>0,
										"note"=>"",
										"cat"=>"0:0",
										"moyen"=>$moyen->get_id(),
										"num_chq"=>$num_chq,
										"montant"=>"",
										"id"=>$ope_id
										));
	}
} else {
	$tpl->assign('type_action', 'edit');
	$tpl->assign('compte_cloture',$compte->is_cloture());
	$operation=$gsb_operations->get_by_id($ope_id);
	// montant
	$value=$operation->get_montant();
	if ($operation->get_categorie()->get_type()==categorie::DEBIT) {
		$value=$value*-1;
	}
	if ($value>0) {
		$tpl->assign('class', "opCredit");
	} else {
		$tpl->assign('class', "opDebit");
	}
	//moyens de paiments
	$moyen=$operation->get_moyen();
	if (empty($moyen)){
		$moyen_id='';
	}else {
		$moyen_id= $moyen->get_id();
	}
	//cat et scat
	$categorie=$operation->get_categorie();
	if ($categorie->has_sub()){
		$cat=$operation->get_categorie()->get_id().':'.$operation->get_scat()->get_id();
	}else {
		$cat=$operation->get_categorie()->get_id().':0';
	}
		$tpl->assign('ope',array("date"=>$operation->get_date(),
										"tier"=>$operation->get_tiers()->get_id(),
										"note"=>$operation->get_notes(),
										"cat"=>$cat,
										"moyen"=>$moyen_id,
										"num_chq"=>$operation->get_num_chq(),
										"montant"=>$value,
										"id"=>$ope_id
										));

}

//gestion du compte
$tpl->assign('compte_id', $cpt_id);
$tpl->assign('compte_nom',$compte->get_nom());
$tpl->assign('compte_devise',$compte->get_devise()->get_isocode());

// creation de la liste des tiers

$tab_nom_tiers=array();
foreach ($gsb_tiers->iter() as $tier) {
	$tab_nom_tiers[$tier->get_id()]=$tier->get_nom();
}

asort($tab_nom_tiers);
$ltiers=array();
foreach ($tab_nom_tiers as $no => $tier) {
	$ltiers[]=array("id" => $no, "nom" => $tier);
}
$tpl->assign('tiers', $ltiers);

// creation de la liste des categories et sous cat
$liste_cat=array();
foreach ($gsb_categories->iter() as $categorie) {
	if ($categorie->has_sub()) {
		foreach ($categorie->iter_sub() as $scat) {
			if ($categorie->get_type()==1) {
				$tpl->append('cats_debit', array(
													'id' => $categorie->get_id().":".$scat->get_id(),
													'nom' => $categorie->get_nom()." : ".$scat->get_nom(),
													'type' => $categorie->get_type(),
												)
								);
			} else {
				$tpl->append('cats_credit', array(
													'id' => $categorie->get_id().":".$scat->get_id(),
													'nom' => $categorie->get_nom()." : ".$scat->get_nom(),
													'type' => $categorie->get_type(),
												)
								);
			}
		}
	} else {
		if ($categorie->get_type()==1) {
			$tpl->append('cats_debit', array(
						'id' => $categorie->get_id().":0",
						'nom' => $categorie->get_nom(),
						'type' => $categorie->get_type(),
						));
		} else {
			$tpl->append('cats_credit', array(
						'id' => $categorie->get_id().":0",
						'nom' => $categorie->get_nom(),
						'type' => $categorie->get_type(),
						));
		}
	}//end if
}//end foreach
$tpl->assign('moyens',array());
foreach ($compte->iter_moyens() as $moyen) {
	$tpl->append(
		'moyens',
		array(
			'id' => $moyen->get_id(),
			'nom' => $moyen->get_nom(),
			'num_en_cours' => $moyen->get_next_num(),
			'entree' => $moyen->has_entree_compl(),
			'signe' => $moyen->get_signe(),
		)
	);
}
if ($compte->is_cloture()){
	$tpl->display('cloture.smarty');
}
elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'n95') || stripos($_SERVER['HTTP_USER_AGENT'], 'iPhone')) {
	$tpl->display('operation_n95.smarty');
} else {
	$tpl->display('operation.smarty');
}
