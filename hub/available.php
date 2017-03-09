<?php
//This script checks whether the service is available
$token = '9b4c6614xxxxxxxxxxx';
$domainname = 'http://hub.esparanza.co.uk';

//Check if service available
$test = 'core_webservice_get_site_info';
$restformat = 'json';
header('Content-Type: text/plain');
$testurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$test;
require_once('./curl.php');
$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
$curls = new curl;
$res = $curls->post($testurl . $restformat);
$resu = json_decode($res);
echo (isset($resu->sitename) ? 'Service is available' : 'Service is not available');

