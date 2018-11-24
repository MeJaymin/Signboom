<?php

$aft_today = date('Y-m-d 15:00:00', time());

/************ Build array of all existing product categories.  ************/
$query_categories = 'SELECT code FROM signboom_category WHERE 1';
$result_categories = mysql_query($query_categories, $DBConn) or die("queryDashboard: Could not read categories from database:" . mysql_error() . "<br><br>" . $query_categories);
$array_categories = array();
while ($row_category = mysql_fetch_array($result_categories))
{
  $category_name = $row_category['code'];
  $array_categories[] = $category_name;
}

/************ Read parameters passed in. ************/

  if (isset($_REQUEST['product'])) 
    $product = $_REQUEST['product'];
  else
    exit;

  if (isset($_REQUEST['queue'])) 
    $queue = $_REQUEST['queue'];
  else 
    exit;
  
/************ Create start of query. ************/

$query_part_1 = <<< End_Of_Query_1
SELECT signboom_ordermast.ID AS ID, signboom_linedetail.id AS jobid, signboom_ordermast.ordertype AS ordertype, 
        signboom_ordermast.subtotal AS cost,
        signboom_linedetail.rushtype AS rushtype, signboom_linedetail.readydatetime AS readydatetime, 
        signboom_ordermast.AcctName AS AcctName, signboom_ordermast.date_created AS date_created,
        signboom_ordermast.firstorder AS firstorder, signboom_ordermast.returningcustomer AS returningcustomer,
        signboom_linedetail.proofed AS proofed, signboom_linedetail.printed AS printed,
        signboom_linedetail.finished AS finished, signboom_linedetail.packed AS packed,
        signboom_linedetail.printedarea AS squarefootage, signboom_linedetail.readydate AS readydate,
        signboom_linedetail.AL AS AdhesiveLamination, signboom_linedetail.RL as RigidLamination,
        signboom_linedetail.AF AS AdhesiveCutting, signboom_linedetail.RF as RigidCutting,
        signboom_linedetail.BF as BannerCutting, 
        signboom_linedetail.AP AS AdhesivePrintSpeed, signboom_linedetail.RP as RigidPrintSpeed,
        signboom_linedetail.BP as BannerPrintSpeed, 
        signboom_linedetail.AK AS AdhesiveInkFinish, signboom_linedetail.RK as RigidInkFinish,
        signboom_linedetail.BK as BannerInkFinish, 
        signboom_linedetail.BB AS BannerSides, signboom_linedetail.RB as RigidSides,
	signboom_linedetail.product as product, 
        signboom_ordermast.refnum AS refnum, signboom_ordermast.customernotes AS customernotes,
        signboom_ordermast.Uploaded AS Uploaded, signboom_ordermast.email AS email, 
       signboom_ordermast.shiptype AS shiptype, signboom_ordermast.ordercompleted AS ordercompleted, 
	signboom_ordermast.team AS team,
        signboom_ordermast.shipattn AS shipattn, signboom_ordermast.shipcompany AS shipcompany,
        signboom_ordermast.shipaddress AS shipaddress, signboom_ordermast.shipcity AS shipcity,
        signboom_ordermast.shipprov AS shipprov, signboom_ordermast.shipzip AS shipzip,
        signboom_ordermast.shipcountry AS shipcountry, signboom_ordermast.shiptoadd AS shiptoadd,
        signboom_ordermast.documentname AS documentname,
        signboom_allproducts.ProdnSortGroup AS ProdnSortGroup, signboom_allproducts.ProdnSortOrder AS ProdnSortOrder 
FROM    signboom_linedetail, signboom_ordermast, signboom_allproducts 
WHERE   ((signboom_ordermast.ID = signboom_linedetail.orderid) AND 
         (signboom_linedetail.product = signboom_allproducts.Code) AND
         (signboom_ordermast.hidden != 'yes') AND
End_Of_Query_1;

/************ Next part of query depends on product parameter. ************/

      if ($product == 'ALL')
      {
$query_part_2 = <<< End_Of_Query_2
End_Of_Query_2;
      }

      else if (in_array($product, $array_categories))
      {
$query_part_2 = <<< End_Of_Query_3
	 (signboom_allproducts.Category = '$product') AND
End_Of_Query_3;
      }

      else // it's a row for a single product
      {
$query_part_2 = <<< End_Of_Query_4
	 (signboom_linedetail.product = '$product') AND
End_Of_Query_4;
      }

/************ Next part of query depends on queue. ************/

      if ($queue != 'Invoice') 
        $query_part_3 = "(signboom_ordermast.ordercompleted = 'no') AND ";
      else
        $query_part_3 = "";

      if ($queue == 'Files') // this is a pseudo-queue
      {
$query_part_3 .= <<< End_Of_Query_5
         (signboom_linedetail.currentqueue != 'Deleted') AND 
         (signboom_linedetail.currentqueue != 'Complete') AND
         (signboom_linedetail.currentqueue != 'Rejected') AND
         (signboom_linedetail.currentqueue != 'Invoice'))
End_Of_Query_5;
      }

      else if ($queue == 'Today') // this is a pseudo-queue
      {
$query_part_3 .= <<< End_Of_Query_6
         (signboom_linedetail.currentqueue != 'Deleted') AND 
         (signboom_linedetail.currentqueue != 'Complete') AND 
         (signboom_linedetail.currentqueue != 'Invoice') AND
         ((signboom_linedetail.readydatetime <= '$aft_today') OR (signboom_linedetail.readydate = 'Call')))
