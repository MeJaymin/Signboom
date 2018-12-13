<?php 
require('authprodn.php');
$aft_today = date('Y-m-d 15:00:00', time());
$queue_names = array('Quote', 'Design', 'Approval', 'Pending', 'RIP', 'Print', 'Lam', 'Kiss', 'CNC', 'Finish', 'Pack', 'Ready', 'Invoice', 'Files');
$array_product_totals = array();
$array_active_categories = array();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
  <title>Queues</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="production-new.css" rel="stylesheet" type="text/css">
</head>

<?php 

/***************************** Start Functions ****************************/

function printAllCell($value, $link_url)
{

  // If total for a cell starts with a *, this means we want to flag that cell visually.
  if (strpos($value, "*") === false)
  {
    printf('<td class="dark_grey_cell"><a href="%s">%.0f</a></td>', $link_url, $value);
  }
  else
  {
    $total = str_replace("*", "", $value);
    printf('<td class="dark_grey_cell"><a class="lineitem_flagged" href="%s">%.0f</a></td>', $link_url, $total);
  }
}

function queryQueueTotals()
{
  global $aft_today, $DBConn, $queue_names;
  $array_totals = array();
 
  foreach ($queue_names AS $queue_name)
  {
    $flag_this_total = false;
    if ($queue_name == 'Files')
    {
$sql = <<< End_Of_Query_1
SELECT SUM(signboom_linedetail.printedarea) AS SquareFeet
FROM signboom_linedetail
WHERE (signboom_linedetail.currentqueue != 'Deleted') AND 
      (signboom_linedetail.currentqueue != 'Complete') AND
      (signboom_linedetail.currentqueue != 'Rejected') AND
      (signboom_linedetail.currentqueue != 'Invoice') 
End_Of_Query_1;
    }

    else if ($queue_name == 'Ready')
    {
$sql = <<< End_Of_Query_3
SELECT SUM(signboom_linedetail.printedarea) AS SquareFeet
FROM signboom_linedetail, signboom_ordermast
WHERE (signboom_linedetail.currentqueue != 'Deleted') AND 
      (signboom_linedetail.currentqueue != 'Complete') AND 
      (signboom_linedetail.currentqueue != 'Rejected') AND
      (signboom_linedetail.currentqueue != 'Invoice') AND
      (signboom_linedetail.orderid = signboom_ordermast.ID) AND
      (signboom_ordermast.readydateconfirmed = 0)
End_Of_Query_3;

      $sql_unconfirmed = $sql . " AND ((signboom_ordermast.date_created < NOW() - INTERVAL 27 HOUR) OR ((signboom_ordermast.rushtype = 'HOT') AND (signboom_ordermast.readydate = 'Call')))"; // 27 = 24 hours passed + 3 hour time difference
      $result_unconfirmed = mysqli_query($conn, $sql); 
      /*$result_unconfirmed = mysqli_query( $DBConn, $sql_unconfirmed); */
      if (!$result_unconfirmed)
      {
        echo "Error #601 has occured while querying the database: $queue_name. Please contact Alison Taylor to investigate this.<br>";
        return false;
      }
      $row_unconfirmed = mysqli_fetch_array($result_unconfirmed);  
      if ($row_unconfirmed['SquareFeet'] > 0)
      {
        $flag_this_total = true;
      }
    }

    else if ($queue_name == 'RIP') // Special case. Len wants two queues on one page.
    {
$sql = <<< End_Of_Query_4
SELECT SUM(signboom_linedetail.printedarea) AS SquareFeet
FROM signboom_linedetail, signboom_ordermast
WHERE ((signboom_linedetail.currentqueue = 'Proof') OR (signboom_linedetail.currentqueue = 'RIP')) AND 
      (signboom_linedetail.orderid = signboom_ordermast.ID) 
End_Of_Query_4;
    }

    else if ($queue_name == 'Pending') // Special case. Put Upload, Rejected and Hold orders on one page.
    {
$sql = <<< End_Of_Query_4a
SELECT SUM(signboom_linedetail.printedarea) AS SquareFeet
FROM signboom_linedetail
WHERE (signboom_linedetail.currentqueue = 'Upload') OR (signboom_linedetail.currentqueue = 'Rejected') OR (signboom_linedetail.currentqueue = 'Hold') 
End_Of_Query_4a;
    }

    else 
    {
$sql = <<< End_Of_Query_5
SELECT SUM(signboom_linedetail.printedarea) AS SquareFeet
FROM signboom_linedetail, signboom_ordermast
WHERE (signboom_linedetail.currentqueue = '$queue_name') AND
      (signboom_linedetail.orderid = signboom_ordermast.ID) 
End_Of_Query_5;
    }
    //echo $sql; die;
    $conn = new mysqli('localhost', 'root', 'root', 'signboom_v1p5');
    $result = mysqli_query($conn, $sql);
    //print_r($result); die;
    /*$result = mysqli_query( $DBConn, $sql); */
    if (!$result)
    {
      echo "Error #602 has occured while querying the database: $queue_name:<br>$sql<br>. Please contact Alison Taylor to investigate this.<br> $sql";
      return false;
    }
    $row = mysqli_fetch_array($result); 
    $total = $row['SquareFeet'];
    if (($queue_name == 'RIP') || ($queue_name == 'Print') || ($queue_name == 'Lam') || ($queue_name == 'Kiss') || 
        ($queue_name == 'CNC') || ($queue_name == 'Finish') || ($queue_name == 'Pack'))
    {
      // Test for late orders, orders due today, and hot orders. 
      $sql_today_late = $sql  . " AND ((signboom_ordermast.readydatetime < NOW() - INTERVAL 3 HOUR) OR (DATE(signboom_ordermast.readydatetime) = CURDATE()) OR  (signboom_ordermast.rushtype = 'HOT'))";
      $result_today_late = mysqli_query($conn, $sql);
      /*$result_today_late = mysqli_query( $DBConn, $sql_today_late); */
      if (!$result_today_late)
      {
        echo "Error #603 has occured while querying the database: $queue_name.<br>$sql_today_late<br>Please contact Alison Taylor to investigate this.<br>";
        return false;
      }
      $row_today_late = mysqli_fetch_array($result_today_late);  
      if ($row_today_late['SquareFeet'] > 0)
        $flag_this_total = true;
    }

    if ($flag_this_total)
      $array_totals[$queue_name] = "*" . $total;
    else
      $array_totals[$queue_name] = $total;
  }

  return $array_totals;
}

