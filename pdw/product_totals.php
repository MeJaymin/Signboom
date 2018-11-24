<?php 
require('authadmin.php');

$query_jobs = <<< End_Of_Query
(SELECT signboom_ordermast.ID AS orderid, signboom_linedetail.id AS jobid, 
        signboom_linedetail.product AS product, signboom_ordermast.ordertype AS ordertype,
        signboom_linedetail.readydatetime AS readydatetime, signboom_linedetail.readydate AS readydate, 
        signboom_linedetail.cost AS cost, signboom_linedetail.filename AS filename, 
        signboom_linedetail.rushtype AS rushtype, signboom_linedetail.rushcost AS rushcost,
        signboom_linedetail.squarefootage AS squarefootage,
        signboom_ordermast.refnum AS refnum
 FROM   signboom_linedetail, signboom_ordermast 
 WHERE (signboom_ordermast.AcctName = 'TED2018') AND 
       (signboom_ordermast.ID = signboom_linedetail.orderid) AND 
       (signboom_ordermast.hidden != 'yes')
) 
ORDER BY product ASC
End_Of_Query;

$jobs = mysql_query($query_jobs, $DBConn) or die(mysql_error());
$num_jobs = mysql_num_rows($jobs);
if ($my_debug) echo "Number of files found by query: $num_jobs<br>";

$product_footage = 0;
$product_cost = 0;
$total_footage = 0;
$total_cost = 0;
$total_rush_fees = 0;
$job_array = array();
$rush_orders = array();
$current_product_name = "";

for ($i = 0; $i < $num_jobs; $i++) 
{

  // Grab a job from the query results.
  $row = mysql_fetch_assoc($jobs);
  if ($row == FALSE) echo "Could not read job from database.";

  $row['cost'] = ltrim($row['cost'], "$"); // remove dollar sign before saving cost, so we can add cost up
  $row['readydate'] = substr($row['readydate'], 0, 10);

  // Rush/hot costs are order-wide (not file-specific) when ordered on regular order form.
  if ($row['ordertype'] == "MIX") 
  {
    $order_id = $row['orderid'];
    // add this order id to a list; we'll get order-wide rush costs for that list later
    if (!in_array($order_id, $rush_orders)) $rush_orders[] = $order_id;
    // for the file-wide rush cost, assume 0 for now.
    $row['rushcost'] = "0.00";
    $row['ordertype'] = "Signboom";
  }
  else // Events have reference built in to filename
  {
    $information = explode("_", $row['filename']);
    $row['refnum'] = $information[7];
    $row['ordertype'] = "PDW";
  }

  // add this row into an array which the template will display
  //echo $row['ordertype'] . " " . $row['orderid'] . " " . $row['jobid'] . ": " . $row['product'] . " " . $row['readydate'] . " " .  $row['cost'] . " " . $row['rushtype'] . " " .  $row['rushcost'] . " " . $row['refnum'] . "<br>";

  $total_cost += $row['cost'];
  $total_rush_fees += $row['rushcost'];

  $job_array[] = $row; 

  if ($row['product'] != $current_product_name) 
  {
    if ($current_product_name != "") 
    {
      // remember the information for the previous product
      $this_product['product'] = $current_product_name;
      $this_product['squarefootage'] = sprintf('%.3f', $product_footage);
      $this_product['cost'] = sprintf('$%.2f', $product_cost);
      $product_information[] = $this_product;

      // now start tracking for new product
      $product_footage = 0;
      $product_cost = 0;
    }
    $current_product_name = $row['product'];
  }

  $product_footage += $row['squarefootage'];
  $product_cost += $row['cost'];

} // end of for loop  

// remember the information for the final product
$this_product['product'] = $current_product_name;
$this_product['squarefootage'] = sprintf('%.3f', $product_footage);
$this_product['cost'] = sprintf('$%.2f', $product_cost);
$product_information[] = $this_product;

mysql_free_result($jobs);

// Now calculate rush costs for orders placed through regular order system.
$order_rush_fees = 0;
for ($j = 0; $j < count($rush_orders); $j++) 
{
  $order_id = $rush_orders[$j];
  $query_rush_order = "SELECT rushfee FROM signboom_ordermast WHERE ID = $order_id";
  $rush_order = mysql_query($query_rush_order, $DBConn) or die(mysql_error());
  $row = mysql_fetch_assoc($rush_order);
  $rush_fee = ltrim($row['rushfee'], "$"); // remove dollar sign before saving cost, so we can add cost up
  //echo "order $order_id rush fee $rush_fee<br>";
  $order_rush_fees += $rush_fee;
  mysql_free_result($rush_order);
}

$total_total = $total_cost + $total_rush_fees + $order_rush_fees;
/*
echo "total product/finishing costs: $total_product_cost<br>";
echo "total file-based rush fees: $total_rush_fees<br>";
echo "total order-based rush fees: $order_rush_fees<br>";
*/

include('templates/product_totals.html.php');
?>
