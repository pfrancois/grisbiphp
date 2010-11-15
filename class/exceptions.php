<?php /* coding: utf-8 */ 

// @codeCoverageIgnoreStart
class Exception_base extends Exception {
    public $message = 'Unknown exception' ; // Exception message
    private $string ; // Unknown
    public $file ; // Source filename of exception
    public $line ; // Source line of exception
    private $trace ; // Unknown

    public function __construct($message = null) {
        if (is_null($message)) {
            $message = " in {$this->file}({$this->line})\n------\n TRACE:\n------\n {$this->getTraceAsString()}" ;
        }
        parent::__construct($message) ;
    }

    public function __toString() {
        return get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n {$this->getTraceAsString()}" ;
    }
    public function message_l() {
        return get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n {$this->getTraceAsString()}" ;
    }
}
/**
 * class renvoye si pas de reponse
 *
 * @version 1.0.0
 */
class Exception_no_reponse extends Exception_base {
    public function __construct($message = null) {
        parent::__construct($message) ;
    }
}

/**
 * class exception_0
 * c'est une exception afin de pouvoir gerer les "numero 0" dans les objects.
 */
class Exception_numero_zero extends Exception_base{
    public function __construct($message = null) {
        parent::__construct($message) ;
    }	
}
/**
 * class renvoye si plusieurs reponse mais une seule attendu
 *
 * @version 1.0.0
 */
class Exception_many_reponse extends Exception_base {
    public function __construct($message = null) {
        parent::__construct($message) ;
    }
}
/**
 * class renvoye si lors de la demande, l'item demande n'existe pas
 * @version 1.0.1 rajout de la possibilite de demander id et type
 */
class exception_not_exist extends Exception_no_reponse {
    public $type ;
    public $id ;

    public function __construct($type, $id) {
        $this->id = $id ;
        $this->type = $type ;
        parent::__construct("$type '$id' n'existe pas") ;
    }
}

/**
* class exception_parametre_invalide
*/

class exception_parametre_invalide extends Exception_base {
    public function __construct($message = null) {
        if (is_null($message)) {
            $message = " variable $message in {$this->file}({$this->line})\n------\n TRACE:\n------\n {$this->getTraceAsString()}" ;
        }
        parent::__construct($message) ;
    }
}
/**
 * class renvoye si lors de la demande, l'item demande existe

 * @version 1.0.1 rajout de la possibilite de demander id et type
 */
class exception_index extends Exception_base {
    public $type ;
    public $id ;

    public function __construct($type, $id) {
        $this->id = $id ;
        $this->type = $type ;
        parent::__construct("$type '$id' existe  et donc ne peut etre utilis&eacute;") ;
    }
}

/**
 * class renvoye si lors de la demande, probleme d'integrite referentielle

 * @version 1.0.1 rajout de la possibilite de demander id et type
 */
class exception_integrite_referentielle extends Exception_base {
    public $type_demande ;
    public $id ;
    public $type_incluant ;
    public $id2 ;
    /**
     * exception_integrite_referentielle::__construct()
     *
     * @param mixed $type_demande type qui n'existe pas
     * @param mixed $id id du type qui n'existe pas
     * @param mixed $type_incluant type existant qui pose probleme
     * @param mixed $id2 id du type existant qui pose probleme
     * @return void
     */
    public function __construct($type_demande, $id, $type_incluant, $id2) {
        $this->id = $id ;
        $this->type_demande = $type_demande ;
        $this->type_incluant = $type_incluant ;
        $this->id2 = $id2 ;
        parent::__construct("$type_demande '$id' ne peut etre efface car il est reference dans le $type_incluant '$id2'") ;
    }
}
// @codeCoverageIgnoreEnd