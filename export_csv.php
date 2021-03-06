<?php /* coding: utf-8 */

require_once ('header.php');
$csv=array();
//on parcourt les operations
foreach ($gsb_operations->iter() as $ope) {
	//gestion des categories preablable
	if ($ope->is_virement()){
		$cat="virement";
		$scat="";
	} else {
		if ($ope->is_ventilee()) {
			continue;// on ne prend pas les operation meres des ventilees
		}else {
			if (is_null($ope->get_categorie())){
				$cat="";
				$scat="";
			}else{
				$cat=$ope->get_categorie()->get_nom();
				if (!is_null($ope->get_scat())){
					$scat=$ope->get_scat()->get_nom();
				}else {
					$scat="";
				}
			}
		}
	}
	$csv[$ope->get_id()]=array(
		$ope->get_id(),
		utf8_decode ($ope->get_compte()->get_nom()),
		date("d/m/Y",$ope->get_date()),
		date("Y",$ope->get_date()),
		date("m",$ope->get_date()),
		util::cent2fr($ope->get_montant()),
		($ope->get_statut_pointage() == rapp::RAPPROCHEE)?1:0,
		($ope->get_statut_pointage() == rapp::POINTEE)?1:0,
		(is_null($ope->get_moyen()))?"":utf8_decode ($ope->get_moyen()->get_nom()),
		utf8_decode ($cat),
		utf8_decode ($scat),
		utf8_decode ($ope->get_tiers()->get_nom()),
		utf8_decode ($ope->get_notes()),
		(is_null($ope->get_ib()))?"":utf8_decode ($ope->get_ib()->get_nom()),
		$ope->get_num_chq(),
		($ope->is_virement())?$ope->get_operation_contrepartie()->get_id():0,
		($ope->is_ventilation())?"vrai":"faux"
	);
}
//creation du csv
$file='comptes.csv';
$fp = fopen($file, 'w');
fputcsv($fp, array("id","nom_compte","date","annee","mois","montant","R","P","moyen","categorie","sous-categorie","tiers","notes","projet","cheque","virement","ventilation"),';','"');
foreach ($csv as $fields) {
	fputcsv($fp, $fields,';','"');
}
fclose($fp);
//affichage
header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="'.$file.'"');
header('Pragma: no-cache');
header('Expires: 0');
header('Content-Length: ' . filesize($file));
readfile($file);
//nettoyage
unlink($file);
exit();
