<?php

  // Load Customer Data
  $loginUsername = $_SESSION['MM_Username'];
  $Qry = sprintf("SELECT * FROM signboom_user WHERE email='%s'",  get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername)); 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $row = mysqli_fetch_array($result,  MYSQLI_BOTH);
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
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

  // Load default Freight Charges & Zone Info
  $Qry = sprintf("SELECT * FROM signboom_rates WHERE City='%s' and Province='%s'", $row['city'], $row['provstate']); 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $defFreightCharge = 0;
  $defZone = "";
  while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) {;
    $defFreightCharge = $row['Charge'];
    $defZone = $row['Zone'];
  }
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
  
  // Load Customer Address List
  $Qry = sprintf("SELECT * FROM signboom_shipto WHERE acctid=".$acctID); 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $i = 0;
  while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) { 
    $i++;
    $shipto[$i]->ID = $row['ID'];
    $shipto[$i]->name = $row['name'];
    $shipto[$i]->address = $row['address'];
    $shipto[$i]->city = $row['city'];
    $shipto[$i]->state = $row['state'];
    $shipto[$i]->zip = $row['postalzip'];
    $shipto[$i]->country = $row['country'];
  } 
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

  $Qry = 'SELECT * FROM signboom_discount WHERE Enabled = 1'; 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $i = 0;
  while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) { 
    $i++;
    $discount[$i]->ID = $row['ID'];
    $discount[$i]->Desc = $row['Desc'];
    $discount[$i]->Footage = $row['Footage'];
    $discount[$i]->Dct = $row['Dct'];
  } 
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

  // Load Holidays
  $today = date("Y-m-d"); 
  $Qry = "SELECT * FROM signboom_holiday WHERE holiday >='".$today."'"; 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $i = 0;
  while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) { 
    $i++;
    $holiday[$i] = $row['holiday'];
  } 
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

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
