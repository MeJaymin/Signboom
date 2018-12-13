<?php

  require_once('../../Connections/DBConn.php');

  $now = date('Y-m-d H:i:s');

  if (isset($_GET['order_id']))
  {
    $order_id = $_GET['order_id'];
    if (ctype_digit($order_id) && ($order_id > 0))
    {
      $query = "UPDATE signboom_ordermast SET Uploaded = 'Yes', UploadCompletionTime = '$now' WHERE ID = $order_id";
      mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
      $result = mysql_query($query, $DBConn) or die(mysql_error());
      if ($result)
        echo "OK";
      else
        echo "ERROR";
    }
    else
    {
      echo "ERROR";
    }
  }
  else
  {
    echo "ERROR";
  }

  return true;

?>
