<?php
//$Id: loader.php 41 2010-09-10 17:10:30Z pfrancois $
/**
 * 
 * function pour gerer l'autoload
 * @param string $className
 */
function autoload_grisbi_base($className) {
    if (strripos($className, 'exception') !== false) {
        require (dirname(__file__) . DIRECTORY_SEPARATOR . 'exceptions.php') ;
    } else {
        require (dirname(__file__) . DIRECTORY_SEPARATOR . $className . '.php') ;
    }
}

spl_autoload_register('autoload_grisbi_base') ;
date_default_timezone_set("Europe/Paris") ;
//definitions des variables globales
//$gsb_xml=new xml($xmlfile);
/**
 * 
 * objet global
 * @var tiers
 */
$gsb_tiers = new tiers() ;
/**
 * 
 * objet global
 * @var operations
 */
$gsb_operations = new operations() ;
/**
 * 
 * objet global
 * @var categories
 */
$gsb_categories = new categories() ;
/**
 * 
 * objet global
 * @var comptes
 */
$gsb_comptes = new comptes() ;
/**
 * 
 *objet global
 * @var echeances
 */
$gsb_echeances = new echeances() ;
/**
 * 
 * objet global
 * @var ibs
 */
$gsb_ibs = new ibs() ;
/**
 * 
 * objet global
 * @var devises
 */
$gsb_devises = new devises() ;
/**
 * 
 * objet global
 * @var banques
 */
$gsb_banques = new banques() ;
/**
 * 
 * objet global
 * @var exercices
 */
$gsb_exercices = new exercices() ;
/**
 * 
 * objet global
 * @var rapps
 */
$gsb_rapps = new rapps() ;
/**
 * 
 * objet global
 * @var etats
 */
$gsb_etats = new etats() ;
