<?php /* coding: utf-8 */
$langueprefere=setlocale(LC_ALL,'C');
define('N',"\n");
define('T',"  ");
error_reporting(E_ALL);
xdebug_enable();

//definition des constantes
/**
 *premiere des deux fonctions de chronometrage
 *@return int : le top du depart
 **/

function start_timing() {
	$time_grab = explode(' ',microtime());
	$start_time = $time_grab[1].substr($time_grab[0], 1);
	return $start_time;
}
/**
 * seconde fonction de chrono
 *
 * @param $start_time: le top depart pr�c�dement donn�
 * @return number le temps mit depuis le top depart
 */
function print_timing($start_time) {
	$timeparts = explode(' ',microtime() );
	$end_time = $timeparts[1].substr($timeparts[0],1);
	$timing = number_format($end_time - $start_time, 4);
	return $timing.' secondes';
}

/**
 * transforme une date du format jj/mm/aaaa vers le format aaaa-mm-jj
 *
 * @param string $dateac au format aaaa-mm-jj
 * @return 0 si la chaine est vide sinon la date au format jj/mm/aaaa
 */
function xml2date($dateac) {
	if (ereg ("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $dateac, $regs)) {
		return "$regs[3]-$regs[2]-$regs[1]";
	} else {
		if (ereg ("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})", $dateac, $regs)){
			return "$regs[3]-$regs[2]-$regs[1]";
		}else {
			return 0;
		}
	}
}
/**
 *transforme une date du format jj/mm/aaaa vers le format aaaa-mm-jj
 *
 * @param string $dateac format jj/mm/aaaa
 * @return 0 si la chaine est vide sinon date au format aaaa-mm-jj
 */
function date2xml($dateac) {
	/*
	 @return 0 si la chaine est vide
	 */
	if ($dateac<>'0000-00-00'){
		ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $dateac, $regs);
		$d=date('j/n/Y',mktime(0,0,0,$regs[2],$regs[3],$regs[1]));
		return $d;
	}else{
		return '';
	}
}

/**
 *
 *remplace les virgules par des points
 * @param int $d
 * @return string
 */
function fr2uk($d) {
	/* remplace les virgules par des points*/
	$d=(string)$d;
	return str_replace(',','.',$d);
}

/**
 * remplace . par ,
 *
 * @param $d
 * @return string
 */
function uk2fr($d) {
	$d=(string)$d;
	//remplace les . par des virgules
	$d=str_replace('.',',',$d);
	return $d.'0';
}

function ins($t){
	return mysql_real_escape_string(utf8_decode((string) $t));
}

function sansaccent($t){
	//retourne une chaine sans accent ni espace
	$string= strtr($t,
        "����������������������������������������������������� ", 
        "AAAAAAaaaaaaOOOOOoooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn_");  
	return $string;
}

/**
 * retourne une chaine en utf8
 *
 * @param  string non ut8 $t
 * @return string utf8
 */
function unins($t){
	//

	return iconv("iso-8859-1","UTF-8",stripslashes($t));
}
/**
 * classe de connexion a mysql
 *
 */
