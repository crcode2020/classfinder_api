<?php

include_once("./functions.inc");
include_once("./simple_html_dom.php");

CheckArgs();

$html = getClassfinderHtml();
$table = $html->find('table', 1);
$td = $table != null ? $table->find('td') : array();

// Remove header row td's
define("CLOSE_STATUS", 0);
$closeStatus = "open";
define("COURSE", 1);
$course = "";
define("POSITIONAL", 2);
$name = "";
$crn = "";
$cap = 0;
$enrl = 0;
$avail = 0;
$proff = "";
$dates = "";
$gurs = "";
$schedule1 = "";
$room1 = "";
$credits = 0;
$fees = "";
define("OPTIONAL", 14);
$schedule2 = "";
$room2 = "";
$restrictions = "";
$preReqs = "";
$notes = "";

$i = 10;
$total = count($td);
$state = CLOSE_STATUS;
$classDone = false;
$count = 0;
$classes = array();

while($i < $total) {
    $data = trim($td[$i]->plaintext);
    if($state == OPTIONAL && ($data == '' || $data == "&nbsp;")) {
        $i++;
    } else {
        switch($state) {
        case CLOSE_STATUS:
            $closeStatus = $data;
            $state = COURSE; $i++;
            break;
        case COURSE:
            $course = $data;
            $state = POSITIONAL; $i++;
            break;
        case POSITIONAL:
            $name = trim($td[$i++]->plaintext);
            $crn = trim($td[$i++]->plaintext);
            $cap = trim($td[$i++]->plaintext);
            $enrl = trim($td[$i++]->plaintext);
            $avail = trim($td[$i++]->plaintext);
            $proff = trim($td[$i++]->plaintext);
            $dates = trim($td[$i++]->plaintext);
            $i++;
            $gurs = trim($td[$i++]->plaintext);
            $schedule1 = trim($td[$i++]->plaintext);
            $room1 = trim($td[$i++]->plaintext);
            $credits = trim($td[$i++]->plaintext);
            if(strpos($td[$i]->plaintext, 'Restrictions') === FALSE) {
                $fees = trim($td[$i++]->plaintext);
                $i++;
            }
            $state = OPTIONAL;
            break;
        case OPTIONAL:
            // Check for extra schedule
            if(preg_match("/([MTWRF]{1,5}\s+\d{2}:\d{2}-\d{2}:\d{2}\s(am|pm))/", $data) == 1) {
                $schedule2 = $data;
                $data = trim($td[++$i]->plaintext);
                $room2 = $data;
                $i++;
            } // Check for new class (closed)
            else if(strpos($data, 'CLOSED') !== FALSE) {
                $classDone = true;
                $state = CLOSE_STATUS;
            } // Check for new class (course)
            else if(strpos($td[$i], 'href="') !== FALSE) {
                $classDone = true;
                $state = COURSE;
            } // Get Optional fees
            else if(preg_match('@\$\d{1,3}\.\d{2}\s+(Per/Cr|Flat\s+Fee)@', $data) == 1) {
                $fees .= " " . trim($td[$i++]->plaintext);
            } // Check for restrictions
            else if(strpos($data, 'Restrictions') !== FALSE) {
                $i += 2;
                $restrictions .= trim($td[$i++]->plaintext) . " ";
                // Get remaining restrictions until we hit prereqs or a new class.
                while($i < $total && count($td[$i]->find('font[color=red]')) > 0 && strpos($td[$i]->plaintext, 'Prerequisites') === FALSE && strpos($td[$i]->plaintext, 'CLOSED:') === FALSE) {
                    $restrictions .= trim($td[$i++]->plaintext) . " ";
                    $i = eatEmptyTags($td, $i, $total);
                }
            } // Check for Prerequisites
            else if(strpos($data, 'Prerequisites') !== FALSE) {
                $i += 2;
                while($i < $total && count($td[$i]->find('font[color=red]')) > 0 && strpos($td[$i]->plaintext, 'CLOSED:') === FALSE) {
                    $preReqs .= trim($td[$i++]->plaintext) . " ";
                    $i = eatEmptyTags($td, $i, $total);
                }
            } // Check for notes
            else if(count($td[$i]->find('font[size=-2]')) > 0 || count($td[$i]->find('font[size=-1]')) > 0) {
                while($i < $total && (count($td[$i]->find('font[size=-2]')) > 0 || count($td[$i]->find('font[size=-1]')) > 0) && strpos($td[$i]->plaintext, 'CLOSED:') === FALSE && strpos($td[$i], 'href="') === FALSE) {
                    $notes .= trim($td[$i++]->plaintext) . " ";
                }
            }
            else {
                echo '<font color="red" size=6>Error: At line 127 in \"search.php\".  else case should never execute.</font> Input was:';
                echo htmlspecialchars($td[$i]);
                echo $name . "<br>" . $crn . "<br>i = $i <br>crs status:$closeStatus <br>Course:$course <br>prereqs:$preReqs<br>";
                http_response_code(500);
                exit();
            }
            break;
        }
    }
    if($classDone || $i >= $total) {
        $classDone = false;
        $classes[$count++] = array("closeStatus" => $closeStatus,
            "course" => $course,
            "name" => $name,
            "crn" => $crn,
            "cap" => $cap,
            "enrl" => $enrl,
            "avail" => $avail,
            "proff" => $proff,
            "dates" => $dates,
            "gurs" => $gurs,
            "schedule1" => $schedule1,
            "room1" => $room1,
            "credits" => $credits,
            "fees" => $fees,
            "schedule2" => $schedule2,
            "room2" => $room2,
            "restrictions" => $restrictions,
            "preReqs" => $preReqs,
            "notes" => $notes);
        $closeStatus = '';
        $gurs = '';
        $fees = "";
        $schedule2 = '';
        $room2 = '';
        $restrictions = '';
        $preReqs = '';
        $notes = '';
    }
}

print json_encode($classes);

?>
