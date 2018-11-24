<?php 
require('authprodn.php');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
  <title>Incidents</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="production-new.css" rel="stylesheet" type="text/css">
</head>

<body>

<div style="width: 1200px; margin: 0px auto; text-align: center;">

  <div style="float: left; margin-top: 20px;"><img src="../images/logo3d.gif" width="308" height="54"></div>
  <div style="float: right;"><h1>Order Processing System: Incidents</h1></div>
  <br style="clear: both;">
  <?php include('menu.html');?>
  <br><br>
</div>

<div style="margin: 0px auto; text-align: center;">
  <table class="evenodd" border="0" align="center" cellpadding="5">
  <tr>
    <td class="heading">Order Id</td>
    <td class="heading">Incident Id</td>
    <td class="heading" width="100px;">Date</td>
    <td class="heading">Value</td>
    <td class="heading">Upload Notes</td>
    <td class="heading">Type</td>
    <td class="heading">Accountable</td>
    <td class="heading">Caused</td>
    <td class="heading">Comments</td>
  </tr>
  <tr><td colspan="9" style="background-color: #ffffff;"><hr></td></tr>

  <?php 
    $query_incidents = "SELECT signboom_ordermast.ID, signboom_incidents.IncidentId, signboom_incidents.Date, signboom_incidents.Value, signboom_incidents.UploadNotes, signboom_incidents.Type, signboom_incidents.Accountable, signboom_incidents.Caused, signboom_incidents.Comments FROM signboom_ordermast, signboom_incidents WHERE signboom_ordermast.AcctName = 'INCIDENT' AND (signboom_incidents.OrderId = signboom_ordermast.ID) ORDER BY signboom_incidents.Date DESC";

    $incidents = mysql_query($query_incidents, $DBConn) or die("queryDashboard: Could not read incidents from database:" . mysql_error() . "<br><br>" . $query_incidents);
      $number_of_incidents = mysql_num_rows($incidents);
	  
		$protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
		$host = $_SERVER['SERVER_NAME'];

    // All the incidents are shown on one page.
    while ($row_incident = mysql_fetch_assoc($incidents))
    {
      if ($row_incident == FALSE) 
      {
        echo "<tr><td colspan=\"9\">Could not read incident from database.</td></tr>";
      } 
      else
      {
        echo '<tr>';
		echo '<td><a href="' . $protocol . '://' . $host . '/Signboom/production/orderitem.php?order_id=' . $row_incident['ID'] . '">' . $row_incident['ID'] . '</a></td>';
        //echo '<td><a href="http://signboom.com/production/orderitem.php?order_id=' . $row_incident['ID'] . '">' . $row_incident['ID'] . '</a></td>';
        echo '<td>' . $row_incident['IncidentId'] . '</td>';
        echo '<td>' . $row_incident['Date'] . '</td>';
        printf('<td style="text-align: right;">%.2f</td>', $row_incident['Value']);

        if (strlen($row_incident['UploadNotes']) > 60) 
	{
	  $upload_notes = $row_incident['UploadNotes'];
          echo "<td style=\"text-align: left; padding-left: 1em;\"><a href=\"#\" onClick=\"alert('$upload_notes')\">";
          echo substr(strip_tags($upload_notes), 0, 57); 
          echo "...";
          echo "</a></td>";
        }
        else 
	{
         echo '<td style="text-align: left; padding-left: 1em;">' . strip_tags($row_incident['UploadNotes']) . '</td>';
        }

        echo '<td>' . $row_incident['Type'] . '</td>';
        echo '<td>' . $row_incident['Accountable'] . '</td>';
        echo '<td>' . $row_incident['Caused'] . '</td>';

	if (strlen($row_incident['Comments']) > 60) 
	{
	  $comments = $row_incident['Comments'];
          echo "<td style=\"text-align: left; padding-left: 1em;\"><a href=\"#\" onClick=\"alert('$comments')\">";
          echo substr(strip_tags($comments), 0, 57); 
          echo "...";
          echo "</a></td>";
        }
        else 
	{
          echo '<td style="text-align: left; padding-left: 1em;">' . strip_tags($row_incident['Comments']) . '</td>';
        }

        echo '</tr>';
      } 
    }
  ?>

  <tr><td colspan="9" style="background-color: #ffffff;"><hr></td></tr>
  </table>

</body>
</html>
<?php
mysql_free_result($incidents);
?>
