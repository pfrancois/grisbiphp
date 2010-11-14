<?php  /* coding: utf-8 */ 

require_once 'PHPUnit/Framework.php';
require_once 'G:\zmws\_web.zmwsc\comptes\class\loader.php';

/**
 * Test class for tier.
 * Generated by PHPUnit on 2010-05-31 at 14:11:28.
 */
class tierTest extends PHPUnit_Framework_TestCase{
	/**
	 * @var tier
	 */
	protected $object;
	protected $xml;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		global $gsb_xml;
		global $gsb_tiers;
	//on prend le fichier de test et on remplace avec celui actuel
		copy('G:/zmws/_web.zmwsc/comptes/tests/fichiers/test_original.gsb','test.gsb');
		$gsb_xml=new xml('test.gsb');
		$this->object=$gsb_tiers->get_by_id(1);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown(){
		@unlink('test.gsb');
	}

	/**
	 * Generated from @assert () == 1.
	 */
	public function testGet_id() {
		$this->assertEquals(1, $this->object->get_id());
	}

	/**
	 * Generated from @assert () == 'premier'.
	 */
	public function testGet_nom() {
		$this->assertEquals('premier', $this->object->get_nom());
	}

	/**
	 * Generated from @assert ('ceci est un test') set $this->object->get_nom().
	 */
	public function testSet_nom() {
		$this->object->set_nom('ceci est un test');
		$this->assertEquals('ceci est un test', $this->object->get_nom());
	}

	public function testSet_nom_vide() {
		$this->setExpectedException('exception_parametre_invalide');
		$this->object->set_nom('');
		$this->assertEquals('ceci est un test', $this->object->get_nom());
	}
		/**
	*test afin de verifier la possiblité d'effacer un tiers. renvoi exception car il existe dans une operation
	*/
	public function testDelete_integrite(){
		$this->setExpectedException('exception_integrite_referentielle');
		$this->object->delete();
	}
	/**
	 * test afin de verifier la possibilité d'effacer
	 */
	public function testDelete(){
		global $gsb_tiers;
		$gsb_tiers->get_by_id(5)->delete();
		$this->setExpectedException('Exception_no_reponse');
		$gsb_tiers->get_by_id(5);
	}

}