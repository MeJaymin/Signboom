<?php

mysql_select_db($database_DBConn, $DBConn);
$current_page = $_SERVER["PHP_SELF"];
$my_debug = 0;

// Variables for displaying jobs in pages of 10 jobs each, so no vertical scrolling required. 
$start_row = 0;

// Don't look at orders created before July 1, 2009, because we didn't track order completion.
$tracking_start_date = date("Y-m-d", mktime(0, 0, 0, 7, 29, 2010));

?>
