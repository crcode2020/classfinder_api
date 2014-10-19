<?php
$docRoot = $_SERVER['DOCUMENT_ROOT'] . "classfinder_api";

include_once("$docRoot/include/functions.inc");
include_once("simple_html_dom.php");

$html = getClassfinderMenu();
$instDom = $html->find('select[name=sel_inst] > option[!selected]');
$insts = array('count' => count($instDom), 'insts' => array());
foreach($instDom as $inst) {
    $insts['insts'][] = array('id' => $inst->value, 'name' => $inst->plaintext);
}

print json_encode($insts);

?>
