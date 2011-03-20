<?php /* coding: utf-8 */
$langueprefere = setlocale(LC_ALL, 'C');
define('N', "\n");
define('T', '  ');
xdebug_enable();

/**
 * Classe utilitaire qui donne des différentes fonctions utilisées ailleurs.

 * @since 2009-08-25
 * @version 1.2 modification de add_date
 * @version 1.1 ajout de la fonction dump, enlevage du <? final
 */
class util {

    /**
     * Fonction de conversion de date du format francais en Timestamp.
     *
     * les formats acceptés sont :
     * JJ/MM/AAAA
     * JJ/MM/AA
     * J/M/AA
     * JJMMAAAA
     *
     * @param string $gd          date au format francais (JJ/MM/AAAA)
     * @param string $return_null si true, renvoi null si date est vide
     * @throws InvalidArgumentException date invalide
     * @return int Timestamp en secondes
     */
    public static function datefr2time($gd, $return_null=false) {
        $gd = (string) $gd;
        if ((stristr($gd, '/') === false) && (strlen($gd) === 8)) {
            $tab = array(
                substr($gd, 0, 2),
                substr($gd, 2, 2),
                substr($gd, 4, 4),
            );
            $gd = implode('/', $tab);
        }
        $date = sscanf($gd, '%d/%d/%d');
        $day = (int) $date[0];
        $month = (int) $date[1];
        $year = (int) $date[2];
        if (($day === 0 && $month === 0 && $year ===0) && $return_null === true) {
            return null;
        }
        if (checkdate($month, $day, $year) === FALSE) {
            throw new InvalidArgumentException("la date '$gd' est invalide");
        }
        return mktime(0, 0, 0, $month, $day, $year);
    }

    /**
     * transforme un timstamp en date au format mysql
     * @param int $timestamp un timestamp
     * @return string
     */
    public static function time2date($timestamp) {
        return date("Y-m-d H:i:s", $timestamp);
    }

    public static function now() {
        return date("Y-m-d");
    }
    /**
     * transforme une date francaise en date mysql
     *
     * @param mixed $gd la date (format jj/mm/aa ou jjmmaa)
     * @return string la date au format mysql
     */
    public static function datefr2date($gd) {
        $gd = (string) $gd;
        if ((stristr($gd, '/') === false) && (strlen($gd) === 8)) {
            $tab = array(
                substr($gd, 0, 2),
                substr($gd, 2, 2),
                substr($gd, 4, 4)
            );
            $gd = implode("/", $tab);
        }
        $date = sscanf($gd, '%d/%d/%d');
        $day = (int) $date[0];
        $month = (int) $date[1];
        $year = (int) $date[2];
        if ($gd === '0/0/0') {
            return null;
        }
        if ($gd === '') {
            return null;
        }
        if (checkdate($month, $day, $year) === FALSE) {
            throw new InvalidArgumentException("la date '$gd' est invalide");
        }
        $timestamp = mktime(0, 0, 0, $month, $day, $year);
        return date("Y-m-d H:i:s", $timestamp);
    }

    /**
     * fonction qui permet d'ajouter une duree à une date
     * @param int     $cd  timestamp
     * @param integer $day nombre de jour à ajouter
     * @param integer $mth nombre de mois à ajouter
     * @param integer $yr  nombre de yr à ajouter
     * @return int date nouvelle
     */
    public static function add_date($cd, $day=0, $mth=0, $yr=0) {
        $newdate = mktime(0, 0, 0, (int) date('m', $cd) + $mth, (int) date('d', $cd) + $day, (int) date('Y', $cd) + $yr);
        return $newdate;
    }
    /**
     * fonction qui trie un tableau associatif non case sensitive
     *
     * @param array $arr tableau a trier
     * @return array
     */
    public static function asorti($arr) {
       $arr2 = $arr;
       foreach($arr2 as $key => $val) {
          $arr2[$key] = strtolower($val);
       }
       asort($arr2);
       foreach($arr2 as $key => $val) {
          $arr2[$key] = $arr[$key];
       }
       return $arr2;
    }
    /**
     * renvoit un get apres les contrôles de base
     *
     * @param string $paramName nom de la variable à récuperer
     * @return mixed la variable verifiée
     */
    public static function get_page_param($paramName) {
        if (isset($_GET[$paramName])) {
            $t = (string) $_GET[$paramName];
            if ((bool) get_magic_quotes_gpc()) {
                $t = stripslashes($t);
            }
            $t = trim($t);
            return (string) $t;
        } else {
            return "";
        }
    }

