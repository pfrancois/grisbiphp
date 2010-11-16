<?php /* coding: utf-8 */
require_once('header.php');
function ral ($string){
	global $tpl;
	ral($string);
}
//----------------gestion des variables d'entree-----------------
$action = util::get_page_param( 'action' );
$phase=util::get_page_param( 'phase' );
//--------------------telechargment du fichier--------------------
if ($action=="get_file"){
	$user_agent = strtolower ($_SERVER["HTTP_USER_AGENT"]);
	$filename=$gsb_xml->get_xmlfile();
	if ((is_integer (strpos($user_agent, "msie" ))) && (is_integer (strpos($user_agent, "win" )))) {
	   header( "Content-Disposition: filename=".basename($filename).";" );
	} else {
	   header( "Content-Disposition: attachment; filename=".basename($filename).";" );
	}
	header("Content-type:application/octet-stream");
	header("Content-Type: application/force-download" );
	readfile("$filename" );
	exit();
}

//------------------effacer les tiers qui n'ont ni operation ni echeances
if ($action=="effacer_tiers_vides"){
	$tpl->assign('titre',"tiers supprim&eacute;s");
	if ($phase==""){
		$i=0;
		foreach ($gsb_tiers->iter() as $tier) {
			try {
				$nom=(string)$tier->get_nom();
				$tier->delete();
				ral($nom);
				$i++;
			} catch (exception_integrite_referentielle $e) {
				$i=$i;
			}
		}
		$tpl->assign("nom_classe_css","ligne");
		$tpl->assign("lien","action_options.php?action=effacer_tiers_vides&amp;phase=2");
		$tpl->display('resultats.smarty');
		exit();
	}
	if ($phase==2){
		$i=0;
		foreach ($gsb_tiers->iter() as $tier) {
			try {
				$tier->delete();
				$i++;
			} catch (exception_integrite_referentielle $e) {
				$i=$i;
			}
		}
		$gsb_xml->save();
		ral("$i tiers effac&eacutes");
		$tpl->assign("nom_classe_css","progress");
		$tpl->assign("lien","options.php");
		$tpl->display('resultats.smarty');
		exit();
	}
}
//-----------------------------change les dates des retraits

//---------------------------change les date des operations cartes differes
if ($action=="dates_ope_diff"){
	$xpath="//Operation";
	function callback($iter){
		//fait ce qui est demandé
		//attention, elle peut changer
		try {
//			var_dump($iter);
			if ($iter['N']!=''){
				preg_match('#CARTE X9438 (../..)#', $iter['N'],$n);
				if (substr($n, 2)>10){
					ral("operation ".$iter['No']);
					$n=$n."/2010";
				}
				ral($n);
				return 1;
			}
			 throw new Exception_no_reponse('pas de reponse');
		} catch (Exception_no_reponse $e) {
			return 0;
		}
	}
	///// normalement, pas besoin de changer en dessous
	if ($phase==""){
		$i=0;
		ral('liste des actions effectues sur '.$gsb_xml->get_xmlfile());
		//remplissez la requete xpath
		try {
			foreach ($gsb_xml->xpath_iter($xpath) as $iter) {
				$i=$i+callback($iter);
			}
			ral("$i actions effectu&eacute;");;
		} catch (Exception_no_reponse $e) {
			ral("aucune actions effectu&eacute; car &laquo;$xpath&raquo; non trouv&eacute;");
		}
		$tpl->assign("nom_classe_css","ligne");
		$tpl->assign("lien","action_outils.php?action=dates_ope_diff&amp;phase=2");
		$tpl->display('resultats.smarty');
		exit();
	}
	if ($phase==2){
		$i=0;
		ral('liste des actions effectues sur '.$gsb_xml->get_xmlfile());
		//remplissez la requete xpath
		foreach ($gsb_xml->xpath_iter($xpath) as $iter) {
			$i=$i+callback($iter);
		}
		$gsb_xml->save();
		ral("$i tiers effac&eacutes");
		$tpl->assign("nom_classe_css","progress");
		$tpl->assign("lien","options.php");
		$tpl->display('resultats.smarty');
		exit();
	}
}


//---------------specifique----------------------
if ($action=="specifique"){
	$xpath="//Operation";
	function callback($iter){
		//fait ce qui est demandé
		//attention, elle peut changer
		try {
//			var_dump($iter);
			if ($iter['N']!=''){
				preg_match('#CARTE X9438 (../..)#', $iter['N'],$n);
				if (substr($n, 2)>10){
					ral("operation ".$iter['No']);
					$n=$n."/2010";
				}
				ral($n);
				return 1;
			}
			 throw new Exception_no_reponse('pas de reponse');
		} catch (Exception_no_reponse $e) {
			return 0;
		}
	}
	///// normalement, pas besoin de changer en dessous
	if ($phase==""){
		$i=0;
		ral('liste des actions effectues sur '.$gsb_xml->get_xmlfile());
		//remplissez la requete xpath
		try {
			foreach ($gsb_xml->xpath_iter($xpath) as $iter) {
				$i=$i+callback($iter);
			}
			ral("$i actions effectu&eacute;");;
		} catch (Exception_no_reponse $e) {
			ral("aucune actions effectu&eacute; car &laquo;$xpath&raquo; non trouv&eacute;");
		}
		$tpl->assign("nom_classe_css","ligne");
		$tpl->assign("lien","action_outils.php?action=specifique&amp;phase=2");
		$tpl->display('resultats.smarty');
		exit();
	}
	if ($phase==2){
		$i=0;
		ral('liste des actions effectues sur '.$gsb_xml->get_xmlfile());
		//remplissez la requete xpath
		foreach ($gsb_xml->xpath_iter($xpath) as $iter) {
			$i=$i+callback($iter);
		}
		$gsb_xml->save();
		ral("$i tiers effac&eacutes");
		$tpl->assign("nom_classe_css","progress");
		$tpl->assign("lien","options.php");
		$tpl->display('resultats.smarty');
		exit();
	}
}
//recupere les action inexistantes, bien mettre un exit dans les if
if (DEBUG){
	$tpl->critic("action demand&eacute;e inexistante: $action");
}else {
	util::redirection_header("comptes.php");
}
