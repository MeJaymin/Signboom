<?php 

  //**** Initialize variables.

  $orders_uploading_total = 0;
  $files_uploading_total = 0;
  $sqft_uploading_total = 0;
  $orders_preflight_total = 0;
  $files_preflight_total = 0;
  $sqft_preflight_total = 0;
  $orders_queued_total = 0;
  $files_queued_total = 0;
  $sqft_queued_total = 0;
  $orders_finishing_total = 0;
  $files_finishing_total = 0;
  $sqft_finishing_total = 0;
  $orders_total_total = 0;
  $files_total_total = 0;
  $sqft_total_total = 0;

  $orders_uploading_hots = 0;
  $files_uploading_hots = 0;
  $sqft_uploading_hots = 0;
  $orders_preflight_hots = 0;
  $files_preflight_hots = 0;
  $sqft_preflight_hots = 0;
  $orders_queued_hots = 0;
  $files_queued_hots = 0;
  $sqft_queued_hots = 0;
  $orders_finishing_hots = 0;
  $files_finishing_hots = 0;
  $sqft_finishing_hots = 0;
  $orders_total_hots = 0;
  $files_total_hots = 0;
  $sqft_total_hots = 0;

  $orders_uploading_late = 0;
  $files_uploading_late = 0;
  $sqft_uploading_late = 0;
  $orders_preflight_late = 0;
  $files_preflight_late = 0;
  $sqft_preflight_late = 0;
  $orders_queued_late = 0;
  $files_queued_late = 0;
  $sqft_queued_late = 0;
  $orders_finishing_late = 0;
  $files_finishing_late = 0;
  $sqft_finishing_late = 0;
  $orders_total_late = 0;
  $files_total_late = 0;
  $sqft_total_late = 0;

  $orders_uploading_today = 0;
  $files_uploading_today = 0;
  $sqft_uploading_today = 0;
  $orders_preflight_today = 0;
  $files_preflight_today = 0;
  $sqft_preflight_today = 0;
  $orders_queued_today = 0;
  $files_queued_today = 0;
  $sqft_queued_today = 0;
  $orders_finishing_today = 0;
  $files_finishing_today = 0;
  $sqft_finishing_today = 0;
  $orders_total_today = 0;
  $files_total_today = 0;
  $sqft_total_today = 0;

  $orders_uploading_this_week = 0;
  $files_uploading_this_week = 0;
  $sqft_uploading_this_week = 0;
  $orders_preflight_this_week = 0;
  $files_preflight_this_week = 0;
  $sqft_preflight_this_week = 0;
  $orders_queued_this_week = 0;
  $files_queued_this_week = 0;
  $sqft_queued_this_week = 0;
  $orders_finishing_this_week = 0;
  $files_finishing_this_week = 0;
  $sqft_finishing_this_week = 0;
  $orders_total_this_week = 0;
  $files_total_this_week = 0;
  $sqft_total_this_week = 0;

  $orders_uploading_next_week = 0;
  $files_uploading_next_week = 0;
  $sqft_uploading_next_week = 0;
  $orders_preflight_next_week = 0;
  $files_preflight_next_week = 0;
  $sqft_preflight_next_week = 0;
  $orders_queued_next_week = 0;
  $files_queued_next_week = 0;
  $sqft_queued_next_week = 0;
  $orders_finishing_next_week = 0;
  $files_finishing_next_week = 0;
  $sqft_finishing_next_week = 0;
  $orders_total_next_week = 0;
  $files_total_next_week = 0;
  $sqft_total_next_week = 0;

  $orders_uploading_later = 0;
  $files_uploading_later = 0;
  $sqft_uploading_later = 0;
  $orders_preflight_later = 0;
  $files_preflight_later = 0;
  $sqft_preflight_later = 0;
  $orders_queued_later = 0;
  $files_queued_later = 0;
  $sqft_queued_later = 0;
  $orders_finishing_later = 0;
  $files_finishing_later = 0;
  $sqft_finishing_later = 0;
  $orders_total_later = 0;
  $files_total_later = 0;
  $sqft_total_later = 0;

