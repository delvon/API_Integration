<?php
echo '<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.1.2/css/buttons.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.11/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.1.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.1.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.1.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.1.2/js/buttons.print.min.js"></script>
<style>.dt-buttons{margin: 0 0 10px 10%}</style>';

require('config.php');
global $DB;
echo '<div style="margin: 20px 5%">';

$table = "<table id=\"medco\" class=\"medco\" width=\"100%\" border=\"1\" style=\"text-align:center\">
                <thead>
                            <tr class=\"tableheader\">
                                <th>Name</th>
                                <th>Medco ID</th>
                                <th>Professional ID</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Completion date</th> 
				<th>Error Code</th>
                           </tr></thead><tbody>";

$sql = "SELECT * FROM {medco_xxxx} WHERE exclxxxxx IS NULL AND (responxxx !=? OR responxxx IS NULL)";
$params = array("200");
$query = $DB->get_records_sql($sql, $params);
if ($query) {
	echo '<h2>MedCo Webservice Error Report</h2><h4>This report shows all records that have not been transferred successfully to MedCo</h4>
              <p>Please see below a key with the description of the error codes</p><h1 style="color:red">CHANGED DATA TO TEST DATA TO PROTECT USER PRIVACY</h1>';
$sql1 = "SELECT userid, (select count(*) from {medco_xxxx} where responxxx = '200') as ct, max(datexxx) as latxxx FROM {medco_webservice} WHERE responxxx != '0'";
$query1 = $DB->get_record_sql($sql1, array());
if ($query1) {echo '<p style="color:red">Last transfer: '.Date('d-M-Y H:i',$query1->latest).'<br>Total successful transfers to date: '.$query1->ct.'</p>';}
    foreach ($query as $meduser) {
	$table.='<tr><td>'.$meduser->firstname.' '.$meduser->lastname.'</td>';
	$table.='<td>'.$meduser->mxxxid.'</td>';
	$table.='<td>'.$meduser->profxxxid.'</td>';
	$table.='<td>'.$meduser->email.'</td>';
	if ($meduser->course == 9) {
		$cse = 'Top Up Medco Accreditation Training';
	} else {
	 	$cse = 'Full Medco Accreditation Training';
	}
	$table.='<td>'.$cse.'</td>';
	$table.='<td>'.Date('d-m-Y',$meduser->completiondate).'</td>';
	$table.='<td>'.$meduser->responxxx.'</td></tr>';
    }

    $table.='</tbody></table>';
    echo $table;
    echo '<h2>Key</h2><table border="0" style="text-align:left"><tr><th>Error Code</th><th>Description</th></tr>
          <tr><td>[Blank]</td><td>If no code is shown it means no attempt to send record to MedCo as yet.</td></tr>
          <tr><td>2010</td><td>Missing or empty medcoid field.</td></tr>
          <tr><td>2011</td><td>medcoid does not conform to the expected expression.</td></tr>
	  <tr><td>2015</td><td>medcoid could not be located in the MedCo system.</td></tr>
	  <tr><td>2020</td><td>Missing or empty professionalid field.</td></tr> 
	  <tr><td>2025</td><td>professionalid does not conform to the expected expression.</td></tr>
	  <tr><td>2026</td><td>professionalid does not match MedCo ProfessionalId, please contact MedCo Support.</td></tr>
	  <tr><td>2030</td><td>Missing or empty startdate field.</td></tr> 
	  <tr><td>2031</td><td>startdate not in expected format.</td></tr> 
	  <tr><td>2040</td><td>Missing or empty enddate field.</td></tr> 
	  <tr><td>2041</td><td>enddate not in expected format.</td></tr>
	  <tr><td>2045</td><td>enddate occurs in the past.</td></tr> 	
          </table>'; 
} else {
 Echo 'There are no reported errors';
}
echo "</div><script>
$(document).ready(function() {
    $('#medco').DataTable( {
        dom: 'lBfrtip',
        buttons: [
            'copy', 'excel', 'print'
        ]
    } );
} );
</script>";
