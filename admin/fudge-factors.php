<?php 
  include ('authadmin.php'); 

  /******************************************************************
    These are all the parameters stored in the database.

    DATABASE -> PHP -> JAVASCRIPT
    -----------------------------   
    - setupfee -> setupfee -> setupfee (used)
    - gfloorsetupfee -> gfloorsetupfee -> gfloorsetupfee 
    - packagingfee -> packfee -> packfee  (used)
    - shipmultipler -> shipmult -> shipmult (used)
    - wastefactor -> wastefactor -> wastefactor (used)
    - cutofftime (read directly from database into php code that calculates ready date, called by AJAX)
    - inkcost -> inkcost -> inkcost (used)
    - custoffdaysrush
    - custoffdayshot
    - discountfactora, discountfactorb (for calculating discounts against order value)
    - sqfttimea, sqfttimeb (for calculating how much time we need to print square footage)
    - expensive (any order >= than this is epensive and gets highlighted in production)

  ******************************************************************/

  $updated = false;
  if (isset($_POST['submit_parameters']))
  {
    if (isset($_POST['setup_fee']))
    {
      $setup_fee = $_POST['setup_fee'];
      if (($setup_fee < 0) || ($setup_fee >= 80))
      {
        $message = "The setup fee must be no less than $0 and no more than $80. Do not include the dollar sign. It has not been updated.";
      }
      else 
      {
        $query = "UPDATE signboom_parm SET setupfee = '$setup_fee' WHERE Id = 1";
        mysqli_select_db( $DBConn, $database_DBConn);
        //echo "Query: $query<br><br>";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $updated = true;
      }
    }
    if (isset($_POST['gfloor_setup_fee']))
    {
      $gfloor_setup_fee = $_POST['gfloor_setup_fee'];
      if (($gfloor_setup_fee < 0) || ($gfloor_setup_fee >= 180))
      {
        $message = "The G-Floor setup fee must be no less than $0 and no more than $180. Do not include the dollar sign. It has not been updated.";
      }
      else 
      {
        $query = "UPDATE signboom_parm SET gfloorsetupfee = '$gfloor_setup_fee' WHERE Id = 1";
        mysqli_select_db( $DBConn, $database_DBConn);
        //echo "Query: $query<br><br>";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $updated = true;
      }
    }
    if (isset($_POST['packaging_fee']))
    {
      $packaging_fee = $_POST['packaging_fee'];
      if (($packaging_fee <= 1) || ($packaging_fee >= 30))
      {
        $message = "The packaging fee must be at least $1 and no more than $30. Do not include the dollar sign. It has not been updated.";
      }
      else 
      {
        $query = "UPDATE signboom_parm SET packagingfee = '$packaging_fee ' WHERE Id = 1";
        mysqli_select_db( $DBConn, $database_DBConn);
        //echo "Query: $query<br><br>";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $updated = true;
      }
    }
    if (isset($_POST['shipping_factor']))
    {
      $shipping_factor = $_POST['shipping_factor'];
      if (($shipping_factor < 0.1) || ($shipping_factor >= 10))
      {
        $message = "The shipping factor factor must be greater than 0.1 and less than 10. It has not been updated.";
      }
      else 
      {
        $query = "UPDATE signboom_parm SET shipmultiplier = '$shipping_factor' WHERE Id = 1";
        mysqli_select_db( $DBConn, $database_DBConn);
        //echo "Query: $query<br><br>";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $updated = true;
      }
    }
    if (isset($_POST['cutoff_time']))
    {
      $cutoff_time = $_POST['cutoff_time'];
      if (($cutoff_time < 7) || ($cutoff_time > 18))
      {
        $message = "The cutoff time must be specified as an hour in 24 hour clock, between 7am and 6pm, inclusive.  For example, 2 pm would be 14 and 10 am would be 10. It has not been updated.";
      }
      else 
      {
        $query = "UPDATE signboom_parm SET cutofftime = '$cutoff_time' WHERE Id = 1";
        mysqli_select_db( $DBConn, $database_DBConn);
        //echo "Query: $query<br><br>";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $updated = true;
      }
    }
    if (isset($_POST['waste_factor']))
    {
      $waste_factor = $_POST['waste_factor'];
      if (($waste_factor < 0.1) || ($waste_factor >= 10))
      {
        $message = "The waste factor must be greater than 0.1 and less than 10. It has not been updated.";
      }
      else 
      {
        $query = "UPDATE signboom_parm SET wastefactor = '$waste_factor ' WHERE Id = 1";
        mysqli_select_db( $DBConn, $database_DBConn);
        //echo "Query: $query<br><br>";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $updated = true;
      }
    }
    if (isset($_POST['ink_cost']))
    {
      $ink_cost = $_POST['ink_cost'];
      if (($ink_cost < 0.0) || ($ink_cost >= 10.00))
      {
        $message = "The ink cost must be greater than $0.00/sq ft and less than $10.00/sq fet. It has not been updated.";
      }
      else 
      {
        $query = "UPDATE signboom_parm SET inkcost = '$ink_cost ' WHERE Id = 1";
        mysqli_select_db( $DBConn, $database_DBConn);
        //echo "Query: $query<br><br>";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $updated = true;
      }
    }
    if (isset($_POST['discount_factor_a']))
    {
      $discount_factor_a = $_POST['discount_factor_a'];
      if (!is_numeric($discount_factor_a))
      {
        $message = "The first factor (A) for the discount calculation equation must be a number.";
      }
      else 
      {
        $query = "UPDATE signboom_parm SET discountfactora = '$discount_factor_a' WHERE Id = 1";
        mysqli_select_db( $DBConn, $database_DBConn);
        //echo "Query: $query<br><br>";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $updated = true;
      }
    }
    if (isset($_POST['discount_factor_b']))
    {
      $discount_factor_b = $_POST['discount_factor_b'];
      if (!is_numeric($discount_factor_b))
      {
        $message = "The second factor (B) for the discount calculation equation must be a number.";
      }
      else 
      {
        $query = "UPDATE signboom_parm SET discountfactorb = '$discount_factor_b' WHERE Id = 1";
        mysqli_select_db( $DBConn, $database_DBConn);
        //echo "Query: $query<br><br>";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $updated = true;
      }
    }
    if (isset($_POST['sq_feet_time_a']))
    {
      $sq_feet_time_a = $_POST['sq_feet_time_a'];
      if (!is_numeric($sq_feet_time_a))
      {
        $message = "The number of sq feet that can be printed in the first day must be an integer.";
      }
      else 
      {
        $query = "UPDATE signboom_parm SET sqfttimea = '$sq_feet_time_a' WHERE Id = 1";
        mysqli_select_db( $DBConn, $database_DBConn);
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $updated = true;
      }
    }
    if (isset($_POST['sq_feet_time_b']))
    {
      $sq_feet_time_b = $_POST['sq_feet_time_b'];
      if (!is_numeric($sq_feet_time_b))
      {
        $message = "The number of sq feet that can be printed in each additional day must be an integer.";
      }
      else 
      {
        $query = "UPDATE signboom_parm SET sqfttimeb = '$sq_feet_time_b' WHERE Id = 1";
        mysqli_select_db( $DBConn, $database_DBConn);
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $updated = true;
      }
    }
    if (isset($_POST['expensive']))
    {
      $expensive = $_POST['expensive'];
      if ($expensive < 0) 
      {
        $message = "The Flag as Expensive limit must be greater than $0. Do not include the dollar sign. It has not been updated.";
      }
      else 
      {
        $query = "UPDATE signboom_parm SET expensive = '$expensive' WHERE Id = 1";
        mysqli_select_db( $DBConn, $database_DBConn);
        //echo "Query: $query<br><br>";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $updated = true;
      }
    }

  }

  // Get the fudge factor parameters from the database.
  $query = "SELECT setupfee, gfloorsetupfee, packagingfee, shipmultiplier, wastefactor, cutofftime, inkcost, discountfactora, discountfactorb, sqfttimea, sqfttimeb, expensive FROM signboom_parm WHERE Id = 1";
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $row = mysqli_fetch_array($result,  MYSQLI_BOTH); 
  $setup_fee= $row['setupfee'];
  $gfloor_setup_fee= $row['gfloorsetupfee'];
  $packaging_fee = $row['packagingfee'];
  $shipping_factor = $row['shipmultiplier'];
  $waste_factor = $row['wastefactor'];
  $cutoff_time = $row['cutofftime'];
  $ink_cost = $row['inkcost'];
  $discount_factor_a = $row['discountfactora'];
  $discount_factor_b = $row['discountfactorb'];
  $sq_feet_time_a = $row['sqfttimea'];
  $sq_feet_time_b = $row['sqfttimeb'];
  $expensive = $row['expensive'];
  
  // Display the parameters
  include ('templates/fudge-factors.php'); 
  
  /* Free memory. */
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
?>

