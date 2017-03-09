<?php
// This file is NOT a part of Moodle - http://moodle.org/
//
// This client for Moodle 2 is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
/**
 * REST client for Moodle 2. This script runs when a form in hub.html is submitted and create a user in Moodle
 * if the data in the form meets the criteria. It will also enrol the user on the course stated in the html form.
 * Return JSON or XML format
 *
 * @authorr Delvon Forrester
 */

$token = 'xxxxxxxxxxxxxxxxxx';
//$domainname = 'http://cpdhub.cltireland.ie';
$domainname = 'http://hub.esparanza.co.uk';
$functionname = 'core_user_create_users';
$enrol_function = 'enrol_manual_enrol_users';
$user_function = 'core_user_get_users_by_field';
// REST RETURNED VALUES FORMAT
$restformat = 'json'; //Also possible in Moodle 2.2 and later: 'json'
                     //Setting it to 'json' will fail all calls on earlier Moodle version
//////// moodle_user_create_users ////////
/// PARAMETERS - NEED TO BE CHANGED IF YOU CALL A DIFFERENT FUNCTION

//This part of the code create the user in moodle
$user1 = new stdClass();
$user1->username = $_POST['username'];
$user1->password = $_POST['password'];
$user1->firstname = $_POST['firstname'];
$user1->lastname = $_POST['lastname'];
$user1->email = $_POST['email'];
$user1->auth = 'manual';
$user1->lang = 'en';
$user1->description = 'latouchevideo????';
$user1->city = $_POST['city'];;
$user1->country = $_POST['country'];
$preferencename1 = 'auth_forcepasswordchange';
$user1->preferences = array(
    array('type' => $preferencename1, 'value' => 1));
$users = array($user1);
$email = $_POST['email'];
$params = array('users' => $users);
/// REST CALL
header('Content-Type: text/plain');
$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
require_once('./curl.php');
$curl = new curl;
//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
$resp = $curl->post($serverurl . $restformat, $params);
$result = json_decode($resp);
if (isset($result->debuginfo)) {
    echo 'Error: '.$result->debuginfo;
} else if (isset($result[0]->id)) {
    echo 'Account Successfully Created!...';
}
//End of user creation

//This is where we enrol a user on a course as stipulated in the form
if (isset($result[0]->id) && $result[0]->id) {
    $user_id = $result[0]->id;
} else {
    $ucurl = new curl;
    $uparam = array('field' => 'email', 'values' => array($email));
    $url = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$user_function;
    $res = $ucurl->post($url . $restformat, $uparam);
    $resu = json_decode($res);
    print_r($resu);
    $user_id = (isset($resu[0]->id) ? $resu[0]->id : 0);
    echo 'Searching for the user to see if we still need to enrol...';
}
if ($user_id) {
    $num = array($_POST['buyvideos']);
    if ($_POST['buyvideos'] == 1) {
        $num = array(2,3,4);
    }
    foreach($num as $n) {
        $curl1 = new curl;
        $course1 = new stdClass();
	$course1->roleid = 5;//The role id for a student
	$course1->userid = $user_id; //The id of the user Just created
	$course1->courseid = $n;
	$cse = array($course1);
	$params1 = array('enrolments' => $cse);
	$serverurl1 = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$enrol_function;
	$resp1 = $curl1->post($serverurl1 . $restformat, $params1);
	$result1 = json_decode($resp1);
        echo "Enrol user on course id ".$n;
    }
} else {
    echo "User does not exist!";
}
//End of enrolment