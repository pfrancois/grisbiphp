<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="CACHE-CONTROL" content="NO-CACHE" />
<meta http-equiv="PRAGMA" content="NO-CACHE" />
<meta http-equiv="EXPIRES" content="0" />
<title>verification du xsd avec le fichier grisbi actuel</title>
</head>
<body>
<h1><?php echo realpath('20040701.gsb')?></h1>
<?php /* coding: utf-8 */
define('CPT_FILE','20040701.gsb');
function libxml_display_error($error)
{
	$return = "<br/>\n";
	switch ($error->level) {
		case LIBXML_ERR_WARNING:
			$return .= "<b>Warning $error->code</b>: ";
			break;
		case LIBXML_ERR_ERROR:
			$return .= "<b>Error $error->code</b>: ";
			break;
		case LIBXML_ERR_FATAL:
			$return .= "<b>Fatal Error $error->code</b>: ";
			break;
	}
	$return .= trim($error->message);
	if ($error->file) {
		$return .=    " in <b>$error->file</b>";
	}
	$return .= " on line <b>$error->line</b><<BR/>\n";

	return $return;
}

function libxml_display_errors() {
	$errors = libxml_get_errors();
	foreach ($errors as $error) {
		$desc=$error->message;
		echo libxml_display_error($error);
	}
	libxml_clear_errors();
}

// Enable user error handling
libxml_use_internal_errors(true);

$xml = new DOMDocument();
$xml->load(CPT_FILE);

$s=simplexml_import_dom($xml);


if (!$xml->schemaValidate('grisbi_0_5_9.xsd')) {
	print '<b>DOMDocument::schemaValidate() Generated Errors!</b><BR/>'."\n";
	libxml_display_errors();
}
else {
	echo "validated";
}
?>
</body>
</html>
