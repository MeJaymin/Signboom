<?php

  require_once( "includes/inc-mysql.php" );
  require_once( "includes/dohash.php" );
  require_once( "includes/inc-signboom.php" );
  require_once( "includes/utils.php" );

////////////////////////////////////
// KILL SESSION
////////////////////////////////////
  session_start();
  session_unset();
  $_SESSION=array();

////////////////////////////////////
// DEBUG FLAG
////////////////////////////////////
  $debug=0;
  $oldcity = (isset($_POST['oldcity'])) ? ($_POST['oldcity']) : "";
  $oldst = (isset($_POST['oldst'])) ? ($_POST['oldst']) : "";
  $Cities[0] = "";
  $cityerr = false;
  global $needcookie;
  $needcookie=0;     
  global $arrProvState;
  global $arrCountry;
  global $arrHintQ;
  global $arrProv;

  // no database connction - bail with error message
  if( 0 != ConnectDB( "signboom_v1p5" ) ){ // GLOBAL_DB_NAME
    print "DB connection failed.";
    exit;
  }

////////////////////////////////////
// BEGIN FUNCTIONS
////////////////////////////////////
function makeRandomPassword() {
  $salt = "abchefghjkmnpqrstuvwxyz0123456789";
  srand((double)microtime()*1000000);
  $i = 0;
  while ($i <= 5) {
    $num = rand() % 33;
    $tmp = substr($salt, $num, 1);
    $pass = $pass . $tmp;
    $i++;
  }
  return $pass;
}

