<?php

  include("Connections/DBConn.php");
  include("includes/utils.php");

  // Set the default timezone to use. Available since PHP 5.1.  Right now we have 4.4.9 so it won't work.
  // so we compensate in code further down by subtracting 3 hours.
  // date_default_timezone_set('UTC');

/*********************************************************************************/
/* ComputeReadyDate: Work out calendar date order should be ready.              */
/*********************************************************************************/

  function ComputeReadyDate($day, $month, $year, $days, $batch) {

    if ($batch)  
    {  
      // Position the date to the next business day in case the order is entered on a weekend,
      // holiday, or shutdown day.  0th weekday is Sunday, 6th weekday is Saturday.
      while ((date("w", mktime(12, 0, 0, $month, $day, $year)) == 0) || 
             (date("w", mktime(12, 0, 0, $month, $day, $year)) == 6) || 
             (!IsBatchWorkingDay($month, $day, $year))) {
        $day++;
      }
      // Now give ourselves $days working days to process the order.
      $k = 0;
      do {
        $day++;
        if ((date("w", mktime(12, 0, 0, $month, $day, $year)) != 0) && 
            (date("w", mktime(12, 0, 0, $month, $day, $year)) != 6) &&
            (IsBatchWorkingDay($month, $day, $year))) {
          $k++;
        }
      }
      while($k < $days);
      // Position the date to the next regular working day if the ready date is on a noship day.
      while ((date("w", mktime(12, 0, 0, $month, $day, $year)) == 0) || 
             (date("w", mktime(12, 0, 0, $month, $day, $year)) == 6) ||
             (!IsRegularWorkingDay($month, $day, $year))) {
        $day++;
      }
    }
    else  
    {
      // Position the date to the next business day in case the order is entered on a weekend,
      // holiday, shutdown or no-ship day.  0th weekday is Sunday, 6th weekday is Saturday.
      while ((date("w", mktime(12, 0, 0, $month, $day, $year)) == 0) || 
             (date("w", mktime(12, 0, 0, $month, $day, $year)) == 6) || 
             (!IsRegularWorkingDay($month, $day, $year))) {
        $day++;
      }
      // Now give ourselves $days working days to process the order.
      $k = 0;
      do {
        $day++;
        if ((date("w", mktime(12, 0, 0, $month, $day, $year)) != 0) && 
            (date("w", mktime(12, 0, 0, $month, $day, $year)) != 6) &&
            (IsRegularWorkingDay($month, $day, $year))) {
          $k++;
        }
      }
      while($k < $days);
    }

    $the_ready_date = mktime(0, 0, 0, $month, $day, $year);
    return($the_ready_date);

  }

/*********************************************************************************/
/* IsBatchWorkingDay: NOSHIP is batch working day; stat holiday and SHUTDOWN are not */
/*********************************************************************************/

  function IsBatchWorkingDay($the_month, $the_day, $the_year) {
    global $hostname_DBConn;
    global $database_DBConn;
    global $username_DBConn;
    global $password_DBConn;
    global $DBConn;

    $the_date = date("Y-m-d", mktime(0, 0, 0, $the_month, $the_day, $the_year));
    $query = "SELECT * FROM signboom_holiday WHERE holiday = '$the_date'";
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_array($result,  MYSQLI_BOTH);
      $type_of_day = $row['Description'];
      if ($type_of_day == 'NOSHIP')
        return true;
      else
        return false;
    }
    else {
      return true;
    }
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
  }

/*********************************************************************************/
/* IsRegularWorkingDay: anything in the holiday database table is not a regular day  */
/*********************************************************************************/

 function IsRegularWorkingDay($the_month, $the_day, $the_year) {
    global $hostname_DBConn;
    global $database_DBConn;
    global $username_DBConn;
    global $password_DBConn;
    global $DBConn;

    $the_date = date("Y-m-d", mktime(0, 0, 0, $the_month, $the_day, $the_year));
    $query = "SELECT * FROM signboom_holiday WHERE holiday = '$the_date'";
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    if (mysqli_num_rows($result) > 0) {
      return false;
    }
    else {
      return true;
    }
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
  }
 

