<?php
// Find all the jobs in the order where id = order_id.
$query_jobs = "SELECT signboom_ordermast.ID AS ID, signboom_linedetail.id AS jobid, signboom_ordermast.ordertype AS ordertype, signboom_linedetail.rushtype AS rushtype, signboom_ordermast.shiptype AS shiptype, signboom_ordermast.customernotes AS customernotes, signboom_ordermast.refnum AS refnum, signboom_linedetail.readydate AS readydate, signboom_ordermast.AcctName AS AcctName, signboom_ordermast.Uploaded AS uploaded, signboom_ordermast.emailproofed AS emailproofed, signboom_ordermast.emailpacked AS emailpacked, signboom_ordermast.firstorder AS firstorder, signboom_ordermast.returningcustomer AS returningcustomer FROM signboom_linedetail, signboom_ordermast WHERE (signboom_linedetail.orderid = $order_id) AND (signboom_ordermast.ID = $order_id) ORDER BY jobid ASC";

if ($my_debug) echo "Query is: $query_jobs<br>";
$jobs = mysql_query($query_jobs, $DBConn) or die(mysql_error());
$num_jobs = mysql_num_rows($jobs);
if ($my_debug) echo "Number of jobs in results of query: $num_jobs<br>";
?>