function queryActiveProducts()
{
  global $DBConn, $queue_names, $array_product_totals, $array_active_categories;
 
$sql = <<< End_Of_Query_6
SELECT DISTINCT signboom_linedetail.product AS ProductCode,
       signboom_allproducts.Name AS ProductName, 
       signboom_allproducts.Category AS Category,
       signboom_category.displayorder AS CategoryOrder
FROM signboom_linedetail, signboom_allproducts, signboom_category
WHERE (signboom_linedetail.currentqueue != 'Deleted') AND 
      (signboom_linedetail.currentqueue != 'Complete') AND
      (signboom_linedetail.product = signboom_allproducts.Code) AND
      (signboom_allproducts.Category = signboom_category.code) 
ORDER BY CategoryOrder ASC, ProductCode ASC
End_Of_Query_6;

  // Get ordered and categorized list of which products are active in system.
  // We'll use this to control the order of the rows and the content in the first column.
  $conn = new mysqli('localhost', 'root', 'root', 'signboom_v1p5');
  /*$result = mysqli_query( $DBConn, $sql);*/
  $result = mysqli_query($conn, $sql);
  if (!$result)
  {
    echo "Error #604 has occured while querying the database: $queue_name. Please contact Alison Taylor to investigate this.<br>";
    return false;
  }

  // Create an indexed 2D array where we still store the counts for each product.
  // Start out with counts of zero for each.
  // In the process, build an array of active categories.
  while ($row = mysqli_fetch_array($result))
  {
    $product_code = $row['ProductCode'];
    $category = $row['Category'];
    if (!in_array($category, $array_active_categories))
      $array_active_categories[] = $category;
    foreach ($queue_names AS $queue_name)
    {
      $array_product_totals[$queue_name][$product_code] = 0;
    }
  }
  mysqli_data_seek($result,  0);
  return $result;
}

