<?php /* coding: utf-8 */

require_once('../class/util.php');
/**
 *  renvoie les resultats relatif � la chaine xpath
 *  @param string $chaine la chaine xpath
 *  @return SimpleXMLElement
 */
function xpath($chaine) {
	global $xml;
	$r = $xml->xpath($chaine);
	if (empty($r[0])){
		return $r;
	}else {
		return $r[0];
	}
}
/**
 *  renvoie un tableau relatif à la chaine xpath
 *  @param string $chaine la chaine xpath
 *  @return SimpleXMLElement
 */

function xpath_tab($chaine){
	global $xml;
	$r = $xml->xpath($chaine);
	if (empty($r[0])){
		return $r;
	}else {
		return $r;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="CACHE-CONTROL" content="NO-CACHE" />
<meta http-equiv="PRAGMA" content="NO-CACHE" />
<meta http-equiv="EXPIRES" content="0" />
<title>xpath</title>
</head>
<body>
<h1><?php echo realpath('20040701.gsb')?></h1>

<form method="get" action="<?php echo $_SERVER['SCRIPT_NAME'];?>">
<fieldset><legend>xpath</legend> <input type="text" name="xpath"
	size="100" maxlength="100"
	value="<?php if(isset($_GET['xpath'])){echo str_replace("\"","'" ,util::get_page_param('xpath'));} ?>"></input>
</fieldset>
<input type="submit" name="ok" /></form>
<?php
$xml = simplexml_load_file('20040701.gsb');
if(isset($_GET['xpath'])){
	$xpath=util::get_page_param('xpath');
	echo 'requete:<pre style="font-family: monospace;font-size: 1.2em;border: solid #BBB;background: #DDD;">'.$xpath.'</pre><br/>';
	$req=xpath_tab($xpath);
	if (!$req){
		echo 'aucune réponse';
	}else{
		if (count($req)==1){
			$req=$req[0];
			echo 'reponse:<pre>';
		} else {
			echo count($req).' réponses:<pre>';
		}
		var_dump($req);
		echo '</pre>';
	}
}
?>
</body>
</html>