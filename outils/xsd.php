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
<?php  /* coding: utf-8 */ 

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
$t=$s->Tiers->Detail_des_tiers->addChild('Tiers');
$t->addAttribute('No','0');
$t->addAttribute('Nom','virement');
$t->addAttribute('Informations','');
$t->addAttribute('Liaison','0');
$s->Tiers->Generalites->Nb_tiers=(int)$s->Tiers->Generalites->Nb_tiers+1;
$t=$s->Categories->Detail_des_categories->addChild('Categorie');
$t->addAttribute('No','0');
$t->addAttribute('Nom','virement');
$t->addAttribute('Type','0');
$t->addAttribute('No_derniere_sous_cagegorie','0');
$s->Categories->Generalites->Nb_categories=(int)$s->Categories->Generalites->Nb_categories+1;

if (!$xml->schemaValidate('grisbi.xsd')) {
    print '<b>DOMDocument::schemaValidate() Generated Errors!</b><BR/>'."\n";
    libxml_display_errors();
}
else {
echo "validated";
}
?>
</body>
</html>