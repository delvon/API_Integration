<?php 
// disable moodle specific debug messages and any errors in output
//define('NO_DEBUG_DISPLAY', true);
define('CLI_SCRIPT', true);
require_once('config.php');
global $DB;

$sql = "SELECT gg.id, u.id as uid, u.firstname, u.lastname, u.email, gi.courseid, gg.itemid, gg.finalgrade, gg.timemodified,
            (select data from {user_info_data} where fieldid = 1 and userid = uid) as medprefix,
            (select data from {user_info_data} where fieldid = 2 and userid = uid) as medcoid,
            (select data from {user_info_data} where fieldid = 3 and userid = uid) as professionalid,
	    (select data from {user_info_data} where fieldid = 11 and userid = uid) as excludefrommedco
            FROM {grade_grades} gg
            JOIN {user} u on u.id=gg.userid
            JOIN {grade_items} gi on gg.itemid=gi.id
            AND (gg.itemid > ? and gg.itemid < ?)
            AND (gg.finalgrade/gi.grademax*100) >=?
            ORDER BY u.id";
$sql1 = "SELECT gg.id, u.id as uid, u.firstname, u.lastname, u.email, gi.courseid, gg.itemid, gg.finalgrade, gg.timemodified,
            (select data from {user_info_data} where fieldid = 1 and userid = uid) as medprefix,
            (select data from {user_info_data} where fieldid = 2 and userid = uid) as medcoid,
            (select data from {user_info_data} where fieldid = 3 and userid = uid) as professionalid,
	    (select data from {user_info_data} where fieldid = 11 and userid = uid) as excludefrommedco
            FROM {grade_grades} gg
            JOIN {user} u on u.id=gg.userid
            JOIN {grade_items} gi on gg.itemid=gi.id
            AND (gg.itemid > ? and gg.itemid < ?)
            AND (gg.finalgrade/gi.grademax*100) >=?
            ORDER BY u.id";
        $params = array(13,22,80);
        $params1 = array(21,26,80);
        $passallquiz = $DB->get_records_sql($sql, $params);
        $passallquiz1 = $DB->get_records_sql($sql, $params1);
        $allgrades = array();
        $allgrades1 = array();
    if ($passallquiz) {
        foreach ($passallquiz as $i=>$numval) {
            $allgrades[$numval->uid]['num'] = 0;
        }

        foreach ($passallquiz as $key=>$value) {
            $allgrades[$value->uid]['medprefix'] = $value->medprefix;
            $allgrades[$value->uid]['medcoid'] = $value->medcoid;
            $allgrades[$value->uid]['profid'] = $value->professionalid;
	    $allgrades[$value->uid]['exc'] = $value->excludefrommedco;
            $allgrades[$value->uid]['fname'] = $value->firstname;
            $allgrades[$value->uid]['lname'] = $value->lastname;
            $allgrades[$value->uid]['email'] = $value->email;
            $allgrades[$value->uid]['cid'] = $value->courseid;
            $allgrades[$value->uid]['time'][$value->itemid] = $value->timemodified;
            $allgrades[$value->uid]['num'] += 1; 
            
        }
    }
    if ($passallquiz1) {
        foreach ($passallquiz1 as $i=>$numval1) {
            $allgrades1[$numval1->uid]['num'] = 0;
        }

        foreach ($passallquiz1 as $key=>$value1) {
            $allgrades1[$value1->uid]['medprefix'] = $value1->medprefix;
            $allgrades1[$value1->uid]['medcoid'] = $value1->medcoid;
            $allgrades1[$value1->uid]['profid'] = $value1->professionalid;
            $allgrades1[$value1->uid]['exc'] = $value1->excludefrommedco;
            $allgrades1[$value1->uid]['fname'] = $value1->firstname;
            $allgrades1[$value1->uid]['lname'] = $value1->lastname;
            $allgrades1[$value1->uid]['email'] = $value1->email;
            $allgrades1[$value1->uid]['cid'] = $value1->courseid;
            $allgrades1[$value1->uid]['time'][$value1->itemid] = $value1->timemodified;
            $allgrades1[$value1->uid]['num'] += 1; 
            
        }
    }
    $medco = array();
    foreach ($allgrades as $k => $val) {
        if ($val['num'] == 7) {
            $medco[$k]['medprefix'] = $val['medprefix'];
            $medco[$k]['medcoid'] = $val['medcoid'];
            $medco[$k]['profid'] = $val['profid'];
            $medco[$k]['exc'] = $val['exc'];
            $medco[$k]['fname'] = $val['fname'];
            $medco[$k]['lname'] = $val['lname'];
            $medco[$k]['email'] = $val['email'];
            $medco[$k]['cid'] = $val['cid'];
            $medco[$k]['time'] = max($val['time']);
        } 
    }
    $medco1 = array();
    foreach ($allgrades1 as $k1 => $val1) {
        if ($val1['num'] == 4) {
            $medco1[$k1]['medprefix'] = $val1['medprefix'];
            $medco1[$k1]['medcoid'] = $val1['medcoid'];
            $medco1[$k1]['profid'] = $val1['profid'];
            $medco1[$k1]['exc'] = $val1['exc'];
            $medco1[$k1]['fname'] = $val1['fname'];
            $medco1[$k1]['lname'] = $val1['lname'];
            $medco1[$k1]['email'] = $val1['email'];
            $medco1[$k1]['cid'] = $val1['cid'];
            $medco1[$k1]['time'] = max($val1['time']);
        } 
    }
    //professionalid Must conform to the following regular expression: (^\d{7}$)|(^PH\d{1,6}$)
    $medcosend = ($medco+$medco1);
        
    if (count($medcosend) >= 1) {
        foreach ($medcosend as $id => $usr) {
	  if (!$usr['exc']) {
            $mprefix = substr($usr['medprefix'],0,3);
            $mid_rep = preg_replace( "/[^0-9]/", "", $usr['medcoid']);
            $mid = $mprefix.$mid_rep;
            $sqlselect = "SELECT userid FROM {medco_xxxx}
                        WHERE userid=?";
        
            $sqlupdate = "UPDATE {medco_xxxx}
                            SET medxxid=?, profxxxlid=?, course=?, completiondate=?
                            WHERE userid=?";
            $sqlinsert = "INSERT INTO {medco_xxx}
                            (userid, medxxxid, profxxxid, firstname, lastname, email, course, completiondate)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $paramselect = array($id);
            $paramsupdate = array($mid, $usr['profid'], $usr['cid'], $usr['time'], $id);
            $paramsinsert = array($id, $mid, $usr['profid'], $usr['fname'], $usr['lname'], $usr['email'], $usr['cid'], $usr['time']);
            try {
            	$check = $DB->get_record_sql($sqlselect, $paramselect);
            } catch (Exception $e) {
            	echo $e;
            }

            if ($check) {
                try {
                    $DB->execute($sqlupdate, $paramsupdate);
                } catch (Exception $f) {
                    echo $f;
                }

            } else {
                try {
                    $DB->execute($sqlinsert, $paramsinsert);
                } catch (Exception $g) {
                    echo $g;
                }
            }
          }
        }
    }

    $medsql = 'SELECT * FROM {medco_xxxx} where datesent IS NOT NULL and responsecode !=?';
    $medparams = array(200);
    $medquery = $DB->get_records_sql($medsql, $medparams);
    $uname = 'email1@test.org';
    $pword = 'Adadadfdxxxxx';
    $base64 = base64_encode($uname.':'.$pword);
    $uri = "https://sgsgfxxxx.medco.org.uk/api/gghhghgxxxxreditation";
    
    if ($medquery) {
        foreach ($medquery as $eachuser) {
            $startdate = date('Y-m-d', $eachuser->completiondate);
            $edate = strtotime('+3 years',$eachuser->completiondate);
            $enddate = date('Y-m-d',$edate);
            $medid = $eachuser->medcoid;
            $proid = $eachuser->professionalid;
            $json = json_encode(array('medxxxid'=>$medid, 'profxxxid'=>$proid,'startdate'=>$startdate,'enddate'=>$enddate));
            $dl = strlen($json);


    $headers = array (
        "Accept: application/json",
        "Content-Type: application/json; charset=utf-8",
        "Content-Length: " .strlen($json),
        "Authorization: Basic ".$base64  
      );
      
      $channel = curl_init($uri);
      curl_setopt($channel, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($channel, CURLOPT_CUSTOMREQUEST, "PUT");
     curl_setopt($channel, CURLOPT_HTTPHEADER, $headers);
     curl_setopt($channel, CURLOPT_POSTFIELDS, $json);
     curl_setopt($channel, CURLOPT_FOLLOWLOCATION, true);
     curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($channel, CURLOPT_CONNECTTIMEOUT, 10);
     
     $status = curl_exec($channel);
     $statusCode = curl_getInfo($channel, CURLINFO_HTTP_CODE);
     curl_close($channel); 
     
     $response = (json_decode($status));
     $code1 = $code = '';
     foreach ($response as $value) {        
         $code1 .= $value->ErrorCode."-";
        }
        $code = rtrim($code1,'-');
            $senddate=time();
            $query = "UPDATE {medco_xxxx}
                            SET datesent=?, responsecode=?
                            WHERE userid=?";
            $vars = array($senddate, $code, $eachuser->userid);
            $res=$DB->execute($query, $vars);
            if ($res) {
                echo '<br>Updated DB with '.$code.' for '.$eachuser->firstname.' '.$eachuser->lastname;
            }
            echo '<br>'.$code;
            
        }
    }
           
    //}