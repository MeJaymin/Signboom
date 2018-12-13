<?php

  require_once('../../Connections/DBConn.php');

  $now = date('Y-m-d H:i:s');

  if (isset($_GET['order_id']))
  {
    $order_id = $_GET['order_id'];
    if (ctype_digit($order_id) && ($order_id > 0))
    {
      $query = "UPDATE signboom_ordermast SET Uploaded = 'Yes', UploadCompletionTime = '$now' WHERE ID = $order_id";
      mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
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
