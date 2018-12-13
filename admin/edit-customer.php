<?php
// /echo phpversion();
//echo "edit-customer.php"; die;
  include ('authadmin.php'); 
  include ('helper-functions.php'); 
  include('../production/includes/date_picker.htm');
  include('../includes/inc-signboom.php');
  include('../includes/cell_email_domains.php');
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

  /* Get list of all discounts in system which are currently enabled. */
  $query_discount = "SELECT ID, Dct FROM signboom_discount WHERE Enabled = 1 ORDER BY Dct";
  $result_discount = mysqli_query( $DBConn, $query_discount) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $discount_array = array();
  $j = 0;
  while ($row_discount = mysqli_fetch_array($result_discount,  MYSQLI_BOTH))
  {
    $discount_array[$j][0] = $row_discount['ID'];
    $discount_array[$j][1] = $row_discount['Dct'];
    $j++;
  }

  $found_an_error = false;
  $error_message = "";
  $created = false;
  $edited = false;
  $edit_mode = "edit";
  $account_name ="";
  $email_address ="";
  if (isset($_REQUEST['account_name']) || isset($_REQUEST['email_address']))
  {
    $account_name =  trim($_REQUEST['account_name']);
    $email_address = trim($_REQUEST['email_address']);
    if (strlen($account_name) > 0)
    {
      $query1 = "SELECT * FROM signboom_user WHERE AcctName = '$account_name'";
    }
    else if (strlen($email_address) > 0)
    {
      $query1 = "SELECT * FROM signboom_user WHERE email = '$email_address'";
    }
    else
    {
      //$error_message = "You must fill in either the customer's account ID or email address.";
      include ('templates/select-customer.php'); 
      exit();
    }

    $result1 = mysqli_query($GLOBALS["___mysqli_ston"], $query1);
    $num_rows = mysqli_num_rows($result1);
    if ($num_rows <= 0)
    {
      $error_message = "There is not an account with that information.<br>";
      include ('templates/select-customer.php'); 
    }
    else 
    {
      if (isset($_POST['submit_edit_customer'])) 
      {
        $account_name = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['account_name']);
        $first_name = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['first_name']);
        $last_name = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['last_name']);
        $email_address = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], trim($_POST['email_address']));
        $password = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['password']);
        $password2 = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['password2']);
        $company = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['company']);
        $street_address = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['street_address']);
        $city = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['city']);
        $province_state = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['province_state']);
        $country = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['country']);
        $postal_zip = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['postal_zip']);
        $phone1 = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['phone1']);
        $phone2 = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['phone2']);
        $hint_question = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['hint_question']);
        $hint_answer = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['hint_answer']);
        $web_url = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['web_url']);
        $discount = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['discount']);
        $html_mail = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['html_mail']);
        $default_courier = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['default_courier']);
        $courier_account = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['courier_account']);
        $user_level = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['user_level']);
        $account_disable = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['account_disable']);
        $pst_number = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['pst_number']);
        $discount_coupon = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['discount_coupon']);
        $next_contact = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['next_contact']);
        $notes = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['notes']);
        $mailing_list = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['mailing_list']);
        $cell_number = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['cell_number']);
        $cell_provider = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['cell_provider']);
        $enable_text_message = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['enable_text_message']);
        $team = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['team']);
        //$copy_to_kim = mysql_real_escape_string($_POST['CopyToKim']); copy_to_kim
		$copy_to_kim = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['copy_to_kim']);

        $found_an_error = true;
        if (strlen($first_name) == 0)
          $error_message = "You must enter a first name.";
        else if (strlen($last_name) == 0)
          $error_message = "You must enter a last name.";
        else if (!isValidEmailAddress(trim($email_address)))
          $error_message = "That is not a valid email address.";
        else if ((strlen($password) > 0) && (strlen($password) < 5))
          $error_message = "The password must be at least five characters long.";
        else if (strcmp($password, $password2) != 0)
          $error_message = "The two copies of the password do not match.  Please fill both in again.";
        else if (strlen($company) == 0)
          $error_message = "You must enter a company name.";
        else if (strlen($street_address) == 0)
          $error_message = "You must enter a street address.";
        else if (strlen($city) == 0)
          $error_message = "You must enter a city.";
        else if (strlen($province_state)== 0)
          $error_message = "You must enter a province or state.";
        else if (strlen($country)== 0)
          $error_message = "You must enter a country.";
        else if (strlen($postal_zip)== 0)
          $error_message = "You must enter a postal or zip code.";
        else if ((strcmp($country, "CA") == 0) && (!isValidPostalCode($postal_zip)))
          $error_message = "That is not a valid Canadian postal code.";
        else if ((strcmp($country, "CA") != 0) && (!isValidZipCode($postal_zip))) // Assume all clients are Canada or US
          $error_message = "That is not a valid US zip code.";
        else if (!isValidPhoneNumber($phone1))
          $error_message = "Primary phone number is not a valid phone number.  Please include the area code.";
        else if ((strcmp($phone2) > 0) && (!isValidPhoneNumber($phone2)))
          $error_message = "Alternate phone number is not a valid phone number.  Please include the area code.";
        else if ((strcmp($hint_question, "SEL") != 0) && (strcmp($hint_question, "CAR") != 0) && (strcmp($hint_question, "MOM") != 0) && (strcmp($hint_question, "SCH") != 0))
          $error_message = "That is not a valid security question.  Please choose one from the drop-down options.";
        else if ((strcmp($hint_question, "SEL") != 0) && (strlen($hint_answer) == 0))
          $error_message = "You must enter an answer to the security question.";
        else if ((strlen($web_url) != 0) && (!isValidURL($web_url)))
          $error_message = "That is not a valid web site URL.";
        else if ((strlen($discount) > 0) && (!isValidDiscountCode($discount)))
          $error_message = "That is not a valid discount code. Please choose one from the drop-down options.";
        else if ((strcmp($html_email, "") != 0) && (strcmp($html_email, "Yes") != 0) && (strcmp($html_email, "No") != 0))
          $error_message = "That is not a valid choice for 'Receive Emails in HTML Format'.  Please choose one from the drop-down options.";
        else if (($user_level != 1) && ($user_level != 2) && ($user_level != 3) && ($user_level != 4))
          $error_message = "That is not a valid user level. Please choose one from the drop-down options.";
        else if (($account_disable != "0") && ($account_disable != "1"))
          $error_message = "That is not a valid choice for 'Disable this Account?'. Please choose one from the drop-down options.";
        else if ((strlen(trim($pst_number)) > 0) && (!preg_match("/^[0-9]{4}-[0-9]{4}$/", trim($pst_number)))) 
          $error_message ="That is not a valid British Columbia PST number. The new BC PST numbers are now in the format 0000-0000.  If your business is located outside of BC, or if you do not have a PST number, please leave the PST number blank.";
        else if ((strlen($next_contact) > 0) && (!isValidDate($next_contact)))
          $error_message = "The date for the next contact is not valid.  Please use the calendar to choose a new date.";
        else if ((strcmp($mailing_list, "") != 0) && (strcmp($mailing_list, "Yes") != 0) && (strcmp($mailing_list, "No") != 0))
          $error_message = "The selection for 'Include this customer in the mailing list.' is not valid.  Please choose one from the drop-down options.";
        else if ((strcmp($enable_text_message, "Yes") != 0) && (strcmp($enable_text_message, "No") != 0))
          $error_message = "The selection for 'Send this customer text messages when orders are proofed and finished.' is not valid.  Please choose one from the drop-down options.";
        else if ((strlen($cell_number) > 0) && (!isValidPhoneNumber($cell_number)))
          $error_message = "The cell phone number is not a valid phone number.  Please include the area code.";
        else if ((strcmp($enable_text_message, "Yes") == 0) && (strlen($cell_number) == 0))
          $error_message = "You must enter a cell phone number for the customer to receive text messages.";
        /*
        else if ((strcmp($enable_text_message, "Yes") == 0) && (!isValidCellProvider($cell_provider)))
          $error_message = "That cell phone provider is not one our system currently handles.  Please call Alison at Usable Web Designs to add this cell phone provider to the system so that this customer can receive text messages.";
        */
        else if (!isValidTeam($team))
          $error_message = "That team is not valid. Please choose from the drop-down options.";
        else if ($copy_to_kim != 1 && $copy_to_kim != 0)
          $error_message = "Copy to Kim must be either No (don't copy to her) or Yes (do copy to her). Please correct your entry.";
        else
        {
           $found_an_error = false;
           if (strlen($password) > 0)
           {
             $user_pass = crypt($password, "urban11oasis22media33");
             $password_query = ", userPass = '$user_pass' ";
           }
           else 
           {
             // don't set password unless user has filled it in
             $password_query = " ";
           }

           $trimmed_email_address = trim($email_address);
$start_query = <<< End_Of_Query
UPDATE signboom_user SET 
  firstName = '$first_name',
  lastName = '$last_name',
  email = '$trimmed_email_address',
  company = '$company',
  address = '$street_address',
  city = '$city',
  provstate = '$province_state',
  country = '$country',
  postalzip = '$postal_zip',
  phone1 = '$phone1',
  phone2 = '$phone2',
  hintq = '$hint_question',
  hinta = '$hint_answer',
  url = '$web_url',
  dct = '$discount',
  htmlmail = '$html_mail',
  defcourier = '$default_courier',
  courieracct = '$courier_account',
  userLevel = '$user_level',
  acctDisable = '$account_disable',
  pstnum = '$pst_number',
  coupon = '$discount_coupon',
  nextContact = '$next_contact',
  notes = '$notes',
  MailingList = '$mailing_list',
  cellNumber = '$cell_number',
  cellProvider = '$cell_provider',
  enableTxtMssg = '$enable_text_message',
  team = '$team', 
  CopyToKim = '$copy_to_kim' 
End_Of_Query;
        $end_query = " WHERE AcctName = '$account_name'";
        $query = $start_query . $password_query . $end_query;
        //echo "QUERY: $query<br>";
        $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $edited = true;

        // Check whether the city is in our database.  
        // If not, issue a "warning" for them to double-check the spelling.
        $city_query = "SELECT * FROM signboom_rates WHERE City = '$city'";
        $city_result = mysqli_query($GLOBALS["___mysqli_ston"], $city_query);
        $city_num_rows = mysqli_num_rows($city_result);
        if ($city_num_rows == 0)
          $error_message = "Warning: The city entered is not in our database.  This will prevent the order form from being able to automatically calculate shipping costs. Please check the spelling, capitalization and number of blank spaces (if applicable) in the city name.";
        }
      }

      // If there was an error and data was not submitted, leave fields populated as is
      // Otherwise, repopulate them using information from the database.
      if (!$found_an_error)
      {
        // Get the details of that customer from the database.
        //$query = "SELECT * FROM signboom_user WHERE AcctName = '$account_name'";
        $result = mysqli_query( $DBConn, $query1) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
        $row = mysqli_fetch_array($result,  MYSQLI_BOTH); 
        $user_id = $row['ID'];
        $account_name = $row['AcctName'];
        $first_name = $row['firstName'];
        $last_name = $row['lastName'];
        $email_address = trim($row['email']);
        $company = $row['company'];
        $street_address = $row['address'];
        $city = $row['city'];
        $province_state = $row['provstate'];
        $country = $row['country'];
        $postal_zip = $row['postalzip'];
        $phone1 = $row['phone1'];
        $phone2 = $row['phone2'];
        $hint_question = $row['hintq'];
        $hint_answer = $row['hinta'];
        $web_url = $row['url'];
        $discount = $row['dct'];
        $html_mail = $row['htmlmail'];
        $default_courier = $row['defcourier'];
        $courier_account = $row['courieracct'];
        $user_level = $row['userLevel'];
        $account_disable = $row['acctDisable'];
        $pst_number = $row['pstnum'];
        $discount_coupon = $row['coupon'];
        $next_contact = $row['nextContact'];
        mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['nextContact']);
        $notes = bbCode($row['notes']);
        $mailing_list = $row['MailingList'];
        $cell_number = $row['cellNumber'];
        $cell_provider = $row['cellProvider'];
        $enable_text_message = $row['enableTxtMssg'];
        $team = $row['team'];
        $copy_to_kim = $row['CopyToKim'];
      }

      include ('templates/edit-customer.php'); 

      // Free memory. 
      ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
    }
  }
  else
  {
    //$error_message = "You must fill in either the customer's account ID or email address.";
    include ('templates/select-customer.php'); 
  }
?>
