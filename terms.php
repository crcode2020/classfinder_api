<?php
include_once("./include/functions.inc");
include_once("./simple_html_dom.php");

$html = getClassfinderMenu();
$termDom = $html->find('select[name=term] > option[!selected]');
$terms = array('count' => count($termDom), 'terms' => array());
foreach($termDom as $term) {
    $terms['terms'][] = array('id' => $term->value, 'term' => $term->plaintext);
}

print json_encode($terms);

?>
