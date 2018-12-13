<?php 
  require('authprodn.php'); 
  require('../admin/helper-functions.php');
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $button_name = 'submit_edit_offcut';
  $button_value = 'Edit Offcut Information';

  $products = array();
  $products[] = '';
  $query_products = "SELECT Code FROM signboom_allproducts WHERE (Category != 'STANDS') && (Category != 'ACCESS') ORDER BY Code";
  $result_products = mysqli_query( $DBConn, $query_products) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  while ($row_product = mysqli_fetch_array($result_products,  MYSQLI_BOTH))
  {
    $products[] = $row_product['Code'];
  }

  if (!isset($_REQUEST['offcut_id']))
  {
    echo "You must specify an offcut id. Please visit the <a href=\"select-offcut.php\">Select Offcuts</a> page and click an offcut there.";
  }
  else
  {
    $offcut_id = $_REQUEST['offcut_id'];
    if (!ctype_digit($offcut_id)) 
    {
      echo "'$offcut_id' is not a valid offcut id. Please visit the <a href=\"select-offcut.php\">Select Offcuts</a> page and click an offcut.";
    }
    else 
    {
      if (isset($_REQUEST['submit_edit_offcut'])) 
      {
        include('includes/validate-offcut.php');

        if ($valid)
        {
          $query_update = "UPDATE signboom_offcuts SET Material = '$material', Width = '$width', Length = '$length', Quantity = '$quantity', PaidFor = '$paid_for', Description = '$description' WHERE OffcutId = $offcut_id";
          //echo "Query: $query_update<br><br>";
          $result_update = mysqli_query( $DBConn, $query_update) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
	  // Refresh the page.
	  //echo '<script type="text/javascript">';
          //echo 'window.location.href="http://signboom.com/production/select-offcut.php?message=Your edits have been saved.';
          //echo '</script>';
	  echo "<script language=\"javascript\">alert(\"Your edits have been saved.\");</script>";
          echo '<script type="text/javascript">';
          echo 'window.location.href="http://signboom.com/production/edit-offcut.php?offcut_id=' . $offcut_id . '";';
          echo '</script>';
        }
      } // end of if edit button submitted
      else
      {
        // Get a information for the offcut.
        $offcut_id = $_GET['offcut_id'];
        if (strlen(trim($offcut_id)) == 0)
        {
          echo 'You must specify an offcut ID<br>';
          exit;
        }
        $query = "SELECT * FROM signboom_offcuts WHERE OffcutId = $offcut_id";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $row = mysqli_fetch_array($result,  MYSQLI_BOTH);
        $date_added = $row['DateAdded'];
        $person_added = $row['PersonAdded'];
        $material = $row['Material'];
        $width = $row['Width'];
        $length = $row['Length'];
        $quantity = $row['Quantity'];
        $claimed = $row['Claimed'];
        $date_claimed = $row['DateClaimed'];
        $person_claimed = $row['PersonClaimed'];
        $used = $row['Used'];
        $date_used = $row['DateUsed'];
        $person_used = $row['PersonUsed'];
        $paid_for = $row['PaidFor'];
        $description = $row['Description'];
      }
    }
  }

  include('templates/create-edit-offcut.html.php');
?>

