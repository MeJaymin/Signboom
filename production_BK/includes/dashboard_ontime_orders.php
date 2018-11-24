<?php 

// For now these targets are hard-coded.  Later, make admin page so Len can
// set them, and save his choices in database, and read them from database here.
$target_ve = 0;
$target_2de = 1;
$target_1de = 2;
$target_ond = 94;
$target_1dl = 2;
$target_2dl = 1;
$target_vl = 0;
$target_ot = $target_ve + $target_2de + $target_1de + $target_ond;
$target_late = 100 - $target_ot;

// Initialize all the variables to zero.
for ($i = 1; $i <= 12; $i++) {
  $month_ot[$i] = 0;   $mtd_ot = 0;   $avg_ot = 0;
  $month_late[$i] = 0; $mtd_late = 0; $avg_late = 0;
  $month_ve[$i] = 0;   $mtd_ve = 0;   $avg_ve = 0;
  $month_2de[$i] = 0;  $mtd_2de = 0;  $avg_2de = 0;
  $month_1de[$i] = 0;  $mtd_1de = 0;  $avg_1de = 0;
  $month_ond[$i] = 0;  $mtd_ond = 0;  $avg_ond = 0;
  $month_1dl[$i] = 0;  $mtd_1dl = 0;  $avg_1dl = 0;
  $month_2dl[$i] = 0;  $mtd_2dl = 0;  $avg_2dl = 0;
  $month_vl[$i] = 0;   $mtd_vl = 0;   $avg_vl = 0;
}

// Remember readydatetime and timecompleted have both time and date, and the time matters.
function countOrders($start, $end, $earlylate, $lower_limit, $upper_limit) {
  global $DBConn;
  global $my_debug;

  // Special Case #1: Include forgiveness time of 3 hours for orders finished on the same day.
  // So we are looking for orders that are between 1 day early and three hours late.
  if (($earlylate == 1) && ($lower_limit == 0) && ($upper_limit == 1)) {
$query_orders_early  = <<< End_Of_Query
SELECT * 
FROM signboom_ordermast 
WHERE (readydatetime >= '$start') AND (readydatetime < '$end') AND
(DATEDIFF(readydatetime, timecompleted) = '0') AND 
(TIMEDIFF(timecompleted, readydatetime) < '03:00:00') AND 
(hidden != 'yes') AND (Uploaded = 'Yes') AND (ordercompleted = 'yes') AND (timecompleted != "0000-00-00 00:00:00")
ORDER BY ID DESC
End_Of_Query;
  }

  // Special Case #2: Take forgiveness time into account when counting up orders that are late by one day,
  // so orders don't get counted twice.  So we are looking for orders that are more than three hours late
  // and less than two days late.
  else if (($earlylate == 0) && ($lower_limit == "1") && ($upper_limit == "2")) {
$query_orders_late = <<< End_Of_Query
SELECT * 
FROM signboom_ordermast 
WHERE (readydatetime >= '$start') AND (readydatetime < '$end') AND
(timecompleted > readydatetime) AND
(TIMEDIFF(timecompleted, readydatetime) >= '03:00:00') AND 
(DATEDIFF(timecompleted, readydatetime) <= '1') AND 
(hidden != 'yes') AND (Uploaded = 'Yes') AND (ordercompleted = 'yes') AND (timecompleted != "0000-00-00 00:00:00")
ORDER BY ID DESC
End_Of_Query;
  }

  // General case
  else {
$query_orders_early  = <<< End_Of_Query
SELECT * 
FROM signboom_ordermast 
WHERE (readydatetime >= '$start') AND (readydatetime < '$end') AND
(timecompleted < readydatetime) AND
(DATEDIFF(readydatetime, timecompleted) >= '$lower_limit') AND 
(DATEDIFF(readydatetime, timecompleted) < '$upper_limit') AND 
(hidden != 'yes') AND (Uploaded = 'Yes') AND (ordercompleted = 'yes') AND (timecompleted != "0000-00-00 00:00:00")
ORDER BY ID DESC
End_Of_Query;

$query_orders_late = <<< End_Of_Query
SELECT * 
FROM signboom_ordermast 
WHERE (readydatetime >= '$start') AND (readydatetime < '$end') AND
(timecompleted > readydatetime) AND
(DATEDIFF(timecompleted, readydatetime) >= '$lower_limit') AND 
(DATEDIFF(timecompleted, readydatetime) < '$upper_limit') AND 
(hidden != 'yes') AND (Uploaded = 'Yes') AND (ordercompleted = 'yes') AND (timecompleted != "0000-00-00 00:00:00")
ORDER BY ID DESC
End_Of_Query;
}


  // Looking for early and ontime jobs.  On-time jobs are between 0 and 1 day early.
  if ($earlylate == 1) { 
    $orders = mysql_query($query_orders_early, $DBConn) or die(mysql_error());
    $numerator = mysql_num_rows($orders);
    return($numerator);
  }
  // Looking for late jobs.
  else if ($earlylate == 0) { 
    $orders = mysql_query($query_orders_late, $DBConn) or die(mysql_error());
    $numerator = mysql_num_rows($orders);
    return($numerator);
  }
}

