<?php 
  include ('authadmin.php'); 
  include ('helper-functions.php'); 
  include('../production/includes/date_picker.htm');
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

  $hide_incident_id = true;
  $error_message = "";
  $edited = false;
  $created = false;
  $edit_mode = "create";
  if (isset($_POST['submit_create_incident'])) 
  {
    $incident_date = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['incident_date']);
    $order_id = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['order_id']);
    $incident_value = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['incident_value']);
    $upload_notes  = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['upload_notes']);
    $incident_type = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['incident_type']);
    $accountable = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['accountable']);
    $caused = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['caused']);
    $comments = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['comments']);

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
    $query = "INSERT INTO signboom_incidents SET Date = '$incident_date', OrderId = '$order_id', Value = '$incident_value', UploadNotes = '$upload_notes', Type = '$incident_type', Accountable = '$accountable', Caused = '$caused', Comments = '$comments'";
      //echo "Query: $query<br><br>";
      $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $created = true;
      $edit_mode = "edit";

      // Get the information of that product from the database.
      $query = "SELECT * FROM signboom_incidents WHERE IncidentID = '$incident_id'";
      $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
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
      // Free memory. 
      ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
    }
  }
  else
  {
    $incident_date = "";
    $order_id = "";
    $incident_value = "";
    $upload_notes = "";
    $incident_type = "";
    $accountable = "";
    $caused = "";
    $comments = "";
  }

  // Display the product description in an editor. 
  include ('templates/edit-incident.php'); 

?>
