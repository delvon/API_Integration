<?php
$token = 'xxxxxxxxxxxxxxxxxx';
$domainname = 'http://hub.esparanza.co.uk';

//Search all courses on system
$test = 'core_course_get_courses';
$restformat = 'json';
$params = array();
header('Content-Type: text/plain');
$testurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$test;
require_once('./curl.php');
$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
$curls = new curl;
$res = $curls->post($testurl . $restformat, $params);
$resu = json_decode($res);
foreach ($resu as $course) {
    if ($course->id > 1) {
        echo "Course id : ".$course->id."\nCourse name : ".$course->fullname."\nIdnumber : ".$course->idnumber."\n\n";
    }
}