/********************************************************************
 * DetermineReadyDate:
 *
 *  This function is called by allorder.js to calculate the ready date for an order
 *  Here is the algorithm.
 *
 *  1. Cut-off is still noon. Orders submitted after 12 pm PST are treated as if they 
 *     were submitted the following day instead. 
 *  
 *  2. Everything then starts out as available the next business day. 
 *  
 *  2.5. Add a single day if the Order is over A square feet. 
 *  
 *  3. Add additional day for every additional B square feet on the Order. 
 *  
 *  4. Add extra days based on what has been placed in the Options database and selected 
 *     on the order. 
 *  
 *  5. Everything is ready by 3pm on the “calculated” business day (ignoring weekends 
 *     and shutdown days) 
 *  
 *  6. Rushes are calculated as half the time (move ready time to 10am in case where the 
 *     standard date and rush are the same day) 
 *  
 *  7. HOTS. Don't give a date… They are set to CALL. 
 *
 ********************************************************************/

  function DetermineReadyDate($grandtotal_sqfootage, $extra_days, $start_day_numeric) {
    global $hostname_DBConn;
    global $database_DBConn;
    global $username_DBConn;
    global $password_DBConn;
    global $DBConn;

    // Look to see if any items in the order are batch items. When there are no batch items,
    // $start_day_numeric is 0. When there ARE batch items,  $start_day will be between 1 (Monday) and 7 (Sunday).
    // Convert the number to the name of that weekday.
    $days = array(
      0 => '',
      1 => 'Monday',
      2 => 'Tuesday',
      3 => 'Wednesday',
      4 => 'Thursday',
      5 => 'Friday',
      6 => 'Saturday',
      7 => 'Sunday'
    );
    $start_day = $days[$start_day_numeric];
    $today_day_numeric = date('N');
    $today_day = $days[date('N')];

    // ------------------------ Get Vancouver date and time---------------------------
    // We are working with three date formats: timestamp, DateTime and string.
    // Last part of variable name tells you which format that variable uses.

    // Our server is in a location that is 3 hours ahead of us.
    // Once we have PHP 5.1 and can set the default time zone, we can get rid of the -3 in the line below.
    $now_vancouver_timestamp = mktime(date("H")-3, date("i"), date("s"), date("m"), date("d"), date("Y"));

    $today_vancouver_timestamp = strtotime(date("Y-m-d", $now_vancouver_timestamp));
    $hour = date("H", $now_vancouver_timestamp);

$now_vancouver_string = date("Y-m-d H:i:s", $now_vancouver_timestamp);   // e.g.2001-03-10 17:16:18 (the mysql DATETIME format)
$today_vancouver_string = date("Y-m-d", $today_vancouver_timestamp);   // e.g.2001-03-10 

    // ------------------------- Read factors from database ---------------------------

    // defcutoff is the cutoff time for orders, in 24 hour clock.  This is stored in the database. 
    // It is set through that admin website in Vancouver time zone.  Usually something like 12 or 13.
    // Read the cutoff time from the database.  If you can't assume it is 12 noon.
    $defcutoff = 12;
    $sq_feet_time_a = 175;
    $sq_feet_time_b = 750;
    $query = "SELECT cutofftime, sqfttimea, sqfttimeb FROM signboom_parm WHERE ID = 1"; 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) { 
      $defcutoff = $row['cutofftime'];
      $sq_feet_time_a = $row['sqfttimea'];
      $sq_feet_time_b = $row['sqfttimeb'];
    } 
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

    // Figure out how many days to give ourselves for printing.
    // Special case.  If square footage is <= 175 sq ft, then order is due the next business day.
    if ($grandtotal_sqfootage <= $sq_feet_time_a) {
        $sqfootage_days = 1; // next day
    }
    else {
      // Calculate ship date based on square footage over and above 175.
      if (($grandtotal_sqfootage - $sq_feet_time_a) % $sq_feet_time_b == 0)
          $sqfootage_days = (($grandtotal_sqfootage - $sq_feet_time_a) / $sq_feet_time_b) + 1; // +1 is to cover the day for the first 175 sqft
      else
          $sqfootage_days = intdiv(($grandtotal_sqfootage - $sq_feet_time_a), $sq_feet_time_b) + 1 + 1;  // second +1 is to cover the day for the first 175 sqft
    }

    // -------------------- Calculate 'effective order date' for ready date calculation ---------------

    // Below we need to use $today_vancouver_date. If we use $today_vancouver_datetime, 
    // date_modify("next X") won't work.

    // If order is placed past the cutoff time, treat the order as if it came in on the
    // following day at 6 am. 
    if ($hour >= $defcutoff)
    {
      // Special case: If the order contains batch item and is placed after cutoff time 
      // on the DAY BEFORE the batch day, then treat it as if it came in at 6am the DAY 
      // AFTER batch day.
      if (($start_day_numeric - $today_day_numeric) == 1)
      {
        $effective_order_date_datetime = date_modify(date_create($today_vancouver_string), "+2 days");
      }
      else
      {
        $effective_order_date_datetime = date_modify(date_create($today_vancouver_string), "+1 days");
      }
    }
    else
    {
      $effective_order_date_datetime = date_create($today_vancouver_string);
    }

    // In batch cases, set $effective_order_date to the next $start_day. Note: If an order has an
    // effective order date of Friday at this point in the calculations, and the $start_day is Friday,
    // we set the effective order date to the NEXT Friday, not this one.  e.g. We don't want to be 
    // printing batch orders today that were just placed this morning. (Conveniently, that's how
    // the "next Friday" feature of date_modify works.) 
    if ($start_day != '')
    {
      //echo "modify effective order date based on start day of '$start_day'<br>";
      $effective_order_date_datetime = date_modify($effective_order_date_datetime, "next $start_day");
    }

    $day = date_format($effective_order_date_datetime, 'd');
    $month = date_format($effective_order_date_datetime, 'm');
    $year = date_format($effective_order_date_datetime, 'Y');

    // -------------------- Calculate 'ready date' for this order ---------------

    // Enforce maximum extra_days of 3.  This number will be configurable in admin soon.
    if ($extra_days > 3) $extra_days = 3;
    $ready_ship = $sqfootage_days + $extra_days;
    // Put upper limit of 21 days (calendar time) on delivery.  So 15 business days.
    if ($ready_ship > 15) $ready_ship = 15;
    // Compute ready date, taking into consideration weekends and holidays.
    // Standard ready date is always at 3 PM.
    $standard_ready = ComputeReadyDate($day, $month, $year, $ready_ship, $start_day_numeric) . " 3PM";
    $standard_ready_formatted = date("m/d/Y", $standard_ready) . " 3PM";

    // Calculate rush shipment date.  Divide ready_ship by 2 and then round up .5's to next integer.
    $rush_ship = intdiv($ready_ship, 2) + $ready_ship % 2;
    // Put upper limit of 15 days (calendar time) on rush delivery.  So 10 business days.
    if ($rush_ship > 10) $rush_ship = 10;

