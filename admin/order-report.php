<?php 
  include ('authadmin.php'); 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  // Variables for controlling period over which we are reporting.
  // If nothing specified, default to the current month.
  $period_specified = false;
  if (isset($_GET['StartDate']) && isset($_GET['EndDate'])) {
    $start_date = $_GET['StartDate'];
    $end_date = $_GET['EndDate'];
    $start_date_time = $start_date . " 00:00:00";
    $end_date_time = $end_date . " 23:59:59";
    $period_specified = true;
    if (isset($my_debug)) echo "Period has been specified.<br>";
  }
  else
  {
    $start_date = date("Y-m-d", mktime(0, 0, 0, date("m"),  1, date("Y"))); // 1st day of THIS month 
    $end_date =   date("Y-m-d", mktime(0, 0, 0, date("m") + 1,  0, date("Y"))); // last day of THIS month = 0th of next month 
    $start_date_time = date("Y-m-d H:i:s", mktime(0,   0,  0, date("m"),  1, date("Y")));  // start of start date 
    $end_date_time =   date("Y-m-d H:i:s", mktime(23, 59, 59, date("m")+1,      0, date("Y")));  // end of end date 
    if (isset($my_debug)) echo "No period has been specified.<br>";
    include('../production/includes/date_picker.htm');
  }
  if (isset($my_debug)) echo "Start Date: $start_date   End Date: $end_date<br>";

if ($period_specified):

$query_jobs = <<< End_Of_Query
  (SELECT signboom_ordermast.ID AS order_id, signboom_linedetail.id AS file_id, 
          signboom_linedetail.linenum AS line_number, 
          date(signboom_ordermast.date_created) AS date_created, 
	  signboom_ordermast.AcctName as account_name,
	  signboom_ordermast.Uploaded as successfully_uploaded,
	  signboom_ordermast.shiptype as ship_type,
	  signboom_ordermast.freight as freight_charge,
	  signboom_ordermast.rushtype as rush_type,
	  signboom_ordermast.rushfee as rush_fee,
	  signboom_ordermast.subtotal as subtotal,
	  signboom_ordermast.setupfee as setup_fee,
	  signboom_ordermast.dct as discount_type,
	  signboom_ordermast.discount as discount_amount,
	  signboom_ordermast.Uploaded as successfully_uploaded,
	  signboom_ordermast.ordercompleted as order_completed,
	  signboom_ordermast.hidden as order_deleted,
	  signboom_ordermast.team as team,
          signboom_linedetail.product AS product, 
          signboom_linedetail.options AS options, 
          signboom_linedetail.quantity AS quantity, 
	  signboom_linedetail.itemheight AS height, signboom_linedetail.itemwidth AS width, 
          signboom_linedetail.cost AS cost, signboom_linedetail.dctcost AS discount_cost,
          signboom_linedetail.squarefootage AS square_footage ,
          signboom_linedetail.AF AS adhesive_cutting, 
          signboom_linedetail.AL AS adhesive_lamination, 
          signboom_linedetail.AI AS adhesive_ink_layers, 
          signboom_linedetail.BF AS banner_cutting, 
          signboom_linedetail.BB AS banner_back_side, 
          signboom_linedetail.BI AS banner_ink_layers, 
          signboom_linedetail.RF AS rigid_cutting, 
          signboom_linedetail.RL AS rigid_lamination, 
          signboom_linedetail.RB AS rigid_back_side, 
          signboom_linedetail.RH AS rigid_hanging, 
          signboom_linedetail.RE AS rigid_edges, 
          signboom_linedetail.RI AS rigid_ink_layers, 
          signboom_linedetail.RO AS rigid_orientation
   FROM   signboom_linedetail, signboom_ordermast 
   WHERE (signboom_ordermast.date_created >= '$start_date_time') AND 
         (signboom_ordermast.date_created <='$end_date_time') AND 
         (signboom_ordermast.ID = signboom_linedetail.orderid) AND 
         (signboom_ordermast.hidden != 'yes')
  ) 
  ORDER BY product ASC, options ASC, 
           adhesive_cutting ASC, adhesive_lamination ASC, adhesive_ink_layers ASC, 
	   banner_cutting ASC, banner_back_side ASC, banner_ink_layers ASC, 
	   rigid_cutting ASC, rigid_lamination ASC, rigid_back_side ASC, 
	   rigid_hanging ASC, rigid_edges ASC, rigid_ink_layers ASC, rigid_orientation ASC
End_Of_Query;
  if (isset($my_debug)) echo "<br><br>Query: " . $query_jobs . "<br><br>";

  $jobs = mysqli_query( $DBConn, $query_jobs) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $num_jobs = mysqli_num_rows($jobs);
  if (isset($my_debug)) echo "Number of files in results of query: $num_jobs<br>";

  // Create a nice filename.
  sscanf($start_date, "%d-%d-%d", $start_year, $start_month, $start_day);
  $start_year -= 2000;
  $start_date_2 = sprintf("%02d%02d%02d", $start_year, $start_month, $start_day);
  sscanf($end_date, "%d-%d-%d", $end_year, $end_month, $end_day);
  $end_year -= 2000;
  $end_date_2 = sprintf("%02d%02d%02d", $end_year, $end_month, $end_day);
  $filename = 'orders-' . $start_date_2 . '-to-' . $end_date_2 . '.csv';

  // send response headers to the browser
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment;filename=' . $filename);

  // Open the output stream
  $fp = fopen('php://output', 'w');

  // output header row (if at least one row exists)
  $row = mysqli_fetch_assoc($jobs);
  if ($row) 
  {
    fputcsv($fp, array_keys($row));
    // reset pointer back to beginning
    mysqli_data_seek($jobs,  0);
  }

  while ($row = mysqli_fetch_assoc($jobs))
  {
    fputcsv($fp, $row);
  }

  fclose($fp);
else:

  // Display the template 
  include ('templates/order-report.php'); 
  
endif;
?>
