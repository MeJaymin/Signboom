<?php

  $my_account_name =  $GLOBALS['MM_AcctName'];

/************ Read parameters passed in. ************/

  if (isset($_GET['special_case'])) 
    $special_case = $_GET['special_case'];
  else 
    $special_case = "";
    
  $start_date_time = $_GET['start'];
  $end_date_time = $_GET['end'];
  $stage = $_GET['stage'];

/************ Create query. ************/

$select_query1 = <<< End_Of_Query
SELECT signboom_ordermast.ID AS ID, signboom_linedetail.id AS jobid, signboom_ordermast.ordertype AS ordertype, 
        signboom_linedetail.rushtype AS rushtype, signboom_linedetail.readydatetime AS readydatetime, 
        signboom_ordermast.AcctName AS AcctName, signboom_ordermast.date_created AS date_created,
        signboom_ordermast.firstorder AS firstorder, signboom_ordermast.returningcustomer AS returningcustomer,
        signboom_linedetail.proofed AS proofed, signboom_linedetail.printed AS printed,
        signboom_linedetail.finished AS finished, signboom_linedetail.packed AS packed,
        signboom_linedetail.printedarea AS squarefootage, signboom_linedetail.readydate AS readydate,
        signboom_linedetail.AL AS AdhesiveLamination, signboom_linedetail.RL as RigidLamination,
        signboom_linedetail.AF AS AdhesiveCutting, signboom_linedetail.RF as RigidCutting,
        signboom_linedetail.BF as BannerCutting, signboom_linedetail.product as product,
        signboom_ordermast.refnum AS refnum, signboom_ordermast.customernotes AS customernotes,
        signboom_ordermast.Uploaded AS Uploaded, signboom_ordermast.email AS email, 
        signboom_ordermast.shiptype AS shiptype, signboom_ordermast.ordercompleted AS ordercompleted, 
        signboom_ordermast.orderinvoiced AS orderinvoiced, signboom_ordermast.team AS team,
        signboom_ordermast.shipattn AS shipattn, signboom_ordermast.shipcompany AS shipcompany,
        signboom_ordermast.shipaddress AS shipaddress, signboom_ordermast.shipcity AS shipcity,
        signboom_ordermast.shipprov AS shipprov, signboom_ordermast.shipzip AS shipzip,
        signboom_ordermast.shipcountry AS shipcountry, signboom_ordermast.shiptoadd AS shiptoadd,
        signboom_ordermast.documentname AS documentname,
        signboom_allproducts.ProdnSortGroup AS ProdnSortGroup, signboom_allproducts.ProdnSortOrder AS ProdnSortOrder 
FROM    signboom_linedetail, signboom_ordermast, signboom_allproducts 
WHERE   ((signboom_ordermast.ID = signboom_linedetail.orderid) AND 
         (signboom_linedetail.product = signboom_allproducts.Code) AND
         (signboom_ordermast.hidden != 'yes') 
End_Of_Query;

  // filter so that the client can only see their own orders
  $select_query1 .= " AND (signboom_ordermast.AcctName = '$my_account_name')";

  if ($special_case == "TOTAL") {
    // change this so that hots without ready dates are included; those have readydatetime of all 0's 
    $select_query1 .= " AND (((signboom_linedetail.readydatetime >= '$start_date_time') AND (signboom_linedetail.readydatetime < '$end_date_time')) OR ((signboom_linedetail.rushtype = 'HOT') AND (signboom_linedetail.readydatetime = '0000-00-00 00:00:00')))";
  }
  else if ($special_case == "HOT") {
    // only want those jobs which are rush type of HOT, with no ready date 
    $select_query1 .= " AND (signboom_linedetail.rushtype = 'HOT')";
  }
  else {
    // filter based on ready date
    $select_query1 .= " AND (signboom_linedetail.readydatetime >= '$start_date_time') AND (signboom_linedetail.readydatetime < '$end_date_time')";
  }

 if ($stage == "UPLOADING") {
    $select_query1 .= " AND (signboom_ordermast.Uploaded != 'Yes')";
 } else if ($stage == "PREFLIGHT") {
    $select_query1 .= " AND (signboom_ordermast.Uploaded = 'Yes') AND (signboom_linedetail.proofed = 'no')";
  } else if ($stage == "QUEUED") {
    $select_query1 .= " AND (signboom_ordermast.Uploaded = 'Yes') AND (signboom_linedetail.proofed = 'yes') AND (signboom_linedetail.printed = 'no')";
  } else if ($stage == "PRINTED") {
    $select_query1 .= " AND (signboom_ordermast.Uploaded = 'Yes') AND (signboom_linedetail.printed = 'yes') AND (signboom_linedetail.packed = 'no')";
  } else if ($stage == "COMPLETE") {
    $select_query1 .= " AND (signboom_ordermast.Uploaded = 'Yes') AND (signboom_linedetail.printed = 'yes') AND (signboom_linedetail.packed = 'yes')";
  }

  $query_jobs = $select_query1 . ") ORDER BY readydatetime ASC, date_created ASC, jobid ASC ";

  $jobs = mysql_query($query_jobs, $DBConn) or die("queryDashboard: Could not read orders from database:" . mysql_error() . "<br><br>" . $query_jobs);
  $number_of_rows = mysql_num_rows($jobs);
  $num_jobs = $number_of_rows;

?>

