<?php  /* coding: utf-8 */

require_once 'header.php';

	$ope_id=(int) util::get_page_param('ope_id');
	$cpt_id=(int) util::get_page_param('cpt_id');
if (util::get_page_param('action')=='delete' && date('dmY',time())==util::get_page_param('date')){
	$operation=$gsb_operations->get_by_id($ope_id);
	if (!$operation->is_virement()){
			try {
				$operation->delete(true);
			} catch (Exception $except) {
				if (DEBUG){
				$tpl->critic(get_class($except) . " '{$except->message}' in {$except->file}({$except->line})\n {$except->getTraceAsString()}" , "operations.php?cpt_id=$cpt_id");
				}else {
					$tpl->critic("{$except->message}" , "operations.php?cpt_id=$cpt_id");
				}
			}//end try
				$gsb_xml->save();
				$tpl->append("resultat","suppression de l'op&eacute;ration: Ok");

	}else{
		try{
			$operation=$gsb_operations->get_by_id($ope_id);
			$jumelle=$operation->get_operation_contrepartie();
			if ($operation->is_ventilation() || $jumelle->is_ventilation() || $operation->is_ventilee() || $jumelle->is_ventilee() || $operation->get_statut_pointage(rapp::RAPPROCHEE) || $jumelle->get_statut_pointage(rapp::RAPPROCHEE)){
				$tpl->critic("impossible d'effacer ".$operation."et ".$jumelle, "operations.php?cpt_id=$cpt_id");
			}
			$operation->delete(false);
			$jumelle->delete(false);
			$gsb_xml->save();
			$tpl->append("resultats","ok pour les operations");
			} catch (Exception $except) {
				if (DEBUG){
				$tpl->critic(get_class($except) . " '{$except->message}' in {$except->file}({$except->line})\n {$except->getTraceAsString()}" , "operations.php?cpt_id=$cpt_id");
				}else {
					$tpl->critic("{$except->message}" , "operations.php?cpt_id=$cpt_id");
				}
			}//end try
	}
	if (DEBUG){
		$tpl->assign('titre',"op&eacute;rations &eacute;fface&eacute;es");
		$tpl->assign('lien','operations.php?cpt_id='.$cpt_id);
		$tpl->assign("nom_classe_css","progress");
		$tpl->display('resultats.smarty');
	} else {
		util::redirection_header('operations.php?cpt_id='.$cpt_id);
	}
} else {
	$tpl->critic("impossible d'effacer car ventilation ou rapprochement","operations.php?cpt_id=$cpt_id");
}
