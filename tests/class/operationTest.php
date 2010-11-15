<?php  /* coding: utf-8 */ 


require_once 'PHPUnit/Framework.php';
require_once 'G:\zmws\_web.zmwsc\comptes\class\loader.php';

/**
 * Test class for operation.
 * Generated by PHPUnit on 2010-05-13 at 11:37:03.
 */
class operationTest extends PHPUnit_Framework_TestCase {

    /**
     * @var operation
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        global $gsb_xml;
        global $gsb_operations;
        //on prend le fichier de test et on remplace avec celui actuel
        copy('G:/zmws/_web.zmwsc/comptes/tests/fichiers/test_original.gsb', 'test.gsb');
        $gsb_xml = new xml('test.gsb');
        $gsb_xml->reload();
        $this->object = $gsb_operations->get_by_id(1);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        @unlink('test.gsb');
    }

    /**
     * Generated from @assert () == 2465.
     */
    public function testGet_id() {
        $this->assertEquals(1, $this->object->get_id());
    }

    /**
     * Generated from @assert () == '7/10/2008'.
     */
    public function testGet_date() {
        $this->assertEquals(util::datefr2time('28/5/2010'), $this->object->get_date());
    }

    /**
     * Generated from @assert () == "-12300".
     */
    public function testGet_montant() {
        $this->assertEquals(-12300, $this->object->get_montant());
    }

    /**
     * Generated from @assert () == false.
     */
    public function testIs_ventilee() {
        $this->assertEquals(false, $this->object->is_ventilee());
    }

    /**
     * Generated from @assert () == false.
     */
    public function testIs_ventilee2() {
        global $gsb_operations;
        $this->assertEquals(true, $gsb_operations->get_by_id(2)->is_ventilee());
    }

    /**
     * Generated from @assert () == "ope avec type avec numero".
     */
    public function testGet_notes() {
        $this->assertEquals("ope avec type avec numero", $this->object->get_notes());
    }

    /**
     * Generated from @assert () == "".
     */
    public function testGet_num_chq() {
        $this->assertEquals("12345", $this->object->get_num_chq());
    }

    /**
     * Generated from @assert () == operation::RIEN
     */
    public function testGet_statut_pointage() {
        $this->assertEquals(rapp::RIEN, $this->object->get_statut_pointage());
    }

    public function testGet_statut_pointage2() {
        global $gsb_operations;
        $this->assertEquals(rapp::POINTEE, $gsb_operations->get_by_id(6)->get_statut_pointage());
    }

    public function testGet_statut_pointage3() {
        global $gsb_operations;
        $this->assertEquals(rapp::RAPPROCHEE, $gsb_operations->get_by_id(5)->get_statut_pointage());
    }

    /**
     * Generated from @assert () == false.
     */
    public function testIs_planifie() {
        $this->assertEquals(false, $this->object->is_planifie());
    }

    public function testIs_planifie2() {
        global $gsb_operations;
        $this->assertEquals(true, $gsb_operations->get_by_id(13)->is_planifie());
    }

    /**
     * Generated from @assert () == false.
     */
    public function testIs_ventilation() {
        $this->assertEquals(false, $this->object->is_ventilation());
    }

    public function testIs_ventilation2() {
        global $gsb_operations;
        $this->assertEquals(true, $gsb_operations->get_by_id(3)->is_ventilation());
    }

    public function testIs_virement() {
        $this->assertEquals(false, $this->object->is_virement());
    }

    public function testIs_virement2() {
        global $gsb_operations;
        $this->assertEquals(true, $gsb_operations->get_by_id(12)->is_virement());
    }

    public function testGet_compte() {
        $c = $this->object->get_compte();
        $this->assertType('compte', $c);
        $this->assertEquals(0, $c->get_id());
    }

    public function testGet_tiers() {
        $c = $this->object->get_tiers();
        $this->assertType('tier', $c);
        $this->assertEquals(1, $c->get_id());
    }

    public function testGet_categorie() {
        $c = $this->object->get_categorie();
        $this->assertType('categorie', $c);
        $this->assertEquals(6, $c->get_id());
    }

    public function testGet_scat() {
        $c = $this->object->get_scat();
        $this->assertType('scat', $c);
        $this->assertEquals(2, $c->get_id());
    }

    public function testGet_moyen() {
        $c = $this->object->get_moyen();
        $this->assertType('moyen', $c);
        $this->assertEquals(5, $c->get_id());
        $this->assertEquals(0, $c->get_mere()->get_id());
    }

    public function testGet_rapp() {
        global $gsb_operations;
        $this->object = $gsb_operations->get_by_id(5);
        $c = $this->object->get_rapp();
        $this->assertType('rapp', $c);
        $this->assertEquals(1, $c->get_id());
    }

    public function testGet_operation_contrepartie() {
        global $gsb_operations;
        $this->object = $gsb_operations->get_by_id(9);
        $c = $this->object->get_operation_contrepartie();
        $this->assertType('operation', $c);
        $this->assertEquals(10, $c->get_id());
    }

