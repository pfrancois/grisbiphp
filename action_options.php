<?php /* coding: utf-8 */
require_once('header.php');
//----------------gestion des variables d'entree-----------------
$action = util::get_page_param( 'action' );
$phase=util::get_page_param( 'phase' );
//--------------------telechargement du fichier--------------------
if ($action=="get_file"){
	$user_agent = strtolower ($_SERVER["HTTP_USER_AGENT"]);
	$filename=$gsb_xml->get_xmlfile();
	header("Cache-Control: no-cache, must-revalidate");
	if ((is_integer (strpos($user_agent, "msie" ))) && (is_integer (strpos($user_agent, "win" )))) {
		header( "Content-Disposition: filename=".basename($filename).";" );
	} else {
		header( "Content-Disposition: attachment; filename=".basename($filename).";" );
	}
	header("Content-Length: ". filesize($filename));
	header("Content-type:application/octet-stream");
	header("Content-Type: application/force-download" );
	readfile("$filename" );
	exit();
}

//------------------effacer les tiers qui n'ont ni operation ni echeances
if ($action=="effacer_tiers_vides"){
	$tpl->assign('titre',"tiers supprimés");
	if ($phase==""){
		$i=0;
		foreach ($gsb_tiers->iter() as $tier) {
			try {
				$nom=(string)$tier->get_nom();
				$tier->delete();
				$tpl->ral($nom);
				$i++;
			} catch (exception_integrite_referentielle $e) {
				$i=$i;
			}
		}
		if ($i>0){
			$tpl->ral("$i tiers à effacer");
		} else {$tpl->ral("aucun tiers à effacer");}
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
		if ($i>0){
			$tpl->ral("$i tiers effacés","progress");
		} else {$tpl->ral("aucun tiers effacé","progress");}
		$tpl->assign("lien","options.php");
		$tpl->display('resultats.smarty');
		exit();
	}
}

//---------------------------verifications des differents totaux
if ($action=="verif_totaux"){
	function callback($phase){
		global $gsb_xml;
		global $gsb_comptes;
		global $gsb_tiers;
		global $tpl;
		global $gsb_operations;
		$err=false;
		if ($gsb_xml->version!="0.5"){
			if (DEBUG){
				$tpl->critic("mauvais format de fichier, il faut que le fichier gsb soit au format 0.5.","outils.php");
			}else {
				util::redirection_header("comptes.php");
			}
		}
		//verif operations
		$nb_ope=0;
		$nb_ope_max=0;
		$x=$gsb_xml->get_xml();
		$nb_a_changer=0;
		//verif des comptes
		foreach ($gsb_comptes->iter() as $compte) {
			$nb_ope_c=0;
			$solde=util::fr2cent($compte->get_xml()->Details->Solde_initial);
			//operation du compte
			foreach ($compte->iter_operations() as $operation) {
				$nb_ope++;
				$nb_ope_c++;
				if ($operation->get_id()>$nb_ope_max){
					$nb_ope_max=$operation->get_id();
				}
				if (!$operation->is_ventilee()) {
					$solde=$operation->get_montant()+$solde;
				}
			}
			//verification
			if ($nb_ope_c!=(int)$compte->get_xml()->Details->Nb_operations) {
				$tpl->ral ("problème dans le comptage des opérations du compte ". $compte->get_nom()." il est noté ".$compte->get_xml()->Details->Nb_operations." alors qu'il y a ".$nb_ope_c."opérations","error" );
				$compte->get_xml()->Details->Nb_operations=$nb_ope_c;
				$err=true;
				if ($phase==2){
					$tpl->ral("correction ok","progress");
				}
			}else {
				$tpl->ral ("ok dans le comptage des opérations du compte ". $compte->get_nom());
			}
			if ($solde!=util::fr2cent($compte->get_xml()->Details->Solde_courant)){
				$tpl->ral ("problème dans le solde des opérations du compte ". $compte->get_nom()." il est noté ".(util::fr2cent($compte->get_xml()->Details->Solde_courant)/100).$compte->get_devise()->get_isocode()." alors que le solde calculé est de ".($solde/100).$compte->get_devise()->get_isocode(),"error" );
				$compte->get_xml()->Details->Solde_courant=util::cent2fr($solde);
				$err=true;
				if ($phase==2){
					$tpl->ral("correction ok","progress");
				}
			}else {
				$tpl->ral ("ok pour le solde des opérations du compte ". $compte->get_nom());
			}

			//moyens de p du cpt
			$nb_moyen=0;
			foreach ($compte->iter_moyens() as $moyen){
				$nb_moyen++;
			}
		}
		//verifications globales
		if ($nb_ope_max<$nb_ope){
			$tpl->ral("le numero max est trop inférieur au nombre total d'opérations.");
			$nb_ope_max=$nb_ope;
			$err=true;
			if ($phase==2){
				$tpl->ral("correction ok","progress");
			}
		}else {
			$tpl->ral("numero max d'opération ok");
		}
		if ($nb_ope_max != ($gsb_operations->get_next())-1) {
			$x->Generalites->Numero_derniere_operation=$nb_ope_max;
			$tpl->ral("id dernière opération incorrect","error");
			$err=true;
			if ($phase==2){
				$tpl->ral("correction ok","progress");
			}
		}else {
			$tpl->ral("numero derniere opération ok");
		}
		$id_tiers_max=(int)$gsb_xml->xpath_uniq('/Grisbi/Tiers/Detail_des_tiers/Tiers[not(@No < /Grisbi/Tiers/Detail_des_tiers/Tiers/@No)]/@No');
		$nb_tiers=count($gsb_xml->xpath_iter('/Grisbi/Tiers/Detail_des_tiers/Tiers'));
		if ((int)$x->Tiers->Generalites->Nb_tiers != $nb_tiers){
			$tpl->ral("probleme dans le nombre de tiers. il y a $nb_tiers alors qu'il est inscrit ".(int)$x->Tiers->Generalites->Nb_tiers." de tiers dans le fichier","error");
			$x->Tiers->Generalites->Nb_tiers=$nb_tiers;
			$err=true;
			if ($phase==2){
				$tpl->ral("correction ok","progress");
			}
		} else {
			$tpl->ral ("ok pour le nombre de tiers ");
		}
		if ((int)$x->Tiers->Generalites->No_dernier_tiers < $id_tiers_max){
			$tpl->ral("probleme pour le dernier id. l'id le max est $id_tiers_max alors qu'il est noté comme étant ".(int)$x->Tiers->Generalites->No_dernier_tiers,"error");
			$x->Tiers->Generalites->No_dernier_tiers=$id_tiers_max;
			$err=true;
			if ($phase==2){
				$tpl->ral("correction ok","progress");
			}
		} else {
			$tpl->ral ("ok pour le dernier id des tiers ");

		}
		return $err;
	}
	if ($phase==""){
		$tpl->assign('titre','liste des actions à effectuer sur '.$gsb_xml->get_xmlfile());
		$err=callback(1);
		// si il n'y pas d'erreur pas besoin de faire la seconde phase
		if ($err){
			$tpl->assign("lien","action_options.php?action=verif_totaux&amp;phase=2");
		} else {
			$tpl->assign("lien","options.php");
		}
		$tpl->display('resultats.smarty');
		exit();
	}
	if ($phase==2){
		$tpl->assign('titre','liste des actions effectuées sur '.$gsb_xml->get_xmlfile());
		//remplissez la requete xpath
		callback(2);
		$gsb_xml->save();
		$tpl->assign("lien","options.php");
		$tpl->display('resultats.smarty');
		exit();
	}
}

