<?php  /* coding: utf-8 */

/**
 * Classe utilitaire qui donne des differentes fonctions utilise ailleurs.
 *
 *
 *
 * @since 2009-08-25
 * @version 1.2 modification de add_date
 * @version 1.1 ajout de la fonction dump, enlevage du <? final
 */
class util {
	/**
	 *  Fonction de conversion de date du format francais  en Timestamp.
	 *
	 *  les formats acceptes sont :
	 *  JJ/MM/AAAA
	 *  JJ/MM/AA
	 *  J/M/AA
	 *  JJMMAAAA
	 * @param string $gd date au format francais (JJ/MM/AAAA)
	 * @throws InvalidArgumentException date invalide
	 * @return int Timestamp en secondes
	 */
	public static function datefr2time($gd) {
		$gd=(string)$gd;
		if ((stristr($gd,'/')===false) && (strlen($gd)==8)){
			$tab=array(substr($gd,0,2),substr($gd,2,2),substr($gd,4,4));
			$gd=implode("/",$tab);
		}
		$date = sscanf($gd, '%d/%d/%d') ;
		$day = (int)$date[0] ;
		$month = (int)$date[1] ;
		$year = (int)$date[2] ;
		if (!checkdate($month, $day, $year)) {
			throw new InvalidArgumentException("la date '$gd' est invalide") ;
		}
		return mktime(0, 0, 0, $month, $day, $year) ;
	}

	/**
	 * fonction qui permet d'ajouter une duree a une date
	 * @param int $cd timestamp
	 * @param integer $day  nombre de jour a ajouter
	 * @param integer $mth nombre de mois a ajouter
	 * @param integer $yr   nombre de yr a ajouter
	 * @return int date nouvelle
	 */
	public static function add_date($cd, $day = 0, $mth = 0, $yr = 0) {
		$newdate =  mktime(0, 0, 0, (int)date('m', $cd) + $mth, (int)date('d',
			$cd) + $day, (int)date('Y', $cd) + $yr) ;
		return $newdate ;
	}

	/**
	 * renvoit un get apres les controle de base
	 *
	 * @param string $paramName nom de la variable a recuperer
	 * @return mixed la variable verifie
	 */
	public static function get_page_param($paramName) {
		if (isset($_GET[$paramName])) {
			$t = (string )$_GET[$paramName] ;
			if ((bool)get_magic_quotes_gpc()) {
				$t = stripslashes($t) ;
			}
			$t=trim($t);
			return (string) $t ;
		} else {
			return "" ;
		}
	}

	/**
	 * transforme un nombre francais en centimes
	 * @param string $n le nombre
	 * @return int
	 * @throws  InvalidArgumentException si $n non possible
	 */
	public static function fr2cent($n) {
		$n = (string)$n ;
		$n = str_replace(' ', '', $n) ;
		$n=str_replace(',', '.', $n) ;
		if (is_numeric($n)){
			$n=(float)$n;
		} else {
			throw new InvalidArgumentException('probleme, '.$n."n'est pas un nombre");
		}
		if ( intval(round($n * 100))== round($n * 100)){
			return intval(round($n * 100)) ;
		}else {
			return round($n * 100) ;
		}
	}

	 /**
	 *transforme un nombre anglais en centimes
	 * @param int $n le nombre a transformer en float
	 * @return string
	 */

	public static function cent2fr($n) {
		if (is_numeric($n)){
			$n=(float)$n;
		} else {
			throw new InvalidArgumentException('probleme, '.$n."n'est pas un nombre");
		}
		$n = floatval($n / 100 );
		return str_replace('.', ',', sprintf("%01.7f", $n)) ;
	}


	//@codeCoverageIgnoreStart
	/**
	 *renvoie un nombre de mot de la chaine
	 *
	 * @param string $s la chaine original
	 * @param integer $nb le nombre de mots
	 * @param integer $debut le numero du premier mot a garder
	 * @return string
	 *
	 */
	public static function extract_mots($s, $nb = null, $debut = 0) {
		$data = explode(" ", $s) ;
		$r = "" ;
		// fonction pour enlever le cas des doubles espaces
		foreach ($data as $k => $l) {
			if (trim($l) === "") {
				unset($data[$k]) ;
			} else {
				$data[$k] = trim($l) ;
			}
		}
		$data = explode(" ", implode(" ", $data)) ;
		// si null, on le fait jusq'a la fin avec les ajustement du au $debut
		if ($nb == null) {
			$nb = count($data) - $debut + 2 ;
		}
		for ($i = $debut; $i < $nb + 1; $i++) {
			$r = $r . " " . $data[$i] ;
		}
		return trim($r) ;
	}

	/**
	 * fonction dump qui marche avec plusieurs variabales
	 *
	 * @param mixed $data la liste des choses a dumper
	 */
	public static function dump($data) {
		if (func_num_args() > 1) {
			$data = func_get_args() ;
		}
		return "<pre>" . print_r($data, true) . "</pre>" ;
	}
	/**
	 * fonction qui ne fait qu'afficher avec un retoura la ligne
	 */
	public static function aff($data) {
		echo $data . PHP_EOL ;
	}
	// @codeCoverageIgnoreEnd

	/**
	 * Cette fonction calcule une clé RIB a partir des informations bancaires
	 * La fonction implemente l'algorithme de clé RIB
	 * Une clé RIB n'est valable que si elle se trouve dans l'intervalle 01 - 97
	 *
	 * @param string code unique de la banque
	 * @param string code unique du guichet (agence ou se trouve le compte)
	 * @param string numéro du compte bancaire (peut contenir des lettres)
	 * @return string clé rib calculée
	 **/
	public static function calculerCleRib($sCodeBanque, $sCodeGuichet, $sNumeroCompte) {
		// Variables locales
		$iCleRib = 0 ;
		$sCleRib = '' ;

		// Calcul de la clé RIB a partir des informations bancaires
		$sNumeroCompte = strtr(strtoupper($sNumeroCompte), 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
			'12345678912345678923456789') ;
		$iCleRib = 97 - (int)fmod(89 * $sCodeBanque + 15 * $sCodeGuichet + 3 * $sNumeroCompte,
			97) ;

		// Valeur de retour
		if ($iCleRib < 10) {
			$sCleRib = '0' . (string )$iCleRib ;
		} else {
			$sCleRib = (string )$iCleRib ;
		}
		return $sCleRib ;
	}
	public static function redirection_header($url){
		$url='http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'],'/')).'/'.$url;
		header( 'Request-URI: '.$url );
		header( 'Content-Location: '.$url );
		header( 'Location: '.$url,301 );
	}
}