/*
    // OLD: Calculate rush shipment time.  If same date as standard, make it 10 AM. Otherwise 3 PM.
    // We are no longer offering 10AM rush time.
    if (strcmp($rush_ship, $ready_ship) == 0) // if strcmp gives 0, they are both same day
      $rush_time = " 10AM";  // rush job needs to be ready earlier that day
    else
*/
      $rush_time = " 3PM";

   /*
   echo "True order date: " . $now_vancouver_string .
         "<br>Effective order date: " . $year . '-' . $month . '-' . $day .
         "<br>Days for square footage: " . $sqfootage_days .
         "<br>Extra days for finishing options:  " . $extra_days . 
         "<br>Days for standard:  " . $ready_ship .
         "<br>Days for rush:  " . $rush_ship . 
         "<br>Time for rush:  " . $rush_time . "<br>";
    */

    // Compute ready date, taking into consideration weekends and holidays.
    $rush_ready = ComputeReadyDate($day, $month, $year, $rush_ship, $start_day_numeric) + $rush_time;
    $rush_ready_formatted = date("m/d/Y", $rush_ready) . $rush_time;

    $return_value = "~" . $standard_ready_formatted . "~" . $rush_ready_formatted . "~";
    echo $return_value;
  }

  $square_footage = $_GET['square_footage'];
  $days_for_finishing = $_GET['days_for_finishing'];
  $batch_start_day = $_GET['batch_start_day'];
  DetermineReadyDate($square_footage, $days_for_finishing, $batch_start_day);
  return true;
?>
