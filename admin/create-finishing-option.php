<?php 
  include ('authadmin.php'); 
  include ('helper-functions.php'); 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

  // Get list of all categories in system. Include those which are NOT currently enabled  
  // on the regular order form so that we can assign finishing options to categories that 
  // are only offered to volume customers like TED/PDW through drag and drop interface.   
  $query_category = "SELECT code FROM signboom_category WHERE 1";
  $result_category = mysqli_query( $DBConn, $query_category) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $category_array = array();
  $i = 0;
  while ($row_category = mysqli_fetch_array($result_category,  MYSQLI_BOTH))
  {
    $category_array[$i] = $row_category['code'];
    $i++;
  }

  // Get a list of all the option sets.
  $query_optionset = "SELECT DISTINCT Name FROM signboom_finishing_sets WHERE 1";
  $result_optionset = mysqli_query( $DBConn, $query_optionset) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $optionset_name_array = array();
  $optionset_display_name_array = array();
  $j = 0;
  while ($row_optionset = mysqli_fetch_array($result_optionset,  MYSQLI_BOTH))
  {
    $optionset_display_name_array[$j] = $row_optionset['Name'];
    $optionset_name_array[$j] = strtoupper($row_optionset['Name']);
    if ($optionset_name_array[$j] == 'CUTTING') $optionset_name_array[$j] = 'FINISHING';
    $j++;
  }

  $error_message = "";
  $edited = false;
  $created = false;
  $edit_mode = "create";
  if (isset($_POST['submit_create_finishing_option'])) 
  {
    $product_category = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['product_category']);
    $finishing_option_set = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['finishing_option_set']);
    $finishing_option_type = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['finishing_option_type']);
    $finishing_option_name = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['finishing_option_name']);
    $finishing_short_name = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['finishing_short_name']);
    $finishing_option_queue = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['finishing_option_queue']);
    $finishing_option_batch_day = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['finishing_option_batch_day']);
    $finishing_option_description = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['finishing_option_description']);
    $finishing_option_code = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['finishing_option_code']);
    $extra_time = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['extra_time']);
    $finishing_option_fixed_cost = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['finishing_option_fixed_cost']);
    $finishing_option_variable_cost = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['finishing_option_variable_cost']);
    $finishing_option_sort_group = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['finishing_option_sort_group']);
    $finishing_option_sort_order = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['finishing_option_sort_order']);
    $units_of_measure = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['units_of_measure']); // EA, BS, SF, PF
    $units_per_hour = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['units_per_hour']);
    $reference = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['reference']);

    if (strlen($product_category) == 0)
      $error_message = "You must choose a product category.";
    else if ($finishing_option_set == "CHOOSE")
      $error_message = "You must choose a finishing option set from the list.";
    else if ($finishing_option_type == "CHOOSE")
      $error_message = "You must choose a finishing option type from the list.";
    else if (!isValidProductName($finishing_option_name)) // same rules as for product names
      $error_message = "'$finishing_option_name' is not a valid finishing option name. Only letters, numbers, dashes, periods, spaces and () are allowed. The name must be between 4 and 64 characters long.";
    else if (strlen($finishing_short_name) > 8) 
      $error_message = "'$finishing_short_name' is too long. The Short Name MUST be 8 characters or less.";
    //else if (strlen($finishing_short_name) < 3) 
      //$error_message = "'$finishing_short_name' is too short. The Short Name MUST be 3 characters or more.";
    else if (!isValidQueue($finishing_option_queue)) 
      $error_message = "'$finishing_option_queue' is not a valid queue name.";