    public function testGet_cpt_contrepartie() {
        global $gsb_operations;
        $this->object = $gsb_operations->get_by_id(10);
        $c = $this->object->get_cpt_contrepartie();
        $this->assertType('compte', $c);
        $this->assertEquals(2, $c->get_id());
    }

    public function testGet_ope_mere() {
        global $gsb_operations;
        $this->object = $gsb_operations->get_by_id(3);
        $c = $this->object->get_operation_mere();
        $this->assertType('operation', $c);
        $this->assertEquals(2, $c->get_id());
    }

    public function testget_exercice() {
        $this->assertType('exercice', $this->object->get_exercice());
        $this->assertEquals("2010", $this->object->get_exercice()->get_nom());
    }

    public function testget_ib() {
        $this->assertType('ib', $this->object->get_ib());
        $this->assertEquals("2", $this->object->get_ib()->get_id());
    }

    public function testget_sib() {
        $this->assertType('sib', $this->object->get_sib());
        $this->assertEquals("1", $this->object->get_sib()->get_id());
    }

    public function testSet_compte() {
        global $gsb_operations;
        $this->object->set_compte(2);
        $operation = $gsb_operations->get_by_id(2);
        $this->assertEquals('2',$operation->get_id());
    }

    /**
     * Generated from @assert ('r') throws exception_parametre_invalide.
     * @expectedException exception_parametre_invalide

     */
    public function testSet_date_invalide() {
        $this->object->set_date('r');
    }

    public function testSet_date2() {
        $date = util::datefr2time("01/01/2009");
        $this->object->set_date($date);
        $this->assertEquals(util::datefr2time("01/01/2009"), $this->object->get_date());
    }

    /**
     * Generated from @assert ('r') throws exception_parametre_invalide.
     * @expectedException exception_parametre_invalide
     */
    public function testSet_montant_invalide() {
        $this->object->set_montant('r');
    }

    public function testSet_montant() {
        $this->object->set_montant(12100);
        $this->assertEquals(12100, $this->object->get_montant());
    }

    public function test_set_tiers() {
        global $gsb_tiers;
        $this->object->set_tiers($gsb_tiers->get_by_id(2));
        $this->assertEquals(2, $this->object->get_tiers()->get_id());
    }

    public function testSet_moyen() {
        $this->object->set_moyen($this->object->get_compte()->get_moyen_by_id(2));
        $this->assertEquals(2, $this->object->get_moyen()->get_id());
    }

    /**
     * @expectedException exception_no_reponse
     */
    public function testSet_moyen2() {
        global $gsb_comptes;
        $this->object->set_moyen($gsb_comptes->get_by_id(1)->get_moyen_by_id(2));
    }

    /**
     * Generated from @assert ('r') throws exception_parametre_invalide.
     * @expectedException exception_parametre_invalide
     */
    public function testSet_statut_pointage_invalide() {
        $this->object->set_statut_pointage('4');
    }

    public function testSet_statut_pointage_valide() {
        $this->object->set_statut_pointage(2);
        $this->assertEquals(2, $this->object->get_statut_pointage());
    }

    /**
     * Generated from @assert ('r') throws exception_parametre_invalide.
     * @expectedException exception_parametre_invalide
     */
    public function testSet_planifie_invalide() {
        $this->object->set_planifie('r');
    }

    public function testSet_planifie() {
        $this->object->set_planifie(true);
        $this->assertEquals(true, $this->object->is_planifie());
    }

    public function testSet_planifie2() {
        $this->object->set_planifie(false);
        $this->assertEquals(false, $this->object->is_planifie());
    }

    public function testSet_ope_mere() {
        global $gsb_operations;
        $this->object->set_ope_mere($gsb_operations->get_by_id(2));
        $this->assertType('operation', $this->object->get_operation_mere());
        $this->assertEquals(2, $this->object->get_operation_mere()->get_id());
    }

    public function testSet_notes() {
        $this->object->set_notes('TT');
        $this->assertEquals('TT', $this->object->get_notes());
    }

    public function testSet_num_chq() {
        $this->object->set_num_chq('TT');
        $this->assertEquals('TT', $this->object->get_num_chq());
    }

    public function testto_string() {
        $this->assertEquals("operation n 1", (string) $this->object);
    }

    public function testset_categorie() {
        global $gsb_categories;
        $this->object->set_categorie($gsb_categories->get_by_id(5));
        $this->assertType('categorie', $this->object->get_categorie());
        $this->assertEquals(5, $this->object->get_categorie()->get_id());
    }

    public function testset_scat() {
        global $gsb_categories;
        $this->object->set_categorie($gsb_categories->get_by_id(6));
        $this->object->set_scat($this->object->get_categorie()->get_sub_by_id(3));
        $this->assertType('scat', $this->object->get_scat());
        $this->assertEquals(6, $this->object->get_categorie()->get_id());
        $this->assertEquals(3, $this->object->get_scat()->get_id());
    }