function queryQueueProducts()
{
  global $aft_today, $DBConn, $queue_names, $array_product_totals;

    foreach ($queue_names AS $queue_name)
    {
      if ($queue_name == 'Files')
      {
$sql = <<< End_Of_Query_7
SELECT signboom_linedetail.product AS ProductCode, 
       signboom_allproducts.Name AS ProductName, 
       signboom_allproducts.Category AS Category,
       signboom_category.displayorder AS CategoryOrder,
       SUM(signboom_linedetail.printedarea) AS SquareFeet,
       SUM(signboom_linedetail.quantity) AS Quantity
FROM signboom_linedetail, signboom_allproducts, signboom_category
WHERE (signboom_linedetail.currentqueue != 'Deleted') AND 
      (signboom_linedetail.currentqueue != 'Complete') AND 
      (signboom_linedetail.currentqueue != 'Rejected') AND
      (signboom_linedetail.currentqueue != 'Invoice') AND 
      (signboom_linedetail.product = signboom_allproducts.Code) AND
      (signboom_allproducts.Category = signboom_category.code) 
GROUP BY ProductCode
ORDER BY CategoryOrder ASC, ProductCode ASC
End_Of_Query_7;
      }

      else if ($queue_name == 'Ready')
      {
$sql = <<< End_of_Query_Ready
SELECT signboom_linedetail.product AS ProductCode,  
       signboom_allproducts.Name AS ProductName,  
       signboom_allproducts.Category AS Category,  
       signboom_category.displayorder AS CategoryOrder,
       SUM(signboom_linedetail.printedarea) AS SquareFeet,
       SUM(signboom_linedetail.quantity) AS Quantity
FROM signboom_linedetail, signboom_allproducts  
INNER JOIN signboom_category
ON signboom_category.code = signboom_allproducts.Category
WHERE (signboom_linedetail.readydateconfirmed =0 ) AND  
      (signboom_linedetail.currentqueue !='Deleted' ) AND  
      (signboom_linedetail.currentqueue !='Complete' ) AND  
      (signboom_linedetail.currentqueue != 'Rejected') AND
      (signboom_linedetail.currentqueue !='Invoice' ) AND  
      (signboom_linedetail.product =signboom_allproducts . Code )  
GROUP BY signboom_linedetail.product
ORDER BY signboom_category.displayorder ASC, signboom_linedetail . product ASC
End_of_Query_Ready;
      }

      else if ($queue_name == 'RIP') // Special case. Len wants two queues on one page.
      {
$sql = <<< End_Of_Query_9
SELECT signboom_linedetail.product AS ProductCode, 
       signboom_allproducts.Name AS ProductName, 
       signboom_allproducts.Category AS Category,
       signboom_category.displayorder AS CategoryOrder,
       SUM(signboom_linedetail.printedarea) AS SquareFeet,
       SUM(signboom_linedetail.quantity) AS Quantity
FROM signboom_linedetail, signboom_allproducts, signboom_category
WHERE ((signboom_linedetail.currentqueue = 'Proof') OR (signboom_linedetail.currentqueue = 'RIP')) AND 
      (signboom_linedetail.product = signboom_allproducts.Code) AND
      (signboom_allproducts.Category = signboom_category.code) 
GROUP BY ProductCode
ORDER BY CategoryOrder ASC, ProductCode ASC
End_Of_Query_9;
      }

      else if ($queue_name == 'Pending') // Special case. Put Upload, Rejected and Hold orders on one page.
      {
$sql = <<< End_Of_Query_9a
SELECT signboom_linedetail.product AS ProductCode, 
       signboom_allproducts.Name AS ProductName, 
       signboom_allproducts.Category AS Category,
       signboom_category.displayorder AS CategoryOrder,
       SUM(signboom_linedetail.printedarea) AS SquareFeet,
       SUM(signboom_linedetail.quantity) AS Quantity
FROM signboom_linedetail, signboom_allproducts, signboom_category
WHERE ((signboom_linedetail.currentqueue = 'Upload') OR (signboom_linedetail.currentqueue = 'Rejected') OR (signboom_linedetail.currentqueue = 'Hold')) AND 
      (signboom_linedetail.product = signboom_allproducts.Code) AND
      (signboom_allproducts.Category = signboom_category.code) 
GROUP BY ProductCode
ORDER BY CategoryOrder ASC, ProductCode ASC
End_Of_Query_9a;
      }


      else 
      {
$sql = <<< End_Of_Query_10
SELECT signboom_linedetail.product AS ProductCode, 
       signboom_allproducts.Name AS ProductName, 
       signboom_allproducts.Category AS Category,
       signboom_category.displayorder AS CategoryOrder,
       SUM(signboom_linedetail.printedarea) AS SquareFeet,
       SUM(signboom_linedetail.quantity) AS Quantity
FROM signboom_linedetail, signboom_allproducts, signboom_category
WHERE (signboom_linedetail.currentqueue = '$queue_name') AND 
      (signboom_linedetail.product = signboom_allproducts.Code) AND
      (signboom_allproducts.Category = signboom_category.code) 
GROUP BY ProductCode
ORDER BY CategoryOrder ASC, ProductCode ASC
End_Of_Query_10;
      }
      //echo $sql; die;
      $conn = new mysqli('localhost', 'root', 'root', 'signboom_v1p5');
      //$result = mysqli_query($conn, $sql);
      $result = mysqli_query($conn, $sql); 
      //print_r($result); die;
      if (!$result)
      {
        echo "Error #605 has occured while querying the database: $queue_name. Please contact Alison Taylor to investigate this.<br>";
	return false;
      }

      // Now we need to store the data from this query in our 2D array $array_product_totals.
      $category = '';
      $category_subtotal = 0;
      while ($row_product = mysqli_fetch_array($result)) 
      {
        if ($category == '') $category = $row_product['Category'];
        if ($row_product['Category'] != $category)
	{
          $array_product_totals[$queue_name][$category] = $category_subtotal;
          $category = $row_product['Category'];
          $category_subtotal = 0;
	}
        $product_code = $row_product['ProductCode'];
	if ($category == 'STANDS')
	{
          $array_product_totals[$queue_name][$product_code] = $row_product['Quantity'];
	  $category_subtotal += $row_product['Quantity'];
	}
	else
	{
	  $category_subtotal += $row_product['SquareFeet'];
          $array_product_totals[$queue_name][$product_code] = $row_product['SquareFeet'];
        }
      }
      // Save the total of the final category.
      $array_product_totals[$queue_name][$category] = $category_subtotal;

    }// end for each queue 

  return true;
}

