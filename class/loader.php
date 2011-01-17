<?php /* coding: utf-8 */

/**
 *
 * function pour gerer l'autoload
 * @param string $className
 */
function autoload_grisbi_base($className) {
	if (strripos($className, 'exception') !== false ) {
		require_once (dirname(__file__) . DIRECTORY_SEPARATOR . 'exceptions.php');

	} elseif (in_array($className,array("util"))) {
		require (dirname(__file__) . DIRECTORY_SEPARATOR . $className . '.php');
	} else {
		require (dirname(__file__) . DIRECTORY_SEPARATOR .VERSION_UTILISE. DIRECTORY_SEPARATOR . $className . '.php');
	}
}

spl_autoload_register('autoload_grisbi_base');
date_default_timezone_set("Europe/Paris");
//definitions des variables globales

/**
 *
 * objet global
 * @var tiers
 */
$gsb_tiers = new tiers();

/**
 *
 * objet global
 * @var operations
 */
$gsb_operations = new operations();

/**
 *
 * objet global
 * @var categories
 */
$gsb_categories = new categories();

/**
 *
 * objet global
 * @var comptes
 */
$gsb_comptes = new comptes();

/**
 *
 *objet global
 * @var echeances
 */
$gsb_echeances = new echeances();

/**
 *
 * objet global
 * @var ibs
 */
$gsb_ibs = new ibs();

/**
 *
 * objet global
 * @var devises
 */
$gsb_devises = new devises();

/**
 *
 * objet global
 * @var banques
 */
$gsb_banques = new banques();

/**
 *
 * objet global
 * @var exercices
 */
$gsb_exercices = new exercices();

/**
 *
 * objet global
 * @var rapps
 */
$gsb_rapps = new rapps();

/**
 *
 * objet global
 * @var etats
 */
$gsb_etats = new etats();

define("GSB_DIR",realpath(str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__FILE__)))."/.."));
