<?php
require_once('../includes/mailord.php');
mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"])); 

/********************************* function ***********************************/
function handle_offcut_submit() {
  global $DBConn;
  $my_debug = 1;
  //print_r($_POST);
  $number_of_offcuts = $_POST['number_of_offcuts'];
  $the_date = date("Y-m-d");
  if (isset($_SESSION["MM_Username"])) 
  {
    $current_user = $_SESSION["MM_Username"];
    $query_user = "SELECT AcctName FROM signboom_user WHERE email = '$current_user'";
    $result_user = mysqli_query( $DBConn, $query_user) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $row_user = mysqli_fetch_assoc($result_user);
    $the_username = $row_user['AcctName'];
  }
  else
  {
    $the_username = 'UNKNOWN';
  }
  // For each of the offcuts on that page....
  for ($k = 0; $k <= $number_of_offcuts; $k++) 
  {
    //echo $_POST['offcut_id_'.$k].'<br>';
    $the_offcut_id = isset($_POST['offcut_id_'.$k])?$_POST['offcut_id_'.$k]:"";
    $send_query = 0;

    // If there is an offcut on that row...
    if ($the_offcut_id  != 0) 
    {
      // Read information the user has just submitted for that offcut.
      if (isset($_POST['claimed_'.$k]))
        $new_claimed = 1;
      else
        $new_claimed = 0; // false or disabled ******** don't update value in database if disabled
      if (isset($_POST['used_'.$k]))
        $new_used = 1;
      else
        $new_used = 0;  // false or disabled ******** don't update value in database if disabled

      // Check what status the offcut was at previously (in the database).
      $get_query = "SELECT * FROM signboom_offcuts WHERE OffcutId = $the_offcut_id";
      $result = mysqli_query( $DBConn, $get_query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $row = mysqli_fetch_assoc($result);
      $old_claimed = $row['Claimed'];
      $old_used = $row['Used'];
      $material = $row['Material'];
      $width = $row['Width'];
      $length = $row['Length'];
      $quantity = $row['Quantity'];

      //echo "$the_offcut_id: Claimed $old_claimed -> $new_claimed, Used $old_used -> $new_used<br>";

      // Create query to update offcut status.
      $update_query = "UPDATE signboom_offcuts SET ";
      if ($old_used == 0) // User is NOT allowed to UNtick claimed and used if used was ticked in the past.
      {
        if (($new_claimed == 1) && ($old_claimed == 0))
	{
	  $update_query .= "Claimed = 1, DateClaimed = '$the_date', PersonClaimed = '$the_username'";
	  $send_query = 1;
        }
        if (($new_claimed == 1) && ($old_claimed == 0) && ($new_used == 1) && ($old_used == 0))
	{
	  $update_query .= ", ";
	}
        if (($new_used == 1) && ($old_used == 0))
	{
	  $update_query .= "Used = 1, DateUsed = '$the_date', PersonUsed = '$the_username' ";
	  $send_query = 1;
        }
        if (($new_claimed == 0) && ($old_claimed == 1))
	{
	  $update_query .= "Claimed = 0, DateClaimed = '', PersonClaimed = '', Used = 0, DateUsed = '', PersonUsed = '' ";
	  $send_query = 1;
        }
      }
      $update_query .= " WHERE OffcutId = $the_offcut_id";

      // Send query up to database.
      if ($send_query) 
      {
        //echo "<script language=\"javascript\">alert(\"Update Query: $update_query\");</script>";
        $result = mysqli_query( $DBConn, $update_query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

        // If an offcut is claimed or used, email Len and Alison and other production staff. 
        include('includes/send_offcut_email.php'); // This code also forces the necessary page refresh
      }

    } // end of if there is an offcut on that row

  }  // end of for loop

} // end of function handle_offcut_submit()

/********************************* main code ***********************************/
    
   if (isset($_POST['update_offcuts'])) {
     handle_offcut_submit();
   }

?>
