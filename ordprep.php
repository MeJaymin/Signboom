<?php

    if ( empty($_POST))
    {
        header('Location: http://signboom.com');
        exit('This page should not be loaded directly.');
    }
	
  require_once('Connections/DBConn.php'); 
  require_once('includes/mailord.php'); 
  include('includes/utils.php');
  include('includes/testmode.php');
  include('includes/build_message.php');
  include('includes/getdetail.php');

  $Qry = 'SELECT * FROM signboom_discount WHERE Enabled = 1'; 
  mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
  $result = mysql_query($Qry, $DBConn) or die(mysql_error());
  $i = 0;
  while ($row = mysql_fetch_array($result, MYSQL_BOTH)) { 
    $i++;
    $discount[$i]->ID = $row['ID'];
    $discount[$i]->Desc = $row['Desc'];
    $discount[$i]->Footage = $row['Footage'];
    $discount[$i]->Dct = $row['Dct'];
  } 
  mysql_free_result($result);

  mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
  $ordtype = $_POST['ordertype'];
  $email =$_POST['emailr'];
  $colname_rs = (get_magic_quotes_gpc()) ? $_POST['emailr'] : addslashes($_POST['emailr']);
  $query_rs = sprintf('SELECT * FROM signboom_user WHERE email = "%s"', $colname_rs);
  $rsUser = mysql_query($query_rs, $DBConn) or die(mysql_error());

  $row_rs = mysql_fetch_assoc($rsUser);

  $custkey = $row_rs['ID'];
  $firstname = $row_rs['firstName'];        
  $lastname = $row_rs['lastName'];  
  $acctid  = $row_rs['AcctName'];  
  $address = $row_rs['address'];  
  $city = $row_rs['city'];  
  $provstate = $row_rs['provstate'];  
  $postalzip = $row_rs['postalzip'];  
  $custid = $row_rs['userName'];  
  $dctid = $row_rs['dct'];
  $team = $row_rs['team'];
  $copy_to_kim = $row_rs['CopyToKim'];
  $most_recent_order = $row_rs['mostRecentOrder'];

  // Add customer shipto address as needed
  $addshiptoaddr = ($_POST['shiptoaddcust'] == "true") ? "yes" : "no";
  if ($addshiptoaddr == "yes") {
    $query_rs = sprintf('SELECT * FROM signboom_shipto WHERE ');
    $query_rs .= 'acctid='.$custkey;
    $colname_rs = (get_magic_quotes_gpc()) ? $_POST['shiptoname'] : addslashes($_POST['shiptoname']);
    $query_rs .= ' and name="'.$colname_rs.'"';
    $colname_rs = (get_magic_quotes_gpc()) ? $_POST['shiptoaddr'] : addslashes($_POST['shiptoaddr']);
    $query_rs .= ' and address="'.$colname_rs.'"';
    $colname_rs = (get_magic_quotes_gpc()) ? $_POST['shiptocity'] : addslashes($_POST['shiptocity']);
    $query_rs .= ' and city="'.$colname_rs.'"';
    $colname_rs = (get_magic_quotes_gpc()) ? $_POST['shiptoprov'] : addslashes($_POST['shiptoprov']);
    $query_rs .= ' and state="'.$colname_rs.'"';
    $colname_rs = (get_magic_quotes_gpc()) ? $_POST['shiptocountry'] : addslashes($_POST['shiptocountry']);
    $query_rs .= ' and country="'.$colname_rs.'"';
    $colname_rs = strtoupper((get_magic_quotes_gpc()) ? $_POST['shiptozip'] : addslashes($_POST['shiptozip']));
    $query_rs .= ' and postalzip="'.$colname_rs.'"';
    //echo $query_rs."<br>";
    $rsShip = mysql_query($query_rs, $DBConn) or die(mysql_error());
    if (mysql_num_rows($rsShip) == 0) {
      $updQ  = "INSERT INTO signboom_shipto ( ";
      $updQ .= "acctid, acctname, name, address, city, state, country, postalzip";
      $updQ .= ") VALUES (";    
      $updQ .= "'".$custkey."', ";
      $updQ .= "'".$acctid."', ";
      $updQ .= "'".$_POST['shiptoname']."', ";
      $updQ .= "'".$_POST['shiptoaddr']."', ";
      $updQ .= "'".$_POST['shiptocity']."', ";
      $updQ .= "'".$_POST['shiptoprov']."', ";
      $updQ .= "'".$_POST['shiptocountry']."', ";
      $updQ .= "'".strtoupper($_POST['shiptozip'])."' ";
      $updQ .= ")";
      $result = mysql_query($updQ, $DBConn) or die(mysql_error());
      //echo $updQ;
      //exit();
    }
  }

  $now_1 = getdate( );
  $dbToken = crypt("$now_1[hours]:$now_1[minutes]:$now_1[seconds]", "yellow44Jacket");
  
  // Convert ready date to datetime format:
  if (($_POST['readydate'] == "Call") || (strlen(trim($_POST['readydate'])) == 0)){
    $readydatetime = "0000/00/00 00:00:00";
  }
  else {
    sscanf($_POST['readydate'], "%d/%d/%d %d%s", $month, $day, $year, $time, $ampm);
    if ($ampm == "AM") 
      $hour = $time;
    else
      $hour = $time + 12;
    $readydatetime = sprintf("%4d/%02d/%02d %2d:00:00", $year, $month, $day, $hour);
  }

  // Convert special characters to their HTML codes, so we don't mess up the alert function.
  $special_chars =      array("&",      "\"",     "'",      "%",      "(",      ")",      "\r\n",   "\r",     "\n");
  $replacement_values = array("&#038;", "&#034;", "&#039;", "&#037;", "&#040;", "&#041;", "&#010;", "&#010;", "&#010;");
  $fnotes = str_replace($special_chars, $replacement_values, $_POST['fnotes']);
  // Replace lines feeds of all types.
  //$fnotes = str_replace(array("\r\n", "\r", "\n"), "&#010;", $fnotes);

  /* Identify whether this order is the customer's first order, or if it is their first order
  in more than 12 months. These cases will be flagged with colour in the production system. */
  $today = date("Y-m-d H:i:s");  
  $one_year_ago = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d"), date("Y") - 1)); 

  if ($most_recent_order == '0000-00-00 00:00:00') 
  {
    $first_order = 1;
    $returning_customer = 0;
  }
  else
  {
    $first_order = 0;
    if (strtotime($most_recent_order) < strtotime($one_year_ago))
      $returning_customer = 1;
    else
      $returning_customer = 0;
  }
  
  // Now update the users table with date of (this) most recent order.
  $updQ2 = "UPDATE signboom_user SET mostRecentOrder = '$today' WHERE AcctName = '$acctid'";
  $result2 = mysql_query($updQ2, $DBConn);
  

  $updQ  = "INSERT INTO signboom_ordermast ( ";
  $updQ .= "ordertype, UserID, AcctName, email, date_created, Token, refnum, customernotes, readydate, readydatetime, ";
  $updQ .= "readydateconfirmed, shiptype, rushtype, subtotal, setupfee, dct, discount, rushfee, gst, pst, freight, ordertotal, ";
  $updQ .= "shipattn, shipcompany, shipaddress, shipcity, shipprov, shipzip, shipcountry, shiptoadd, documentname, ";
  $updQ .= "ordercompleted, orderinvoiced, team, firstorder, returningcustomer, promocode, promodiscount ";
  $updQ .= ") VALUES (";
  $updQ .= "'".$_POST['ordertype']."', ";
  $updQ .= "'".$custkey."', ";
  $updQ .= "'".$acctid."', ";
  $updQ .= "'".$email."', ";
  //$updQ .= "NOW(), ";
  $updQ .= "DATE_SUB(NOW(), INTERVAL 3 HOUR), "; /* adjust for 3 hour time difference between server location and BC */
  $updQ .= "'".$dbToken."', ";
  $updQ .= "'".$_POST['frefnumber']."', ";
  $updQ .= "'".$fnotes."', ";
  //$updQ .= ajmmakedate($_POST['readydate']).", ";
  $updQ .= "'".$_POST['readydate']."', ";
  $updQ .= "'".$readydatetime."', ";
  $updQ .= "'0', ";
  $updQ .= "'".$_POST['fpickuptype']."', ";
  $updQ .= "'".$_POST['fservicetype']."', ";
  $updQ .= "'".$_POST['fsubtotal']."', ";
  $updQ .= "'".$_POST['fsetup']."', ";
  $updQ .= "'".$_POST['dctname']."', ";
  $updQ .= "'".$_POST['fdiscount']."', ";
  $updQ .= "'".$_POST['frushamt']."', ";
  $updQ .= "'".$_POST['fGST']."', ";
  $updQ .= "'".$_POST['fPST']."', ";
  $updQ .= "'".$_POST['ffreight']."', ";
  $updQ .= "'".$_POST['ftotal']."', ";
  $updQ .= "'".$_POST['shiptoattn']."', ";
  $updQ .= "'".$_POST['shiptoname']."', ";
  $updQ .= "'".$_POST['shiptoaddr']."', ";
  $updQ .= "'".$_POST['shiptocity']."', ";
  $updQ .= "'".$_POST['shiptoprov']."', ";
  $updQ .= "'".$_POST['shiptozip']."', ";
  $updQ .= "'".$_POST['shiptocountry']."', ";
  $updQ .= "'".$addshiptoaddr."', ";
  $updQ .= "'".$_POST['fshipdocname']."', ";
  $updQ .= "'no', ";
  $updQ .= "'no', ";
  $updQ .= "'".$team."', ";
  $updQ .= "'".$first_order."', ";
  $updQ .= "'".$returning_customer."', ";
  $updQ .= "'".strtoupper($_POST['promocode'])."', ";
  $updQ .= "'".$_POST['fpromodiscountdollars']."' ";
  $updQ .= ")";

  if ($this_is_a_test == 1) {
    echo "<script language=\"javascript\">alert(\"" . $updQ. "\");</script>";
    $orderid = 12345;
  }
  else {
	  
    $result = mysql_query($updQ, $DBConn) or die(mysql_error());
    $orderid = mysql_insert_id($DBConn);

    if ($acctid == 'INCIDENT')
    {
      $order_total = str_replace('$', '', $_POST['fsubtotal']);
      $insert_query = "INSERT INTO signboom_incidents SET Date = DATE_SUB(NOW(), INTERVAL 3 HOUR), OrderId = '$orderid', Value = '$order_total', UploadNotes = '$fnotes', Type = '', Accountable = '', Caused = '', Comments = ''";
      $result_insert = mysql_query($insert_query, $DBConn);
	  
    }

  }

  for ($i = 1; $i <= 10; $i++) { 
    $lineitem = $_POST['xprod'.$i];
    if ($lineitem != "") {
      $l_items = explode("~", $lineitem);
      $prodidx = 0;
      $optidx = 1;
      $heightidx = 2;
      $widthidx = 3;
      $linfootidx = 4;
      $quanidx = 5;
      $fileidx = 6;
      $linetotidx = 7;
      $dctcostidx = 8;
      $wasteidx = 9;
      $pcodeidx = 10;
      $pdescidx = 11;
      $ocodeidx = 12; // just STD or CUS
      $odescidx = 13; // details of finishing with ^ separator
      $sqftidx = 14;
      $printedareaidx = 15;
      $wasteareaidx = 16;
      $wastecostidx = 17;
      $inkcostidx = 18;

      // Parse out the finishing options and create variables for each.
      $AF = "";
      $AL = "";
      $AI = "";
      $AP = "";
      $AK = "";
      $BF = "";
      $BB = "";
      $BI = "";
      $BP = "";
      $BK = "";
      $RF = "";
      $RL = "";
      $RB = "";
      $RH = "";
      $RE = "";
      $RI = "";
      $RP = "";
      $RK = "";
      $RO = "";
      $the_options = explode("^", $l_items[$odescidx]);
      // skip item 0 in $the_options
      for ($j = 1; $j < count($the_options); $j++){
        // find which type of option this is, AF, AL, etc...
        $type_of_option = substr($the_options[$j], 0, 2);
        // set that type of option to be this one in particular
        ${$type_of_option} = $the_options[$j];
      }

        if ($l_items[$quanidx] != "") {
	
        $category_query = "SELECT Category FROM signboom_allproducts WHERE Code = '" . $l_items[$prodidx] . "'";
        $category_result = mysql_query($category_query, $DBConn) or die(mysql_error());
        $category = mysql_result($category_result, 0, 'Category');
        if (($category == 'STANDS') || ($category == 'ACCESS'))
            $starting_queue = 'Finish';
        else
          $starting_queue = 'Upload';
        $updQ  = "INSERT INTO signboom_linedetail ( ";
        $updQ .= "orderid, linenum, product, options, ";
        $updQ .= "quantity, itemwidth, itemheight, ";
        $updQ .= "filename, cost, dctcost, proofed, printed, finished, packed, ";
        $updQ .= "currentqueue, squarefootage, printedarea, wastearea, wastecost, inkcost, ";
        $updQ .= "AF, AL, AI, AP, AK, BF, BB, BI, BP, BK, RF, RL, RB, RH, RE, RI, RP, RK, RO, ";
        $updQ .= "eventlocationcode, readydate, readydatetime, rushtype ";
        $updQ .= ") VALUES (";
        $updQ .= "'".$orderid."', ";
        $updQ .= "'".$i."', ";
        $updQ .= "'".$l_items[$prodidx]. "', ";
        $updQ .= "'".$l_items[$ocodeidx]. "', ";
        $updQ .= "'".$l_items[$quanidx]."', ";
        $updQ .= "'".$l_items[$widthidx]."', ";
        $updQ .= "'".$l_items[$heightidx]."', ";
        $updQ .= "'".$l_items[$fileidx]."', ";
        $updQ .= "'".$l_items[$linetotidx]."', ";
        $updQ .= "'".$l_items[$dctcostidx]."', ";
        $updQ .= "'no', ";
        $updQ .= "'no', ";
        $updQ .= "'no', ";
        $updQ .= "'no', ";
        $updQ .= "'".$starting_queue."', ";
        $updQ .= "'".$l_items[$sqftidx]."', ";
        $updQ .= "'".$l_items[$printedareaidx]."', ";
        $updQ .= "'".$l_items[$wasteareaidx]."', ";
        $updQ .= "'".$l_items[$wastecostidx]."', ";
        $updQ .= "'".$l_items[$inkcostidx]."', ";
        $updQ .= "'".$AF."', ";
        $updQ .= "'".$AL."', ";
        $updQ .= "'".$AI."', ";
        $updQ .= "'".$AP."', ";
        $updQ .= "'".$AK."', ";
        $updQ .= "'".$BF."', ";
        $updQ .= "'".$BB."', ";
        $updQ .= "'".$BI."', ";
        $updQ .= "'".$BP."', ";
        $updQ .= "'".$BK."', ";
        $updQ .= "'".$RF."', ";
        $updQ .= "'".$RL."', ";
        $updQ .= "'".$RB."', ";
        $updQ .= "'".$RH."', ";
        $updQ .= "'".$RE."', ";
        $updQ .= "'".$RI."', ";
        $updQ .= "'".$RP."', ";
        $updQ .= "'".$RK."', ";
        $updQ .= "'".$RO."', ";
        $updQ .= "'', ";
        $updQ .= "'".$_POST['readydate']."', ";
        $updQ .= "'".$readydatetime."', ";
        $updQ .= "'".$_POST['fservicetype']."' ";
        $updQ .= ")";
  
        if ($this_is_a_test == 1) {
          echo "<script language=\"javascript\">alert(\"" . $updQ . "\");</script>";
        }
        else {
          $result = mysql_query($updQ, $DBConn) or die(mysql_error());
        }
      }
    }
  }
  
  if ($this_is_a_test != 1) {

    $Query = 'SELECT * FROM signboom_ordermast WHERE ID = "' . $orderid.'" AND Token = "'.$dbToken.'"';
    $result = mysql_query($Query, $DBConn) or die(mysql_error());
    $refnum = mysql_result($result,0,'refnum');
    $orderid = mysql_result($result,0,'ID');
    $accountid = mysql_result($result,0,'AcctName');
    $subjectline = "Signboom Order Entry #$orderid - $accountid -  '$refnum'";

    $detail = array();
    getOrderDetails($orderid, $detail);

    $msg = bldmsg($result, $detail, $rsUser);

    mail("orders@signboom.com", $subjectline, $msg, "From: " . mysql_result($rsUser,0,'email') . "\r\n"); // Sandra
    mail("tammy@signboom.com", $subjectline, $msg, "From: " . mysql_result($rsUser,0,'email') . "\r\n"); // Tammy
    //if ($copy_to_kim) Copy all orders to Kim per Len's request on 170822.
    mail("kim@signboom.com", $subjectline, $msg, "From: " . mysql_result($rsUser,0,'email') . "\r\n"); // Kim
    mail("leonardjamesmoore@gmail.com", $subjectline, $msg, "From: " . mysql_result($rsUser,0,'email') . "\r\n"); // Leonard
    mail("orders.signboom@gmail.com", $subjectline, $msg, "From: " . mysql_result($rsUser,0,'email') . "\r\n"); // Staff, because emails to @signboom addresses aren't getting through reliably (July 2018)
    mail("alison_j_taylor@hotmail.com", $subjectline, $msg, "From: " . mysql_result($rsUser,0,'email') . "\r\n"); // Alison for testing

    $ftotal = str_replace('$', '', $_POST['ftotal']);
    if ($ftotal > 1500)
    {
      mail("sandra@signboom.com", "Check this Expensive Order against checklist.", $msg, "From: orders@signboom.com\r\n");
      mail("kim@signboom.com", "Check this expensive order against checklist.", $msg, "From: orders@signboom.com\r\n"); 
    }
  }
  
  function ajmmakedate($d) {
    //return substr($d,6).substr($d,0,2).substr($d,3,2);
    if ($d == "Call") return date("Ymd", strtotime("2000-01-01"));
    return date("Ymd", strtotime($d)); 
  }

  function GetProductInfo($sz) {  // used to be GetSigrProduct
    include('Connections/DBConn.php');
    mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
    $query_rs = sprintf('SELECT * FROM `signboom_allproducts` WHERE ID = "%s"', $sz);
    $rs = mysql_query($query_rs, $DBConn) or die(mysql_error());
    $row_rs = mysql_fetch_assoc($rs);
    $temp = $row_rs['code'];
    mysql_free_result($rs);  
    return addslashes($temp);
  }
  
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Signboom Order Post</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="no-cache">
<meta http-equiv="expires" content="Wed, 26 Feb 1997 08:21:57 GMT">
<link href="admin/admin.css" rel="stylesheet" type="text/css">
</head>
<script src="script/cookieLibrary.js" LANGUAGE="JavaScript" TYPE="text/javascript"></script>

