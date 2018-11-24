<?php
	require_once('Connections/DBConn.php');

	$Qry = sprintf("SELECT * FROM signboom_rates WHERE City='%s' and Province='%s'", $_REQUEST['city'], $_REQUEST['st']); 
	mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
  	$result = mysql_query($Qry, $DBConn) or die(mysql_error());
	$Charge = 0;
	$Zone = "";
	while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
		$Charge = $row['Charge'];
		$Zone = $row['Zone'];
	}
	mysql_free_result($result);
// Get Zone Multiplier
	$Qry = sprintf("SELECT * FROM signboom_zone WHERE Zone='%s'", $Zone); 
	mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
  	$result = mysql_query($Qry, $DBConn) or die(mysql_error());
	$defZoneAdd = 0;
	while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {;
		$defZoneAdd = $row['XTRA'];
	}
	mysql_free_result($result);

// If nothing is found, load up list of cities
   	if (($Charge == 0) && ($_REQUEST['newcust'])) {
		$Qry = sprintf("SELECT * FROM signboom_rates WHERE Province='%s' ORDER BY City", $_REQUEST['st']); 
		mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
	  	$result = mysql_query($Qry, $DBConn) or die(mysql_error());
		//$Cities[0] = null;
		$Zone = "";
		$i = 0;
		while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
			$Cities[$i] = $row['City'];
			$i++;
			//$Cities[$i]->Charge = $row['Charge'];
		}
		mysql_free_result($result);
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
</head>
<script type="text/javascript">

	var cities = new Array();

  <?php
	echo ('window.parent.postdata("Charge","'.$Charge.'");');
	echo ('window.parent.postdata("Zone","'.$Zone.'");');
	echo ('window.parent.postdata("ZoneAdd","'.$defZoneAdd.'");');
   	if (($Charge == 0) && ($_REQUEST['newcust'] == "true")) {
		for ($i = 0; $i <= count($Cities); $i++) { 
			echo ('cities['.$i.'] = "'.$Cities[$i].'";');
		} 
		echo ('window.parent.DisplayCities(cities);');
		echo ('cities.length = 0;');
	} else {
		echo ('window.parent.completepost();');
	}
  ?>
</script>

<body>
<?php print(date("l dS of F Y h:i:s A")); print ("<br>".$_REQUEST['city']."<br>".$_REQUEST['st']."<br> NewCust=".$_REQUEST['newcust']);  ?>
</body>
</html>
