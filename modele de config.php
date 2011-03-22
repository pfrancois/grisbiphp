<?php /* coding: utf-8 */
date_default_timezone_set("Europe/Paris");
/**
 * nom du fichier
 */
define('CPT_FILE', "outils/bdd/fichier_test.gsb");
/**
 * est ce que ce site est heberge sur free
 */
define("SUR_FREE", true);

/*
 * nombre de jours maximum d'affichage
 */
define('NB_JOURS_AFF', 6000);
/**
 * numero du compte d'origine par defaut pour les virement
 */
define("CPT_VIREMENT", 0);

/**
 * id de la devise generalement utilise
 */
define("DEVISE",1);
/**
 * variable de debug
 * debug affiche des ecrans intermediaires
 * debug_smarty affiche la fenetre debug smarty
*/
define('DEBUG', false);
define('DEBUG_SMARTY', false);
/**
 * Id du tiers par defaut pour les virement
 */
define('TIERS_VIREMENT',2);
/**
* id de la categorie pour plus ou moins values latentes
*/
define('PMVALUES',25);
/**
* id de la categorie des operations sur titres
*/
define('OSTITRES',31);
/**
* id de la categorie pour operations sur titres
*/
define('OPE_TITRE',31);
