<?php
// $Id: comptes.php 45 2010-09-20 03:12:17Z pfrancois $
require_once ('header.php') ;

$solde_total_bq = 0 ;
$solde_total_pl = 0 ;
//soldes des comptes bancaires (ceux qui sont affichables)
foreach ($gsb_comptes->iter($cpt_aff) as $compte) {
    if ($compte->get_devise()->get_id() == DEVISE) {
        $solde_total_bq += $compte->get_solde_courant() ;
    } else {
        $solde_total_bq = $solde_total_bq + $compte->get_solde_courant() * $compte->get_devise()->get_change() ;
    }
    $tpl->append('comptes', array("id" => $compte->get_id(), "nom" => $compte->get_nom
        (), "m" => ($compte->get_solde_courant()) / 100, "devise" => $compte->get_devise
        ()->get_isocode())) ;
}
//soldes des comptes de placement
foreach ($gsb_comptes->iter(array_diff(array(compte::T_ACTIF, compte::T_BANCAIRE,
    compte::T_ESPECE, compte::T_PASSIF), $cpt_aff)) as $compte) {
    if ($compte->get_devise()->get_id() == DEVISE) {
        $solde_total_pl += $compte->get_solde_courant() ;
        $tpl->append('placements', array("id" => $compte->get_id(), "nom" => $compte->get_nom
            (), "m" => (($compte->get_solde_courant()) / 100) * $compte->get_devise()->get_change
            (), "devise" => $compte->get_devise()->get_isocode())) ;
    } else {
        $solde_total_pl += $compte->get_solde_courant() * $compte->get_devise()->get_change() ;

    }
}
$tpl->assign('devise', $gsb_devises->get_by_id(DEVISE)->get_isocode()) ;
$tpl->assign('total_bq', $solde_total_bq / 100) ;
$tpl->assign('total_pl', $solde_total_pl / 100) ;

$tpl->display('comptes.smarty') ;

