<?php 
  include ('authadmin.php'); 
  include('../production/includes/date_picker.htm');
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  // Variables for controlling period over which we are reporting.
  // If nothing specified, default to the current month.
  if (isset($_GET['StartDate']) && isset($_GET['EndDate'])) {
    $start_date = $_GET['StartDate'];
    $end_date = $_GET['EndDate'];
    $start_date_time = $start_date . " 00:00:00";
    $end_date_time = $end_date . " 23:59:59";
    if (isset($my_debug)) echo "Period has been specified.<br>";
  }
  else {
    $start_date = date("Y-m-d", mktime(0, 0, 0, date("m"),  1, date("Y"))); // 1st day of THIS month 
    $end_date =   date("Y-m-d", mktime(0, 0, 0, date("m") + 1,  0, date("Y"))); // last day of THIS month = 0th of next month 
    $start_date_time = date("Y-m-d H:i:s", mktime(0,   0,  0, date("m"),  1, date("Y")));  // start of start date 
    $end_date_time =   date("Y-m-d H:i:s", mktime(23, 59, 59, date("m")+1,      0, date("Y")));  // end of end date 
    if (isset($my_debug)) echo "No period has been specified.<br>";
  }
  if (isset($my_debug)) echo "Start Date: $start_date   End Date: $end_date<br>";

$query_offcuts = <<< End_Of_Query
  (SELECT signboom_offcuts.Material AS Material, signboom_offcuts.Width AS Width, 
          signboom_offcuts.Length AS Length, signboom_offcuts.Quantity AS Quantity, 
          signboom_allproducts.CostWaste AS CostWaste
   FROM   signboom_offcuts, signboom_allproducts
   WHERE  (Claimed = 1) AND 
          (((DateClaimed >= '$start_date_time') AND (DateClaimed <= '$end_date_time')) OR 
	   ((DateUsed >= '$start_date_time') AND (DateUsed <= '$end_date_time'))) AND
          (signboom_offcuts.Material = signboom_allproducts.Code) 
  ) 
  ORDER BY Material ASC
End_Of_Query;
  if (isset($my_debug)) echo "<br><br>Query: " . $query_offcuts . "<br><br>";

  $offcuts = mysqli_query( $DBConn, $query_offcuts) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $num_offcuts = mysqli_num_rows($offcuts);
  if (isset($my_debug)) echo "Number of items in results of query: $num_offcuts<br>";

  $total_sqft = 0.0;
  $total_items = 0;
  $total_dollar_value = 0.0;
  $current_product_name = "";
  $data = "";

  for ($i = 0; $i < $num_offcuts; $i++) 
  {
    // Grab an offcut from the query results.
    $row = mysqli_fetch_assoc($offcuts);
    if ($row == FALSE) echo "Could not read offcut information from database.";
    /*
    $offcut_id = $row['OffcutId'];
    $date_added = $row['DateAdded'];
    $person_added = $row['PersonAdded'];
    $claimed = $row['Claimed'];
    $date_claimed = $row['DateClaimed'];
    $person_claimed = $row['PersonClaimed'];
    $used = $row['Used'];
    $date_used = $row['DateUsed'];
    $person_used = $row['PersonUsed'];
    $paid_for = $row['PaidFor'];
    */
    $product = $row['Material'];
    $width = $row['Width'];
    $length = $row['Length'];
    $quantity = $row['Quantity'];
    $media_cost = $row['CostWaste'];

    $sqft = $width * $length * $quantity / 144.0; // width and length are in inches
    $dollar_value = $sqft * $media_cost;

    if ($product != $current_product_name) 
    {
      // SPECIAL CASE - FIRST PRODUCT
      if ($current_product_name == "")  
      {
        $current_product_name = $product; 
        $product_sqft = $sqft;
        $product_items = $quantity;
	$product_dollar_value = $dollar_value;
      }
      else
      {
        // PRINT OUT TOTALS FOR THIS PRODUCT AND THEN START NEW TOTALS
        // This it a bit of a hack, but time is short right now.  To follow the controller-template model properly, 
        // this should be an array, which is then formatted using code in the template.
        $temp = sprintf("<tr><td>%s</td><td style=\"text-align: right;\">%.3f</td><td style=\"text-align: right;\">%d</td><td style=\"text-align: right;\">%.2f</td></tr>", $current_product_name, $product_sqft, $product_items, $product_dollar_value);
        $data .= $temp;
        $product_sqft = $sqft;
        $product_items = $quantity;
        $current_product_name = $product;
	$product_dollar_value = $dollar_value;
      }
    }
    else
    {
      $product_sqft += $sqft;
      $product_items += $quantity;
      $product_dollar_value += $dollar_value;
    }

    $total_sqft += $sqft;
    $total_items += $quantity;
    $total_dollar_value += $dollar_value;
  }

  if(isset($product_sqft) || isset($product_items))
  {
    // SPECIAL CASE - FINAL PRODUCT 
    $temp = sprintf("<tr><td>%s</td><td style=\"text-align: right;\">%.3f</td><td style=\"text-align: right;\">%d</td><td style=\"text-align: right;\">%.2f</td></tr>", $current_product_name, $product_sqft, $product_items, $product_dollar_value);
    $data .= $temp;
  }

  // Display the parameters
  include ('templates/offcuts-used-report.php'); 
  
  // Free memory. 
  /*((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);*/
?>