//---------------------------change les date des operations cartes differes
if ($action=="dates_ope_diff"){
	function callback($iter){
		global $tpl;
		try {
			$notes=$iter->get_notes();
			if ($notes!=''){
				if (preg_match('#CARTE X9438 (../..)#', $notes,$n)){
					$date_a_verifier=util::datefr2time($n[1]."/".(idate("Y")));
					//verifie si on ne met pas l'annee suivante
					if ($date_a_verifier>time()){
						$date_a_verifier=util::datefr2time($n[1]."/".(idate("Y")-1));
					}
					if ($iter->get_date()!=$date_a_verifier){
						$iter->set_date($date_a_verifier);
						$nouvelle_note=str_replace("CARTE X9438","CB SG",$notes);
						$iter->set_notes($nouvelle_note);
						$tpl->ral("operation ".$iter->get_id()." du ".date('d/m/Y',$iter->get_date())." pour ".(util::cent2fr($iter->get_montant(),2)));
						return 1;
					}
				}
			}
			throw new Exception_no_reponse('pas de réponse');
		} catch (Exception_no_reponse $e) {
			return 0;
		}
	}
	///// normalement, pas besoin de changer en dessous
	if ($phase==""){
		$i=0;
		$tpl->assign('titre','liste des opérations à modifier sur '.$gsb_xml->get_xmlfile());
		foreach ($gsb_operations->iter() as $iter) {
			$i=$i+callback($iter);
		}
		$tpl->ral("$i opérations à modifier","progress");;
		$tpl->assign("lien","action_options.php?action=dates_ope_diff&amp;phase=2");
		$tpl->display('resultats.smarty');
		exit();
	}
	if ($phase==2){
		$i=0;
		$tpl->assign('titre','liste des actions effectuées sur '.$gsb_xml->get_xmlfile());
		foreach ($gsb_operations->iter() as $iter) {
			$i=$i+callback($iter);
		}
		$tpl->ral("$i opérations modifiées","progress");;
		$gsb_xml->save();
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
					$tpl->ral("operation ".$iter['No']);
					$n=$n."/2010";
				}
				$tpl->ral($n);
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
		$tpl->ral('liste des actions effectues sur '.$gsb_xml->get_xmlfile());
		//remplissez la requete xpath
		try {
			foreach ($gsb_xml->xpath_iter($xpath) as $iter) {
				$i=$i+callback($iter);
			}
			$tpl->ral("$i actions effectué");;
		} catch (Exception_no_reponse $e) {
			$tpl->ral("aucune actions effectué car &laquo;$xpath&raquo; non trouvé");
		}
		$tpl->assign("lien","action_options.php?action=specifique&amp;phase=2");
		$tpl->display('resultats.smarty');
		exit();
	}
	if ($phase==2){
		$i=0;
		$tpl->ral('liste des actions effectues sur '.$gsb_xml->get_xmlfile());
		//remplissez la requete xpath
		foreach ($gsb_xml->xpath_iter($xpath) as $iter) {
			$i=$i+callback($iter);
		}
		$gsb_xml->save();
		$tpl->ral("$i tiers effac&eacutes","progress");
		$tpl->assign("lien","options.php");
		$tpl->display('resultats.smarty');
		exit();
	}
}
//recupere les action inexistantes, bien mettre un exit dans les if
if (DEBUG){
	$tpl->critic("action demandée inexistante: $action","outils.php");
}else {
	util::redirection_header("comptes.php");
}