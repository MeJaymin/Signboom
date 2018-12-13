<?php


mysqli_select_db( $DBConn, $database_DBConn);
$current_page = $_SERVER["PHP_SELF"];
$my_debug = 0;

function isOrderAllFinished($the_order_id)
{
  global $DBConn;

  $sql = "SELECT currentqueue FROM signboom_linedetail WHERE orderid = $the_order_id AND currentqueue != 'Pack'";
  $result = mysqli_query( $DBConn, $sql);

  //if no results then order is all finished
  if ((mysqli_num_rows($result) == false) || (mysqli_num_rows($result) == 0))
    return 1;
  else
    return 0;
}

// Don't look at orders created before July 1, 2009, because we didn't track order completion.
$tracking_start_date = date("Y-m-d", mktime(0, 0, 0, 7, 29, 2010));

?>