<script language="JavaScript" type="text/JavaScript">

  function submitOrder() {
    if (validatedata()) {
      var expdate = new Date ();
      FixCookieDate (expdate); // Correct for Mac date bug - call only once for given Date object!
      expdate.setTime (expdate.getTime() + (60 * 24 * 60 * 60 * 1000)); // 60 days from now 
      <?php echol ('SetCookie ("userid", "'.$email.'", expdate);'); ?>
      window.opener.StartEditUpload();
    }
    self.close();
  }
  
  function ComputeDct(id) {
    if (validatedata()) {
      var expdate = new Date ();
      FixCookieDate (expdate); // Correct for Mac date bug - call only once for given Date object!
      expdate.setTime (expdate.getTime() + (60 * 24 * 60 * 60 * 1000)); // 60 days from now 
      //SetCookie ("userid", window.document.custform.emailr.value, expdate);
      <?php echol ('SetCookie ("userid", "'.$email.'", expdate);'); ?>
      window.opener.StartDctCompute(id);
      if (id == 0) {
        window.opener.SetError("-Loyalty discount is not active on this account.  Please contact us to discuss.-");
      }
    }
    self.close();
  }

  function validatedata() {
      
    sform = window.opener.document.getElementById("orderForm");
    <?php
    echol ('sform.emailr.value = "'.$email.'";');
    echol ('sform.custid.value = "'.$custid.'";');
    echol ('sform.acctid.value = "'.$acctid.'";');
    echol ('sform.dctid.value = "'.$dctid.'";');  
    echol ('sform.orderid.value = "'.$orderid.'";');
    echol ('sform.dbToken.value = "'.$dbToken.'";');
    ?>
    return true;
  }
</script>

  <?php
  if ($dcttype == "yes") {
    print('<body onLoad="ComputeDct('.$dct.')">');
  } else {
    print('<body onLoad="submitOrder()">');
  }
  ?>

<p>&nbsp;</p>

<p align="center" class="addetail1"><?php print($msg) ?></p>

</body>
</html>
<?php
mysql_free_result($rsUser);
mysql_free_result($result);
?>
