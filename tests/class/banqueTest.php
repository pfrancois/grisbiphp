<?php
//$Id: banqueTest.php 30 2010-08-20 15:22:44Z pfrancois $
require_once 'PHPUnit/Framework.php';

require_once 'G:\zmws\_web.zmwsc\comptes\class\loader.php';

/**
 * Test class for banque.
 * Generated by PHPUnit on 2010-08-17 at 14:41:47.
 */
class banqueTest extends PHPUnit_Framework_TestCase {
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
		global $gsb_banques;
	//on prend le fichier de test et on remplace avec celui actuel
		copy('G:/zmws/_web.zmwsc/comptes/tests/fichiers/test_original.gsb','test.gsb');
		$gsb_xml=new xml('test.gsb');
		$this->object=$gsb_banques->get_by_id(1);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown(){
		unlink('test.gsb');
	}
	public function testGet_id()
	{
		$this->assertEquals(1,$this->object->get_id(1));
	}
	public function testGet_nom()
	{
		$this->assertEquals('banque test',$this->object->get_nom(1));
	}

	/**
	 * Generated from @assert (1) == 30003.
	 */
	public function testGet_code()
	{
		$this->assertEquals(30003,$this->object->get_code(1));
	}

	/**
	 * Generated from @assert (1) == "rue du paradis".
	 */
	public function testGet_adresse()
	{
		$this->assertEquals("rue du paradis",$this->object->get_adresse(1)
		);
	}

	/**
	 * Generated from @assert (1) == 0123456789.
	 */
	public function testGet_tel()
	{
		$this->assertEquals("0123456789",$this->object->get_tel(1));
	}

	/**
	 * Generated from @assert (1) == "toto@toto.com".
	 */
	public function testGet_mail()
	{
		$this->assertEquals("toto@toto.com",$this->object->get_mail(1));
	}

	/**
	 * Generated from @assert (1) == "http:\\\\www.banque.fr".
	 */
	public function testGet_site_web()
	{
		$this->assertEquals("http:\\\\www.banque.fr",$this->object->get_site_web(1));
	}
	/**
	 * Generated from @assert (1) == "madame".
	 */
	public function testGet_correpondant()
	{
		$this->assertEquals("madame", $this->object->get_correpondant(1));
	}

	/**
	 * Generated from @assert (1) == "0123456789".
	 */
	public function testGet_fax_correspondant()
	{
		$this->assertEquals("6789012345",$this->object->get_fax_correspondant(1));
	}

	/**
	 * Generated from @assert (1) == "0123456789".
	 */
	public function testGet_tel_correpondant()
	{
		$this->assertEquals("0987654321", $this->object->get_tel_correpondant(1));
	}

	/**
	 * Generated from @assert (1) == "tata@toto.com".
	 */
	public function testGet_mail_correpondant()
	{
		$this->assertEquals("tata@toto.com", $this->object->get_mail_correpondant(1));
	}

	/**
	 * Generated from @assert (1) == "voici qq remarques".
	 */
	public function testGet_notes()
	{
		$this->assertEquals( "voici qq remarques", $this->object->get_notes(1));
	}
}
?>
