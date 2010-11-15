<?php /* coding: utf-8 */ 

require_once 'PHPUnit/Framework.php';
require_once 'G:\zmws\_web.zmwsc\comptes\class\loader.php';

class operationsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var comptes
	 */
	protected $c;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()	{
		global $gsb_xml;
		global $gsb_operations;
		//on prend le fichier de test et on remplace avec celui actuel
		copy('G:/zmws/_web.zmwsc/comptes/tests/fichiers/test_original.gsb','test.gsb');
		$gsb_xml=new xml('test.gsb');
		$gsb_xml->reload();
		$this->c = $gsb_operations;
	}
/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown(){
		@unlink('test.gsb');
	}
	/**
	 * test afin de voir si on recoit bien le numero

	 */
	public function testGet_by_id()	{
		$r=$this->c->get_by_id(1);
		$this->assertType('operation',$r);
		$this->assertEquals(1,$r->get_id());
	}
	/**
	 *verifie que get_next renvoie bien
	 */
	public function testGet_next()	{
		$r=$this->c->get_next();
		$this->assertEquals(15,$r);
	}

	/**
	* verifie que l'implementation de count marche
	*/
	public function test_count(){
		$this->assertEquals(13,count($this->c));
	}
	/**
	* test que ca nous renvoit un iter comme on veut
	*/
	public function testiter_ope(){
		$x=$this->c->iter();
		foreach ($x as $y){
			$this->assertType('operation',$y);
		}
		$this->assertEquals(13,count($x));
	}
	/**
	 * test afin de voir si en lui donnant un id de compte inexistant il renvoit bien une exception
	 * @expectedException exception_not_exist
	 */
	public function testGet_by_id_id_inexistant()	{
		$r=$this->c->get_by_id(235400);
	}

	/**
	 * test afin de voir si je peux recuperer le numero du compte inexistant
	 */
	public function testGet_by_id_id_inexistant_recup_numero_cpt()	{
		try{
			$r=$this->c->get_by_id(235400);
		}catch (exception_not_exist $e) {
			$this->assertEquals(235400,$e->id);
		}
	}
	/**
	 * test afin de voir si en lui donnant une valeur incorrect (une chaine au lieur d'un chiffre) il renvoit bien une exception
	* @expectedException exception_parametre_invalide
	 */
	public function testGet_by_id_var_incorrect()	{
		$r=$this->c->get_by_id('toto');
	}

}