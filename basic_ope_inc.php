<?php /* coding: utf-8 */
/**
 * ajoute une operation de base sans aucun truc specifique
 *
 * @param compte $compte
 * @param timestamp $date_ech
 * @param tier $tier
 * @param int $montant
 * @param int $id_ope
 * @return operation
 */
function ajout_ope_simple(compte $compte, $date_ech,tier $tier,$montant,$id_ope=null){
	//on ajoute l'operation au compte
	if (!is_null($id_ope)){
		$operation=$compte->new_operation($id_ope);
	}else{
		$operation=$compte->new_operation();
	}
	$operation->set_date($date_ech);
	$operation->set_montant($montant);
	if ($montant>0){
		$moyen_par_def=$compte->get_moyen_debit_defaut();

	}	else{
		$moyen_par_def=$compte->get_moyen_credit_defaut();
	}
	if(!is_null($moyen_par_def)){
		$operation->set_moyen($moyen_par_def);
	}
	$operation->set_tiers($tier);
	return $operation;
}
/**
 * ajoute une operation de base sans aucun truc specifique
 *
 * @param compte $c_orig
 * @param compte $c_dest
 * @param timestamp $date_ope
 * @param int $montant
 * @param int $id_orig
 * @param int $id_dest
 * @return operation
 */
function ajout_virement_simple(compte $c_orig, compte $c_dest,$date_ope,$montant,$id_orig=null,$id_dest=null){
	global $gsb_tiers;
	if ($montant>0){
		$montant=$montant*-1;
	}
	$tiers=$gsb_tiers->get_by_id(TIERS_VIREMENT);
	$ope_orig=ajout_ope_simple($c_orig,$date_ope,$tiers,$montant,$id_orig);
	$ope_dest=ajout_ope_simple($c_dest,$date_ope,$tiers,$montant*-1,$id_dest);
	$ope_orig->set_operation_contrepartie($ope_dest);
	$ope_dest->set_operation_contrepartie($ope_orig);
	return $ope_orig;
}
