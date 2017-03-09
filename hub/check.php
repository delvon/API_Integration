<?php
// This script checks if user enrolled on any course in the system
$token = 'xxxxxxxxxxxxxxxxxx';
$domainname = 'http://hub.esparanza.co.uk';
$course_function = 'core_enrol_get_users_courses';
$user_function = 'core_user_get_users_by_field';
$restformat = 'json';
header('Content-Type: text/plain');
$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$course_function;
require_once('./curl.php');
$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
$ucurls = new curl;
$email = 'fel@del.com';//This should be the email the person uses
$uparams = array('field' => 'email', 'values' => array($email));
$url = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$user_function;
$res = $ucurls->post($url . $restformat, $uparams);
$resu = json_decode($res);
print_r($uparams);
print_r($resu);
$user_id = (isset($resu[0]->id) ? $resu[0]->id : 0);
if ($user_id) {
    $params=array('userid' => $user_id);//user id from the check
    $curl = new curl;
    $response = $curl->post($serverurl . $restformat, $params);
    $result = json_decode($response);
    print_r($result);//echo the array with courses returned
    $cses = array();
    if ($result[0]->id) {//if it returns at least one course
        foreach ($result as $cid => $course) {
            $cses[$cid] = $course->fullname; //stores all courses user is enrolled on using the course id as the array keys
        }
    }
    print_r($cses);// echo the array on screen with the course id and fullname
}