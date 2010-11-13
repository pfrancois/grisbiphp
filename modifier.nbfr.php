<?php
// $Id: modifier.nbfr.php 45 2010-09-20 03:12:17Z pfrancois $
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 * @author  francois pegory
 */
/**
 * Smarty string_format modifier plugin
 *
 * Type : modifier<br>
 * Name : money_format<br>
 * Purpose : format strings with money logo
 * @param string $n chaine a formater
 * @param string la devise au format ISO
 * @return string
 */
function smarty_modifier_nbfr($n,$devise='&#8364;') {
	if ($devise=='EUR'){
		$devise='&#8364;';
	}
	if (!empty($n)) {
		$s=number_format($n, 2, ',', ' ');
		$s=$s." ".$devise;
	} else {
		$s=' - ';
	}
	return $s;
}
$tpl->register_modifier('nbfr', 'smarty_modifier_nbfr');
?>
