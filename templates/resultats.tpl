{* $Id: resultats.tpl 41 2010-09-10 17:10:30Z pfrancois $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta http-equiv="CACHE-CONTROL" content="NO-CACHE" />
<meta http-equiv="PRAGMA" content="NO-CACHE" />
<meta http-equiv="EXPIRES" content="0" />
<link rel="stylesheet" type="text/css" href="css/common.css" />
<link rel="stylesheet" type="text/css" href="css/operation.css" />
<link rel="stylesheet" type="text/css" href="css/calendar.css" />
  <title>modification apportes</title>
</head>
<body >
{foreach from=$resultats item=resultat}
		<div class='progress'>{$resultat}</div>
{/foreach}
<div class="error"><a href='{$lien}'>suite</a></div>

</body>
</html>