/*
    else if (strlen(trim($finishing_option_description)) < 100)
      $error_message = "Your finishing option description is too short.  Please provide a complete product description of at least 100 characters.";
*/
    else if (!isValidFinishingOptionCode($finishing_option_code))
      $error_message = "'$finishing_option_code' is not a valid finishing option code. The code must be in the format XX-X or XX-XX or XX-XXX where the X's are capital letters.";
    else if (strlen($extra_time) == 0) 
      $error_message = "You must specify the number of extra days this finishing requires.  The value can be 0 if no extra days are required.";
    //else if ((!is_int($extra_time)) || ($extra_time < 0))
    else if ($extra_time < 0)
      $error_message = "The number of extra days this finishing requires must be 0 or greater.";
    else if (!isValidCostFactor($finishing_option_fixed_cost))
      $error_message = "'$finishing_option_fixed_cost' is not a valid fixed cost. The fixed cost must be given in dollars and cents, with no dollar sign included.";
    else if (!isValidCostFactor($finishing_option_variable_cost))
      $error_message = "'$finishing_option_variable_cost' is not a valid variable cost. The variable must be given in dollars and cents, with no dollar sign included.";
    else if (!isValidSortGroup($finishing_option_sort_group))
      $error_message = "'$finishing_option_sort_group' is not a valid sort group. Sort groups must be alphabetical only, and are typically one or two upper-case letters.";
    else if (!isValidSortOrder($finishing_option_sort_order))
      $error_message = "'$finishing_option_sort_order' is not a valid sort order. Sort orders must be numeric only and are typically from 1 to 99.";
    //else if ((!is_int($units_per_hour)) || ($units_per_hour < 0))
    else if ($units_per_hour < 0)
      $error_message = "The units per hour must be blank or 0 or an integer greater than 0.";
    else if ((strlen($units_per_hour) > 0) && ($units_of_measure == "CHOOSE"))
      $error_message = "Whenever you enter a value for Units per Hour, you must choose a unit from the Units list.";
    else if ((strlen($reference) > 0) && (!isValidProductName($reference)))
      $error_message = "'$reference' is not valid. Only letters, numbers, dashes, periods, spaces and () are allowed. The reference must be less than 32 characters long.";
    else
    {

      // First, make sure that finishing option is not already in the database.
      $query_already = "SELECT * FROM signboom_finishing WHERE Code = '$finishing_option_code'";
      $result_already = mysqli_query( $DBConn, $query_already) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $already = mysqli_num_rows($result_already);

      if ($already)
      {
        $error_message = 'There is already a finishing option with the code ' . $finishing_option_code . '. The information you have just submitted has NOT been saved. If you wish to edit an existing option, please visit the <a href="select-finishing-option.php">Finishing Options</a> page.';
      }
      else
      {
        $query = "INSERT INTO signboom_finishing SET Category = '$product_category', OptionSet = '$finishing_option_set', OptionType = '$finishing_option_type', OptionName = '$finishing_option_name', ShortName = '$finishing_short_name', Queue = '$finishing_option_queue', BatchDay = $finishing_option_batch_day, Description = '$finishing_option_description',  Code = '$finishing_option_code', ExtraTime= '$extra_time', Fixed = '$finishing_option_fixed_cost', Variable = '$finishing_option_variable_cost', SortGroup = '$finishing_option_sort_group', SortOrder = '$finishing_option_sort_order', Units= '$units_of_measure', UnitsPerHour= '$units_per_hour', Reference = '$reference'";
        //echo "Query: $query<br><br>";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $created = true;
        $edit_mode = "edit";

        // Get the description of that option from the database.
        $finishing_option_id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
        $query = "SELECT * FROM signboom_finishing WHERE Id = $finishing_option_id";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $row = mysqli_fetch_array($result,  MYSQLI_BOTH);

        $product_category = $row['Category'];
        $finishing_option_set = $row['OptionSet'];
        $finishing_option_type = $row['OptionType'];
        $finishing_option_name = $row['OptionName'];
        $finishing_short_name = $row['ShortName'];
        $finishing_option_queue = $row['Queue'];
        $finishing_option_batch_day = $row['BatchDay'];
        $finishing_option_description = bbCode($row['Description']);
        $finishing_option_code = $row['Code'];
        $extra_time = $row['ExtraTime'];
        $finishing_option_fixed_cost = $row['Fixed'];
        $finishing_option_variable_cost = $row['Variable'];
        $finishing_option_sort_group = $row['SortGroup'];
        $finishing_option_sort_order = $row['SortOrder'];
        $units_of_measure = $row['Units'];
        $units_per_hour = $row['UnitsPerHour'];
        $reference = $row['Reference'];
      }
    }
  }
  else
  {
    $finishing_option_id = "";
    $product_category = "";
    $finishing_option_set = "";
    $finishing_option_type = "";
    $finishing_option_name = "";
    $finishing_short_name = "";
    $finishing_option_queue = "";
    $finishing_option_batch_day = "";
    $finishing_option_description = "";
    $finishing_option_code = "";
    $extra_time = "";
    $finishing_option_fixed_cost = "";
    $finishing_option_variable_cost = "";
    $finishing_option_sort_group = "";
    $finishing_option_sort_order = "";
    $units_of_measure = "";
    $units_per_hour = "";
    $reference = "";
  }

  // Display the finishing option description in an editor. 
  include ('templates/edit-finishing-option.php'); 

  // Free memory. 
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

?>