    /**
     * transforme un nombre francais en centimes
     * @param string $n le nombre
     * @return int
     * @throws InvalidArgumentException si $n non possible
     */
    public static function fr2cent($n) {
        $n = (string)$n;
        $n = str_replace(' ', '', $n);
        $n = str_replace(',', '.', $n);
        if (is_numeric($n)) {
            $n = (float) $n;
        } else {
            throw new InvalidArgumentException('problème, '.$n."n'est pas un nombre");
        }
        $r = intval(round($n * 100));
        return $r;
    }

    /**
    * transforme un nombre francais en nombre aux normes anglosaxones avec .
    * @param string $n le nombre
    * @return float
    */
    public static function fr2uk($n) {
        $n = (string) $n;
        $n = str_replace(' ', '', $n);
        $n = str_replace(',', '.', $n);
        if (is_numeric($n)) {
            $n = (float) $n;
        } else {
            throw new InvalidArgumentException('problème, '.$n."n'est pas un nombre");
        }
        return $n;
    }

    /**
     *transforme un nombre en centimes en nombre francais
     * @param int $n     le nombre a transformer en float
     * @param int $digit le nombre de chiffres apres la virgule
     * @return string
     */
    public static function cent2fr($n, $digit=7) {
        if (is_numeric($n)) {
            $n = (float) $n;
        } else {
            throw new InvalidArgumentException('problème, '.$n."n'est pas un nombre");
        }
        $n = floatval($n / 100);
        $n=sprintf("%01.".$digit."f", $n);
        return str_replace('.', ',', $n);
    }

    //@codeCoverageIgnoreStart
    /**
     *renvoie un nombre de mot de la chaine
     *
     * @param string  $s     la chaine original
     * @param integer $nb    le nombre de mots
     * @param integer $debut le numero du premier mot a garder
     * @return string
     *
     */
    public static function extract_mots($s, $nb=null, $debut=0) {
        $data = explode(" ", $s);
        $r = "";
        // fonction pour enlever le cas des doubles espaces
        foreach ($data as $k => $l) {
            if (trim($l) === "") {
                unset($data[$k]);
            } else {
                $data[$k] = trim($l);
            }
        }
        $data = explode(" ", implode(" ", $data));
        // si nb est null, on le fait jusqu'a la fin avec les ajustement du a $debut
        if ($nb === null) {
            $nb = count($data) - $debut + 2;
        }
        for ($i = $debut; $i < $nb + 1; $i++) {
            $r = $r." ".$data[$i];
        }
        return trim($r);
    }

    // @codeCoverageIgnoreEnd
    /**
     * Cette fonction calcule une clé RIB a partir des informations bancaires
     * La fonction implemente l'algorithme de clé RIB
     * Une clé RIB n'est valable que si elle se trouve dans l'intervalle 01 - 97
     *
     * @param string $sCodeBanque   code unique de la banque
     * @param string $sCodeGuichet  code unique du guichet (agence ou se trouve le compte)
     * @param string $sNumeroCompte numéro du compte bancaire (peut contenir des lettres)
     * @return string clé rib calculée
     **/
    public static function calculerCleRib($sCodeBanque, $sCodeGuichet, $sNumeroCompte) {
        // Variables locales
        $iCleRib = 0;
        $sCleRib = '';

        // Calcul de la clé RIB a partir des informations bancaires
        $sNumeroCompte = strtr(strtoupper($sNumeroCompte), 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', '12345678912345678923456789');
        $iCleRib = 97 - (int) fmod(89 * $sCodeBanque + 15 * $sCodeGuichet + 3 * $sNumeroCompte, 97);

        // Valeur de retour
        if ($iCleRib < 10) {
            $sCleRib = '0'.(string) $iCleRib;
        } else {
            $sCleRib = (string) $iCleRib;
        }
        return $sCleRib;
    }

    //@codeCoverageIgnoreStart
    /**
     *
     * renvoi vers une url via les fonction header
     * @param string $url url vers qui on va rediriger
     * @return void envoie les headers
     */
    public static function redirection_header($url) {
        $url = 'http://'.$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')).'/'.$url;
        header('Request-URI: '.$url);
        header('Content-Location: '.$url);
        header('Location: '.$url, 301);
    }

    /**
     * ecrit fichier ini
     *
     * @param array  $assoc_arr (array imbriqué si sections)
     * @param string $path      chmin du fichier ini
     * @throws exception_parametre_invalide si problème fichier
     * @return void
     */
    public static function write_ini_file($assoc_arr, $path) {
        $content = "";
        if (is_array($assoc_arr[0])) {
            foreach ($assoc_arr as $key => $elem) {
                $content .= "[".$key."]\n";
                foreach ($elem as $key2 => $elem2) {
                    if (is_array($elem2)) {
                        $count = count($elem2);
                        for ($i = 0; $i < $count; $i++) {
                            $content .= $key2."[] = \"".$elem2[$i]."\"\n";
                        }
                    }
                    else if ($elem2 === "") {
                        $content .= $key2." = \n";
                    }else {
                        $content .= $key2." = \"".$elem2."\"\n";
                    }
                }
            }
        }
        else {
            foreach ($assoc_arr as $key => $elem) {
                if (is_array($elem)) {
                    $count = count($elem);
                    for ($i = 0; $i < $count; $i++) {
                        $content .= $key."[] = \"".$elem[$i]."\"\n";
                    }
                }
                else if ($elem === "") {
                    $content .= $key." = \n";
                } else {
                    $content .= $key." = \"".$elem."\"\n";
                }
            }
        }//end if
        $handle = fopen($path, 'w');
        if (!$handle) {
            throw new exception_parametre_invalide("le ficher $path ne peut etre ouvert");
        }
        if (!fwrite($handle, $content)) {
            throw new exception_parametre_invalide("le ficher $path ne peut etre écris");
        }
        fclose($handle);
    }

    /**
     * affiche le fichier xml
     *
     * @param simplexml $xml l'element xml a deboguer
     * @return string
     */
    public static function debogXML($xml) {
        $t = $xml->AsXML();
        $t = str_replace('><', '>'.N.'<', $t);
        highlight_string($t);
        return $t;
    }

    //@codeCoverageIgnoreEnd
}


/**
 * classe de connexion a mysql
 *
 */
class MySQLConnector {