class MySQLConnector {
	/**
	 * permet le passage en mode debogage, si true , cela affiche la requete et le nombre de lignes affect�s
	 */
	public $debogagesql=false;
	/**
	 * nom de la base de donn�e
	 *
	 * @var public
	 */
	public $dbname='';
	private $link;
	/**
	 * constructeur de la classe
	 *
	 * @param $host
	 * @param $user
	 * @param $password
	 * @param $database
	 */
	public function __construct($host, $user, $password, $database) {
		$this->link = mysql_connect($host, $user, $password);
		if (!$this->link)throw new Exception("Impossible de se connecter : " . mysql_error());
		$this->dbname=$database;
		$r=mysql_select_db($database, $this->link);
		if (!$r)throw new Exception("Impossible de se connecter : " . mysql_error());
	}
	/**
	 * ferme la connexion avec la bdd
	 *
	 */
	public function close() {
		$r=mysql_close($this->link);
		if (!$r)throw new Exception("erreur lors de la fermeture : " . mysql_error());
	}
	/**
	 * function qui fait n'importe quelle query
	 *
	 * @param $query la requete a faire
	 * @param bool $escape si true, cela va appeler mysql_real_escape_string
	 * 		pour la requete. true par defaut
	 * @return la requete
	 * @version 1.2
	 */
	public function q($sql,$escape=true) {
		if ($escape) $sql=mysql_real_escape_string($sql);
		if ($this->debogagesql) {
			printf("requete sql: \"%s\" \n", $sql);
		}
		$result = mysql_query($sql,$this->link) ;
		if (($this->debogagesql)&& (mysql_affected_rows($this->link)!=1)){
			printf("lignes affect�es: %d\n", mysql_affected_rows($this->link));
			echo("<BR>\n");
		}

		try {
			if (!$result) {
				throw new Exception(mysql_error()." \n � la ligne ".xdebug_call_line().' du fichier '.xdebug_call_file()." � la fonction ".xdebug_call_function()."\n"." alors que la requete sql �tait \"".$sql."\"");
			}
			return $result;
		} catch (Exception $e) {
			throw new Exception('Caught exception: '.$e->getMessage());
			Exit;
		}
	}
	/**
	 * une methode qui appelle uniquement select le premier terme de la requete
	 * @param string $query
	 * @return string 1er terme de la requete
	 */
	public function s($query,$escape=true) {
		$r=$this->q($query,$escape);
		$row=mysql_fetch_row($r);
		return $row[0];
	}
	/**
	 * liste les tables de la bases
	 *
	 * @return tables array
	 */
	public function liste_tables(){
		$r=$this->q("SHOW TABLES FROM $this->dbname");
		$tables= array();
		while ($row = mysql_fetch_row($r)) {
			if ($this->debogagesql) var_dump($row[0]);
			$tables[]=$row[0];
		}
		return $tables;
	}
	/**
	 * effaces toutes les tables de la base
	 *
	 */
	public function efface_tables(){
		$result=$this->q("SHOW TABLES FROM $this->dbname");
		while ($r = mysql_fetch_row($result)) {
			$q="DELETE from `$r[0]`";
			if ($this->debogagesql) echo($q.N);
			$result2=$this->q($q,false);
			if ($this->debogagesql) echo "$r[0] est effac�.\n";
			if (!$result2) throw new Exception("imposible d'effacer les tables \n en particulier".mysql_error()." \n � la ligne ".xdebug_call_line().' du fichier '.xdebug_call_file()."� la fonction ".xdebug_call_function()."\n"." alors que la requete sql �tait ".$sql);
		}
		mysql_free_result($result);
	}
	/**
	 * insert les donn�es dans la base de la table
	 *
	 * @param $s array associatif avec comme cle les noms des champs ou rentrer les donn�es
	 * @param string $table
	 */
	public function insert($table,$s){
		$key=implode(',',array_keys($s));
		$val='\''.implode('\',\'',$s).'\'';
		$q="insert into $table ($key) values($val)";
		$r=$this->q($q,false);
		if (!$r) throw new Exception(mysql_error()." \n � la ligne ".xdebug_call_line().' du fichier '.xdebug_call_file()." � la fonction ".xdebug_call_function()."\n");
	}

	public function tab($q){
		$r=$this->q($q,false);
		if (!$r) throw new Exception(mysql_error()." \n � la ligne ".xdebug_call_line().' du fichier '.xdebug_call_file()." � la fonction ".xdebug_call_function()."\n");
		$tab=array();
		while ($row = mysql_fetch_array($r, MYSQL_ASSOC)) {
			if ($this->debogagesql) var_dump($row);
			$tab[]=$row;
		}
		return $tab;
	}
}



/**
 * Affiche le contenu d'une variable (pour d�bugage, pratique pour les bool�ens et les tableaux).
 *
 * @param string $var le nom de la variable � tester (chaine)
 * @return void
 */
function debog($nomvar,$v,$html=true) {
	$t=$v;
	if ($html) echo "<div>\n <b>";
	echo "\$$nomvar :";
	if ($html)echo "</b>";
	if ($html){
		echo '<pre style="margin:0 0 0 30px; border-left:1px #999 dotted; padding-left:5px;">';
		var_dump($t);
		echo '</pre></div>';
	}else{
		var_dump($t);
	}
}

/**
 * affiche le fichier xml
 *
 * @param simplexml $xml
 */
function debogXML($xml){
	$t=$xml->AsXML();
	$t=str_replace('><','>'.N.'<',$t);
	highlight_string($t);
}

/**
 * affiche $s plus retour � la ligne
 *
 * @param string $s
 */
function aff($s)
{
	echo (string)$s.N;
}

function vardump($n){
	var_dump($n);
}
?>