<?php /* coding: utf-8 */  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="CACHE-CONTROL" content="NO-CACHE" />
<meta http-equiv="PRAGMA" content="NO-CACHE" />
<meta http-equiv="EXPIRES" content="0" />
<link rel="stylesheet" type="text/css" href="css/common.css" />
<link href="data:image/x-icon;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QAAAAAAAD5Q7t/
AAACeElEQVQ4y62TzUtUcRSGn3vvzDg64wejpk2FRpKBSSJBprSzoF2ErYI2ERRB6/oH2rQt/AMygoKkhdnCRJQyLc2kSQw1bXQ+HOfem
bnj3I+ZO78WkqIJtuhdn/eBc877SuzRrZkzwl30sTQR5+2dRYkDtGvgwc/Loj14iYCngQxRavNB6tVT6AmDGTHEd2mMdCbDRjZJaCjK/K
Ok5PpjvjvbKaorarEsh4QTpkXu4LjZik0eo3Ie/7EC5znLJmnmIjOEhmMAbANM02TqxzQZexSPU849pZWsPIstWfTJD8mpG5R7KnC7JCJ
qgqVP2m5Aruggu924RBmK8KOmVGQrg2aoDMmTaI5OwYGCVKTM9lJSqpDH2QF0U0m1aVC0TALlPZRXNpOIRAiXhuk+cRrNyBHP6MyvreFV
XNidMtkBewsQHWkTVcF27FSEzfgc0ZV36FUdzJVMMHOon+baRtrKGkmmNAK2zPRyjNh6dmeFcNyh5EiaZHyV8IKFh49krQEmXUsMpUJML
cdoqqkBSzASWib9y4TRPW/UV26KpfFB1JiBuVng+bjBh7pq8l1Z1qU8skfCb3iIf9bhNRDd8m7fwK0IZFlCjWrkLOhqPcyNRj8GChNqii
cxCy3qQN/u7CgAY70BgaJgpeOkNBPZX03P7avU1FVS73NzzgffnmmE3jh/JdMVenFU1LZcwEgn8Nc1cNJbSjYrYdsSLo+XnFSGbroxrf2
j7HK8QSqCTSglPmw9gSjKiPwqLx8/JVAlo6s5+t8XGJxm315IAImvV4RjpFhdjJHLmDi5JPd7dSYW+LcyXe/2imsXfYjNJGtxieEvgleTB
5v/i34DxAkozbMmr3sAAAAASUVORK5CYII=" rel="icon" type="image/x-icon" />
<title>Options</title>
</head>
<body>
<p><a href="action_options.php?action=get_file"> télécharger le fichier </a></p>
<p><a href="export_csv.php"> exporter l'ensemble des comptes en csv </a></p>
<p><a href="action_options.php?action=effacer_tiers_vides"> effacer les tiers vides </a></p>
<p><a href="action_options.php?action=dates_ope_diff">change les date des operations cartes differés</a></p>
<p><a href="action_options.php?action=verif_totaux">vérifier les totaux</a></p>
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