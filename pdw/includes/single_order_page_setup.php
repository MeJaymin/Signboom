<?php

mysql_select_db($database_DBConn, $DBConn);
$current_page = $_SERVER["PHP_SELF"];
$my_debug = 0;

// Variables for displaying jobs in pages of 10 jobs each, so no vertical scrolling required. 
$rows_per_page = 10;
if (isset($_GET['page_number'])) 
  $page_number = $_GET['page_number'];
else if (isset($_POST['page_number']))
  $page_number = $_POST['page_number'];
else 
  $page_number = 0;
if (isset($_GET['order_id'])) 
  $order_id = $_GET['order_id'];
else if (isset($_POST['order_id'])) 
  $order_id = $_POST['order_id'];
else if (isset($_GET['the_order_id'])) 
  $order_id = $_GET['the_order_id'];
else 
  $order_id = 0;
$start_row = $page_number * $rows_per_page;