function printRow($product_code, $product_name, $cell_class)
{
  global $array_product_totals;

  printf('<tr>');

  if (strlen(trim($product_name)) > 0)
    printf('<td class="'. $cell_class . ' heading"><a href="#" onClick="alert(\'%s\')">%s</a></td>', $product_name, $product_code);
  else
    printf('<td class="'. $cell_class . ' heading">%s</td>', $product_code);

  //printf('<td class="'. $cell_class . '"><a href="orders.php?product=' . $product_code . '&queue=Quote">%.0f</a></td>', $array_product_totals['Quote'][$product_code]);
  //printf('<td class="'. $cell_class . '"><a href="orders.php?product=' . $product_code . '&queue=Design">%.0f</a></td>', $array_product_totals['Design'][$product_code]);
  //printf('<td class="'. $cell_class . '"><a href="orders.php?product=' . $product_code . '&queue=Approval">%.0f</a></td>', $array_product_totals['Approval'][$product_code]);

  if ($cell_class == 'dark_grey_cell')
  {
    printf('<td class="dark_grey_cell"><a href="jobs.php?product=' . $product_code . '&queue=Files">%.0f</a></td>', $array_product_totals['Files'][$product_code]);
  }
  else
  {
    printf('<td class="light_grey_cell"><a href="jobs.php?product=' . $product_code . '&queue=Files">%.0f</a></td>', $array_product_totals['Files'][$product_code]);
  }

  //printf('<td class="'. $cell_class . '"><a href="jobs.php?product=' . $product_code . '&queue=Proof">%.0f</a></td>', $array_product_totals['Proof'][$product_code]);
  printf('<td class="'. $cell_class . '"><a href="jobs.php?product=' . $product_code . '&queue=RIP">%.0f</a></td>', isset($array_product_totals['RIP'][$product_code])?$array_product_totals['RIP'][$product_code]:"");
  printf('<td class="'. $cell_class . '"><a href="jobs.php?product=' . $product_code . '&queue=Print">%.0f</a></td>', isset($array_product_totals['Print'][$product_code])?$array_product_totals['Print'][$product_code]:"");
  printf('<td class="'. $cell_class . '"><a href="jobs.php?product=' . $product_code . '&queue=Lam">%.0f</a></td>', isset($array_product_totals['Lam'][$product_code])?$array_product_totals['Lam'][$product_code]:"");
  printf('<td class="'. $cell_class . '"><a href="jobs.php?product=' . $product_code . '&queue=Kiss">%.0f</a></td>', isset($array_product_totals['Kiss'][$product_code])?$array_product_totals['Kiss'][$product_code]:"");
  printf('<td class="'. $cell_class . '"><a href="jobs.php?product=' . $product_code . '&queue=CNC">%.0f</a></td>', isset($array_product_totals['CNC'][$product_code])?$array_product_totals['CNC'][$product_code]:"");
  printf('<td class="'. $cell_class . '"><a href="jobs.php?product=' . $product_code . '&queue=Finish">%.0f</a></td>', $array_product_totals['Finish'][$product_code]);
  printf('<td class="'. $cell_class . '"><a href="jobs.php?product=' . $product_code . '&queue=Pack">%.0f</a></td>', isset($array_product_totals['Pack'][$product_code])?$array_product_totals['Pack'][$product_code]:"");


  if ($cell_class == 'dark_grey_cell')
  {
    printf('<td class="dark_grey_cell"><a href="orders.php?product=' . $product_code . '&queue=Pending">%.0f</a></td>', isset($array_product_totals['Pending'][$product_code])?$array_product_totals['Pending'][$product_code]:"");
    printf('<td class="dark_grey_cell"><a href="orders.php?product=' . $product_code . '&queue=Ready">%.0f</a></td>', isset($array_product_totals['Ready'][$product_code])?$array_product_totals['Ready'][$product_code]:"");
    printf('<td class="dark_grey_cell"><a href="orders.php?product=' . $product_code . '&queue=Invoice">%.0f</a></td>', isset($array_product_totals['Invoice'][$product_code])?$array_product_totals['Invoice'][$product_code]:"");
  }
  else
  {
    printf('<td class="light_grey_cell"><a href="orders.php?product=' . $product_code . '&queue=Pending">%.0f</a></td>', $array_product_totals['Pending'][$product_code]);
    printf('<td class="light_grey_cell"><a href="orders.php?product=' . $product_code . '&queue=Ready">%.0f</a></td>', $array_product_totals['Ready'][$product_code]);
    printf('<td class="light_grey_cell"><a href="orders.php?product=' . $product_code . '&queue=Invoice">%.0f</a></td>', $array_product_totals['Invoice'][$product_code]);
  }

  printf('</tr>');

  return;
}

