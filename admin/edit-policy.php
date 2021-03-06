<?php 
  include ('authadmin.php'); 
  include ('helper-functions.php'); 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

  // Get list of all policy categories. 
  $query_category = "SELECT DISTINCT Category FROM signboom_policies WHERE 1";
  $result_category = mysqli_query( $DBConn, $query_category) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $category_array = array();
  $j = 0;
  while ($row_category = mysqli_fetch_array($result_category,  MYSQLI_BOTH))
  {
    $category_array[$j] = $row_category['Category'];
    $j++;
  }

  $policy_details = "";
  $error_message = "";
  $created = false;
  $edited = false;
  $edit_mode = "edit";
  $policy_id="";
  if (isset($_REQUEST['id']))
  {
    $policy_id = $_REQUEST['id'];
    if (!isValidPolicyId($policy_id))
    {
      echo "You must specify a policy id. Please visit the <a href=\"select-policy.php\">Policies</a> page and EDIT for the policy you wish to edit.";
    }
    else 
    {
      if (isset($_POST['submit_edit_policy'])) 
      {
        $policy_category = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['policy_category']);
        $policy_title = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['policy_title']);
        $policy_display = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['policy_display']);
        $policy_details = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['policy_details']);

        if (strlen($policy_category) == 0)
          $error_message = "You must choose a policy category.";
        else if (!isValidProductName($policy_title))
          $error_message = "'$policy_title' is not a valid policy title. Only letters, numbers, dashes, periods, spaces and () are allowed. The name must be between 4 and 64 characters long.";
        else if (strlen($policy_display) == 0)
          $error_message = "You must indicate whether this policy is to be displayed in the Production system or not.";
        else if (($policy_display != 0) && ($policy_display != 1))
          $error_message = "The value for display policy must be 0 (don't display it) or 1 (display it).";
        else if (strlen(trim($policy_details)) < 100)
          $error_message = "Your policy is too short.  Please provide a complete policy description of at least 100 characters.";
 
        else
        {
          $query = "UPDATE signboom_policies SET Title = '$policy_title', Display = '$policy_display', Category = '$policy_category', Policy = '$policy_details' WHERE ID = $policy_id";
          //echo "Query: $query<br><br>";
          $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
          $edited = true;
        }
      }

      // Get the description of that policy from the database.
      $query = "SELECT * FROM signboom_policies WHERE ID = $policy_id";
      $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $row = mysqli_fetch_array($result,  MYSQLI_BOTH); 
      $policy_category = $row['Category'];
      $policy_title = $row['Title'];
      $policy_display = $row['Display'];
      $policy_details = $row['Policy'];
  
      // Display the policy details in an editor. 
      include ('templates/edit-policy.php'); 
  
      /* Free memory. */
      ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
    }
  }
  else
  {
    echo "You must specify a policy id. Please visit the <a href=\"select-policy.php\">Policies</a> page and click EDIT for the policy you wish to edit.";
  }

?>
