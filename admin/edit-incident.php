<?php 
  include ('authadmin.php'); 
  include ('helper-functions.php'); 
  include('../production/includes/date_picker.htm');
  //include('../includes/inc-signboom.php');
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

  $found_an_error = false;
  $error_message = "";
  $created = false;
  $edited = false;
  $edit_mode = "edit";

  $incident_list = array();
  $query_list = "SELECT OrderId FROM signboom_incidents WHERE 1 ORDER BY Date DESC";
  $result_list = mysqli_query( $DBConn, $query_list) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  while ($row = mysqli_fetch_array($result_list,  MYSQLI_BOTH))
  {
    $incident_list[] = $row['OrderId'];
  }

  if (isset($_REQUEST['incident_id']) || isset($_REQUEST['order_id']))
  {
    $incident_id =  trim($_REQUEST['incident_id']);
    $order_id = trim($_REQUEST['order_id']);
    if (strlen(trim($incident_id)) > 0)
    {
      $query1 = "SELECT * FROM signboom_incidents WHERE IncidentId = '$incident_id'";
    }
    else if (strlen(trim($order_id)) > 0)
    {
      $query1 = "SELECT * FROM signboom_incidents WHERE OrderId = '$order_id'";
    }
    else
    {
      $error_message = "You must fill in either the incident ID or the order ID.";
      include ('templates/select-incident.php'); 
      exit();
    }

    $result1 = mysqli_query($GLOBALS["___mysqli_ston"], $query1);
    $num_rows = mysqli_num_rows($result1);
    if ($num_rows <= 0)
    {
      $error_message = "There is not an incident with that information.<br>";
      include ('templates/select-incident.php'); 
    }
    else 
    {
      if (isset($_POST['submit_edit_incident'])) 
      {
        //$incident_id = mysql_real_escape_string($_POST['incident_id']); Autoincrement by database.
        $incident_date = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['incident_date']);
        $order_id = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], trim($_POST['order_id']));
        $incident_value = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['incident_value']);
        $upload_notes = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['upload_notes']);
        $incident_type = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['incident_type']);
        $accountable = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['accountable']);
        $caused = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['caused']);
        $comments = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['comments']);

        $found_an_error = true;
        if (!isValidDate(trim($incident_date)))
          $error_message = "That is not a valid date.";
        else if (!isValidOrderId(trim($order_id)))
          $error_message = "There is no order in the system with order ID '$order_id'.";
        else if ((strlen(trim($incident_value)) > 0) && (!is_numeric($incident_value)))
          $error_message = "The value '$incident_value' is not valid. Please leave out the dollar sign.";
        else if (strlen($incident_type) == 0)
          $error_message = "You must enter an incident type.";
        else
        {
           $found_an_error = false;
$start_query = <<< End_Of_Query
UPDATE signboom_incidents SET 
  Date = '$incident_date',
  OrderId = '$order_id',
  Value = '$incident_value',
  UploadNotes = '$upload_notes',
  Type = '$incident_type',
  Accountable = '$accountable',
  Caused = '$caused',
  Comments = '$comments'
End_Of_Query;
        $end_query = " WHERE IncidentId = '$incident_id'";
        $query = $start_query . $end_query;
        //echo "QUERY: $query<br>";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $edited = true;
	}
      }

      // Display information form database for this incident.
      if (!$found_an_error) // Leave entered data in place if an error was found.
      {
        // Get the details of that incident from the database.
        $result = mysqli_query( $DBConn, $query1) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        //echo "QUERY: $query1<br>";
        $row = mysqli_fetch_array($result,  MYSQLI_BOTH); 
        $incident_id = $row['IncidentId'];
        $incident_date = $row['Date'];
        $order_id = $row['OrderId'];
        $incident_value = trim($row['Value']);
        $upload_notes = $row['UploadNotes'];
        $incident_type = $row['Type'];
        $accountable = $row['Accountable'];
        $caused = $row['Caused'];
        $comments = $row['Comments'];
      }

      include ('templates/edit-incident.php'); 

      // Free memory. 
      ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
    }
  }
  else
  {
    include ('templates/select-incident.php'); 
  }
?>