function calculateBacklog($team, $printer, $special_case, 
                          $start_date_time, $end_date_time, $stage, &$orders, &$files, &$sqft) {
  global $DBConn;
  global $my_debug;

$select_query1 = <<< End_Of_Query
SELECT signboom_ordermast.ID AS orderid, signboom_linedetail.id AS jobid, signboom_ordermast.ordertype AS ordertype, 
        signboom_linedetail.rushtype AS rushtype, signboom_linedetail.readydatetime AS readydatetime, 
        signboom_ordermast.AcctName AS accountname, signboom_ordermast.date_created AS date_created,
        signboom_ordermast.team AS team, signboom_linedetail.readydate AS readydate,
        signboom_linedetail.proofed AS proofed, signboom_linedetail.printed AS printed,
        signboom_linedetail.finished AS finished, signboom_linedetail.packed AS packed,
        signboom_linedetail.printedarea AS squarefootage 
FROM    signboom_linedetail, signboom_ordermast 
WHERE   ((signboom_ordermast.ID = signboom_linedetail.orderid) AND 
        (signboom_ordermast.ordercompleted = 'no') AND 
        (signboom_ordermast.hidden != 'yes') 
End_Of_Query;

  if ($special_case == "TOTAL") {
    // change this so that hots are included; those have readydatetime of all 0's and 
    $select_query1 .= " AND (((signboom_linedetail.readydatetime >= '$start_date_time') AND (signboom_linedetail.readydatetime < '$end_date_time')) OR ((signboom_linedetail.rushtype = 'HOT') AND (signboom_linedetail.readydatetime = '0000-00-00 00:00:00')))";
  }
  else if ($special_case == "HOT") {
    // only want those jobs which are rush type of HOT with no ready date yet
    $select_query1 .= " AND (signboom_linedetail.rushtype = 'HOT') ";
  }
  else {
    // filter based on ready date
    $select_query1 .= " AND (signboom_linedetail.readydatetime >= '$start_date_time') AND (signboom_linedetail.readydatetime < '$end_date_time')";
  }

  if ($team != "ALL") {
    $select_query1 .= " AND (signboom_ordermast.team = '$team')";
  }

  // An array of products specific to the HP printer only
  $hp_products = array(
    'PAV',
    'VAV'
  );

  if ($printer == "VUTEK") { // everything except $hp_products
    $select_query1 .= " AND product NOT IN ('" . implode("', '", $hp_products) . "') ";
  }
  else if ($printer == "HP") { // only $hp_products
    $select_query1 .= " AND product IN ('" . implode("', '", $hp_products) . "') ";
  }

  if ($stage == "UPLOADING") {
    $select_query1 .= " AND (signboom_ordermast.Uploaded != 'Yes')";
  } else if ($stage == "PREFLIGHT") {
    $select_query1 .= " AND (signboom_ordermast.Uploaded = 'Yes') AND (signboom_linedetail.proofed = 'no')";
  } else if ($stage == "QUEUED") {
    $select_query1 .= " AND (signboom_ordermast.Uploaded = 'Yes') AND (signboom_linedetail.proofed = 'yes') AND (signboom_linedetail.printed = 'no')";
  } else if ($stage == "PRINTED") {
    $select_query1 .= " AND (signboom_ordermast.Uploaded = 'Yes') AND (signboom_linedetail.printed = 'yes') AND (signboom_linedetail.packed = 'no')";
  }


  $query_jobs = $select_query1 . ") ORDER BY orderid DESC, jobid ASC ";
  // Count all orders, files, and square feet.
  $jobs = mysql_query($query_jobs, $DBConn) or die("calculateBacklog: Could not read orders from database:" . mysql_error() . "<br><br>" . $query_jobs);
  $orders = 0;
  $order_number = 0;
  $files = mysql_num_rows($jobs);
  $sqft = 0;
  for ($i = 0; $i < $files; $i++) {
    $row = mysql_fetch_assoc($jobs);

    // count how many orders are involved; jobs are sorted in reverse order id 
    $order_id = $row['orderid'];
    if ($order_id != $order_number) $orders++; 
    $order_number = $order_id;

    // count how many square feet are involved 
    $sqft += $row['squarefootage']; // mapped to printedarea in query
  }

}

function calculateAllBacklog() {
  global $my_debug;
  global $orders_uploading_total, $files_uploading_total, $sqft_uploading_total;
  global $orders_preflight_total, $files_preflight_total, $sqft_preflight_total;
  global $orders_queued_total, $files_queued_total, $sqft_queued_total;
  global $orders_finishing_total, $files_finishing_total, $sqft_finishing_total;
  global $orders_total_total, $files_total_total, $sqft_total_total;
  global $orders_uploading_hots, $files_uploading_hots, $sqft_uploading_hots;
  global $orders_preflight_hots, $files_preflight_hots, $sqft_preflight_hots;
  global $orders_queued_hots, $files_queued_hots, $sqft_queued_hots;
  global $orders_finishing_hots, $files_finishing_hots, $sqft_finishing_hots;
  global $orders_total_hots, $files_total_hots, $sqft_total_hots;
  global $orders_uploading_late, $files_uploading_late, $sqft_uploading_late;
  global $orders_preflight_late, $files_preflight_late, $sqft_preflight_late;
  global $orders_queued_late, $files_queued_late, $sqft_queued_late;
  global $orders_finishing_late, $files_finishing_late, $sqft_finishing_late;
  global $orders_total_late, $files_total_late, $sqft_total_late;
  global $orders_uploading_today, $files_uploading_today, $sqft_uploading_today;
  global $orders_preflight_today, $files_preflight_today, $sqft_preflight_today;
  global $orders_queued_today, $files_queued_today, $sqft_queued_today;
  global $orders_finishing_today, $files_finishing_today, $sqft_finishing_today;
  global $orders_total_today, $files_total_today, $sqft_total_today;
  global $orders_uploading_this_week, $files_uploading_this_week, $sqft_uploading_this_week;
  global $orders_preflight_this_week, $files_preflight_this_week, $sqft_preflight_this_week;
  global $orders_queued_this_week, $files_queued_this_week, $sqft_queued_this_week;
  global $orders_finishing_this_week, $files_finishing_this_week, $sqft_finishing_this_week;
  global $orders_total_this_week, $files_total_this_week, $sqft_total_this_week;
  global $orders_uploading_next_week, $files_uploading_next_week, $sqft_uploading_next_week;
  global $orders_preflight_next_week, $files_preflight_next_week, $sqft_preflight_next_week;
  global $orders_queued_next_week, $files_queued_next_week, $sqft_queued_next_week;
  global $orders_finishing_next_week, $files_finishing_next_week, $sqft_finishing_next_week;
  global $orders_total_next_week, $files_total_next_week, $sqft_total_next_week;
  global $orders_uploading_later, $files_uploading_later, $sqft_uploading_later;
  global $orders_preflight_later, $files_preflight_later, $sqft_preflight_later;
  global $orders_queued_later, $files_queued_later, $sqft_queued_later;
  global $orders_finishing_later, $files_finishing_later, $sqft_finishing_later;
  global $orders_total_later, $files_total_later, $sqft_total_later;

  global $team, $printer;
  global $start_total, $end_total, $start_late, $end_late, $start_today, $end_today;
  global $start_this_week, $end_this_week, $start_next_week, $end_next_week, $start_later, $end_later;

  // Date version 1.5 started tracking square feet on all orders. Can't do stats for orders before this.
  $start_tracking_time = date("Y-m-d H:i:s", mktime(0, 0, 0, 5, 29, 2012));  // May 29, 2012

  // All orders which are not yet completed.
  $special_case = "TOTAL";
  $start_total = $start_tracking_time; 
  $end_total =   date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")+ 2)); // 2 years in future 

  calculateBacklog($team, $printer, $special_case, $start_total, $end_total, "UPLOADING",
                   $orders_uploading_total, $files_uploading_total, $sqft_uploading_total);
  calculateBacklog($team, $printer, $special_case, $start_total, $end_total, "PREFLIGHT",
                   $orders_preflight_total, $files_preflight_total, $sqft_preflight_total);
  calculateBacklog($team, $printer, $special_case, $start_total, $end_total, "QUEUED",
                   $orders_queued_total, $files_queued_total, $sqft_queued_total);
  calculateBacklog($team, $printer, $special_case, $start_total, $end_total, "PRINTED",
                   $orders_finishing_total, $files_finishing_total, $sqft_finishing_total);
  calculateBacklog($team, $printer, $special_case, $start_total, $end_total, "TOTAL_BACKLOG",
                   $orders_total_total, $files_total_total, $sqft_total_total);

  // Hot orders which are not yet completed.
  $special_case = "HOT";

  calculateBacklog($team, $printer, $special_case, $start_total, $end_total, "UPLOADING",
                   $orders_uploading_hots, $files_uploading_hots, $sqft_uploading_hots);
  calculateBacklog($team, $printer, $special_case, $start_total, $end_total, "PREFLIGHT",
                   $orders_preflight_hots, $files_preflight_hots, $sqft_preflight_hots);
  calculateBacklog($team, $printer, $special_case, $start_total, $end_total, "QUEUED",
                   $orders_queued_hots, $files_queued_hots, $sqft_queued_hots);
  calculateBacklog($team, $printer, $special_case, $start_total, $end_total, "PRINTED",
                   $orders_finishing_hots, $files_finishing_hots, $sqft_finishing_hots);
  calculateBacklog($team, $printer, $special_case, $start_total, $end_total, "TOTAL_BACKLOG",
                   $orders_total_hots, $files_total_hots, $sqft_total_hots);

  // All orders which were due yesterday or before and are not yet completed.
  $special_case = "";
  $start_late = $start_tracking_time;
  $end_late =   date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d"), date("Y"))); // today at 0:00:00

  calculateBacklog($team, $printer, $special_case, $start_late, $end_late, "UPLOADING",
                   $orders_uploading_late, $files_uploading_late, $sqft_uploading_late);
  calculateBacklog($team, $printer, $special_case, $start_late, $end_late, "PREFLIGHT",
                   $orders_preflight_late, $files_preflight_late, $sqft_preflight_late);
  calculateBacklog($team, $printer, $special_case, $start_late, $end_late, "QUEUED",
                   $orders_queued_late, $files_queued_late, $sqft_queued_late);
  calculateBacklog($team, $printer, $special_case, $start_late, $end_late, "PRINTED",
                   $orders_finishing_late, $files_finishing_late, $sqft_finishing_late);
  calculateBacklog($team, $printer, $special_case, $start_late, $end_late, "TOTAL_BACKLOG",
                   $orders_total_late, $files_total_late, $sqft_total_late);

  // All orders which are due today and are not yet completed.
  $special_case = "";
  $start_today = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d"), date("Y"))); // today at 0:00:00
  $end_today =   date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"))); // tomorrow at 0:00:00

  calculateBacklog($team, $printer, $special_case, $start_today, $end_today, "UPLOADING",
                   $orders_uploading_today, $files_uploading_today, $sqft_uploading_today);
  calculateBacklog($team, $printer, $special_case, $start_today, $end_today, "PREFLIGHT",
                   $orders_preflight_today, $files_preflight_today, $sqft_preflight_today);
  calculateBacklog($team, $printer, $special_case, $start_today, $end_today, "QUEUED",
                   $orders_queued_today, $files_queued_today, $sqft_queued_today);
  calculateBacklog($team, $printer, $special_case, $start_today, $end_today, "PRINTED",
                   $orders_finishing_today, $files_finishing_today, $sqft_finishing_today);
  calculateBacklog($team, $printer, $special_case, $start_today, $end_today, "TOTAL_BACKLOG",
                   $orders_total_today, $files_total_today, $sqft_total_today);

  // All orders which are due this week and are not yet completed.
  $special_case = "";

  // Work out day of the week in numerical format
  // We want ISO standard: 1 = Monday and 7 = Sunday, e.g. date ("N"),
  // However, date("N") is only available in PHP 5.1.0 and we have PHP 4.4.9
  // So code it outselves using date("w"), which gives 0 = Sunday and 6 = Saturday
  $today_digit = date("w", mktime()); 
  if ($today_digit == 0) $today_digit = 7; // Change Sunday from 0 to 7.

  // >= Past Monday 00:00:00
  $start_this_week = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - $today_digit + 1, date("Y")));
  // make sure we don't try to handle jobs from before cutoff
  if ($start_this_week < $start_tracking_time) $start_this_week = $start_tracking_time; 
  // < Upcoming Monday 00:00:00
  $end_this_week =   date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - $today_digit + 8, date("Y"))); 

  calculateBacklog($team, $printer, $special_case, $start_this_week, $end_this_week, "UPLOADING",
                   $orders_uploading_this_week, $files_uploading_this_week, $sqft_uploading_this_week);
  calculateBacklog($team, $printer, $special_case, $start_this_week, $end_this_week, "PREFLIGHT",
                   $orders_preflight_this_week, $files_preflight_this_week, $sqft_preflight_this_week);
  calculateBacklog($team, $printer, $special_case, $start_this_week, $end_this_week, "QUEUED",
                   $orders_queued_this_week, $files_queued_this_week, $sqft_queued_this_week);
  calculateBacklog($team, $printer, $special_case, $start_this_week, $end_this_week, "PRINTED",
                   $orders_finishing_this_week, $files_finishing_this_week, $sqft_finishing_this_week);
  calculateBacklog($team, $printer, $special_case, $start_this_week, $end_this_week, "TOTAL_BACKLOG",
                   $orders_total_this_week, $files_total_this_week, $sqft_total_this_week);

  // All orders which are due next week and are not yet completed.
  $special_case = "";
  //Return the day of the week in numerical format: 0 = Sunday, 6 = Saturday 
  $today_digit = date("w", mktime()); 
  if ($today_digit == 0) $today_digit = 7; // Change Sunday from 0 to 7.
  // >= Next Monday 00:00:00 
  $start_next_week = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - $today_digit + 8, date("Y")));
  // make sure we don't try to handle jobs from before cutoff
  if ($start_next_week < $start_tracking_time) $start_next_week = $start_tracking_time; 
  // < Following Monday 00:00:00
  $end_next_week =   date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - $today_digit + 15, date("Y"))); 

  calculateBacklog($team, $printer, $special_case, $start_next_week, $end_next_week, "UPLOADING",
                   $orders_uploading_next_week, $files_uploading_next_week, $sqft_uploading_next_week);
  calculateBacklog($team, $printer, $special_case, $start_next_week, $end_next_week, "PREFLIGHT",
                   $orders_preflight_next_week, $files_preflight_next_week, $sqft_preflight_next_week);
  calculateBacklog($team, $printer, $special_case, $start_next_week, $end_next_week, "QUEUED",
                   $orders_queued_next_week, $files_queued_next_week, $sqft_queued_next_week);
  calculateBacklog($team, $printer, $special_case, $start_next_week, $end_next_week, "PRINTED",
                   $orders_finishing_next_week, $files_finishing_next_week, $sqft_finishing_next_week);
  calculateBacklog($team, $printer, $special_case, $start_next_week, $end_next_week, "TOTAL_BACKLOG",
                   $orders_total_next_week, $files_total_next_week, $sqft_total_next_week);

  // All orders which are due after next week and are not yet completed.
  $special_case = "";
  //Return the day of the week in numerical format: 0 = Sunday, 6 = Saturday) 
  $today_digit = date("w", mktime()); 
  if ($today_digit == 0) $today_digit = 7; // Change Sunday from 0 to 7.
  // >= Monday after next week 00:00:00 
  $start_later = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - $today_digit + 15, date("Y")));
  // make sure we don't try to handle jobs from before cutoff
  if ($start_later < $start_tracking_time) $start_later = $start_tracking_time; 
  // Up till, say, three years from now
  $end_later =   date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d"), date("Y") + 3)); 

  calculateBacklog($team, $printer, $special_case, $start_later, $end_later, "UPLOADING",
                   $orders_uploading_later, $files_uploading_later, $sqft_uploading_later);
  calculateBacklog($team, $printer, $special_case, $start_later, $end_later, "PREFLIGHT",
                   $orders_preflight_later, $files_preflight_later, $sqft_preflight_later);
  calculateBacklog($team, $printer, $special_case, $start_later, $end_later, "QUEUED",
                   $orders_queued_later, $files_queued_later, $sqft_queued_later);
  calculateBacklog($team, $printer, $special_case, $start_later, $end_later, "PRINTED",
                   $orders_finishing_later, $files_finishing_later, $sqft_finishing_later);
  calculateBacklog($team, $printer, $special_case, $start_later, $end_later, "TOTAL_BACKLOG",
                   $orders_total_later, $files_total_later, $sqft_total_later);
}

?>
