<?php

function CheckArgs() {
    $fields = array('term', 'sel_subj');
    $success = true;
    foreach($fields as $field) {
        if(!isset($_GET[$field])) {
            http_response_code(400);
            print "Missing $field<br>";
            exit();
        }
    }
    /*
    if(!(isset($_GET['sel_inst']) || isset($_GET['sel_crse']) || isset($_GET['sel_gur']) || isset($_GET['sel_cdts']))) {
        http_response_code(400);
        print "Missing at least 1 required parameter<br>";
        exit();
    }
    */
}

function getClassfinderHtml() {
    $dummy_vars = array('sel_subj', 'sel_subj', 'sel_gur', 'sel_gur', 'sel_day', 'sel_open');
    $req_vars = array('sel_crn' => '',
                     'term' => 201440,
                     'sel_gur' => 'All',
                     'sel_subj' => 'All',
                     'sel_inst' => 'ANY',
                     'sel_crse' => '',
                     'begin_hh' => 0,
                     'begin_mi' => 'A',
                     'end_hh' => 0,
                     'end_mi' => 'A',
                     'sel_cdts' => '%25');

    if (preg_match('/20[0-9]{2}[1-4]0/', $_GET['term']) == 1) {
        $req_vars['term'] = $_GET['term'];
    } else {
        http_response_code(500);
        print "Error: Could not parse term";
        exit();
    }
    if (isset($_GET['sel_subj'])) {
        $req_vars['sel_subj'] = $_GET['sel_subj'];
    }
    if (isset($_GET['sel_inst'])) {
        $req_vars['sel_inst'] = $_GET['sel_inst'];
    }
    if (isset($_GET['sel_crse'])) {
        $req_vars['sel_crse'] = $_GET['sel_crse'];
    }
    if (isset($_GET['sel_cdts'])) {
        $req_vars['sel_cdts'] = $_GET['sel_cdts'];
    }
    if (isset($_GET['sel_gur'])) {
        $req_vars['sel_gur'] = $_GET['sel_gur'];
    }

    $url = 'https://admin.wwu.edu/pls/wwis/wwsktime.ListClass';
    $postVars = '';
    $first = true;
    foreach($dummy_vars as $dum_var) {
        $postVars .= ($first ? '' : '&') . "$dum_var=dummy";
        $first = false;
    }
    foreach($req_vars as $key => $val) {
        $postVars .= "&$key=$val";
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $descriptorspec = array(
        0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
        1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
        2 => array("file", "./error-output.txt", "a") // stderr is a file to write to
    );
    $process = proc_open('python validify.py', $descriptorspec, $pipes);
    if (is_resource($process)) {
        // $pipes now looks like this:
        // 0 => writeable handle connected to child stdin
        // 1 => readable handle connected to child stdout
        // Any error output will be appended to /tmp/error-output.txt

        fwrite($pipes[0], $response);
        fclose($pipes[0]);

        $response = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        // It is important that you close any pipes before calling
        // proc_close in order to avoid a deadlock
        $return_value = proc_close($process);

        //echo "command returned $return_value\n";
    }
    return str_get_html($response);
}

function eatEmptyTags($td, $i, $total) {
    while($i < $total && (trim($td[$i]->plaintext) == '' || trim($td[$i]->plaintext)  == "&nbsp;")) {
        $i++;
    }
    return $i;
}

function getClassfinderMenu() {

    $url = 'https://admin.wwu.edu/pls/wwis/wwsktime.SelClass';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    /*
    $descriptorspec = array(
        0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
        1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
        2 => array("file", "./error-output.txt", "a") // stderr is a file to write to
    );
    $process = proc_open('python validify.py', $descriptorspec, $pipes);
    if (is_resource($process)) {
        // $pipes now looks like this:
        // 0 => writeable handle connected to child stdin
        // 1 => readable handle connected to child stdout
        // Any error output will be appended to /tmp/error-output.txt

        fwrite($pipes[0], $response);
        fclose($pipes[0]);

        $response = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        // It is important that you close any pipes before calling
        // proc_close in order to avoid a deadlock
        $return_value = proc_close($process);

        //echo "command returned $return_value\n";
    }*/
    return str_get_html($response);
}

?>
