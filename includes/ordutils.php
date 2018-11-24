<?php

  // Load Customer Data
  $loginUsername = $_SESSION['MM_Username'];
  $Qry = sprintf("SELECT * FROM signboom_user WHERE email='%s'",  get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername)); 
  mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
  $result = mysql_query($Qry, $DBConn) or die(mysql_error());
  $row = mysql_fetch_array($result, MYSQL_BOTH);
  $acctID  =  $row['ID'];
  $acctname =  $row['AcctName'];
  $acctcompany =  $row['company'];
  $acctaddr =  $row['address'];
  $acctcity =  $row['city'];
  $acctprov =  $row['provstate'];
  $acctcountry =  $row['country'];
  $acctzip =  $row['postalzip'];
  $acctpst =  $row['pstnum'];
  $acctdct =  $row['dct'];
  mysql_free_result($result);

  // Load default Freight Charges & Zone Info
  $Qry = sprintf("SELECT * FROM signboom_rates WHERE City='%s' and Province='%s'", $row['city'], $row['provstate']); 
  mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
  $result = mysql_query($Qry, $DBConn) or die(mysql_error());
  $defFreightCharge = 0;
  $defZone = "";
  while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {;
    $defFreightCharge = $row['Charge'];
    $defZone = $row['Zone'];
  }
  mysql_free_result($result);
  
  // Load Customer Address List
  $Qry = sprintf("SELECT * FROM signboom_shipto WHERE acctid=".$acctID); 
  mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
  $result = mysql_query($Qry, $DBConn) or die(mysql_error());
  $i = 0;
  while ($row = mysql_fetch_array($result, MYSQL_BOTH)) { 
    $i++;
    $shipto[$i]->ID = $row['ID'];
    $shipto[$i]->name = $row['name'];
    $shipto[$i]->address = $row['address'];
    $shipto[$i]->city = $row['city'];
    $shipto[$i]->state = $row['state'];
    $shipto[$i]->zip = $row['postalzip'];
    $shipto[$i]->country = $row['country'];
  } 
  mysql_free_result($result);

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

  // Load Holidays
  $today = date("Y-m-d"); 
  $Qry = "SELECT * FROM signboom_holiday WHERE holiday >='".$today."'"; 
  mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
  $result = mysql_query($Qry, $DBConn) or die(mysql_error());
  $i = 0;
  while ($row = mysql_fetch_array($result, MYSQL_BOTH)) { 
    $i++;
    $holiday[$i] = $row['holiday'];
  } 
  mysql_free_result($result);

  // Get full province name from two digit code
  function GetProv($prov, $arrProvState) {
      if(count($arrProvState)) {
      while( list($id, $val) = each($arrProvState) ){
        if ($id === $prov) {
          return ($val);
        }
      }
    }
    return "";
  }

  function printcurrency($num) {
    print sprintf('$'."%01.0f", $num);
  }

  function printcurrencypennies($num) {
    if (is_numeric($num)) {
      print sprintf('$'."%01.2f", $num);
    } else {
      print $num;
    }
  }

  function printpercent($num) {
    print sprintf("%01.2f%s", $num*100, "%");
  }

  function printwastecolor($num) {
    if ($num >= .5) {
      print ("#FF0000");
    } elseif ($num >= .25) {
      print ("#FF9900");
    } else {
      print ("#33FF99");
    }
  }

?>
