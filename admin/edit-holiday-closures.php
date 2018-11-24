<?php 
  include ('authadmin.php'); 
  include ('helper-functions.php'); 
  include('../production/includes/date_picker.htm');
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

  $list_of_holiday_names[] = "SHUTDOWN";
  $list_of_holiday_names[] = "NOSHIP";
  $list_of_holiday_names[] = "New Years Day";
  $list_of_holiday_names[] = "Family Day";
  $list_of_holiday_names[] = "Good Friday";
  $list_of_holiday_names[] = "NS Easter Monday";
  $list_of_holiday_names[] = "Victoria Day";
  $list_of_holiday_names[] = "Canada Day";
  $list_of_holiday_names[] = "BC Day";
  $list_of_holiday_names[] = "Labour Day";
  $list_of_holiday_names[] = "Thanksgiving";
  $list_of_holiday_names[] = "Rememberance Day";
  $list_of_holiday_names[] = "Christmas Day";
  $list_of_holiday_names[] = "NS Boxing Day";

  $error_message = "";
  $deleted = false;

  // If user has asked to delete a closure day...
  if (isset($_POST['delete'])) 
  {
    // Read in posted information.
    $delete = $_POST['delete'];

    // Validate posted information.
    $query = "SELECT * FROM signboom_holiday WHERE ID = $delete";
    $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $number_of_rows = mysqli_num_rows($result);
    if ($number_of_rows == 0)
    {
      // If information is NOT valid, give error message.
      $error_message = "That closure date is no longer in the holiday database.";
    }
    else 
    {
      $query = "DELETE FROM signboom_holiday WHERE ID = $delete";
      $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $deleted = true;
    }
  }

  // If user has asked to add a new closure date...
  else if (isset($_POST['add'])) 
  {
    // Read in posted information.
    $new_date = $_POST['new_date'];
    $name_of_holiday = $_POST['name_of_holiday'];

    // Validate posted information.
    if (!isValidDate($new_date))
    {
      $error_message = "That is not a valid date. It has not been added to the holiday database.";
    }
    else
    {
      $query = "SELECT * FROM signboom_holiday WHERE holiday = '$new_date'";
      $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      if (mysqli_num_rows($result) > 0)
      {
        $error_message = "That date is already marked down as a holiday/closure.";
      }
      else 
      {
        $query = "INSERT INTO signboom_holiday SET holiday = '$new_date', Description = '$name_of_holiday'";
echo $query . '<br>';
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      }
    }
  } 

  // Get the list of all holidays and closure dates from the database, so we can display them.
  $query = "SELECT * FROM signboom_holiday ORDER BY holiday";
  $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

  // Display the product description in an editor. 
  include ('templates/edit-holiday-closures.php'); 
  
  /* Free memory. */
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

?>
