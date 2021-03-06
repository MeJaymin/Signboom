<?php 
  require('authprodn.php'); 
  // Get a list of all offcuts (which have not yet been used) from the database.
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $query = "SELECT * FROM signboom_offcuts WHERE Used != 1 ORDER BY Material, DateAdded"; 
  $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
  <title>Offcuts</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="production-new.css" rel="stylesheet" type="text/css">
</head>

<body>

  <div style="width: 1200px; margin: 0px auto; text-align: center;">
    <div style="float: left; margin-top: 20px;"><img src="../images/logo3d.gif" width="308" height="54"></div>
    <div style="float: right;"><h1>Order Processing System: Offcuts</h1></div>
    <?php include('menu.html');?>
    <h1>Select Offcut to Edit</h1>
  </div>

  <div style="padding-left: 30px;">

  <?php
  $product = "";
  while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) 
  { 
    if ($product == "") 
    {
      $product = $row['Material'];
      echo '<div class="narrow_column"><b>' . $product. '</b><br><br>';
    }
    else if ($product != $row['Material']) 
    {
      $product = $row['Material'];
      echo '</div><div class="narrow_column"><b>' . $product . '</b><br><br>';
    }
    echo '<a href="edit-offcut.php?offcut_id=' . $row['OffcutId'] .'">';
    echo $row['OffcutId'] . ' (' . $row['Material'] . ': ' . $row['Width'] . ' x ' . $row['Length'] . ' x ' . $row['Quantity'] . ') ';
    echo '</a><br>';
  } 
  echo '</div>';

  // Free memory. 
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
  ?>

  </div>
</body>
</html>

