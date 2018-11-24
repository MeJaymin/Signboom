<?php

  require_once('../../Connections/DBConn.php');

//echo "a<br>";

  // Get information about this order.
  $account_name = $_GET['account_name'];
  $token = $_GET['token'];
  $due_date = $_GET['due_date']; // earliest due date of the items in this order (yymmdd)
  $subtotal = round($_GET['subtotal'], 2);
  $service_cost = round($_GET['service_cost'], 2);
  $gst = round($_GET['gst'], 2);
  $total_cost = round($_GET['total_cost'], 2);

//echo "b: $account_name, $token, $due_date: $subtotal +  $service_cost + $gst == $total_cost<br>";

  // Sum up costs 
  $order_total = $subtotal + $service_cost + $gst;
  //if ($total_cost != $order_total) echo "The totals DIDN'T match!";

//echo "c: $total_cost == $order_total<br>";

  // Get information about that account from signboom_user database.
  $query_select = "SELECT ID, email, team FROM signboom_user WHERE AcctName = '$account_name'";
  mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
  $result_select = mysql_query($query_select, $DBConn) or die(mysql_error());
  while ($row = mysql_fetch_array($result_select, MYSQL_BOTH)) 
  { 
    $user_id = $row['ID'];
    $email_address = $row['email'];
    $team = $row['team'];
  }

//echo "d: id $user_id at $email_address $team<br>";

  // Convert date formats.  Assume 3PM time for daily deliveries.
  sscanf($due_date, "%2d%2d%2d", $year, $month, $day);
  $year_long = 2000 + $year;
  $readydate     = sprintf("%02d/%02d/%4d 3PM", $month, $day, $year_long);
  $readydatetime = sprintf("%4d-%02d-%02d 15:00:00", $year_long, $month, $day);
  //$readydate = $month . '/' . $day . '/' . $year_long . ' 3PM';
  //$readydatetime = $year_long . '-' . $month . '-' . $day . ' 15:00:00';

  // Record the time now in format yyyy-mm-dd hh:mm:ss.
  $date_created = date("Y-m-d H:i:s");

//echo "e: ordered on $date_created, due on $readydate OR $readydatetime<br>";

  $subtotal_formatted     = sprintf('$%.2f', $subtotal);
  $service_cost_formatted = sprintf('$%.2f', $service_cost);
  $gst_formatted          = sprintf('$%.2f', $gst);
  $order_total_formatted  = sprintf('$%.2f', $order_total);

//echo "f: ordered on $date_created, due on $readydate OR $readydatetime<br>";

  // Add new line item into ordermast database table
$query = <<< End_Of_Query
INSERT INTO signboom_ordermast SET 
  ordertype = 'EVT', 
  UserId = '$user_id', 
  AcctName = '$account_name', 
  email = '$email_address', 
  date_created = '$date_created',
  Token = '$token',
  refnum = 'SEE FILES',
  readydate = '$readydate',
  readydatetime = '$readydatetime',
  Uploaded = '', 
  UploadCompletionTime = '0000-00-00 00:00:00',
  shiptype = 'EVENT',
  rushtype = 'MIX',
  subtotal = '$subtotal_formatted',
  setupfee = '$0.00', 
  dct = '',
  discount = '$0.00',
  rushfee = '$service_cost_formatted',
  gst = '$gst_formatted',
  pst = '$0.00',
  hst = '$0.00',
  freight = '$0.00',
  ordertotal = '$order_total_formatted',
  shipattn = '',
  shipcompany = '',
  shipaddress = '',
  shipcity = '',
  shipprov = '',
  shipzip = '',
  shipcountry = '',
  shiptoadd = 'no',
  documentname = '',
  promo = '',
  ordercompleted = 'no',
  timecompleted = '0000-00-00 00:00:00',
  orderinvoiced = 'no',
  emailproofed = 'no',
  emailpacked = 'no',
  hidden = 'no',
  customernotes = '',
  team = '$team',
  firstorder = 0,
  returningcustomer = 0
End_Of_Query;

//echo "g: $query";
  mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
  $result = mysql_query($query, $DBConn) or die(mysql_error());

  $new_order_id = mysql_insert_id($DBConn);
  echo $new_order_id;
  return true;

?>
