<?php 
  include ('authadmin.php'); 
  include ('helper-functions.php'); 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

  $error_message = "";
  $deleted = false;
  $made_default = false;
  if (isset($_REQUEST['product']))
  {
    $product = $_REQUEST['product'];
    if (strlen(trim($product)) == 0)
    {
      echo "'$product' is not a valid product. Please visit the <a href=\"view-products.php\">Products</a> page and click one of the FINISHING links.";
    }
    else 
    {
      // Find what category that product is in.
      $query_category = "SELECT Category FROM signboom_allproducts WHERE Code = '$product'";
      $result_category = mysqli_query( $DBConn, $query_category) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $row_category = mysqli_fetch_array($result_category,  MYSQLI_BOTH); 
      $category = $row_category['Category'];

      // If user has asked to delete a pair...
      if (isset($_POST['delete'])) 
      {
        // Read in posted information.
        $delete = $_POST['delete'];

        // Validate posted information.
        $query = "SELECT ProductCode, FinishingOptionCode, Value FROM signboom_product_finishing WHERE Id = $delete";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $row = mysqli_fetch_array($result,  MYSQLI_BOTH);
        $product_code = $row['ProductCode'];
        $finishing_option_code = $row['FinishingOptionCode'];
        $value = $row['Value'];
        if (strcmp($product_code, $product) != 0)
        {
          // If information is NOT valid, give error message.
          $error_message = "That finishing option pair does not apply to the product you are editing.  This is likely a software bug that needs to be fixed.";
        }
        else if ($value == "2")
        {
          // This is a bit of a hack, that works because the options all start with XX- where the XX is unique for a particular
          // option set.  The code should actually look up each finishing option in the database to find what OptionSet is is
          // in and then check if any of the other options for this product are in that option set.  I have coded the bit below
          // as a stop-gap fix and put a task in BaseCamp to do the permanent fix, along with changing the way the option
          // set of each option is stored in the database.  (Alison)
          $option_set = substr($finishing_option_code, 0, 2);
          $query_check = "SELECT Id FROM signboom_product_finishing WHERE FinishingOptionCode LIKE '$option_set%' AND ProductCode = '$product_code'";
          $result_check = mysqli_query( $DBConn, $query_check) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
          $number_of_pairs = mysqli_num_rows($result_check);

          if ($number_of_pairs == 1)
          {
            // This is the only option in that option set for this product, so it can be deleted.
            $query = "DELETE FROM signboom_product_finishing WHERE Id = $delete";
            $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
            $deleted = true;
          }
          else 
          {
            // This is the default option, and there are other options.  User must make one of them the default first.
            $error_message = "That finishing option is a default setting.  You must choose one of the other settings to be the default before you can delete this option.";
          }
        }
        else
        {
          // If information is valid, update database.
          $query = "DELETE FROM signboom_product_finishing WHERE Id = $delete";
          $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
          $deleted = true;
        }
      }

      // If user has asked to make a pair into a default ...
      else if (isset($_POST['make_default'])) 
      {
        // Read in posted information.
        $make_default = $_POST['make_default'];

        // Validate posted information.
        $query = "SELECT ProductCode, FinishingOptionCode, Value FROM signboom_product_finishing WHERE Id = $make_default";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $row = mysqli_fetch_array($result,  MYSQLI_BOTH);
        $product_code = $row['ProductCode'];
        $option_code = $row['FinishingOptionCode'];
        $value = $row['Value'];
        if (strcmp($product_code, $product) != 0)
        {
          // If information is NOT valid, give error message.
          $error_message = "That finishing option pair does not apply to the product you are editing.  This is likely a software bug that needs to be fixed.";
        }
        else if ($value == "2")
        {
          // If information is NOT valid, give error message.
          $error_message = "That finishing option is already a default setting.";
        }
        else
        {
          // If information is valid, update database.

          // Identify the group this finishing option is in. (First two letters of the option code.)
          $group = substr($option_code, 0, 2);

          // Find the existing default option in that group for that product, and set it to NOT be a default.
          $query = "SELECT Id FROM signboom_product_finishing WHERE ProductCode = '$product' AND Value = 2 AND FinishingOptionCode LIKE '$group%'";
          //echo "Query: $query<br><br>";
          $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
          $row = mysqli_fetch_array($result,  MYSQLI_BOTH);
          if ($row)  // This test handles case where new default is only option in that group for that product.
          {
            $old_default = $row['Id'];
            $query = "UPDATE signboom_product_finishing SET Value = 1 WHERE Id = $old_default";
            //echo "Query: $query<br><br>";
            $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
          }

          // Then make the new default.
          $query = "UPDATE signboom_product_finishing SET Value = 2 WHERE Id = $make_default";
          //echo "Query: $query<br><br>";
          $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
          $made_default = true;
        }
      }

      // If user has asked to add a pair...
      else if (isset($_POST['add'])) 
      {
        // Read in posted information.
        $new_option = $_POST['new_option'];

        // Validate posted information.
        $query = "SELECT * FROM signboom_finishing WHERE Code = '$new_option' AND Category = '$category'";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        if (mysqli_num_rows($result) == 0)
        {
          // If information is NOT valid, give error message.
          $error_message = "I cannot find that finishing option in this product category. This is likely a software bug that needs to be fixed.";
        }
        else 
        {
          // Make sure that finishing option isn't already included for that product.
          $query = "SELECT * FROM signboom_product_finishing WHERE ProductCode = '$product' AND FinishingOptionCode = '$new_option'";
          $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
          if (mysqli_num_rows($result) == 0)
          {
            $query = "INSERT INTO signboom_product_finishing SET ProductCode = '$product', FinishingOptionCode = '$new_option', Value = 1";
            //echo "$query<br>";
            $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
          }
          else
          {
            $error_message = "That finishing option is already applied to this product.";
          }
        }
      }

      // Get the finishing options for that product from the database, so we can display them.
      //$query = "SELECT signboom_product_finishing.Id, ProductCode, FinishingOptionCode, Value, OptionName FROM signboom_product_finishing INNER JOIN signboom_finishing ON signboom_finishing.Code = signboom_product_finishing.FinishingOptionCode WHERE ProductCode = '$product' ORDER BY FinishingOptionCode";
      $query = "SELECT signboom_product_finishing.Id, ProductCode, FinishingOptionCode, Value, OptionName FROM signboom_product_finishing INNER JOIN signboom_finishing ON signboom_finishing.Code = signboom_product_finishing.FinishingOptionCode WHERE ProductCode = '$product' ORDER BY signboom_finishing.SortOrder";
      $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

      // Get a list of all finishing options for the "Add Finishing Option" drop-down.
      if ($category == 'SPECIALTY')
        $query2 = "SELECT Code, OptionName FROM signboom_finishing ORDER BY Category, SortGroup, SortOrder";
      else
        $query2 = "SELECT Code, OptionName FROM signboom_finishing WHERE Category = '$category' ORDER BY SortGroup, SortOrder";
      $result2 = mysqli_query( $DBConn, $query2) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  
      // Display the product description in an editor. 
      include ('templates/edit-product-finishing.php'); 
  
      /* Free memory. */
      ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
    }
  }
  else
  {
    echo "You must specify a product. Please visit the <a href=\"view-products.php\">Products</a> page and click one of the FINISHING links.";
  }

?>