function saveUser()
{
/*	
    global $firstName, $lastName, $email;
    global $userName, $company, $address, $url, $PST;
    global $selectProvState, $city, $phone1, $phone2;
    global $selectCountry, $postalzip, $selectHintQ, $hinta, $hintq;
    global $defcourier, $courieracct;
    global $arrHintQ, $arrHintQ2;
    global $errMsg;
    global $needcookie;
    global $exitPopup;
    global $epassword1, $epassword2;
    global $nextContact;
*/
    global $global_welcome_email_msg;
    global $global_welcome_email_footer;
    global $global_welcome_email_subject;
    global $global_sender;
	
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	$email = $_POST['email'];
	$userName = $_POST['userName'];
	$company = $_POST['company'];
	$address = $_POST['address'];
	$url = $_POST['url'];
	$PST = $_POST['PST'];
	$selectProvState = $_POST['selectProvState'];
	$city = $_POST['city'];
	$phone1 = $_POST['phone1'];
	$phone2 = $_POST['phone2'];
	$selectCountry = $_POST['selectCountry'];
	$selectHintQ = $_POST['selectHintQ'];
	$hinta = $_POST['hinta'];
	$hintq = $_POST['hintq'];
	$defcourier = $_POST['defcourier'];
	$courieracct = $_POST['courieracct'];
	$arrHintQ = $_POST['arrHintQ'];
	$arrHintQ2 = $_POST['arrHintQ2'];
	$errMsg = $_POST['errMsg'];
	$epassword1 = $_POST['epassword1'];
	$epassword2 = $_POST['epassword2'];
	$nextContact = $_POST['nextContact'];
	$needcookie = $_POST['needcookie'];
	/*
	$firstName = $_POST['firstName'];
	$firstName = $_POST['firstName'];
	$firstName = $_POST['firstName'];
	$firstName = $_POST['firstName'];
	*/	
	
	//echo "epassword1 : " . $_POST['epassword1']; die;

    // Check that you can connect to database.
    if( 0 != ConnectDB( "signboom_v1p5" ) ) //GLOBAL_DB_NAME
	{ 
      print "DB connection failed.";
      exit;
    }
	
    // Ensure that a new account is not created with same email as an existing one.
    $email=((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], trim($email)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
    $myQuery="SELECT * FROM signboom_user WHERE email='$email'"; 
    $result=mysqli_query($GLOBALS["___mysqli_ston"], $myQuery);
    if(mysqli_num_rows($result)){
      $errMsg="An account already exists for email address $email.";
      return;
    }

   // Encrypt the password the user has chosen.
   $encpass = crypt($epassword1, "urban11oasis22media33");

   // Save which hint question they chose (using an abbreviation for question)
   $question=((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $arrHintQ2[$selectHintQ]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
  
   // Create account name with 4 letters of first name and 6 letters of company name.
   $tmpfname = preg_replace('/\W/','', $firstName);
   $tmpco = preg_replace('/\W/','', $company);
   $account_name = addslashes(strtoupper(substr($tmpfname,0,4).substr($tmpco,0,6)));

   // Avoid creating a duplicate account name, by editing last letter of name if necessary.
   $replacement_letter = 'A';
   do {
     $sql_check = "SELECT AcctName FROM signboom_user WHERE AcctName = '$account_name'";
     $result_check = mysqli_query($GLOBALS["___mysqli_ston"], $sql_check) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
     $num_rows_check = mysqli_num_rows($result_check);
     // If that account name is already used, replace the last letter with a new letter
     if ($num_rows_check) {
       $name_length = strlen($account_name);
       $account_name = substr($account_name, 0, $name_length - 1) . $replacement_letter;
       $replacement_letter++; // go to next replacement letter in case we need to loop again
     }
   }
   while ($num_rows_check > 0);

   // Want to contact new clients 7 days after signup.
   $nextContact = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")+7, date("Y")));
   $username = ""; /* we no longer use this */

   // Create a unique confirmation code for this client. We will send them an email with
   // a link they have to click to enable their account. This way we can ensure that enabled
   // accounts all have real email addresses, so we don't get lots of bounces when we send
   // out newsletters through MailChimp.
    $confirmation_code = md5($account_name . time() . rand(1,1000000));
	
	/* Added by zCon */
		$protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
		$host = $_SERVER['SERVER_NAME'];
		$confirmation_link = '<a href="'. $protocol .'://'. $host .'/Signboom' . '/activate-account.php?account_name=' . $account_name . '&confirmation_code=' . $confirmation_code . '">Account_Activation_Link</a>';
	/* Added by zCon */
	
	/*
    $confirmation_link = '<a href="http://signboom.com/activate-account.php?account_name=' . $account_name . '&confirmation_code=' . $confirmation_code . '">http://signboom.com/activate-account.php?account_name=' . $account_name . '&confirmation_code=' . $confirmation_code . '</a>';
	*/
	
	echo "</br>" . $confirmation_link . "</br>";

   // Insert new account into database, but disable it. 
   $myQuery ="INSERT INTO signboom_user ( firstName, lastName, userName, AcctName, company, url, address, "; 
   $myQuery.="provstate, city, phone1, phone2, country,";
   $myQuery.="postalzip, hintq, hinta, userPass, email, userLevel, confirmationCode, acctDisable, defcourier, courieracct, pstnum, nextContact ) VALUES ( ";
   $myQuery.="'$firstName', '$lastName', '$userName', '$account_name', '$company', '$url', '$address', '$selectProvState', '$city', '$phone1', ";
   $myQuery.="'$phone2', '$selectCountry', '$postalzip', ";
   $myQuery.="'$question', LOWER('$hinta'), '$encpass', '$email', '2', '$confirmation_code', '1', '$defcourier', '$courieracct', '$PST', '$nextContact' )";
   
   $result=mysqli_query($GLOBALS["___mysqli_ston"], $myQuery); //, $DBConn
   if(!$result){
      $errMsg="DB Error: " . mysqli_error($GLOBALS["___mysqli_ston"]);
	  //echo $errMsg; die;
      $needcookie=0;  
      return;
   }else{
      $errMsg="An account has been created for you.  To activate it, you must log in to the email address you just provided, and click the link in that email. Once you've done that, you can start to place orders.";
      $needcookie=1;
   }
   
   //echo "New Account Inserted into DB done"; die;

//////////////////////////
//MAIL USER
//////////////////////////
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: Signboom <info@signboom.com>' . "\r\n";

    $msg ="<html><head></head><body>Dear $firstName $lastName<br><br>";
    $msg.=$global_welcome_email_msg . $confirmation_link;
    $msg.="<br><br>Account ID: " . $email;
    $msg.="<br><br>Account Password: " . $epassword1;
    $msg.=$global_welcome_email_footer . "</body></html>";
    mail($email, $global_welcome_email_subject, $msg, $headers);

    $msg2 ="<html><head></head><body>Dear $firstName $lastName<br><br>";
    $msg2.=$global_welcome_email_msg . "<br><br>Account ID: " . $email;
    $msg2.="<br><br>Account Password: XXXXXX";
    $msg2.=$global_welcome_email_footer . "</body></html>";
    mail("alison_j_taylor@hotmail.com", $global_welcome_email_subject, $msg2, $headers);

    $msg ="Customer $email has added a customer record.\n\n";
    $msg.=bldmsg($account_name, $account_name, "Account Name");
    $msg.=bldmsg($firstName, $firstName, "First Name");
    $msg.=bldmsg($lastName, $lastName, "Last Name");
    $msg.=bldmsg($email, $email, "Email Address");
    $msg.=bldmsg($company, $company, "Company");
    $msg.=bldmsg($address, $address, "Address");
    $msg.=bldmsg($selectProvState, $selectProvState, "Province/State");
    $msg.=bldmsg($city, $city, "City");
    $msg.=bldmsg($postalzip, $postalzip, "Postal Code");
    $msg.=bldmsg($phone1, $phone1, "Phone");
    $msg.=bldmsg($phone2, $phone2, "Fax");
    $msg.=bldmsg($url, $url, "URL");
    $msg.=bldmsg($PST, $PST, "PST");
    //$msg.=bldmsg($defcourier, $defcourier, "Courier");
    //$msg.=bldmsg($courieracct, $courieracct, "Account#");
    $msg.=bldmsg($question, $question, "Security Question");
    $msg.=bldmsg($hinta, $hinta, "Security Question Answer", true);
    $msg.="\n\n".date("D M j G:i:s T Y");

    mail( "newcustomer@signboom.com", "New Customer Setup", $msg, "From: " . $global_sender . "@{$_SERVER['SERVER_NAME']}\r\n");
    mail( "leonard@signboom.com", "New Customer Setup", $msg, "From: " . $global_sender . "@{$_SERVER['SERVER_NAME']}\r\n");

//
// CLOSE POPUP
//
    $exitPopup=1;
}

function bldmsg($oldval, $newval, $heading, $prthide = false){
    $ind = " ";
    if ($oldval != $newval) $ind = "*";
    if ($prthide) $newval = "*****";
    $tab = "\t";
    if (strlen($heading) < 9) $tab .= $tab;
        return("\t".$ind.$heading.": ".$tab.$newval."\n");
}


function valcity($city, $st)
{
    include('Connections/DBConn.php');
    global $oldcity, $oldst;
	
    if (($city == $oldcity) && ($st == $oldst)) return true;

    $oldcity = $city;
    $oldst = $st;
	
    if (GetCountry($st) == "United States") return true;

    $qry = sprintf("SELECT * FROM signboom_rates WHERE City='%s' and Province='%s'", $city, $st); 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $rs = mysqli_query( $DBConn, $qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $rscount = mysqli_num_rows($rs);
    ((mysqli_free_result($rs) || (is_object($rs) && (get_class($rs) == "mysqli_result"))) ? true : false);
    if ($rscount == 0) return false;
    return true;
}


function loadcities($st)
{
    include('Connections/DBConn.php');
    global $Cities;
    $Qry = sprintf("SELECT * FROM signboom_rates WHERE Province='%s' ORDER BY City", $st); 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $i = 0;
    while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH))
	{
		$Cities[$i] = $row['City'];
		$i++;
    }
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
}


function GetCountry($st)
{
    global $arrProv;
    reset($arrProv);
	while (list($id, $val) = each($arrProv) )
	{
		if ($id == $st)
		{
			return("Canada");
		}
    }
    return "United States";
}


////////////////////////////////////
// END FUNCTIONS
////////////////////////////////////


////////////////////////////////////
// START HERE: INPUT VALIDATION
////////////////////////////////////

//if(isset($btnSave)){
if(isset($_POST['btnSave']))
{
	/*	
	if(trim($HTTP_POST_VARS['firstName'])==""){
      $errMsg="Please enter your first name.";
    }
	*/
	if(trim($_POST['firstName'])=="")
	{
		$errMsg="Please enter your first name.";
	}
	
	/*
    elseif(trim($HTTP_POST_VARS['lastName'])==""){
      $errMsg="Please enter your last name.";
    }
	*/
	elseif(trim($_POST['lastName'])=="")
	{
		$errMsg="Please enter your last name.";
    }
	
	/*
    elseif($result=checkEmail($HTTP_POST_VARS['email'])){
      switch($result){
        case 1:
          $errMsg="Please enter your email address.";
          break;
        case 2:
          $errMsg="That email address is not valid. Please enter a valid email address.";
          break;
      }
    }
	*/
	elseif($result=checkEmail($_POST['email']))
	{
      switch($result)
		{
			case 1:
				$errMsg="Please enter your email address.";
			break;
			
			case 2:
				$errMsg="That email address is not valid. Please enter a valid email address.";
			break;
		}
    }
	
	/*
    elseif (($HTTP_POST_VARS['email'] == "christopherjohn2050@gmail.com") || 
            ($HTTP_POST_VARS['email'] == "harmeetpunjabi@gmail.com")) {
      // A hardcoded hack to prevent nuisance repeat signups.  
      $errMsg="Please call us to create an account.";
    }
	*/
	elseif (($_POST['email'] == "christopherjohn2050@gmail.com") || ($_POST['email'] == "harmeetpunjabi@gmail.com"))
	{
      // A hardcoded hack to prevent nuisance repeat signups.  
		$errMsg="Please call us to create an account.";
    }
	
	/*
    elseif(trim($HTTP_POST_VARS['company'])==""){
      $errMsg="Please enter your company name.";
    }
	*/
	elseif(trim($_POST['company'])=="")
	{
		$errMsg="Please enter your company name.";
    }	
	
	/*
    elseif(trim($HTTP_POST_VARS['address'])==""){
      $errMsg="Please enter your street address.";
    }
	*/
	elseif(trim($_POST['address'])=="")
	{
		$errMsg="Please enter your street address.";
	}
	
	/*
    elseif(trim($HTTP_POST_VARS['city'])==""){
      $errMsg="Please enter your city.";
    }
	*/
	elseif(trim($_POST['city'])=="")
	{
		$errMsg="Please enter your city.";
    }
	
	/*
    elseif(trim($HTTP_POST_VARS['postalzip'])==""){
      $errMsg="Please enter your zip code.";
    }
	*/
    elseif(trim($_POST['postalzip'])=="")
	{
		$errMsg="Please enter your zip code.";
    }
	
	/*
    elseif(trim($HTTP_POST_VARS['phone1'])==""){
      $errMsg="Please enter your phone number.";
    }
	*/
	elseif(trim($_POST['phone1'])=="")
	{
		$errMsg="Please enter your phone number.";
    }
	
	/*
    elseif (trim($HTTP_POST_VARS['selectHintQ'])=="0") {
      $errMsg="Please select a security question.";
    }
	*/
	elseif(trim($_POST['selectHintQ'])=="0")
	{
		$errMsg="Please select a security question.";
    }
	
	/*
    elseif(trim($HTTP_POST_VARS['hinta'])==""){
      $errMsg="Please enter the answer to the security question you selected.";
    }
	*/
	elseif(trim($_POST['hinta'])=="")
	{
		$errMsg="Please enter the answer to the security question you selected.";
    }
	
	/*
    elseif ((strlen(trim($HTTP_POST_VARS['PST'])) > 0) && (!preg_match("/^[0-9]{4}-[0-9]{4}$/", trim($HTTP_POST_VARS['PST'])))) {
       $errMsg="That is not a valid British Columbia PST number. The new BC PST numbers are now in the format 0000-0000.  If your business is located outside of BC, or if you do not have a PST number, please leave the PST number blank.";
    }
	*/
	elseif ((strlen(trim($_POST['PST'])) > 0) && (!preg_match("/^[0-9]{4}-[0-9]{4}$/", trim($_POST['PST']))))
	{
		$errMsg="That is not a valid British Columbia PST number. The new BC PST numbers are now in the format 0000-0000.  If your business is located outside of BC, or if you do not have a PST number, please leave the PST number blank.";
    }
	
    else
	{
      //CANADA check.
      /*
	  if(array_key_exists($HTTP_POST_VARS['selectProvState'], $arrProv)){
        if($selectCountry != "CA"){
          $errMsg="Please correct the country selected. " . $arrProv[$HTTP_POST_VARS['selectProvState']] . " is in Canada !";
        }
      }
	  */
		if(array_key_exists($_POST['selectProvState'], $arrProv))
		{
			//if($selectCountry != "CA")
			if($_POST['selectCountry'] != "CA")
			{
				$errMsg="Please correct the country selected. " . $arrProv[$_POST['selectProvState']] . " is in Canada !";
			}
		}
	  
      //USA check.
      /*
	  if(array_key_exists($HTTP_POST_VARS['selectProvState'], $arrState)){
        if($selectCountry != "US"){
          $errMsg="Please correct the country selected. " . $arrState[$HTTP_POST_VARS['selectProvState']] . " is in the United States !";
        }
      }
	  */
		if(array_key_exists($_POST['selectProvState'], $arrState))
		{
			//if($selectCountry != "US")
			if($_POST['selectCountry'] != "US")
			{
				$errMsg="Please correct the country selected. " . $arrState[$_POST['selectProvState']] . " is in the United States !";
			}
		}
	  
      //Password check
	  /*
      if (trim($HTTP_POST_VARS['epassword1'], " ") == "") {
         $errMsg="The password cannot be blank. Please enter a password.";
      } elseif ($HTTP_POST_VARS['epassword1'] != $HTTP_POST_VARS['epassword2']) {
         $errMsg="The passwords do not match. Please re-enter them.";
      }
        if((trim($HTTP_POST_VARS['selectProvState'])=="0") || (trim($HTTP_POST_VARS['selectProvState'])=="1")){
          $errMsg="Please enter a valid province or state.";
        } elseif (!(valcity($HTTP_POST_VARS['city'], $HTTP_POST_VARS['selectProvState']))) {
        loadcities($HTTP_POST_VARS['selectProvState']);
        $cityerr = true;
      }
	  */
		if (trim($_POST['epassword1'], " ") == "")
		{
			$errMsg="The password cannot be blank. Please enter a password.";
		}
		elseif ($_POST['epassword1'] != $_POST['epassword2'])
		{
			$errMsg="The passwords do not match. Please re-enter them.";
		}
		
		if((trim($_POST['selectProvState'])=="0") || (trim($_POST['selectProvState'])=="1"))
		{
			$errMsg="Please enter a valid province or state.";
		}
		elseif (!(valcity($_POST['city'], $_POST['selectProvState'])))
		{
			loadcities($_POST['selectProvState']);
			$cityerr = true;
		}
    }

    if(($errMsg=="") && ($cityerr == false))
	{
		saveUser();
    }
}  // end of if BtnSave
 

if($debug)
{
	print "<pre>";
	var_dump($arrProv);
	print "</pre>";
}

////////////////////////////////////
// PRESENTATION
////////////////////////////////////
  include("includes/inc-signup.php");

////////////////////////////////////
// ERROR POPUP
////////////////////////////////////
  //error popup
	if(!empty($errMsg))
	{
		echo "<script language=\"javascript\">alert(\"" . $errMsg . "\");</script>";
	}

////////////////////////////////////
// CLOSE POPUP
////////////////////////////////////
	if($exitPopup==1)
	{
		print "<script language=\"javascript\">window.close();</script>";
	}
////////////////////////////////////
// NEED COOKIE
////////////////////////////////////
  //error popup
if($needcookie == 1)
{
	echo "<script src=\"script/cookieLibrary.js\" LANGUAGE=\"JavaScript\" TYPE=\"text/javascript\"></script>\n";
	echo "<script language=\"javascript\">\n";
	echo "var expdate = new Date ();\n";
	echo "FixCookieDate (expdate);\n"; // Correct for Mac date bug - call only once for given Date object!
	echo "expdate.setTime (expdate.getTime() + (60 * 24 * 60 * 60 * 1000));\n"; // 60 days from now 
	echo "SetCookie (\"userid\", \"".$email."\", expdate);\n";
	echo "</script>\n";

	echo "<META HTTP-EQUIV=\"refresh\" content=\"0; URL=login.php\"> ";
}