/***************************** End Functions ****************************/

// Get the total sqft of items IN EACH QUEUE. 
// This populates the 'ALL' row of the production queues table.
// An associative array of decimal numbers is returned. Array indices are queue names.
$array_totals = queryQueueTotals();
if ($array_totals === false)
{
  echo "A: There are no active products in the system. Or there is a bug.";
  exit();
}

// Return a (query results) list of all products currently active in the system,
// and start a global 2D array to track the square feet counts for each product/queue.
$result_product_list = queryActiveProducts();
if ($result_product_list === false)
{
  echo "B: There are no active products in the system. Or there is a bug.";
  exit();
}

// Populate the global 2D array with the square feet counts for each product/queue.
// This populates the remaining (product-specific) rows of the production queues table.
// While we're at it, sum up the totals for each category/queue box.
$success = queryQueueProducts();
if (!$success)
{
  echo "C: Unable to update the queues. There is probably a bug in this page.";
  exit();
}

?>


<body>

  <div style="width: 1200px; margin: 0px auto; text-align: center;">

    <div style="float: left; margin-top: 20px;"><img src="../images/logo3d.gif" width="308" height="54"></div>
    <div style="float: right;"><h1>Order Processing System: Queues</h1></div>
    <br style="clear: both;">
    <?php include('menu.html');?>
    <br><br>

    <?php 
    date_default_timezone_set('America/Los_Angeles'); 
    $datetime = date("l M j, Y - g:i a"); 
    ?>
    <table class="withborder">
    <tr>
      <td class="table_title" colspan="1"></td>
      <!-- <td class="table_title" colspan="3">Design Queues</td>-->
      <!-- <td class="table_title" colspan="2">Overview Queues</td>-->
      <td class="table_title" colspan="8">Production Queues: <?php echo $datetime; ?></td>
      <td class="table_title" colspan="3">Admin Queues</td>
    </tr>
    <tr>
      <td class="heading">Product</td>
      <!--
      <td class="heading">Quote</td>
      <td class="heading">Design</td>
      <td class="heading">Approval</td>
      -->
      <td class="heading">Files</td>
      <td class="heading">RIP</td>
      <td class="heading">Print</td>
      <td class="heading">Lam</td>
      <td class="heading">Kiss</td>
      <td class="heading">CNC</td>
      <td class="heading">Finish</td>
      <td class="heading">Pack</td>

      <td class="heading">Work</td> <!-- called 'Pending' in the code -->
      <td class="heading">Ready</td>
      <td class="heading">Invoice</td>
    </tr>

    <?php 
    printf('<tr>');
      printf('<td class="dark_grey_cell heading">ALL</td>');
      //printAllCell($array_totals['Quote'], 'orders.php?product=ALL&queue=Quote');
      //printAllCell($array_totals['Design'], 'orders.php?product=ALL&queue=Design');
      //printAllCell($array_totals['Approval'], 'orders.php?product=ALL&queue=Approval');
      printAllCell($array_totals['Files'], 'jobs.php?product=ALL&queue=Files');
      printAllCell($array_totals['RIP'], 'jobs.php?product=ALL&queue=RIP');
      printAllCell($array_totals['Print'], 'jobs.php?product=ALL&queue=Print');
      printAllCell($array_totals['Lam'], 'jobs.php?product=ALL&queue=Lam');
      printAllCell($array_totals['Kiss'], 'jobs.php?product=ALL&queue=Kiss');
      printAllCell($array_totals['CNC'], 'jobs.php?product=ALL&queue=CNC');
      printAllCell($array_totals['Finish'], 'jobs.php?product=ALL&queue=Finish');
      printAllCell($array_totals['Pack'], 'jobs.php?product=ALL&queue=Pack');
      printAllCell($array_totals['Pending'], 'orders.php?product=ALL&queue=Pending');
      printAllCell($array_totals['Ready'], 'orders.php?product=ALL&queue=Ready');
      printAllCell($array_totals['Invoice'], 'orders.php?product=ALL&queue=Invoice');
    printf('</tr>');

    // print information for every product/category in $result_product_list...
    $category = '';
    while ($product_info = mysqli_fetch_array($result_product_list) ) 
    {
      $product_code = $product_info['ProductCode'];
      $product_name = str_replace(' ', '&nbsp;', $product_info['ProductName']);
      if ($category == '') $category = $product_info['Category'];
      if ($product_info['Category'] != $category)
      {
        $cell_class = 'dark_grey_cell';
        //if ($category == 'ADHESIVE') $category = 'ROLLS'; // it's called ADHESIVE in the databse (legacy), but now we call it ROLLS 
        printRow($category, '', $cell_class); // print out the total counts of the previous category
        $category = $product_info['Category'];
        $cell_class = 'white_cell';
        printRow($product_code, $product_name, $cell_class); // print out the counts for this product
      }
      else
      {
        $cell_class = 'white_cell';
        printRow($product_code, $product_name, $cell_class); // print out the counts for this product
      }
    }
    $cell_class = 'dark_grey_cell';
    //if ($category == 'ADHESIVE') $category = 'ROLLS'; // it's called ADHESIVE in the databse (legacy), but now we call it ROLLS 
    printRow($category, '', $cell_class); // print out the total counts of the final category
    ?>

    </table>
    <br><br>

  </div> 

</body>
</html>

<?php
 ((mysqli_free_result($result_product_list) || (is_object($result_product_list) && (get_class($result_product_list) == "mysqli_result"))) ? true : false); 
?>
