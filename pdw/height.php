<?php 
require('authadmin.php');

/*
        signboom_linedetail.readydatetime AS readydatetime, signboom_linedetail.readydate AS readydate, 
	signboom_linedetail.filename AS filename, 
        signboom_linedetail.rushtype AS rushtype, signboom_linedetail.rushcost AS rushcost,
        signboom_ordermast.refnum AS refnum
*/

$query_jobs = <<< End_Of_Query
(SELECT signboom_linedetail.id AS jobid, signboom_linedetail.product AS product, 
        signboom_ordermast.ID AS orderid, signboom_ordermast.ordertype AS ordertype,
        signboom_linedetail.cost AS cost, 
        signboom_linedetail.itemheight AS height, signboom_linedetail.itemwidth AS width,
        signboom_linedetail.squarefootage AS square_footage, signboom_linedetail.options AS options,
        signboom_linedetail.quantity AS quantity, signboom_linedetail.RB as double_sided,
	signboom_linedetail.filename AS filename
 FROM   signboom_linedetail, signboom_ordermast 
 WHERE (signboom_ordermast.AcctName = 'TED2018') AND 
       (signboom_ordermast.ID = signboom_linedetail.orderid) AND 
       (signboom_ordermast.hidden != 'yes')
) 
ORDER BY CAST(height AS DECIMAL) ASC
End_Of_Query;


// what is cost column? per item or line total? before or after discount?
// try to parse out type of finishing from filename; if filename doesn't parse, leave it empty

$jobs = mysql_query($query_jobs, $DBConn) or die(mysql_error());
$num_jobs = mysql_num_rows($jobs);
if ($my_debug) echo "Number of files found by query: $num_jobs<br>";

$total_product_cost = 0;
$job_array = array();
$rush_orders = array();

for ($i = 0; $i < $num_jobs; $i++) 
{
  // Grab a job from the query results.
  $row = mysql_fetch_assoc($jobs);
  if ($row == FALSE) echo "Could not read job from database.";

  $row['cost'] = ltrim($row['cost'], "$"); // remove dollar sign before saving cost, so we can add cost up
  if (($row['double_sided'] == 'RB-X') || ($row['double_sided'] == '')) // bug in event page: has blank when it should be RB-X
    $row['double_sided'] = 'No';
  else
    $row['double_sided'] = 'Yes';

  // Rush/hot costs are order-wide (not file-specific) when ordered on regular order form.
  if ($row['ordertype'] == "MIX") 
  {
    $row['ordertype'] = "Signboom";
    $information = explode("_", $row['filename']);
    if (count($information) == 8)
    {
      $row['refnum'] = $information[4] . '_' . $information[5] . '_' .  $information[7];
      $row['finishing_option_set'] = $information[6];
    }
    else
    {
      $row['refnum'] = $row['filename'];
      $row['finishing_option_set'] = '';
    }
  }
  else // Events have reference built in to filename
  {
    $row['ordertype'] = "PDW";
    $information = explode("_", $row['filename']);
    $row['refnum'] = $information[4] . '_' . $information[5] . '_' .  $information[7];
    $row['finishing_option_set'] = $information[6];
  }

  // add this row into an array which the template will display
  //echo $row['ordertype'] . " " . $row['orderid'] . " " . $row['jobid'] . ": " . $row['product'] . " " . $row['readydate'] . " " .  $row['cost'] . " " . $row['rushtype'] . " " .  $row['rushcost'] . " " . $row['refnum'] . "<br>";

  $total_product_cost += $row['cost'];
  $row['unit_cost'] = round($row['cost'] / $row['quantity'], 2);

  $job_array[] = $row; 

} // end of for loop  
mysql_free_result($jobs);

$page_title = 'Details: Sorted by Height';
include('templates/details.html.php');
?>
