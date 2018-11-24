<?php

/************ Create query. ************/

$select_query3 = <<< End_Of_Query
SELECT signboom_ordermast.ID AS ID, signboom_linedetail.id AS jobid, signboom_ordermast.ordertype AS ordertype,
        signboom_linedetail.printedarea AS squarefootage,
        signboom_linedetail.wastearea AS wastearea,
        signboom_ordermast.timecompleted AS timecompleted,
        signboom_ordermast.ordercompleted AS ordercompleted,
        signboom_ordermast.AcctName AS AcctName, signboom_ordermast.team AS team  
FROM    signboom_linedetail, signboom_ordermast
WHERE   ((signboom_ordermast.ID = signboom_linedetail.orderid) AND
        (signboom_ordermast.ordercompleted = 'yes') AND
        (signboom_ordermast.hidden != 'yes') AND
        (signboom_ordermast.timecompleted >= '$completed_start') AND
        (signboom_ordermast.timecompleted < '$completed_end') 
End_Of_Query;

  if ($team != "ALL") {
    $select_query3 .= " AND (signboom_ordermast.team = '$team')";
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

  $query_completed_jobs = $select_query3 . ") ORDER BY ID DESC, jobid ASC ";
  //echo "QUERY: $query_completed_jobs<br>";
  $completed_jobs = mysql_query($query_completed_jobs, $DBConn) or die("queryOrdersComplete: Could not read orders from database:" . mysql_error() . "<br><br>" . $query_completed_jobs);
  $number_of_completed_jobs = mysql_num_rows($completed_jobs);
  //echo "NUMBER OF JOBS = $number_of_completed_jobs<br>";

  $start_row = 0;       // with dashboard, we put everything on one page
  $rows_per_page = 500; // some large number that we'll never reach

?>

