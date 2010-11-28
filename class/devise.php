<?php /* coding: utf-8 */

class devise extends item {
	/**
	 * demande le caractere d'affichage de la devise
	 * @assert () == html_entity_decode("&#x20AC;","utf-8")
	 * @return string
	 */
	public function get_code() {
		return (string )$this->_item_xml['Code'] ;
	}
	/**
	 * demande le code iso de la devise
	 * @assert () == 'EUR'
	 * @return string
	 */
	public function get_isocode() {
		return (string )$this->_item_xml['IsoCode'] ;
	}
	/**
	 * demande la date du dernier change
	 * @return int timestamp
	 */
	public function get_date_dernier_change() {
		return (int)util::datefr2time($this->_item_xml['Date_dernier_change']) ;
	}
	/**
	 * demande le dernier taux de change
	 * @return float
	 */
	public function get_change() {
		$n=(string)$this->_item_xml['Change'];
		$n = str_replace(' ', '', $n) ;
		$n=str_replace(',', '.', $n) ;
		if (is_numeric($n)){
			$change=(float)$n;
		} else {
			//@codeCoverageIgnoreStart
			throw new InvalidArgumentException('probleme, '.$n."n'est pas un nombre");
			//@codeCoverageIgnoreEnd
		}
		if($change==0){
			$change=1;
		}
		return $change ;
	}
}
