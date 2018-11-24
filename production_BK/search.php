<?php
require('authprodn.php');

/* for later
if (strlen(trim($account_name)) > 0)
{
  $query = SELECT * FROM signboom_ordermast WHERE AcctName = '$account_name' AND hidden = 'no' AND ordercompleted = 'no'";
}
else 
*/

if (strlen(trim($_POST['$order_id'])) > 0)
{
  $order_id = trim($_POST['order_id']);
}
/*
else if (strlen(trim($_POST['$file_id'])) > 0)
{
  $file_id = trim($_POST['file_id']);
  $query = "SELECT orderid FROM signboom_linedetail WHERE ID = '$file_id'";
  $result = mysql_query($sql, $DBConn);
  if (!$result)
  {
    $message = "I can't find a file with that ID.";
  }
  else
  {
    $row = mysql_fetch_array($result); 
    $order_id = $row['orderid'];
  }
}
*/

if (strlen($order_id) > 0)
{
  // bring up single order page for that order
  header("Location: orderitem.php?order_id=" . $order_id);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
  <title>Find Order</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="production-new.css" rel="stylesheet" type="text/css">
</head>

<body>

<div style="width: 1200px; margin: 0px auto; text-align: center;">

  <div style="float: left; margin-top: 20px;"><img src="../../images/logo3d.gif" width="308" height="54"></div>
  <div style="float: right;"><h1>Order Processing System: Find Order</h1></div>
  <br style="clear: both;">
  <?php include('menu.html');?>
  <br><br>
</div>

<div style="width: 600px; margin: 0px auto;">
  <div style="color: #cc0000; font-weight: bold;"><?php echo $message; ?></div>
  <!-- Form which passes search request information in to be handled. -->
  <form name="main_form" action=<?php echo $_SERVER['PHP_SELF'] ?> method="POST">
    <!--
    Please fill in just ONE box below.
    <br><br>
    Account Name: <input type="text" name="account_name" id="account_name">
    <br><br>
    (Note: Only <b>active</b> orders are listed when you search for Account Name.)
    -->
    Order ID: <input type="text" name="order_id" id="order_id">
    <br><br>
    <!--
    File ID: <input type="text" name="file_id" id="file_id">
    <br><br>
    -->
    <input type="submit" name="find_order" id="find_order" value="Find Order">
    <br><br>
  </form>

</div>
</body>
</html>

<?php
mysql_free_result($orders);
mysql_free_result($jobs);
mysql_free_result($result);
?>
