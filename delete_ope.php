<?php /* coding: utf-8 */

require_once 'header.php';

$ope_id=(int) util::get_page_param('ope_id');
$cpt_id=(int) util::get_page_param('cpt_id');
if (util::get_page_param('action')=='delete' && date('dmY',time())==util::get_page_param('date')){
	$operation=$gsb_operations->get_by_id($ope_id);
	if (!$operation->is_virement()){
		$tpl->assign('titre',"Opération effacée");
		$t=" opération $ope_id, d'un montant de ".$operation->get_montant()." ".$operation->get_compte()->get_devise()->get_isocode().", à ".$tier->get_nom()." le ".date('d/m/Y',$ope_date)." ok";
		try {
			$operation->delete();
		} catch (Exception $except) {
			if (DEBUG){
				$tpl->critic(get_class($except) . " '{$except->message}' in {$except->file}({$except->line})\n {$except->getTraceAsString()}" , "operations.php?cpt_id=$cpt_id");
			}else {
				$tpl->critic("{$except->message}" , "operations.php?cpt_id=$cpt_id");
			}
		}//end try
		$gsb_xml->save();
		$tpl->ral("suppression de l'opération:".$t);
	}else{
		$tpl->assign('titre',"Opérations effacées");
		try{
			$operation=$gsb_operations->get_by_id($ope_id);
			$jumelle=$operation->get_operation_contrepartie();
			if ($operation->is_ventilation() || $jumelle->is_ventilation() || $operation->is_ventilee() || $jumelle->is_ventilee() || $operation->get_statut_pointage(rapp::RAPPROCHEE) || $jumelle->get_statut_pointage(rapp::RAPPROCHEE)){
				$tpl->critic("impossible d'effacer ".$operation."et ".$jumelle, "operations.php?cpt_id=$cpt_id");
			}
			$tpl->ral("suppression de l'opération:"." opération $ope_id, d'un montant de ".$operation->get_montant()." ".$operation->get_compte()->get_devise()->get_isocode().", à ".$tier->get_nom()." le ".date('d/m/Y',$jumelle->get_date())." ok");
			$tpl->ral("suppression de l'opération:"." opération ".$jumelle->get_id().", d'un montant de ".$jumelle->get_montant()." ".$jumelle->get_compte()->get_devise()->get_isocode().", à ".$tier->get_nom()." le ".date('d/m/Y',$jumelle->get_date())." ok");
			$operation->delete(false);
			$jumelle->delete(false);
			$gsb_xml->save();
			$tpl->ral("ok pour les opérations");
		} catch (Exception $except) {
			if (DEBUG){
				$tpl->critic(get_class($except) . " '{$except->message}' in {$except->file}({$except->line})\n {$except->getTraceAsString()}" , "operations.php?cpt_id=$cpt_id");
			}else {
				$tpl->critic("{$except->message}" , "operations.php?cpt_id=$cpt_id");
			}
		}//end try
	}
	if (DEBUG){
		$tpl->assign('lien','operations.php?cpt_id='.$cpt_id);
		$tpl->assign("nom_classe_css","progress");
		$tpl->display('resultats.smarty');
	} else {
		util::redirection_header('operations.php?cpt_id='.$cpt_id);
	}
} else {
	$tpl->critic("impossible d'effacer car ventilation ou rapprochement","operations.php?cpt_id=$cpt_id");
}
