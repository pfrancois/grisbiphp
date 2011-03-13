<?php /* coding: utf-8 */


require_once 'G:\zmws\_web.zmwsc\comptes\outils\bdd\functions.php';
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
	public function testdatefr2time_null1(){
		$this->assertEquals(null,util::datefr2time("0/0/00",true));
	}
	public function testdatefr2time_null2(){
		$this->assertEquals(null,util::datefr2time('',true));
	}
	public function testtime2date(){
        $this->assertEquals("2011-01-01 00:00:00", util::time2date(mktime(0,0,0,1,1,2011)) );
    }
	public function testdatefr2date(){
        $this->assertEquals("2011-01-01 00:00:00", util::datefr2date("01/01/2011") );
    }
    public function testdatefr2date2(){
        $this->assertEquals("2011-01-01 00:00:00", util::datefr2date("01012011") );
    }
    public function testdatefr2date3(){
        $this->assertEquals(null, util::datefr2date("") );
    }
    public function testdatefr2date4(){
        $this->assertEquals(null, util::datefr2date("0/0/0") );
    }
    public function testdatefr2date5(){
        $this->setExpectedException('InvalidArgumentException');
        util::datefr2date("31/13/2010");
    }

	public function test_fr2cent(){
		$this->assertEquals(100001,util::fr2cent('1 000,01'));
	}
	public function test_cent2fr_1(){
		$this->assertEquals('1000,0100000',util::cent2fr(100001));
	}
	public function test_cent2fr_2(){
		$this->assertEquals('1000,0000000',util::cent2fr(100000));
	}
	public function test_cent2fr_3(){
		$this->assertEquals('-2594,9100000',util::cent2fr(-259491));
	}
	/**
	 * permet de voir si lorsque on lui met des mauvaises données, il dit merde
	 * @expectedException InvalidArgumentException
	 */
	public function test_cent2fr_argument_non_decimal(){
		util::cent2fr('tptp');
	}
    public function test_fr2uk(){
        $this->assertEquals(1000,util::fr2uk('1000,0000000'));
    }
    public function test_fr2uk2(){
        $this->assertEquals(1000.25,util::fr2uk('1000,2500000'));
    }
    public function test_fr2uk3(){
        $this->setExpectedException('InvalidArgumentException');
        util::fr2uk('nbjhfk');
    }
    public function test_fr2uk4(){
        $this->assertEquals(-1000,util::fr2uk('-1000,0000000'));
    }
	/**
	 * permet de voir si lorsque on lui met des mauvaises donn�es, il dit merde
	 * @expectedException InvalidArgumentException
	 */
	public function test_centimes_argument_non_decimal(){
		util::fr2cent('tptp');
	}

	public function testget_page_param(){
		$_GET['toto']="ceci'est'u\"";
		$this->assertEquals('',util::get_page_param('atat'));
		$this->assertEquals("ceci'est'u\"",util::get_page_param('toto'));
	}

	public function test_rib(){
		$this->assertEquals('40', util::calculerCleRib('30001','01635','001234567890'));
	}
	public function test_rib2(){
		$this->assertEquals('05', util::calculerCleRib('30001','01635','00020430380'));
	}
}
