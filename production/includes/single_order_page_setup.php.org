<?php

mysql_select_db($database_DBConn, $DBConn);
$current_page = $_SERVER["PHP_SELF"];
$my_debug = 0;


if (isset($_REQUEST['order_id'])) 
  $order_id = $_REQUEST['order_id'];
else 
  $order_id = 0;

$this_is_single_order_page = 1;
$include_change_upload_in_footer = 1;

