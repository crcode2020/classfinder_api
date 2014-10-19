<?php
$docRoot = $_SERVER['DOCUMENT_ROOT'] . "classfinder_api";

include_once("$docRoot/include/functions.inc");
include_once("simple_html_dom.php");

$html = getClassfinderMenu();
$gurDom = $html->find('select[name=sel_gur] > option[!selected]');
$gurs = array('count' => count($gurDom), 'gurs' => array());
foreach($gurDom as $gur) {
    $gurs['gurs'][] = array('id' => $gur->value, 'gur' => $gur->plaintext);
}

print json_encode($gurs);

?>
