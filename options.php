<?php /* coding: utf-8 */   ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="CACHE-CONTROL" content="NO-CACHE" />
<meta http-equiv="PRAGMA" content="NO-CACHE" />
<meta http-equiv="EXPIRES" content="0" />
<link rel="stylesheet" type="text/css" href="css/common.css" />
<title>Options</title>
</head>
<body>
<p><a href="action_options.php?action=get_file"> telecharger le fichier </a></p>
<p><a href="export_csv.php"> exporter l'ensemble des comptes en csv </a></p>
<p><a href="action_options.php?action=effacer_tiers_vides"> effacer les tiers vides </a></p>
<p><a href="action_options.php?action=dates_ope_diff">change les date des operations cartes differés</a></p>
<p><a href="action_options.php?action=verif_totaux">verifie les totaux</a></p>
<p><a href="action_options.php?action=specifique"> travaux en batch </a></p>
<form method="post" action="action_telechargement.php" enctype="multipart/form-data">
	<fieldset class="operationMainTable">
	<legend>chargement sur le serveur d'un nouveau fichier gsb</legend>
	<input type="file" name="temp_gsb"/><br/>
	<input type="submit" value="Envoyer"/><br/>
	</fieldset>
</form>
<p><a href="comptes.php">retour vers le menu général</a></p>
</body>
</html>
