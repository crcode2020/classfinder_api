<?php
include_once("./functions.inc");
include_once("./simple_html_dom.php");

$html = getClassfinderMenu();
$subjectsDom = $html->find('select[name=sel_subj] > option[!selected]');
$subjects = array('count' => count($subjectsDom), 'subjects' => array());
foreach($subjectsDom as $subj) {
    $subjects['subjects'][] = array('abbreviation' => $subj->value, 'full' => preg_replace('/.*-\s/', '', $subj->plaintext));
}

echo json_encode($subjects);

?>
