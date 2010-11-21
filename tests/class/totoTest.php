<?php /* coding: utf-8 */

require_once 'PHPUnit/Framework.php';
require_once 'G:\zmws\_web.zmwsc\comptes\class\util.php';
date_default_timezone_set('Europe/Paris');

class totoTest extends PHPUnit_Framework_TestCase{
	protected $object;
	protected $xml;
	protected function setUp() {
		//on prend le fichier de test et on remplace avec celui actuel
		copy('G:/zmws/_web.zmwsc/comptes/tests/fichiers/test_original.gsb','test.gsb');
		$this->xml=new xml('test.gsb');
		$this->object=null;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown(){
		@unlink('test.gsb');
	}

	public function testdatefr2time_valide_01_01_2009(){
		$this->assertEquals(mktime(0,0,0,1,1,2009),util::datefr2time("01/01/2009"));
	}
}