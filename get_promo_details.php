<?php

  include("Connections/DBConn.php");
  include("includes/utils.php");

  // Promo codes can be entered with any case. They are stored with all upper case in the database.
  $promo_code = $_GET['promo_code'];
  $promo_code = strtoupper($promo_code);

  // Get information on that promo code.
  $query = "SELECT * FROM signboom_promo_codes WHERE PromoCode = '$promo_code'";
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

  if (mysqli_num_rows($result) == 0) 
  {
    $return_value = "~~~"; // that promo code is not in the database
    echo $return_value;
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
    return true;
  }
  else 
  {
    $promo_details = mysqli_fetch_array($result,  MYSQLI_BOTH);
    $promo_amount = $promo_details['PromoAmount'];
    $promo_type = $promo_details['PromoType'];
    $promo_start = $promo_details['PromoStart'];
    $promo_end = $promo_details['PromoEnd'];

    // Check that this promo code is currently valid.
    // Once we have PHP 5.1 and can set the default time zone, we can get rid of the -3 in the line below.
    $today_vancouver = mktime(date("H")-3, date("i"), date("s"), date("m"), date("d"), date("Y"));
    $today = date("Y-m-d", $today_vancouver);   // e.g. 2001-03-10 
    if (($today < $promo_start) || ($today > $promo_end))
    {
      $return_value = "~~~"; // that promo code is not currently valid
      echo $return_value;
      ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
      return true;
    }

    $return_value = "~" . $promo_amount. "~" . $promo_type. "~";
    echo $return_value;
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
    return true;
  }
?>
