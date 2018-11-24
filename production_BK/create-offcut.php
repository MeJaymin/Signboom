<?php 
  require('authprodn.php'); 
  require('../admin/helper-functions.php');
  $button_name = 'submit_create_offcut';
  $button_value = 'Add Offcut to List';

  $products = array();
  $products[] = '';
  $query_products = "SELECT Code FROM signboom_allproducts WHERE (Category != 'STANDS') && (Category != 'ACCESS') ORDER BY Code";
  $result_products = mysql_query($query_products, $DBConn) or die(mysql_error());
  while ($row_product = mysql_fetch_array($result_products, MYSQL_BOTH))
  {
    $products[] = $row_product['Code'];
  }

  if (isset($_REQUEST['submit_create_offcut'])) 
  {
    include('includes/validate-offcut.php');

    if ($valid)
    {
      $query_update = "INSERT INTO signboom_offcuts SET DateAdded = '$date_added', PersonAdded = '$person_added', Material = '$material', Width = '$width', Length = '$length', Quantity = '$quantity', PaidFor = '$paid_for', Description = '$description'";
      //echo "Query: $query_update<br><br>";
      $result_update = mysql_query($query_update, $DBConn) or die(mysql_error());
     // Refresh the page.
     echo '<script type="text/javascript">';
     echo 'window.location.href="http://signboom.com/production/create-offcut.php?message=Your offcut has been added to the list.";';
     echo '</script>';

    }
  } // end of if create button submitted

  include('templates/create-edit-offcut.html.php');
?>

