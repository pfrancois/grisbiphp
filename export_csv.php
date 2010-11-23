<?php  /* coding: utf-8 */

require_once ('header.php') ;
$csv=array();
foreach ($gsb_operations->iter() as $ope) {
	//var_dump($ope);
	//gestion des categories preablable
	if ($ope->is_virement()){
		$cat="virement";
	} else {
		if ($ope->is_ventilee()) {
			$cat="ovm";
		}else {
			if (is_null($ope->get_categorie())){
				$cat="";
			}else{
				$cat=$ope->get_categorie()->get_nom();
				if (!is_null($ope->get_scat())){
					$cat=$cat."/".$ope->get_scat()->get_nom();
				}
			}
		}
	}
	$csv[$ope->get_id()]=array(
	$ope->get_id(),
	utf8_decode ($ope->get_compte()->get_nom()),
	date("d/m/Y",$ope->get_date()),
	util::cent2fr($ope->get_montant()),
	($ope->get_statut_pointage() == rapp::RAPPROCHEE)?1:0,
	($ope->get_statut_pointage() == rapp::POINTEE)?1:0,
	(is_null($ope->get_moyen()))?"":utf8_decode ($ope->get_moyen()->get_nom()),
	utf8_decode ($cat),
	utf8_decode ($ope->get_tiers()->get_nom()),
	utf8_decode ($ope->get_notes()),
	(is_null($ope->get_ib()))?"":utf8_decode ($ope->get_ib()->get_nom()),
	$ope->get_num_chq(),
	($ope->is_virement())?$ope->get_operation_contrepartie()->get_id():0,
	($ope->is_ventilation())?$ope->get_operation_mere():0	
	);
}
$file='comptes.csv';
$fp = fopen($file, 'w');
fputcsv($fp, array("id","nom_compte","date","montant","R","P","moyen","categorie","tiers","notes","projet","chequ","virement","ovm"),';','"');
foreach ($csv as $fields) {
	fputcsv($fp, $fields,';','"');
}
fclose($fp);
header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="'.$file.'"');
header('Pragma: no-cache');
header('Expires: 0');
header('Content-Length: ' . filesize($file));
readfile($file);
exit();


