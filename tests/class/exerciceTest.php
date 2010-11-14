<?php  /* coding: utf-8 */ 

require_once 'PHPUnit/Framework.php';
require_once 'G:\zmws\_web.zmwsc\comptes\class\loader.php';

/**
 * Test class for exercice.
 * Generated by PHPUnit on 2010-08-17 at 17:00:23.
 */
class exerciceTest extends PHPUnit_Framework_TestCase{
	/**
	 * @var banque
	 */
	protected $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp(){
		global $gsb_xml;
		global $gsb_exercices;
	//on prend le fichier de test et on remplace avec celui actuel
		copy('G:/zmws/_web.zmwsc/comptes/tests/fichiers/test_original.gsb','test.gsb');
		$gsb_xml=new xml('test.gsb');
		$this->object=$gsb_exercices->get_by_id(5);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown(){
		@unlink('test.gsb');
	}
	public function testGet_id(){
		$this->assertEquals(5,$this->object->get_id());
	}
	public function testGet_nom(){
		$this->assertEquals('2010',$this->object->get_nom());
	}
	public function testGet_debut(){
		$this->assertEquals(util::datefr2time('1/1/2010'),$this->object->get_date_debut());
	}
	public function testGet_fin(){
		$this->assertEquals(util::datefr2time('31/12/2010'),$this->object->get_date_fin());
	}
	public function testaffiche(){
		$this->assertEquals(true,$this->object->is_affiche());
	}
	public function testaffiche2(){
		global $gsb_exercices;
		$this->assertEquals(false,$gsb_exercices->get_by_id(6)->is_affiche());
	}

}