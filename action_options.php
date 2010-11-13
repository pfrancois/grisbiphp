<?php
require_once('header.php');
function ral($st){
	echo ((string)$st)."<br />";
}
//----------------gestion des variables d'entree-----------------
$action = util::get_page_param( 'action' );
$phase=util::get_page_param( 'phase' );
if ($action=="get_file"){
	header("Content-disposition:filename=".$gsb_xml->get_xmlfile());
	header("Content-type:application/octetstream"); 
	exit();	
}
if ($action=="effacer_tiers_vides"){
			echo '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="CACHE-CONTROL" content="NO-CACHE" />
<meta http-equiv="PRAGMA" content="NO-CACHE" />
<meta http-equiv="EXPIRES" content="0" />
<title>Comptes</title>
</head>
<body>
		';
	if ($phase==""){
		$i=0;
		echo 'liste des tiers effaces:<br />';
		foreach ($gsb_tiers->iter() as $tier) {
			try {
				$nom=(string)$tier->get_nom();
				$tier->delete();
				echo $nom."<br />";
				$i++; 
			} catch (exception_integrite_referentielle $e) {
				$i=$i;
			}
		}
		 
		echo('<a href="action_outils.php?action=effacer_tiers_vides&amp;phase=2">cliquez ici pour les supprimer</a>');
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
		echo "$i tiers en moins<br />";
		echo('<a href="comptes.php">revenir au d&eacute;but</a>');
		exit();
	}
}
if ($action=="specifique"){
				echo '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="CACHE-CONTROL" content="NO-CACHE" />
<meta http-equiv="PRAGMA" content="NO-CACHE" />
<meta http-equiv="EXPIRES" content="0" />
<title>Comptes-outil_custom</title>
</head>
<body>
		';
	$xpath="//Operation";
	function callback($iter){
		//fait ce qui est demandé
		try {
//			var_dump($iter);			
			if ($iter['N']!=''){
				preg_match('#CARTE X9438 (../..)#', $iter['N'],$n);
				var_dump($n);
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
	if ($phase==""){
		$i=0;
		ral('liste des actions effectues sur '.$gsb_xml->get_xmlfile());
		//remplissez la requete xpath
		try {
			foreach ($gsb_xml->xpath_iter($xpath) as $iter) {
				$i=$i+callback($iter);
			}
			echo "$i actions effectu&eacute;"."<br />";
		} catch (Exception_no_reponse $e) {
			echo "aucune actions effectu&eacute; car &laquo;$xpath&raquo; non trouv&eacute;<br />";
		}

		
		echo('<a href="action_outils.php?action=specifique&amp;phase=2">cliquez ici pour confirmer</a>');
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
		echo "$i actions effectu&eacute;"."<br />";
		echo('<a href="comptes.php">revenir au d&eacute;but</a>');
		exit();
	}
}
//recupere les action inexistantes, bien mettre un exit dans les if
if (DEBUG){
	echo ("action demand&eacute;e inexistante: $action");
}else {
	util::redirection_header("comptes.php");
}
