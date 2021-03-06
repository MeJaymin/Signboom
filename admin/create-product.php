<?php 
  include ('authadmin.php'); 
  include ('helper-functions.php'); 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

  /* Get list of all categories in system which are currently enabled. */
  $query_category = "SELECT code FROM signboom_category WHERE enabled = 1 OR enabled = 2";
  $result_category = mysqli_query( $DBConn, $query_category) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $category_array = array();
  $j = 0;
  while ($row_category = mysqli_fetch_array($result_category,  MYSQLI_BOTH))
  {
    $category_array[$j] = $row_category['code'];
    $j++;
  }

  $error_message = "";
  $edited = false;
  $created = false;
  $edit_mode = "create";
  if (isset($_POST['submit_create_product'])) 
  {
    $product_code = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_code']);
    $product_name = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_name']);
    $product_thickness = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_thickness']);
    $product_uom_thickness = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_uom_thickness']);
    $product_enabled = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_enabled']);
    $product_category = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_category']);
    $product_batch_day = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_batch_day']);
    $product_description = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], isset($_POST['product_description'])?$_POST['product_description']:"");
    $product_descr_image = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_descr_image']);
    $product_descr_text= mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_descr_text']);
    $product_descr_finishing = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_descr_finishing']);
    $product_descr_limitations = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_descr_limitations']);
    $product_descr_extras = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_descr_extras']);
    $product_width = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_width']);
    $product_length = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_length']);
    $product_cost_waste = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_cost_waste']);
    $product_cost_non = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_cost_non']);
    $product_cost_disc = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_cost_disc']);
    $product_sort_group = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_sort_group']);
    $product_sort_order = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_sort_order']);

    if (!isValidProductCode($product_code))
      $error_message = "'$product_code' is not a valid product code. The code may only contain only capital letters and numbers, and must be between 3 and 5 characters in length.";
    else if (!isValidProductName($product_name))
      $error_message = "'$product_name' is not a valid product name. Only letters, numbers, dashes, periods, spaces and () are allowed. The name must be between 4 and 64 characters long.";
    else if (!is_numeric($product_thickness))
      $error_message = "'$product_thickness' is not a valid value. Only integer and decimal numbers are allowed. Note: the thickness is given in mm.";
    else if (($product_uom_thickness != 'MM') && ($product_uom_thickness != 'IN') && ($product_uom_thickness != 'MIL') && ($product_uom_thickness != ''))
      $error_message = "'$product_uom_thickness' is not a valid value. Value must be either inches, mm,  mil or blank.";
    else if (strlen($product_enabled) == 0)
      $error_message = "You must indicate whether this product is to be enabled or not.";
    else if (strlen($product_category) == 0)
      $error_message = "You must choose a product category.";
/*
    else if (strlen(trim($product_description)) < 100)
      $error_message = "Your product description is too short.  Please provide a complete product description of at least 100 characters.";
    else if (strlen(trim($product_descr_text)) < 100)
      $error_message = "Your product description text is too short.  Please provide a complete product description of at least 100 characters.";
*/
    else if (!isValidDimension($product_width))
      $error_message = "'$product_width' is not a valid product dimension. The width must be given in inches, with only numbers and the period allowed.";
    else if (!isValidDimension($product_length))
      $error_message = "'$product_length' is not a valid product dimension. The length must be given in inches, with only numbers and the period allowed.";
    else if (!isValidCostFactor($product_cost_waste))
      $error_message = "'$product_cost_waste' is not a valid waste cost. The waste cost must be given in dollars and cents, with no dollar sign included.";
    else if (!isValidCostFactor($product_cost_non))
      $error_message = "'$product_cost_non' is not a valid non-discountable cost. The non-discountable cost must be given in dollars and cents, with no dollar sign included.";
    else if (!isValidCostFactor($product_cost_disc))
      $error_message = "'$product_cost_disc' is not a valid discountable cost. The discountable cost must be given in dollars and cents, with no dollar sign included.";
    else if (!isValidSortGroup($product_sort_group))
      $error_message = "'$product_sort_group' is not a valid sort group. Sort groups must be alphabetical only, and are typically one or two upper-case letters.";
    else if (!isValidSortOrder($product_sort_order))
      $error_message = "'$product_sort_order' is not a valid sort order. Sort orders must be numeric only and are typically from 1 to 99.";
    else
    {
      $query = "INSERT INTO signboom_allproducts SET Code = '$product_code', Name = '$product_name', Thickness = '$product_thickness', Units = '$product_uom_thickness', Enabled = '$product_enabled', Category = '$product_category', BatchDay = $product_batch_day, Description = '$product_description', DescriptionImage = '$product_descr_image', DescriptionText = '$product_descr_text', DescriptionFinishing = '$product_descr_finishing', DescriptionLimitations = '$product_descr_limitations', DescriptionExtras = '$product_descr_extras', Width = '$product_width', Length = '$product_length', CostWaste = '$product_cost_waste', CostNon = '$product_cost_non', CostDisc = '$product_cost_disc', SortGroup = '$product_sort_group', SortOrder = '$product_sort_order'";
      //echo "Query: $query<br><br>";
      $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $created = true;
      $edit_mode = "edit";

      // Get the information of that product from the database.
      $query = "SELECT Id, Code, Name, Enabled, Category, Description, Width, Length, CostWaste, CostNon, CostDisc, SortGroup, SortOrder FROM signboom_allproducts WHERE Code = '$product_code'";
      $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $row = mysqli_fetch_array($result,  MYSQLI_BOTH); 
      $product_id = $row['Id'];
      $product_code = $row['Code'];
      $product_name = $row['Name'];
      $product_thickness = $row['Thickness'];
      $product_uom_thickness = $row['Units'];
      $product_enabled= $row['Enabled'];
      $product_category = $row['Category'];
      $product_batch_day = $row['BatchDay'];
      $product_description = bbCode($row['Description']);
      $product_descr_image = bbCode($row['DescriptionImage']);
      $product_descr_text = bbCode($row['DescriptionText']);
      $product_descr_finishing = bbCode($row['DescriptionFinishing']);
      $product_descr_limitations = bbCode($row['DescriptionLimitations']);
      $product_descr_extras = bbCode($row['DescriptionExtras']);
      $product_width = $row['Width'];
      $product_length= $row['Length'];
      $product_cost_waste = $row['CostWaste'];
      $product_cost_non = $row['CostNon'];
      $product_cost_disc= $row['CostDisc'];
      $product_sort_group = $row['SortGroup'];
      $product_sort_order = $row['SortOrder'];
      // Free memory. 
      ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
    }
  }
  else
  {
    $product_id = "";
    $product_code = "";
    $product_name = "";
    $product_thickness = "";
    $product_uom_thickness = "";
    $product_enabled= "";
    $product_category = "";
    $product_batch_day = "";
    $product_description = "";
    $product_descr_image = "";
    $product_descr_text = "";
    $product_descr_finishing = "";
    $product_descr_limitations = "";
    $product_descr_extras = "";
    $product_width = "";
    $product_length = "";
    $product_cost_waste = "";
    $product_cost_non = "";
    $product_cost_disc= "";
    $product_sort_group = "";
    $product_sort_order = "";
  }

  // Display the product description in an editor. 
  include ('templates/edit-product.php'); 

  

?>