End_Of_Query_6;
      }

      else if ($queue == 'Ready') // this is a pseudo-queue
      {
$query_part_3 .= <<< End_Of_Query_7
         (signboom_linedetail.currentqueue != 'Deleted') AND 
         (signboom_linedetail.currentqueue != 'Complete') AND 
         (signboom_linedetail.currentqueue != 'Rejected') AND
         (signboom_linedetail.currentqueue != 'Invoice') AND 
         (signboom_ordermast.readydateconfirmed = 0))
End_Of_Query_7;
      }

      else if ($queue == 'RIP') // special case: Len wants Proof and RIP queues displayed on one page
      {
$query_part_3 .= <<< End_Of_Query_8
         ((signboom_linedetail.currentqueue = 'Proof') OR (signboom_linedetail.currentqueue = 'RIP')))
End_Of_Query_8;
      }

      else if ($queue == 'Pending') // special case: Upload, Rejected and Hold queues are displayed on one page
      {
$query_part_3 .= <<< End_Of_Query_8a
         ((signboom_linedetail.currentqueue = 'Upload') OR (signboom_linedetail.currentqueue = 'Rejected') OR (signboom_linedetail.currentqueue = 'Hold')))
End_Of_Query_8a;
      }

      else // $queue == regular queue, one particular station on the floor
      {
$query_part_3 .= <<< End_Of_Query_9
         (signboom_linedetail.currentqueue = '$queue'))
End_Of_Query_9;
      }

/************ Finally order of rows intable depends on whether page lists jobs or orders.  ************/

  if ($this_is_orders_page)
    $query_jobs = $query_part_1 . $query_part_2 . $query_part_3 . " ORDER BY AcctName ASC, readydate ASC, signboom_linedetail.orderid ASC";
  else if (($queue == 'Files') || ($queue == 'Pack'))
    $query_jobs = $query_part_1 . $query_part_2 . $query_part_3 . " ORDER BY signboom_ordermast.readydatetime, signboom_ordermast.ID, signboom_allproducts.Category ASC, signboom_linedetail.product ASC, AdhesiveLamination ASC, RigidLamination ASC, AdhesivePrintSpeed ASC, BannerPrintSpeed ASC, RigidPrintSpeed ASC, AdhesiveInkFinish ASC, BannerInkFinish ASC, RigidInkFinish ASC, jobid ASC ";

  else if ($queue == 'Pending')
    $query_jobs = $query_part_1 . $query_part_2 . $query_part_3 . " ORDER BY currentqueue DESC, signboom_allproducts.Category ASC, signboom_linedetail.product ASC, AdhesiveLamination ASC, RigidLamination ASC, AdhesivePrintSpeed ASC, BannerPrintSpeed ASC, RigidPrintSpeed ASC, AdhesiveInkFinish ASC, BannerInkFinish ASC, RigidInkFinish ASC, jobid ASC ";
  else
    $query_jobs = $query_part_1 . $query_part_2 . $query_part_3 . " ORDER BY signboom_allproducts.Category ASC, signboom_linedetail.product ASC, AdhesiveLamination ASC, RigidLamination ASC, AdhesivePrintSpeed ASC, BannerPrintSpeed ASC, RigidPrintSpeed ASC, AdhesiveInkFinish ASC, BannerInkFinish ASC, RigidInkFinish ASC, jobid ASC ";
    // WAS $query_jobs = $query_part_1 . $query_part_2 . $query_part_3 . " ORDER BY ProdnSortGroup ASC, ProdnSortOrder ASC, AdhesiveLamination ASC, RigidLamination ASC, AdhesiveCutting ASC, BannerCutting ASC, RigidCutting ASC, jobid ASC ";

  $jobs = mysql_query($query_jobs, $DBConn) or die("queryDashboard: Could not read orders from database:" . mysql_error() . "<br><br>" . $query_jobs);
  $number_of_rows = mysql_num_rows($jobs);
  $num_jobs = $number_of_rows;

?>

