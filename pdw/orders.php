<?php 
require('authadmin.php');
$this_is_orders_page = 1;  // tells include files to handle things differently; don't remove
include('includes/orders_page_setup.php');
include('includes/query_dashboard.php');
include('templates/orders.html.php');
mysql_free_result($orders);
?>
