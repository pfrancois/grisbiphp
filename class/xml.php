<?php  /* coding: utf-8 */

class xml {
    /**
     * ficher xml simplexmlise
     * @var simplexml
     */
    protected $_xml_complet ;
    /**
     * le nom du fichier xml source
     * @var string
     */
    protected $_xmlfile ;
    /**
     * variable qui permet de savoir si le fichier est le server free.
     * il n'autorise pas LIBXML_COMPACT
     * @var bool
     */
	protected $_sur_free;
    /**
     * fonction de creation de la classe
     *
     * @param string $_xmlfile nom du fichier grisbi
     * @throws InvalidArgumentException si le fichier n'existe pas
     */
    public function __construct($_xmlfile, $sur_free = false) {
        $this->xmlfile = $_xmlfile ;
        if (!file_exists($_xmlfile)) {
            throw new InvalidArgumentException("le fichier '$_xmlfile' n'existe pas") ;
        } else {
            if ($sur_free) {
                $this->_xml_complet = simplexml_load_file($_xmlfile) ;
            } else {
                $this->_xml_complet = simplexml_load_file($_xmlfile, 'SimpleXMLElement',
                    LIBXML_COMPACT) ;
            }
        }
        $this->_surfree=$sur_free;
        if ($this->_xml_complet->Generalites->Version_fichier != '0.5.0') {
            throw new UnexpectedValueException("le fichier n'est pas au bon format") ;
        }
    }

    /**
     * renvoi un tableau de la classe demandée suite a la requete demande
     * @param string $chaine la chaine xpath qui renvoit la collection
     * @param string $type le nom de la classe a renvoyer
     * @param SimpleXMLElement $_xml si rempli cela fait la requete xpath sur le xml fourni sinon ca utilise le xml habituel
     * @throws Exception_no_reponse si pas de reponse
     * @return array
     */
    public function iter_class($chaine, $type, SimpleXMLElement $_xml = null) {
        if ($_xml === null) {
            $_xml = $this->_xml_complet ;
        }
        try {
            $iter = $this->xpath_iter($chaine, $_xml) ;
            $req = array() ;
            foreach ($iter as $object) {
                $req[] = new $type($object) ;
            }
            return $req ;
        }
        catch (Exception_no_reponse $except) {
            return array() ;
        }
    }
    /**
     * renvoie un tableau relatif à la chaine xpath
     * @param string $chaine la chaine xpath
     * @param SimpleXMLElement, si rempli cela fait la requete xpath sur le xml fourni sinon ca utilise le xml habituel
     * @throws Exception_no_reponse si pas de reponse
     * @return array
     */
    public function xpath_iter($chaine, SimpleXMLElement $_xml = null) {
        if ($_xml === null) {
            $_xml = $this->_xml_complet ;
        }
        $req = $_xml->xpath($chaine) ;
        if (empty($req)) {
            throw new Exception_no_reponse($chaine) ;
        }
        return $req ;
    }
    /**
     * renvoie les resultats relatif à la chaine xpath
     * @param string $chaine la chaine xpath
     * @param SimpleXMLElement $_xml element xml sur laquelle on fait la requete
     * @return SimpleXMLElement
     * @throws Exception_no_reponse si pas de reponse
     * @throws Exception_many_reponse si plusieurs reponse
     */
    public function xpath_uniq($chaine, SimpleXMLElement $_xml = null) {
        if ($_xml === null) {
            $_xml = $this->_xml_complet ;
        }
        $req = $_xml->xpath($chaine) ;
        if (empty($req)) {
            throw new Exception_no_reponse($chaine) ;
        }
        if (count($req) > 1) {
            throw new Exception_many_reponse($chaine) ;
        }
        $req = $req[0] ;
        return $req ;
    }
    /**
     * renvoie le nom du fichier xml
     * @return string le nom du fichier xml
     */
    public function get_xmlfile() {
        return realpath($this->xmlfile) ;
    }
    /**
     * sauvegarde le fichier xml
     * @since 20100425 possibilitee de choisir le nom du fichier de sauvegarde
     * @throw Exception si erreur de fichier
     */
    public function save($_xmlfile = '') {
        if ($_xmlfile <> '') {
            if (!$this->_xml_complet->asXML($_xmlfile)) {
                // @codeCoverageIgnoreStart
                throw new Exception("erreur dans l'ecriture du fichier $_xmlfile") ;
                // @codeCoverageIgnoreEnd
            }
        } else {
            if (!$this->_xml_complet->asXML($this->xmlfile)) {
                // @codeCoverageIgnoreStart
                throw new Exception("erreur dans l'ecriture du fichier {$this->xmlfile}") ;
                // @codeCoverageIgnoreEnd
            }
        }
    }
    /**
     * retourne le fichier simplexmlise en entier
     *
     * @return SimpleXMLElement
     */
    public function get_xml() {
        return $this->_xml_complet ;
    }
    /**
     * recharge le fichier comme il etait
     * @throws InvalidArgumentException si le fichier n'existe pas
     * @throws UnexpectedValueException si le fichier n'est pas au bon format
     */
    public function reload() {
        $_xmlfile = $this->xmlfile ;
        if (!file_exists($_xmlfile)) {
            // @codeCoverageIgnoreStart
            throw new InvalidArgumentException("le fichier '$_xmlfile' n'existe pas") ;
            // @codeCoverageIgnoreEnd
        } else {
            if ($this->_sur_free) {
                $this->_xml_complet = simplexml_load_file($_xmlfile) ;
            } else {
                $this->_xml_complet = simplexml_load_file($_xmlfile, 'SimpleXMLElement',
                    LIBXML_COMPACT) ;
            }
        }
        if ($this->_xml_complet->Generalites->Version_fichier != '0.5.0') {
            // @codeCoverageIgnoreStart
            throw new UnexpectedValueException("le fichier n'est pas au bon format") ;
            // @codeCoverageIgnoreEnd
        }
    }
}
