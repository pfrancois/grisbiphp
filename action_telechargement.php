<?php /* coding: utf-8 */
require_once('header.php');
//verifications
if (!isset($_FILES['temp_gsb'])){
	$tpl->critic("fichier non defini","outils.php");
}

if ($_FILES['temp_gsb']['error']) {
	switch ($_FILES['temp_gsb']['error']){
		case 1: // UPLOAD_ERR_INI_SIZE
		   $tpl->critic("Le fichier d�passe la limite autoris�e par le serveur (fichier php.ini) !","outils.php");
		   break;
		case 2: // UPLOAD_ERR_FORM_SIZE
		   $tpl->critic("Le fichier d�passe la limite autoris�e dans le formulaire HTML !","outils.php");
		   break;
		case 3: // UPLOAD_ERR_PARTIAL
		   $tpl->critic("L'envoi du fichier a �t� interrompu pendant le transfert !","outils.php");
		   break;
		case 4: // UPLOAD_ERR_NO_FILE
		   $tpl->critic("Le fichier que vous avez envoy� a une taille nulle !","outils.php");
		   break;
	}
}
//c'est ok
else {
	$info = pathinfo($_FILES['temp_gsb']['name']);
	//on essaye d'ouvrir le fichier
	if ($info['extension']!="gsb"){
		$tpl->critic("pas le bon format de fichier","outils.php");
	}
	try{
		@$temp_gsb=new xml($_FILES['temp_gsb']['tmp_name'] , SUR_FREE) ;
	} catch (Exception $e) {
		$tpl->critic($e->getMessage(),"outils.php");
	}
	unset($temp_gsb);
	$gsb_path=realpath(CPT_FILE);
	$gsb_file=basename(CPT_FILE,".gsb");
	unset($gsb_xml);
	copy($gsb_path,dirname($gsb_path).DIRECTORY_SEPARATOR.$gsb_file."_svg_".date("Ymd_His").".gsb");
	move_uploaded_file($_FILES["temp_gsb"]["tmp_name"],$gsb_path);
	if (DEBUG){
	$tpl->assign("titre", "import du fichier gsb");
	$tpl->assign("nom_classe_css","progress");
	$tpl->ral("ok pour l'import du fichier");
	$tpl->assign('lien',"options.php");
	$tpl->display('resultats.smarty');
	} else {
		util::redirection_header("options.php");
	}
}
//~ $tpl->assign("lien","options.php");
//~ $tpl->display('resultats.smarty');


