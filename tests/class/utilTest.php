<?php  /* coding: utf-8 */ 

require_once 'PHPUnit/Framework.php';
require_once 'G:\zmws\_web.zmwsc\comptes\class\util.php';
date_default_timezone_set('Europe/Paris');
class utilTest extends PHPUnit_Framework_TestCase {


	public function testdatefr2time_valide_01_01_2009(){
		$this->assertEquals(mktime(0,0,0,1,1,2009),util::datefr2time("01/01/2009"));
	}
	public function testdatefr2time_valide_01_01_2009_sans_slash(){
		$this->assertEquals(mktime(0,0,0,1,1,2009),util::datefr2time("01012009"));
	}
	public function testdatefr2time_valide_01_01_2009_en_reduit(){
		$this->assertEquals(mktime(0,0,0,1,1,2009),util::datefr2time("1/1/09"));
	}
	public function test_add_date(){
		$this->assertEquals(mktime(0,0,0,1,2,2009),util::add_date(util::datefr2time("01/01/2009"),1));
	}

	public function testdatefr2time_valide_1_1_2009(){
		$this->assertEquals(mktime(0,0,0,1,1,2009),util::datefr2time("1/1/2009"));
	}
	public function test_datefr2time_invalide(){
		$this->setExpectedException('InvalidArgumentException');
		util::datefr2time('32/12/2009');
	}
	public function test_fr2cent_et_cent2fr(){
		$this->assertEquals(100001,util::fr2cent('1 000,01'));
		$this->assertEquals('1000,0100000',util::cent2fr(100001));
		$this->assertEquals('1000,0000000',util::cent2fr(100000));
		$this->assertEquals('-2594,9100000',util::cent2fr(-259491));
	}
	/**
	 * permet de voir si lorsque on lui met des mauvaises données, il dit merde
	 * @expectedException InvalidArgumentException
	 */
	public function test_cent2fr_argument_non_decimal(){
		util::cent2fr('tptp');
	}
	/**
	 * permet de voir si lorsque on lui met des mauvaises données, il dit merde
	 * @expectedException InvalidArgumentException
	 */
	public function test_centimes_argument_non_decimal(){
		util::fr2cent('tptp');
	}

	public function testget_page_param(){
		$_GET['toto']=addslashes("ceci'est'u\"");
		$this->assertEquals('',util::get_page_param('atat'));
		$this->assertEquals("ceci'est'u\"",util::get_page_param('toto'));
		set_magic_quotes_runtime(false);
		$this->assertEquals("ceci'est'u\"",util::get_page_param('toto'));
	}
	public function test_dump(){
		$this->assertEquals("<pre>1</pre>", util::dump(1));
		$this->assertEquals("<pre>Array\n(\n    [0] => 1\n    [1] => 2\n)\n</pre>", util::dump(1,2));
	}
	
	public function test_rib(){
		$this->assertEquals('40', util::calculerCleRib('30001','01635','001234567890'));
	}
	public function test_rib2(){
		$this->assertEquals('05', util::calculerCleRib('30001','01635','00020430380'));
	}
}