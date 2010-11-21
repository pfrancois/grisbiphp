<?php /* coding: utf-8 */


class banque extends item {
	/**
	 * demande le cib de la banque
	 * @return int
	 */
	public function get_code() {
		return (int)$this->_item_xml['Code'] ;
	}
	/**
	 * demande son adresse
	 * @return string
	 */
	public function get_adresse() {
		return (string )$this->_item_xml['Adresse'] ;
	}
	/**
	 * donne le telephone principal
	 * @return string
	 */
	public function get_tel() {
		return (string )$this->_item_xml['Tel'] ;
	}
	/**
	 * donne le mail principal
	 * @return string
	 */
	public function get_mail() {
		return (string )$this->_item_xml['Mail'] ;
	}
	/**
	 * donne l'adresse internet de la banque
	 * @return string
	 */
	public function get_site_web() {
		return (string )$this->_item_xml['Web'] ;
	}
	/**
	 * donne le nom du correspondant
	 * @return string
	 */
	public function get_correpondant() {
		return (string )$this->_item_xml['Nom_correspondant'] ;
	}
	/**
	 * donne le fax du correspondant
	 * @return string
	 */
	public function get_fax_correspondant() {
		return (string )$this->_item_xml['Fax_correspondant'] ;
	}
	/**
	 * donne le tel du correspondant
	 * @return string
	 */
	public function get_tel_correpondant() {
		return (string )$this->_item_xml['Tel_correspondant'] ;
	}
	/**
	 * donne le mel du correspondant
	 * @return string
	 */
	public function get_mail_correpondant() {
		return (string )$this->_item_xml['Mail_correspondant'] ;
	}
	/**
	 * donne les remarques concernant ce compte
	 * @return string
	 */
	public function get_notes() {
		return (string )$this->_item_xml['Remarques'] ;
	}
}