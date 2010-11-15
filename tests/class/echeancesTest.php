<?php /* coding: utf-8 */ 

require_once 'PHPUnit/Framework.php';
require_once 'G:\zmws\_web.zmwsc\comptes\class\loader.php';
/**
 * Test class for echeances.
 * Generated by PHPUnit on 2010-08-19 at 21:08:16.
 */
class echeancesTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var echeances
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp(){
		global $gsb_xml;
		global $gsb_echeances;
		//on prend le fichier de test et on remplace avec celui actuel
		copy('G:/zmws/_web.zmwsc/comptes/tests/fichiers/test_original.gsb','test.gsb');
		$gsb_xml=new xml('test.gsb');
		$gsb_xml->reload();
		$this->object = $gsb_echeances;
	}
	protected function tearDown(){
		@unlink('test.gsb');
	}
	/**
	 * test afin de voir si on recoit bien le categorie que l'on veut
	 */
	public function testGet_by_id()	{
		$r=$this->object->get_by_id(2);
		$this->assertType('echeance',$r);
		$this->assertEquals(2,$r->get_id());
	}
	/**
	 * test afin de voir si en lui donnant un id de categorie inexistant il renvoit bien une exception
	 * @expectedException exception_not_exist
	 */
	public function testGet_by_id_id_inexistant()	{
		$r=$this->object->get_by_id(235400);
	}

	/**
	 * test afin de voir si je peux recuperer le numero du categorie inexistant
	 */
	public function testGet_by_id_id_inexistant_recup_numero_cpt()	{
		try{
			$r=$this->object->get_by_id(235400);
		}catch (exception_not_exist $e) {
			$this->assertEquals(235400,$e->id);
		}
	}
	/**
	 * test afin de voir si en lui donnant une valeur incorrect (une chaine au lieur d'un chiffre) il renvoit bien une exception
	* @expectedException exception_parametre_invalide
	 */
	public function testGet_by_id_var_incorrect()	{
		$r=$this->object->get_by_id('toto');
	}

	public function testGet_next()	{
		$r=$this->object->get_next();
		$this->assertEquals(4,$r);
	}

	/**
	* verifie que l'implementation de count marche,attention, souvent on commence a 0
	*/
	public function test_count(){
		$this->assertEquals(2,count($this->object));
	}
	/**
	* test que ca nous renvoit un iter comme on veut
	*/
	public function testiter_categories(){
		$x=$this->object->iter();
		foreach ($x as $y){
			$this->assertType('echeance',$y);
		}
		$this->assertEquals(2,count($x));
	}
}