<?php 
  require('authprodn.php'); 
  require('../admin/helper-functions.php');
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $button_name = 'submit_create_offcut';
  $button_value = 'Add Offcut to List';

  $products = array();
  $products[] = '';
  $query_products = "SELECT Code FROM signboom_allproducts WHERE (Category != 'STANDS') && (Category != 'ACCESS') ORDER BY Code";
  $result_products = mysqli_query( $DBConn, $query_products) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  while ($row_product = mysqli_fetch_array($result_products,  MYSQLI_BOTH))
  {
    $products[] = $row_product['Code'];
  }

  if (isset($_REQUEST['submit_create_offcut'])) 
  {
    include('includes/validate-offcut.php');

    if ($valid)
    {
      $query_update = "INSERT INTO signboom_offcuts SET DateAdded = '$date_added', PersonAdded = '$person_added', Material = '$material', Width = '$width', Length = '$length', Quantity = '$quantity', PaidFor = '$paid_for', Description = '$description' , DateClaimed = '$date_added'";
      //echo "Query: $query_update<br><br>"; date('Y-m-d');die;
      $conn = new mysqli('localhost', 'root', 'root', 'signboom_v1p5');
      $result_update = mysqli_query($conn, $query_update);
      //print_r($result_update); die;
      /*$result_update = mysqli_query( $DBConn, $query_update) or die(mysqli_error($GLOBALS["___mysqli_ston"]));*/
     // Refresh the page.
     echo '<script type="text/javascript">';
     echo 'window.location.href="http://signboom.com/production/create-offcut.php?message=Your offcut has been added to the list.";';
     echo '</script>';

    }
  } // end of if create button submitted

  include('templates/create-edit-offcut.html.php');
?>

