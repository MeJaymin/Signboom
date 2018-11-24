<?php 

/************ Initialize variables. ************/
  $orders_complete_avg = 0;
  $files_complete_avg = 0;
  $sqft_complete_avg = 0;
  $waste_complete_avg = 0;
  $orders_complete_today = 0;
  $files_complete_today = 0;
  $sqft_complete_today = 0;
  $waste_complete_today = 0;
  $orders_complete_yesterday = 0;
  $files_complete_yesterday = 0;
  $sqft_complete_yesterday = 0;
  $waste_complete_yesterday = 0;
  $orders_complete_this_week = 0;
  $files_complete_this_week = 0;
  $sqft_complete_this_week = 0;
  $waste_complete_this_week = 0;
  $orders_complete_this_month = 0;
  $files_complete_this_month = 0;
  $sqft_complete_this_month = 0;
  $waste_complete_this_month = 0;
  $orders_complete_last_month = 0;
  $files_complete_last_month = 0;
  $sqft_complete_last_month = 0;
  $waste_complete_last_month = 0;
  $orders_complete_custom = 0;
  $files_complete_custom = 0;
  $sqft_complete_custom = 0;
  $waste_complete_custom = 0;


function calculateCompleted($completed_start, $completed_end, &$number_orders, &$number_files, &$square_feet, &$waste) {

  global $DBConn;
  global $my_debug;
  global $team;
  /*global $printer;*/

  // Find and count all orders with desired range of ready dates. 
  include('includes/query_orders_complete.php');
  $number_files = $number_of_completed_jobs;

  //Count all files and all square feet in those orders.
  $order_number = 0;
  $number_orders = 0;
  $square_feet = 0;
  $waste = 0;

  for ($i = 0; $i < $number_of_completed_jobs; $i++) {

    $row_completed_job = mysql_fetch_assoc($completed_jobs);
    if ($row_completed_job == FALSE) echo "Could not read job from database.";
    $order_id = $row_completed_job['ID'];
    $square_footage = $row_completed_job['squarefootage']; // mapped to printedarea in query 
    $waste_footage = $row_completed_job['wastearea'];

    // Sum up square feet.
    $square_feet += $square_footage;

    // Sum up waste.
    $waste += $waste_footage;
  
    // Count number of orders.
    if ($order_id != $order_number) {
      $number_orders++;
    }
    $order_number = $order_id;
  } 
  $square_feet = sprintf("%.0f", $square_feet);
  $waste = sprintf("%.0f", $waste);
  //echo "in calculateCompleted: $completed_start -> $completed_end: #orders = $number_orders, #files = $number_files, sqft = $square_footage, waste = $waste<br>";

}

function calculateAllCompleted() {
  global $my_debug;
  global $custom_start, $custom_end;
  global $orders_complete_avg,        $files_complete_avg,        $sqft_complete_avg,         $waste_complete_avg; 
  global $orders_complete_today,      $files_complete_today,      $sqft_complete_today,       $waste_complete_today;
  global $orders_complete_yesterday,  $files_complete_yesterday,  $sqft_complete_yesterday,   $waste_complete_yesterday; 
  global $orders_complete_this_week,  $files_complete_this_week,  $sqft_complete_this_week,   $waste_complete_this_week;  
  global $orders_complete_this_month, $files_complete_this_month, $sqft_complete_this_month,  $waste_complete_this_month; 
  global $orders_complete_last_month, $files_complete_last_month, $sqft_complete_last_month,  $waste_complete_last_month; 
  global $orders_complete_custom,     $files_complete_custom,     $sqft_complete_custom,      $waste_complete_custom;

  // Date version 1.5 we started tracking printed area for sqft.  Can't do stats for orders before this.
  $start_tracking_time = date("Y-m-d H:i:s", mktime(0, 0, 0, 5, 29, 2012));      // May 29, 2012


  // Orders completed today.
  $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d"),     date("Y"))); // today at 0:00:00
  $end   = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"))); // tomorrow at 0:00:00
  calculateCompleted($start, $end, $orders_complete_today, $files_complete_today, $sqft_complete_today, $waste_complete_today);

  // Orders completed yesterday.
  $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))); // yesterday at 00:00:00
  $end   = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d"),     date("Y"))); // today at 00:00:00
  calculateCompleted($start , $end, $orders_complete_yesterday, $files_complete_yesterday, $sqft_complete_yesterday, $waste_complete_yesterday);

  // Orders completed this work week: from Monday to Sunday.
  $today_digit = date("w", mktime()); //Return the day of the week in numerical format: 0 = Sunday, 6 = Saturday) 
  $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - $today_digit + 1, date("Y"))); // Past Monday 00:00:00
  if ($start < $start_tracking_time) $start = $start_tracking_time; // handle cutoff 
  $end   = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - $today_digit + 8, date("Y"))); // Upcoming Monday 00:00:00
  calculateCompleted($start, $end, $orders_complete_this_week, $files_complete_this_week, $sqft_complete_this_week, $waste_complete_this_week);

  // Orders completed this month.
  $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"),      1, date("Y"))); // first of this month
  if ($start < $start_tracking_time) $start = $start_tracking_time; // handle cutoff 
  $end   = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") + 1,  1, date("Y"))); // first of next month
  calculateCompleted($start, $end, $orders_complete_this_month, $files_complete_this_month, $sqft_complete_this_month, $waste_complete_this_month);

  // Orders completed last month.
  $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))); // first of last month
  if ($start < $start_tracking_time) $start = $start_tracking_time; // handle cutoff 
  $end   = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"),     1, date("Y")));        // first of this month
  calculateCompleted($start, $end, $orders_complete_last_month, $files_complete_last_month, $sqft_complete_last_month, $waste_complete_last_month);

  if (($custom_start == "") || ($custom_end == "")) {
    // If no custom period has been selected, set the start and end for calculating the totals and 
    // average per day to be the previous 12 month period + current MTD (to match ontime summary avg) 
    $start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y") - 1)); // first of this month, but last year
    if ($start < $start_tracking_time) $start = $start_tracking_time; // handle cutoff 
    $end =   date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"))); // tomorrow at 0:00:00
    }
  else {
    // If a custom period HAS been selected, use that date range.
    $start = $custom_start . " 00:00:00";
    if ($start < $start_tracking_time) $start = $start_tracking_time; // handle cutoff 
    $end   = $custom_end . " 23:59:59";
  }

  // Calculate totals for the desired period.
  calculateCompleted($start, $end, $orders_complete_custom, $files_complete_custom, 
                     $sqft_complete_custom, $waste_complete_custom);

  // Calculate the averages over that period.
  $days_passed = (strtotime($end) - strtotime($start)) / (60 * 60 * 24);
  $working_days_passed = $days_passed / 7.0 * 5.0;
  $orders_complete_avg = sprintf("%.0f", ($orders_complete_custom / $working_days_passed));
  $files_complete_avg = sprintf("%.0f", ($files_complete_custom / $working_days_passed));
  $sqft_complete_avg = sprintf("%.0f", ($sqft_complete_custom / $working_days_passed));
  $waste_complete_avg = sprintf("%.0f", ($waste_complete_custom / $working_days_passed));
}
?>