    /**
     * permet le passage en mode debogage, si true , cela affiche la requete et le nombre de lignes affectés
     */
    public $debogagesql = false;

    /**
     * nom de la base de donnée
     *
     * @var public
     */
    public $dbname = '';

    private $link;

    public $total = false;

    public $total_query;
    /**
     * constructeur de la classe
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     */

    public function __construct($host, $user, $password, $database) {
        $this->link = mysqli_connect($host, $user, $password, $database);
        if (!$this->link) {
            throw new Exception("Impossible de se connecter : ".mysqli_error($this->link));
        }
        $this->dbname = $database;
        $total_query = array();
    }

    /**
     * ferme la connexion avec la bdd
     *
     * @return void
     */
    public function close() {
        $r = mysqli_close($this->link);
        if (!$r) {
            throw new Exception("erreur lors de la fermeture : ".mysqli_error($this->link));
        }
    }

    /**
     * function qui fait n'importe quelle query
     *
     * @param string $sql    la requete a faire
     * @param bool   $escape si true, cela va appeler mysqli_real_escape_string pour la requete. true par defaut
     * @return la requete
     * @version 1.2
     */
    public function q($sql, $escape=true) {
        if ($escape) {
            $sql = mysqli_real_escape_string($this->link, $sql);
        }
        if ($this->debogagesql) {
            printf("%s\n", $sql);
        }
        $result = mysqli_query($this->link, $sql);
        if (($this->debogagesql) && (mysqli_affected_rows($this->link) > 1)) {
            printf("lignes affectées: %d\n", mysqli_affected_rows($this->link));
            //echo("<BR>\n");
        }
        try {
            if (!$result) {
                throw new Exception(mysqli_error($this->link)." \n à la ligne ".xdebug_call_line($result).' du fichier '.xdebug_call_file()." à la fonction ".xdebug_call_function()."\n alors que la requete sql était \"$sql\"");
            }
            return $result;
        } catch (Exception $e) {
            throw new Exception('Caught exception: '.$e->getMessage());
            exit;
        }
    }

    /**
     * une methode qui appelle uniquement select le premier terme de la requete
     * @param string $query
     * @param bool   $escape si true, cela va appeler mysqli_real_escape_string pour la requete. true par defaut
     * @return string 1er terme de la requete
     */
    public function s($query, $escape=true) {
        $r = $this->q($query, $escape);
        $row = mysqli_fetch_row($r);
        return $row[0];
    }

    /**
     * liste les tables de la bases
     *
     * @return tables array
     */
    public function liste_tables() {
        $r = $this->q("SHOW TABLES FROM $this->dbname");
        $tables = array();
        while ($row = mysqli_fetch_row($r)) {
            $tables[] = $row[0];
        }
        return $tables;
    }

    public function exist($table, $id, $index='id') {
        $id = (int) $id;
        if ($id === 1 && $table === "ope") {
            return false;
        }
        try {
            $r = $this->q("select * from $table where $index = $id;", false);
        } catch (Exception $e) {
            echo $e;
        }
        if (mysqli_num_rows($r) > 0) {
            return true;
        }else {
            return false;
        }
    }