function calculateOnTime($start_date_time, $end_date_time,
  &$month_ot,  &$month_late, &$month_ve, &$month_2de, &$month_1de, 
  &$month_ond, &$month_1dl, &$month_2dl, &$month_vl) {

  global $DBConn;
  global $my_debug;
  global $target_ot, $target_late, $target_ve, $target_2de; 
  global $target_1de, $target_ond, $target_1dl, $target_2dl, $target_vl;

  // Find and count all orders with desired range of ready dates. Don't indent <<< code snippet.
$query_orders = <<< End_Of_Query
SELECT * 
FROM signboom_ordermast 
WHERE (readydatetime >= '$start_date_time') AND (readydatetime < '$end_date_time') AND 
(Uploaded = 'Yes') AND (hidden != 'yes') AND (ordercompleted = 'yes') AND (timecompleted != "0000-00-00 00:00:00")
End_Of_Query;

  $orders = mysql_query($query_orders, $DBConn) or die("calculateOnTime: Could not read orders from database:" . mysql_error() . "<br><br>" . $query_orders);
  $denominator = mysql_num_rows($orders);

  // Find and count all orders which were completed more than 2 days early. (Say between 3 and 300 days.)
  $early = 1;
  $numerator = countOrders($start_date_time, $end_date_time, $early, "3", "300");
  if ($denominator == 0) 
    $month_ve = 0.0;
  else
    $month_ve = $numerator / $denominator * 100;

  // Find and count all orders which were completed 2 days early.
  $early = 1;
  $numerator = countOrders($start_date_time, $end_date_time, $early, "2", "3");
  if ($denominator == 0)
    $month_2de = 0.0;
  else 
    $month_2de = $numerator / $denominator * 100;

  // Find and count all orders which were completed 1 day early.
  $early = 1;
  $numerator = countOrders($start_date_time, $end_date_time, $early, "1", "2");
  if ($denominator == 0) 
    $month_1de = 0.0;
  else 
    $month_1de = $numerator / $denominator * 100;

  // Find and count all orders which were completed on the right day.
  $early = 1;
  $numerator = countOrders($start_date_time, $end_date_time, $early, "0", "1");
  if ($denominator == 0) 
    $month_ond = 0.0;
  else 
    $month_ond = $numerator / $denominator * 100;

  // Find and count all orders which were completed 1 day late.
  $early = 0;
  $numerator = countOrders($start_date_time, $end_date_time, $early, "1", "2");
  if ($denominator == 0) 
    $month_1dl = 0.0;
  else 
    $month_1dl = $numerator / $denominator * 100;

  // Find and count all orders which were completed 2 days late.
  $early = 0;
  $numerator = countOrders($start_date_time, $end_date_time, $early, "2", "3");
  if ($denominator == 0) 
    $month_2dl = 0.0;
  else 
    $month_2dl = $numerator / $denominator * 100;

  // Find and count all orders which were completed more than 2 days late. (Say between 3 and 20 days.)
  $early = 0;
    $numerator = countOrders($start_date_time, $end_date_time, $early, "3", "20");
  if ($denominator == 0) 
    $month_vl = 0.0;
  else 
    $month_vl = $numerator / $denominator * 100;

  $month_ot = $month_ve + $month_2de + $month_1de + $month_ond;
  $month_late = 100 - $month_ot;
}

