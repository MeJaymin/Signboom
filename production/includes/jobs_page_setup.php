<?php

mysqli_select_db( $DBConn, $database_DBConn);
$current_page = $_SERVER["PHP_SELF"];
$my_debug = 0;

$tracking_start_date = date("Y-m-d", mktime(0, 0, 0, 7, 29, 2010));

// Query database for those orders we are interested in.
$query_orders = "SELECT ID, ordertype FROM signboom_ordermast WHERE (date_created >= '$tracking_start_date') AND (ordercompleted = 'no') AND (hidden != 'yes') AND (Uploaded = 'Yes') ORDER BY ID ASC";
$orders = mysqli_query( $DBConn, $query_orders) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
$num_orders = mysqli_num_rows($orders);
if ($my_debug) echo "Number of orders in results of query: $num_orders<br>";

// Find oldest order in those results (ie. the order with the lowest order id, which will be the first one in the sort)
$row_orders = mysqli_fetch_assoc($orders);
$oldest_order_id = $row_orders['ID'];
$oldest_order_type = $row_orders['ordertype'];
if ($my_debug) echo "Oldest order is ID $oldest_order_id and type $oldest_order_type<br>";

?>