    public function testset_scat_version_raccourci() {
        global $gsb_categories;
        $this->object->set_scat($gsb_categories->get_by_id(7)->get_sub_by_id(3));
        $this->assertType('scat', $this->object->get_scat());
        $this->assertEquals(7, $this->object->get_categorie()->get_id());
        $this->assertEquals(3, $this->object->get_scat()->get_id());
    }

    public function test_set_ventilee() {
        $this->object->set_ventilee(true);
        $this->assertEquals(true, $this->object->is_ventilee(true));
    }

    public function test_set_ventilee2() {
        $this->object->set_ventilee(false);
        $this->assertEquals(false, $this->object->is_ventilee(true));
    }

    /**
     * @expectedException exception_parametre_invalide
     */
    public function test_set_ventilee3() {
        $this->object->set_ventilee('toto');
        $this->assertEquals(false, $this->object->is_ventilee(true));
    }

    public function test_set_rapp() {
        global $gsb_rapps;
        $this->object->set_rapp($gsb_rapps->get_by_id(1));
        $this->assertEquals(1, $this->object->get_rapp()->get_id());
    }

    public function test_set_exercice() {
        global $gsb_exercices;
        $this->object->set_exercice($gsb_exercices->get_by_id(6));
        $this->assertEquals(6, $this->object->get_exercice()->get_id());
    }

    public function test_set_operation_contrepartie() {
        global $gsb_operations;
        $this->object->set_operation_contrepartie($gsb_operations->get_by_id(2));
        $this->assertEquals(2, $this->object->get_operation_contrepartie()->get_id());
    }

    public function test_iter_ventilee() {
        global $gsb_operations;
        $x = $gsb_operations->get_by_id(2)->iter_operation_ventilees();
        foreach ($x as $y) {
            $this->assertType('operation', $y);
        }
        $this->assertEquals(2, count($x));
    }

    public function test_iter_ventilee_mais_pas_ventilee() {
        global $gsb_operations;
        $this->setExpectedException('Exception_no_reponse');
        $x = $gsb_operations->get_by_id(1)->iter_operation_ventilees();
    }

    public function test_get_nom() {
        $this->setExpectedException('exception_base');
        $this->object->get_nom();
    }

    public function test_set_nom() {
        $this->setExpectedException('exception_base');
        $this->object->set_nom('toto');
    }

    public function testset_ib() {
        global $gsb_ibs;
        $this->object->set_ib($gsb_ibs->get_by_id(1));
        $this->assertType('ib', $this->object->get_ib());
        $this->assertEquals(1, $this->object->get_ib()->get_id());
    }

    public function testset_sib() {
        global $gsb_ibs;
        $this->object->set_ib($gsb_ibs->get_by_id(2));
        $this->object->set_sib($this->object->get_ib()->get_sub_by_id(1));
        $this->assertType('sib', $this->object->get_sib());
        $this->assertEquals(2, $this->object->get_ib()->get_id());
        $this->assertEquals(1, $this->object->get_sib()->get_id());
    }

    public function testset_sib_version_raccourci() {
        global $gsb_ibs;
        $this->object->set_sib($gsb_ibs->get_by_id(3)->get_sub_by_id(1));
        $this->assertType('sib', $this->object->get_sib());
        $this->assertEquals(3, $this->object->get_ib()->get_id());
        $this->assertEquals(1, $this->object->get_sib()->get_id());
    }

    public function test_delete() {
        global $gsb_operations;
        $id = $this->object->get_id();
        $compte = $this->object->get_compte();
        $this->object->delete();
        $this->assertEquals(14, $gsb_operations->get_next() - 1);
        $this->assertEquals(8, $compte->get_nb_ope());
        $this->setExpectedException('Exception_not_exist');
        $gsb_operations->get_by_id($id);
    }

    public function test_delete2() {
        global $gsb_operations;
        $compte = $this->object->get_compte();
        $ope = $gsb_operations->get_by_id(14);
        $ope->delete();
        $this->assertEquals(13, $gsb_operations->get_next() - 1);
        $this->assertEquals(9, $compte->get_nb_ope());
        $this->setExpectedException('Exception_not_exist');
        $gsb_operations->get_by_id(14);
    }

    public function test_delete_operation_ventilee() {
        global $gsb_operations;
        $this->setExpectedException('exception_integrite_referentielle');
        $gsb_operations->get_by_id(2)->delete();
    }

    public function test_delete_virement() {
        global $gsb_operations;
        $this->setExpectedException('exception_integrite_referentielle');
        $gsb_operations->get_by_id(10)->delete();
    }

    public function test_delete_ventilation() {
        global $gsb_operations;
        $this->setExpectedException('exception_integrite_referentielle');
        $gsb_operations->get_by_id(3)->delete();
    }

}