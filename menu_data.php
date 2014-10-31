<?php
include_once("./include/functions.inc");
include_once("./simple_html_dom.php");

$html = getClassfinderMenu();

$gurDom = $html->find('select[name=sel_gur] > option[!selected]');
$gurs = array('count' => count($gurDom), 'gurs' => array());
foreach($gurDom as $gur) {
    $gurs['gurs'][] = array('id' => $gur->value, 'gur' => $gur->plaintext);
}

$instDom = $html->find('select[name=sel_inst] > option[!selected]');
$instructors = array('count' => count($instDom), 'instructors' => array());
foreach($instDom as $inst) {
    $instructors['instructors'][] = array('id' => $inst->value, 'name' => $inst->plaintext);
}

$termDom = $html->find('select[name=term] > option[!selected]');
$terms = array('count' => count($termDom), 'terms' => array());
foreach($termDom as $term) {
    $terms['terms'][] = array('id' => $term->value, 'term' => $term->plaintext);
}

$subjectsDom = $html->find('select[name=sel_subj] > option[!selected]');
$subjects = array('count' => count($subjectsDom), 'subjects' => array());
foreach($subjectsDom as $subj) {
    $subjects['subjects'][] = array('id' => $subj->value, 'name' => preg_replace('/.*-\s/', '', $subj->plaintext));
}

$menuItems = array('terms' => $terms, 'subjects' => $subjects, 'gurs' => $gurs, 'instructors' => $instructors);
print json_encode($menuItems);

?>
