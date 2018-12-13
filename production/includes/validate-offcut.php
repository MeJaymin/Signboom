<?php
        if ($button_name == 'submit_create_offcut')
        {
          // Code populates these two variables.
	  $date_added = date('Y-m-d');
	  $person = $_SESSION["MM_Username"];
	  $query_user = "SELECT AcctName FROM signboom_user WHERE email = '$person'";
	  $result_user = mysqli_query( $DBConn, $query_user) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
          $row_user  = mysqli_fetch_array($result_user,  MYSQLI_BOTH);
          $person_added = $row_user['AcctName'];
        }
        else // editing offcut information; not allowed to change date and person
        {
	  $date_added = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['date_added']); 
          $person_added = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['person_added']);
        }

        //Read the rest of the data from what was posted.
        $material = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['material']);
        $width = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['width']);
        $length = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['length']);
        $quantity = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['quantity']);
        $paid_for = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['paid_for']);
        $description = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['description']);

	if (strlen(trim($quantity)) == 0)  $quantity = '1';

	// Validate posted data.
	$valid = false;
	if (strlen(trim($material)) == 0)
	  echo "<script language=\"javascript\">alert(\"You must choose a Product (Media).\");</script>";
        else if (!isPrintableProduct($material))
	  echo "<script language=\"javascript\">alert(\"Product '$material' is not a valid product. You must enter the correct product code for a printable product.\");</script>";
        else if (!isValidDimension($width)) 
	  echo "<script language=\"javascript\">alert(\"Width '$width' is not valid. It must be a numeric value.\");</script>";
	else if ($width == 0)
	  echo "<script language=\"javascript\">alert(\"The width of the offcut cannot be zero.\");</script>";
        else if (!isValidDimension($length)) 
	  echo "<script language=\"javascript\">alert(\"Length '$length' is not valid. It must be a numeric value.\");</script>";
	else if ($length == 0)
	  echo "<script language=\"javascript\">alert(\"The length of the offcut cannot be zero.\");</script>";
        else if (!ctype_digit($quantity)) 
	  echo "<script language=\"javascript\">alert(\"Quantity '$quantity' is not valid. It must be an integer.\");</script>";
        else if (($paid_for != "0") && ($paid_for != "1") && ($paid_for != ""))
	  echo "<script language=\"javascript\">alert(\"Paid For must be either Yes or No.\");</script>";
        else
	  $valid = true;
?>
