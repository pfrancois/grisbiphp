<?php /* coding: utf-8 */

require_once 'PHPUnit/Framework.php';
require_once 'G:\zmws\_web.zmwsc\comptes\class\loader.php';

/**
 * Test class for sib.
 * Generated by PHPUnit on 2010-06-29 at 20:15:31.
 */
class sibTest extends PHPUnit_Framework_TestCase{
	/**
	 * @var sib
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		global $gsb_xml;
		global $gsb_ibs;
		//on prend le fichier de test et on remplace avec celui actuel
		copy('G:/zmws/_web.zmwsc/comptes/tests/fichiers/test_original.gsb','test.gsb');
		$gsb_xml=new xml('test.gsb');
		$gsb_xml->reload();
		$this->object=$gsb_ibs->get_by_id(2)->get_sub_by_id(1);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown(){
		@unlink('test.gsb');
	}

	public function testGet_mere()	{
		$this->assertEquals(2, $this->object->get_mere()->get_id());
	}
	public function testGet_id() {
		$this->assertEquals(1, $this->object->get_id());
	}
	public function testGet_nom() {
		$this->assertEquals("sous_imputation", $this->object->get_nom());
	}
	public function testSet_nom() {
		$this->object->set_nom('ceci est un test');
		$this->assertEquals('ceci est un test',$this->object->get_nom());
	}
	public function test_tostring(){
		$this->assertEquals("sib #1 'sous_imputation' de l'ib #2 'imputation_debit'",(string)$this->object);
	}
}