function calculateAllOnTime() {
  global $my_debug;
  global $start_current_month, $end_current_month, $month_text, $start_month, $end_month; // Used by dashboard.php
  global $month_ot, $month_late, $month_ve, $month_2de, $month_1de, $month_ond, $month_1dl, $month_2dl, $month_vl;
  global $mtd_ot, $mtd_late, $mtd_ve, $mtd_2de, $mtd_1de, $mtd_ond, $mtd_1dl, $mtd_2dl, $mtd_vl;
  global $avg_ot, $avg_late, $avg_ve, $avg_2de, $avg_1de, $avg_ond, $avg_1dl, $avg_2dl, $avg_vl;

  // Identify the range of dates for the current month.  Used by dashboard.php.
  $start_current_month = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"),     1, date("Y"))); 
  $end_current_month   = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") + 1, 1, date("Y"))); 

  // Work out which months which are going to display information for.
  for ($i = 1; $i <= 12; $i++) {
    $month_text[$i]  = strtoupper(date("M", mktime(0, 0, 0, date("m") - $i, 1,  date("Y"))));   // Jan - Dec
  }

  // Date version 1.5 started tracking when orders were completed.  Can't do stats for orders before this.
  $start_tracking_time = date("Y-m-d H:i:s", mktime(0, 0, 0, 5, 29, 2012));  // May 29, 2012

  for ($i = 1; $i <= 12; $i++) {
    $start_date_time = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - $i,      1, date("Y"))); // >= first of month
    $end_date_time =   date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - $i + 1,  1, date("Y"))); // < first of next month
    $start_month[$i] = $start_date_time; // Used by dashboard.php
    $end_month[$i] = $end_date_time;     // Used by dashboard.php
    if ($start_date_time < $start_tracking_time) {
      // no stats available for that month
      // initialize value of variable will display (i.e. 0)
      $end_month[$i] = $start_month[$i]; // so that orders page linked to by dashboard.php displays 0 orders for this period
    }
    else {
      calculateOnTime($start_date_time, $end_date_time,
         $month_ot[$i], $month_late[$i], $month_ve[$i], $month_2de[$i], $month_1de[$i], 
         $month_ond[$i], $month_1dl[$i], $month_2dl[$i], $month_vl[$i]);
    }
  }

  // On time date for jobs over the past 12 months.  Do not include MTD.
  $start_date_time = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 12, 1, date("Y"))); // >= first of month, 12 mos ago
  $end_date_time =   date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"),      1, date("Y"))); // <  first of this month
  if ($start_date_time < $start_tracking_time) $start_date_time = $start_tracking_time;
  calculateOnTime($start_date_time, $end_date_time,
      $avg_ot, $avg_late, $avg_ve, $avg_2de, $avg_1de, $avg_ond, $avg_1dl, $avg_2dl, $avg_vl);

  // On time date for jobs this month, to date.
  $start_date_time = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"),      1, date("Y"))); // >= first of month
  $end_date_time =   date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") + 1,  1, date("Y"))); // < first of next month
  if ($start_date_time < $start_tracking_time) $start_date_time = $start_tracking_time;
  calculateOnTime($start_date_time, $end_date_time,
      $mtd_ot, $mtd_late, $mtd_ve, $mtd_2de, $mtd_1de, $mtd_ond, $mtd_1dl, $mtd_2dl, $mtd_vl);
}

?>
