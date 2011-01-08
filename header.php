<?php /* coding: utf-8 */

require_once ('class/loader.php') ;
require_once('basic_ope_inc.php');

/**
 * nom du fichier
 */
define('CPT_FILE', "outils/20040701.gsb") ;

/**
 * tableau des comptes affiches
 * @see compte::T_*
 */
$cpt_aff=array(compte::T_BANCAIRE,compte::T_ESPECE);
/*
 * nombre de jours maximum d'affichage
 */
define('NB_JOURS_AFF', 600) ;
/**
 * numero du compte d'origine par defaut pour les virement
 */
define("CPT_VIREMENT", 0) ;
/**
 * est ce que ce site est heberge sur free
 */
define("SUR_FREE", true) ;
/**
 * id de la devise generalement utilise
 */
define("DEVISE",1);
/**
 * variable de debug
 * debug affiche des ecrans intermediaires
  * debug_smarty affiche la fenetre debug smarty
*/
define('DEBUG', true);
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

//-----------fin de la configuration a changer, normalement vous n'avez pas a modifier en dessous

//include_once('FirePHPCore/fb.php');
/*function fb_smarty($var){
 global $tpl;
 fb($tpl->_tpl_vars["$var"],$var);
 }*/
require_once ('smarty/libs/Smarty.class.php') ;

/**
 * class template qui etend smarty et qui sert a  l'affichage
 *
 */
class template extends Smarty {
	function __construct() {
		$this->Smarty() ;
		$this->template_dir = './templates' ;
		$this->compile_dir = './templates/compiled' ;
		$this->config_dir = './templates/config' ;
		$this->cache_dir = './templates/cache' ;

		if (DEBUG_SMARTY){
			$this->debugging=true;
		}
		$this->caching = false ;
		$this->config_read_hidden = true ;
		if ($this->caching) {
			$this->cache_lifetime = 180 ;
		} else {
			$this->_CachePath = null ;
			$this->_CacheLifeTime = null ; // En secondes
		}
	}

	/**
	 * affiche un message dans un boite predetermine
	 * @param string $type (ok ou error)
	 * @param string $msg
	 * @param string $lien
	 * @param bool $critique si true, lance l'affichage et arrete le script apres
	 */
	public function critic($msg,$lien){
		$this->assign("titre","erreur critique");
		$this->append("resultats",array("texte"=>$msg,"css"=>"error"));
		$this->assign('lien',"$lien");
		$this->display('resultats.smarty') ;
		exit( 1 );
	}

	/**
	 * @param string $string la chaine a afficher
	 * @param $css string le style css a afficher
	 * @return void
	 */
	public function ral($string,$css="ligne"){
		$this->append("resultats",array("texte"=>$string,"css"=>$css));
	}
	/**
	 * fonction afin d'inserer au mieux firebug sans smarty
	 */

	public function DebugFirePHP() {
		//get required debug variables
		$assigned_vars = $this->_tpl_vars ;
		ksort($assigned_vars) ;
		$config_vars = array() ;
		if (@is_array($this->_config[0])) {
			$config_vars = $this->_config[0] ;
			ksort($config_vars) ;
		}
		//permet comme ca d'eviter a avoir a changer
		if (class_exists(FirePHP)) {
			$firephp = FirePHP::getInstance(true) ;

			$firephp->group('Smarty Debug Output') ;
			/*Log template files*/
			$firephp->group('included templates & config files (load time in seconds)') ;
			foreach ($this->_smarty_debug_info as $tml) {
				$msg = str_repeat('--', $tml['depth']) ;
				$msg .= ($tml['depth'] != 0) ? '>' : '' ;
				$msg .= $tml['filename'] . ' (' . substr($tml['exec_time'], 0, 7) . 's)' ;
				$firephp->log($msg) ;
			}
			$firephp->groupEnd() ; //end group 'included templates &...'

			/*Log assigned template variables*/
			$firephp->group('assigned template variables') ;
			foreach ($assigned_vars as $key => $value) {
				if (is_array($value)) {
					$firephp->dump('{$' . $key . '}', $value) ;
					$firephp->log($value, '{$' . $key . '}') ;
				} else {
					$firephp->log($value, '{$' . $key . '}') ;
				}
			}
			$firephp->groupEnd() ; //end group 'assigned template variables'

			/*Log assigned config file variables (outer template scope)*/
			$firephp->group('assigned config file variables (outer template scope)') ;
			/*Check if there is something in the config*/
			if (!empty($config_vars)) {
				foreach ($config_vars as $key => $value) {
					$firephp->log($value, '{#' . $key . '#}') ;
				}
			} else {
				$firephp->log("No configuration values available") ;
			}
			$firephp->groupEnd() ; //end group 'assigned config file variables (outer template scope)'
			$firephp->groupEnd() ; //end group 'Smarty Debug Output'
		}
	}
}
/**
 *
 * objet smarty optimise avec les ajout pour
 * @var template
 */
$tpl = new template() ;
//ajoute un composant specifique pour smarty a cette application
require_once ('modifier.nbfr.php') ;

//chargement du fichier xml
/**
 *
 * objet xml
 * @var xml
 */
$gsb_xml = new xml(CPT_FILE, SUR_FREE) ;