    /**
     * effaces toutes les tables de la base
     *
     * @return void
     */
    public function efface_tables() {
        $result = $this->q("SHOW TABLES FROM $this->dbname");
        while ($r = mysqli_fetch_row($result)) {
            $q = "DELETE from `$r[0]`";
            aff($q);
            $result2 = $this->q($q, false);
            //if ($this->debogagesql) echo "$r[0] est effacé.\n";
            if (!$result2) {
                throw new Exception("imposible d'effacer les tables \n en particulier".mysqli_error($this->link)." \n à la ligne ".xdebug_call_line().' du fichier '.xdebug_call_file()."à la fonction ".xdebug_call_function()."\n alors que la requete sql était ".$q);
            }
        }
        mysqli_free_result($result);
    }

    /**
     * insert les données dans la base de la table
     *
     * @param string $table nom de la table
     * @param array  $s tableau associatif avec comme cle les noms des champs ou rentrer les données
     * @return void
     */
    public function insert($table, $s) {
        foreach ($s as $k => $t) {
            if (is_numeric($t)) {
                $final[$k] = $t;
            }else if ($t === 'null'||$t === 'NULL'||$t === NULL) {
                $final[$k] = 'NULL';
            }else {
                $final[$k] = "'".$t."'";
            }
        }
        $key = implode(', ', array_keys($final));
        $val = implode(', ', $final);
        $q = "insert into $table ($key) values($val);";
        if (!$this->total) {
            $r = $this->q($q, false);
            if (!$r) {
                throw new Exception(mysqli_error($this->link)." \n à la ligne ".xdebug_call_line().' du fichier '.xdebug_call_file()." à la fonction ".xdebug_call_function()."\n");
            }
        }else {
            $this->total_query[$table][] = array(
                                                    'key' => $key,
                                                    'val' => '('.$val.')'
                                                    );
        }
    }

    public function save($table) {
        if (!isset($this->total_query[$table])) {
            throw new Exception("probleme de sauvegarde \n à la ligne ".xdebug_call_line().' du fichier '.xdebug_call_file()." à la fonction ".xdebug_call_function()."\n");
        }
        $val = '';
        $key = $this->total_query[$table][0]['key'];
        foreach ($this->total_query[$table] as $s) {
            if ($key === $s['key']) {
                if ($val === '') {
                    $val = $s['val'];
                }
                else{
                    echo $s['val'].N;
                    $val = $val.', '.N.$s['val'];
                }
            }
        }
        $q = "insert into $table ($key) values $val;";
        if (!mysqli_query($this->link, $q)) {
             throw new Exception(mysqli_error($this->link)." \n à la ligne ".xdebug_call_line().' du fichier '.xdebug_call_file()." à la fonction ".xdebug_call_function()."\n");
        }
        if ($this->debogagesql) {
            printf("%s\n", $q);
        }
        $this->total_query = '';
        $this->total = false;

    }

    public function tab($q) {
        $r = $this->q($q, false);
        if (!$r) {
            throw new Exception(mysqli_error($this->link)." \n à la ligne ".xdebug_call_line().' du fichier '.xdebug_call_file()." à la fonction ".xdebug_call_function()."\n");
        }
        $tab = array();
        while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
            if ($this->debogagesql) {
                var_dump($row);
            }
            $tab[] = $row;
        }
        return $tab;
    }

    public function ins($t) {
        if (is_numeric($t)) {
            if (!is_int($t)) {
                throw new exception_parametre_invalide("ce n'est pas un int");
            }
            if ((int) $t === 1) {
                return NULL;
            }else {
                return (int) $t;
            }
        }
        $t = utf8_decode((string) $t);
        return mysqli_real_escape_string($this->link, (string) $t);
    }

    /**
     * retourne une chaine en utf8
     *
     * @param string $t non ut8
     * @return string utf8
     */
    public function unins($t) {
        return iconv("iso-8859-1", "UTF-8", stripslashes($t));
    }
}

/**
 * Affiche le contenu d'une variable (pour débugage, pratique pour les booléens et les tableaux).
 *
 * @param string $nomvar le nom de la variable à tester (chaine)
 * @param mixed  $v la variable a afficher
 * @param bool   $html si on affiche avec entourage html
 * @return void
 */
function debog($nomvar, $v, $html=true) {
    $t = $v;
    if ($html) {echo "<div>\n <b>";}
    echo "\$$nomvar :";
    if ($html) {echo "</b>";}
    if ($html) {
        echo '<pre style = "margin:0 0 0 30px; border-left:1px #999 dotted; padding-left:5px;">';
        var_dump($t);
        echo '</pre></div>';
    }else{
        var_dump($t);
    }
}


/**
 * affiche $s plus retour à la ligne
 *
 * @param string $s
 * @return void
 */
function aff($s) {
    global $db;
    if (isset($db)){
        if ($db->debogagesql) {
            echo (string) $s.N;
        }
    } else {
        echo (string) $s.N;
    }
}
