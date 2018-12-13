<?php 

  require_once('Connections/DBConn.php');
  include('Connections/needLogin.php'); 
  setcookie("userid", $_SESSION['MM_Username'], time() + (3600 * 24 * 60)); 
  require_once( "includes/utils.php" );
  require_once( "includes/inc-signboom.php" );
  require_once('helpers/db_helper.php');

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <title>Signboom: Your Online Print Engine</title>
  <script src="script/utility.js" LANGUAGE="JavaScript" TYPE="text/javascript"></script>

  <!--INTERNET EXPLORER SPECIFIC STYLING CODE FOLLOWS-->
  <?php
    include ('browser_detection.php');
    $my_browser = browser_detection('browser');
    if ($my_browser == 'msie6') 
    {
      echo '<link rel="stylesheet" href="ie6_specific.css" type="text/css" title="default_style">';
    }
    else
    {
      echo '<link rel="stylesheet" href="signboom.css" type="text/css" title="default_style">';
    }
  ?>

  <style>
  #citydiv {
    position:absolute; 
    visibility: hidden; 
    top: 140px;
    left: 616px; 
    min-height:261px; 
    width:253px; 
    z-index:1; 
    background-color: #FFFFFF;
    padding: 10px 10px 10px 10px; 
    border: 1px solid #CC0000; 
  }
  </style>
</head>

<?php

  $MM_redirectLoginFailed = "login.php";
 
  if (isset($_SESSION['MM_Username']) && false) {  
    header("Location: ". $MM_redirectLoginFailed );
  }
  
  $loginUsername = $_SESSION['MM_Username'];
  
  $shipID = (isset($_POST['shipID'])) ? ($_POST['shipID']) : "";
  $shipName =  (isset($_POST['shipName'])) ? ($_POST['shipName']) : "";
  $shipAddress = (isset($_POST['shipAddress'])) ? ($_POST['shipAddress']) : "";
  $shipCity = (isset($_POST['shipCity'])) ? ($_POST['shipCity']) : "";
  $shipState = (isset($_POST['shipState'])) ? ($_POST['shipState']) : "";
  $shipZip = (isset($_POST['shipZip'])) ? ($_POST['shipZip']) : "";
  $acctID = (isset($_POST['acctid'])) ? ($_POST['acctid']) : "";
  $AcctName = (isset($_POST['acctname'])) ? ($_POST['acctname']) : "";
  $oldcity = (isset($_POST['oldcity'])) ? ($_POST['oldcity']) : "";
  $oldst = (isset($_POST['oldst'])) ? ($_POST['oldst']) : "";
  $lastshipcity = (isset($_POST['lastshipcity'])) ? ($_POST['lastshipcity']) : "";
  $lastshipst = (isset($_POST['lastshipst'])) ? ($_POST['lastshipst']) : "";
  $Cities[0] = "";
  $cityerr = false;
  $shipcityerr = false;
    

  if (isset($_POST['Submit'])) {
	//LoadInitalCustomerData();
    UpdateCustomer($loginUsername);
    $loginUsername = $_SESSION['MM_Username'];
  } else if (isset($_POST['UpdateShipping'])) {
	//LoadInitalCustomerData();
    UpdateShipping() ;
    $loginUsername = $_SESSION['MM_Username'];
  } else if (isset($_POST['DeleteShipping'])) {
	//LoadInitalCustomerData();
    DeleteShipping() ;
    $loginUsername = $_SESSION['MM_Username'];
  }
//	else {
	//LoadInitalCustomerData();

    $RS__query=sprintf("SELECT * FROM signboom_user WHERE email='%s'",  get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername)); 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
     $RS = mysqli_query( $DBConn, $RS__query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $FoundUser = !mysqli_num_rows($RS);
      if ($FoundUser) {
      header("Location: ".$MM_redirectLoginFailed );
      exit();
    }
    $userLevel = mysqli_result($RS, 0, 'userLevel');
    $acctID=mysqli_result($RS, 0, 'ID');
    $firstName=mysqli_result($RS, 0, 'firstName');
    $lastName=mysqli_result($RS, 0, 'lastName');
    $email=mysqli_result($RS, 0, 'email');
    $company=mysqli_result($RS, 0, 'company');
    $AcctName=mysqli_result($RS, 0, 'AcctName');
    $country=mysqli_result($RS, 0, 'country');
    $url=mysqli_result($RS, 0, 'url');
    $address=mysqli_result($RS, 0, 'address');
    $provstate=mysqli_result($RS, 0, 'provstate');
    $city=mysqli_result($RS, 0, 'city');
    $phone1=mysqli_result($RS, 0, 'phone1');
    $phone2=mysqli_result($RS, 0, 'phone2');
    $htmlmail=mysqli_result($RS, 0, 'htmlmail');
    $defcourier=mysqli_result($RS, 0, 'defcourier');
    $courieracct=mysqli_result($RS, 0, 'courieracct');
    $postalzip=mysqli_result($RS, 0, 'postalzip');
    $hintq=mysqli_result($RS, 0, 'hintq');
    $hinta=mysqli_result($RS, 0, 'hinta');
    $dct=mysqli_result($RS, 0, 'dct');
    $pstnum=mysqli_result($RS, 0, 'pstnum');
    $userName=mysqli_result($RS, 0, 'userName');
    $acctDisable=mysqli_result($RS, 0, 'acctDisable');
    $htmlmail=mysqli_result($RS, 0, 'htmlmail');
    
    $shipID = 0;
    $shipName = "";
    $shipAddress = "";
    $shipCity = "";
    $shipState = "";
    $shipZip = "";
    $shipCountry = "";

    //This will load up all of the different shipping addresses available to this user.
    //$RS__query=sprintf("SELECT ID, CONCAT(name,' - ',city,', ',state) AS disp FROM signboom_shipto WHERE acctname='%s'", $AcctName) ; 

    //mysql_select_db($database_DBConn, $DBConn);
    //$shipto = mysql_query($RS__query, $DBConn) or die(mysql_error());
    // Load Customer Address List
//  }

  $Qry = sprintf("SELECT * FROM signboom_shipto WHERE acctid=".$acctID); 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"])); 
  $i = 0;
  if(!isset($shipto))
  {
    $shipto[$i] = new stdClass();
  }
  //print_r($shipto); die;
  
  $shipto[$i]->ID = 0;
  $shipto[$i]->name = "New Address";
  $shipto[$i]->address = "";
  $shipto[$i]->city = "";
  $shipto[$i]->state = "";
  $shipto[$i]->zip = "";
  $shipto[$i]->country = "";
   while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) {
    $i++;
    if(!isset($shipto[$i]))
    {
      $shipto[$i] = new stdClass();
      $shipto[$i]->ID = "";
      $shipto[$i]->name = "";
      $shipto[$i]->address = "";
      $shipto[$i]->city = "";
      $shipto[$i]->state = "";
      $shipto[$i]->zip = "";
      $shipto[$i]->country = "";
    }
    $shipto[$i]->ID = $row['ID'];
    $shipto[$i]->name = $row['name'];
    $shipto[$i]->address = $row['address'];
    $shipto[$i]->city = $row['city'];
    $shipto[$i]->state = $row['state'];
    $shipto[$i]->zip = $row['postalzip'];
    $shipto[$i]->country = $row['country'];
  } 
   ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false); 

  /*
  require_once( "includes/inc-mysql.php" );
  require_once( "includes/dohash.php" );
  require_once( "includes/inc-signboom.php" );
  */

  //
  // DEBUG FLAGS
  //
  $debug=0;
  $dbWrite=1; //0 = skip db writes
  $show_user_array=0;

  //print "<pre>";
  //var_dump($HTTP_POST_VARS);
  //print "</pre>";


//////////////////////////////////////////////////
//  BEGIN FUNCTIONS
//////////////////////////////////////////////////


//////////////////////////////////////////////////
// UpdateCustomer()
//////////////////////////////////////////////////
function UpdateCustomer($UserEmail)
{
/*
    global $arrUsers;

    global $selectUser;
    global $selectLevel;
    global $firstName, $lastName, $email;
    $email = trim($email);
    global $userName, $btnDisable, $userPass, $confPass;
    global $userLevel;
    global $oldcity, $oldst, $cityerr;

    global $statusMsg;
    global $editmode;
    global $dbName;

    global $errMsg;
    global $debug;
    global $dbWrite;

    global $company, $address, $url, $dct, $pstnum;
    global $provstate, $city, $phone1, $phone2;
    global $country, $postalzip, $hintq, $hinta;
    global $htmlmail;
    global $defcourier, $courieracct;
    global $selectHintQ;
    global $arrHintQ, $arrHintQ2;

    global $database_DBConn;
*/
  //  echo '<pre>'; print_r($_POST);
  $arrUsers = isset($_POST['arrUsers'])?$_POST['arrUsers']:"";
  $selectUser = isset($_POST['selectUser'])?$_POST['selectUser']:"";
  $selectLevel = isset($_POST['selectLevel'])?$_POST['selectLevel']:"";
  $firstName = isset($_POST['firstName'])?$_POST['firstName']:"";
  $lastName = isset($_POST['lastName'])?$_POST['lastName']:"";
  $email = isset($_POST['email'])?$_POST['email']:"";
  $userName = isset($_POST['userName'])?$_POST['userName']:"";
  $btnDisable = isset($_POST['btnDisable'])?$_POST['btnDisable']:"";
  $userPass = isset($_POST['userPass'])?$_POST['userPass']:"";
  $confPass = isset($_POST['confPass'])?$_POST['confPass']:"";
  $userLevel = isset($_POST['userLevel'])?$_POST['userLevel']:"";
  //$oldcity = $_POST['oldcity'];
  //$oldst = $_POST['oldst'];
  $selectProvState = isset($_POST['selectProvState'])?$_POST['selectProvState']:"";
  $city = isset($_POST['city'])?$_POST['city']:"";
  $cityerr = isset($_POST['cityerr'])?$_POST['cityerr']:"";
  $statusMsg = isset($_POST['statusMsg'])?$_POST['statusMsg']:"";
  $editmode = isset($_POST['editmode'])?$_POST['editmode']:"";
  $dbName = isset($_POST['dbName'])?$_POST['dbName']:"";
  $errMsg = isset($_POST['errMsg'])?$_POST['errMsg']:"";
  $debug = isset($_POST['debug'])?$_POST['debug']:"";
  $dbWrite = isset($_POST['dbWrite'])?$_POST['dbWrite']:"";
  $company = isset($_POST['company'])?$_POST['company']:"";
  $address = isset($_POST['address'])?$_POST['address']:"";
  $url = isset($_POST['url'])?$_POST['url']:"";
  $dct = isset($_POST['dct'])?$_POST['dct']:"";
  $pstnum = isset($_POST['pstnum'])?$_POST['pstnum']:"";
  $provstate = isset($_POST['provstate'])?$_POST['provstate']:"";
  $city = isset($_POST['city'])?$_POST['city']:"";
  $phone1 = isset($_POST['phone1'])?$_POST['phone1']:"";
  $phone2 = isset($_POST['phone2'])?$_POST['phone2']:"";
  $country = isset($_POST['country'])?$_POST['country']:"";
  $postalzip = isset($_POST['postalzip'])?$_POST['postalzip']:"";
  $hintq = isset($_POST['hintq'])?$_POST['hintq']:"";
  $hinta = isset($_POST['hinta'])?$_POST['hinta']:"";
  $htmlmail = isset($_POST['htmlmail'])?$_POST['htmlmail']:"";
  $defcourier = isset($_POST['defcourier'])?$_POST['defcourier']:"";
  $courieracct = isset($_POST['courieracct'])?$_POST['courieracct']:"";
  $selectHintQ = isset($_POST['selectHintQ'])?$_POST['selectHintQ']:"";
  $arrHintQ = isset($_POST['arrHintQ'])?$_POST['arrHintQ']:"";
  $arrHintQ2 = isset($_POST['arrHintQ2'])?$_POST['arrHintQ2']:"";
  $hint_question = isset($_POST['hint_question'])?$_POST['hint_question']:"";

    //EMAIL GLOBALS
    global $global_sender;
    global $global_welcome_email_subject;
    global $global_welcome_email_footer;
    global $global_welcome_email_msg;

    //////////////////////////////
    //VALIDATION
    //////////////////////////////

    if ($firstName=="")
  {
      $errMsg="Please enter a First Name.";
      return;
    }

    if ($lastName==""){
      $errMsg="Please enter a Last Name.";
      return;
    }

    /*if(ereg('[^_A-Za-z0-9-]', $userName)){
     $errMsg="User Name must contain only letters and numbers.";
     return;
    }*/

    //Process password if there is any information entered
    if (($userPass != "") OR ($confPass != "")) {
      if ($userPass == "") {
        $errMsg = "Please enter a password.";
        return;
      }
      if (strlen($userPass) < 5) {
        $errMsg = "Password must be at least 5 digits in length.";
        return;
      }
      if ($userPass != $confPass) {
        $errMsg = "Passwords need to be the same.  Please re-enter password.";
        return;
      }
    }

    // Don't allow blank email.
    if (!strlen($email)) {
      $errMsg="Please enter an email address.";
      return;
    }

    // Make sure email address is valid.  This doesn't catch blank email, so still need check above.
    if ($result=checkEmail($email)) {
      switch($result){
        case 2:
          $errMsg="Please enter a valid email address.";
          return;
      }
    }

    if ((strlen(trim($pstnum)) > 0) && (!preg_match("/^[0-9]{4}-[0-9]{4}$/", trim($pstnum)))) {
       $errMsg="That is not a valid British Columbia PST number. The new BC PST numbers are now in the format 0000-0000.  If your business is located outside of BC, or if you do not have a PST number, please leave the PST number blank.";
       return;
    }

    if (($selectHintQ == 0) && ( $hint_question == 0)) {
       $errMsg="You must choose a security question to answer.";
       return;
    }

    if (trim($hinta) == "") {
       $errMsg="You must specify an answer to the security question.";
       return;
    }

    if (($city != $oldcity) || ($provstate != $oldst)) {
      if (!(valcity($city, $provstate))) {
        loadcities($provstate);    
        $cityerr = true;
        return;
      }
    }

    
    //////////////////////////////
    //  END VALIDATION
    //////////////////////////////

    $userPass=trim($userPass);
    if(!empty($userPass)){
    $encpass = crypt($userPass, "urban11oasis22media33");
    }

    // Retrieve old data  
    include ('Connections/DBConn.php');
    $RS__query=sprintf("SELECT * FROM signboom_user WHERE email='%s'",  get_magic_quotes_gpc() ? $UserEmail : addslashes($UserEmail)); 
    mysql_select_db($database_DBConn, $DBConn);
    $RS = mysql_query($RS__query, $DBConn) or die(mysql_error());

    $oAcctName =mysql_result($RS,0,'AcctName');
    $ofirstName=mysql_result($RS,0,'firstName');
    $olastName=mysql_result($RS,0,'lastName');
    $oemail=mysql_result($RS,0,'email');
    $ocompany=mysql_result($RS,0,'company');
    $ocountry=mysql_result($RS,0,'country');
    $ourl=mysql_result($RS,0,'url');
    $oaddress=mysql_result($RS,0,'address');
    $oprovstate=mysql_result($RS,0,'provstate');
    $ocity=mysql_result($RS,0,'city');
    $ophone1=mysql_result($RS,0,'phone1');
    $ophone2=mysql_result($RS,0,'phone2');
    $ohtmlmail=mysql_result($RS,0,'htmlmail');
    $odefcourier=mysql_result($RS,0,'defcourier');
    $ocourieracct=mysql_result($RS,0,'courieracct');
    $opostalzip=mysql_result($RS,0,'postalzip');
    $ohintq=mysql_result($RS,0,'hintq');
    $ohinta=mysql_result($RS,0,'hinta');
    $ouserPass=mysql_result($RS,0,'userPass');
    $opstnum=mysql_result($RS,0,'pstnum');

    $ouserName=mysql_result($RS,0,'userName');
    $oacctDisable=mysql_result($RS,0,'acctDisable');

    // ************** UPDATE DATABASE HERE **************
    $myQuery  = "UPDATE signboom_user SET ";
    $myQuery .= "firstName='" . $firstName . "', ";
    $myQuery .= "lastName='" . $lastName . "', ";
    $myQuery .= "email='" . trim($email) . "', ";
    $myQuery .= "company='" . $company . "', ";
    $myQuery .= "address='" . $address . "', ";
    $myQuery .= "city='" . $city . "', ";
    $myQuery .= "provstate='" . $provstate . "', ";
    $myQuery .= "url='" . $url . "', ";
    //$myQuery .= "country='" . $country . "', ";
    $myQuery .= "postalzip='" . $postalzip . "', ";
    $myQuery .= "phone1='" . $phone1 . "', ";
    $myQuery .= "phone2='" . $phone2 . "', ";
    $myQuery .= "htmlmail='" . $htmlmail . "', ";
    $myQuery .= "defcourier='" . $defcourier . "', ";
    $myQuery .= "courieracct='" . $courieracct . "', ";
    if (trim($arrHintQ2[$selectHintQ]) != "") {
      $myQuery .= "hintq='" . $arrHintQ2[$selectHintQ] . "', ";
    }
    else {
      $myQuery .= "hintq='" . $hintq . "', ";
    }
    $myQuery .= "hinta= '" . strtolower($hinta) . "', ";
    if(!empty($encpass)){ $myQuery .= "userPass='" . $encpass . "', "; }
    $myQuery .= "url='" . $url . "', ";
    $myQuery .= "pstnum='" . $pstnum . "' ";
    $myQuery .= sprintf("WHERE email='%s'",  get_magic_quotes_gpc() ? $UserEmail : addslashes($UserEmail)); 
    mysql_select_db($database_DBConn, $DBConn);
  // echo $myQuery; die;
    $result = mysql_query( $myQuery )  or die(mysql_error());

    $statusMsg="<font color=\"#00AAEA\">+ <b>Create Record</b></font>&nbsp;&nbsp;";
    if (!($UserEmail == $email)) {
      $GLOBALS['MM_Username'] = $email;
      session_register("MM_Username");
    }
    $editmode = 0;
    if($debug){
      print "Existing: $myQuery<BR>";
    }

    // If account has been updated, send out email message to Signboom.
    if (mysql_affected_rows() > 0 ) {
    
      $RS__query=sprintf("SELECT * FROM signboom_user WHERE email='%s'",  get_magic_quotes_gpc() ? $email : addslashes($email)); 
      mysql_select_db($database_DBConn, $DBConn);
      $RS = mysql_query($RS__query, $DBConn) or die(mysql_error());

      $errMsg= "Your Account has been Updated";
      $msg ="Customer $email has updated their customer record.\n\n";
      $msg.=bldmsg($oAcctName, mysql_result($RS,0,'AcctName'), "Account Name");
      $msg.=bldmsg($ofirstName, mysql_result($RS,0,'firstName'), "First Name");
      $msg.=bldmsg($olastName, mysql_result($RS,0,'lastName'), "Last Name");
      $msg.=bldmsg($oemail, mysql_result($RS,0,'email'), "email Addr");
      $msg.=bldmsg($ocompany, mysql_result($RS,0,'company'), "Company");
      $msg.=bldmsg($oaddress, mysql_result($RS,0,'address'), "Address");
      $msg.=bldmsg($oprovstate, mysql_result($RS,0,'provstate'), "Province/State");
      $msg.=bldmsg($ocity, mysql_result($RS,0,'city'), "City");
      $msg.=bldmsg($opostalzip, mysql_result($RS,0,'postalzip'), "Postal Code");
      $msg.=bldmsg($ophone1, mysql_result($RS,0,'phone1'), "Phone");
      $msg.=bldmsg($ophone2, mysql_result($RS,0,'phone2'), "Fax");
      $msg.=bldmsg($ourl, mysql_result($RS,0,'url'), "URL");
      $msg.=bldmsg($opstnum, mysql_result($RS,0,'pstnum'), "PST Num");
      $msg.=bldmsg($ohtmlmail, mysql_result($RS,0,'htmlmail'), "HTML Email");    
      $msg.=bldmsg($ohintq, mysql_result($RS,0,'hintq'), "Hint Question");
      $msg.=bldmsg($ohinta, mysql_result($RS,0,'hinta'), "Hint Answer", true);
      $msg.=bldmsg($ouserPass, mysql_result($RS,0,'userPass'), "Password", true);

      $msg.="\n\nAll changes are indicated with an '*'";
      $msg.="\n".date("D M j G:i:s T Y");
      mail( "orders@signboom.com",        "Customer Maintenance Notification", $msg, "From: " . $global_sender . "@{$_SERVER['SERVER_NAME']}\r\n");
      mail( "leonard@signboom.com",       "Customer Maintenance Notification", $msg, "From: " . $global_sender . "@{$_SERVER['SERVER_NAME']}\r\n");
      //mail( "alison@usablewebdesigns.com", "Customer Maintenance Notification", $msg, "From: " . $global_sender . "@{$_SERVER['SERVER_NAME']}\r\n");
    } else {
      $errMsg = "No changes were entered.";
    }

    return TRUE;
}//end function UpdateCustomer()


  function bldmsg($oldval, $newval, $heading, $prthide = false){
    $ind = " ";
    if ($oldval != $newval) $ind = "*";
    if ($prthide) $newval = "*****";
    $tab = "\t";
    if (strlen($heading) < 9) $tab .= $tab;
    return("\t".$ind.$heading.": ".$tab.$newval."\n");
  }

/*
  //error popup
  if(!empty($errMsg)){
    echo "<script language=\"javascript\">alert(\"" . $errMsg . "\");</script>";
  }
*/

  function GetDiscount($id) {
    global $database_DBConn, $DBConn;
    $qry = "SELECT * FROM signboom_discount WHERE Enabled = 1 AND ID = '".$id."'";
    $rs = mysqli_query( $DBConn, $qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    if (mysqli_num_rows($rs) == 0) return "Loyalty discount is not active on this account.  Please contact us to discuss.";
    return mysqli_result($rs, 0, 'Desc');
  } 
  
  function valcity($city, $st) {
    global $database_DBConn, $DBConn;
    if (GetCountry($st) == "United States") return true;

    $qry = sprintf("SELECT * FROM signboom_rates WHERE City='%s' and Province='%s'", $city, $st); 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $rs = mysqli_query( $DBConn, $qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $rscount = mysqli_num_rows($rs);
    ((mysqli_free_result($rs) || (is_object($rs) && (get_class($rs) == "mysqli_result"))) ? true : false);
    if ($rscount == 0) return false;
    return true;
  } 

  function valshipaddr() {
    global $database_DBConn, $DBConn;
    global $shipName, $shipAddress, $shipCity, $shipState, $shipZip;
    global $errMsg;
  
    if(!strlen($shipName)){
      $errMsg="Please enter a name in your shipping address.";
    return false;
    }
    if(!strlen($shipAddress)){
      $errMsg="Please enter a valid address in your shipping address.";
    return false;
    }
    if(!strlen($shipCity)){
      $errMsg="Please enter a city in your shipping address.";
    return false;
    }
    if(($shipState == "0") || ($shipstate == "1")){
      $errMsg="Please enter a valid state or province in your shipping address.";
    return false;
    }
    if(!strlen($shipZip)){
      $errMsg="Please enter a zip code in your shipping address.";
    return false;
    }

    return true;
  }

  function loadcities($st) {
    global $Cities;
    global $database_DBConn, $DBConn;
    $Qry = sprintf("SELECT * FROM signboom_rates WHERE Province='%s' ORDER BY City", $st); 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $i = 0;
    while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) {
      $Cities[$i] = $row['City'];
      $i++;
    }
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
  } 
  
  //Adds a new shipping address with the company ID passed in $id.
  function AddShipping() {
    
    global $acctID, $AcctName, $errMsg, $shipID;
    if (!(valshipaddr())) return;
    include ('Connections/DBConn.php');
    mysqli_select_db( $DBConn, $database_DBConn);
  
    $updQ  = "INSERT INTO signboom_shipto ( ";
    $updQ .= "acctid, acctname, name, address, city, state, country, postalzip";
    $updQ .= ") VALUES (";    
    $updQ .= "'".$acctID."', ";
    $updQ .= "'".$AcctName."', ";
    $updQ .= "'".$_POST['shipName']."', ";
    $updQ .= "'".$_POST['shipAddress']."', ";
    $updQ .= "'".$_POST['shipCity']."', ";
    $updQ .= "'".$_POST['shipState']."', ";
    $updQ .= "'".GetCountry($_POST['shipState'])."', ";
    $updQ .= "'".strtoupper($_POST['shipZip'])."' ";
    $updQ .= ")";
    mysqli_select_db( $DBConn, $database_DBConn);
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $updQ)  or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $shipID = ((is_null($___mysqli_res = mysqli_insert_id($DBConn))) ? false : $___mysqli_res);
    
    $errMsg="<font color=\"#00AAEA\"><b>Create Shipping Address Completed</b></font>&nbsp;&nbsp;";
  } 

  function GetCountry($st) {
    global $arrProv;
    reset($arrProv);
    while (list($id, $val) = each($arrProv) ){
      if ($id == $st){ 
        return("Canada"); 
      }
    }
    return "United States";
  }
  
  //Updates the shipping address currently listed in the box.
  function UpdateShipping() {
    global $cityerr, $shipcityerr;
    global $lastshipcity, $lastshipst;

    if (!(valshipaddr())) return;
    if (($_POST['shipCity'] == $_POST['lastshipcity']) || ($_POST['shipState'] == $_POST['shipst'])) {
    } else {
      $lastshipcity = $_POST['shipCity'];
      $lastshipst = $_POST['shipState'];
      if (!(valcity($lastshipcity, $lastshipst))){
        $cityerr = true;
        $shipcityerr = true;
        loadcities($lastshipst);
        return false;
      }
    }

    if ($_POST['shipID'] == "0") return AddShipping() ;
    
    global $acctID, $AcctName, $errMsg;
    
    include ('Connections/DBConn.php');
    mysqli_select_db( $DBConn, $database_DBConn); 
  
    $updQ  = "UPDATE signboom_shipto SET ";
    $updQ .= "acctid='".$acctID."', ";
    $updQ .= "acctname='".$AcctName."', ";
    $updQ .= "name='".$_POST['shipName']."', ";
    $updQ .= "address='".$_POST['shipAddress']."', ";
    $updQ .= "city='".$_POST['shipCity']."', ";
    $updQ .= "state='".$_POST['shipState']."', ";
    $updQ .= "country='".GetCountry($_POST['shipState'])."', ";
    $updQ .= "postalzip='".strtoupper($_POST['shipZip'])."' ";
    $updQ .= "WHERE (ID='".$_POST['shipID']."')";
    mysqli_select_db( $DBConn, $database_DBConn); 
    $result = mysql_query($updQ)  or die(mysql_error());
  
    $shipID = $_POST['shipID'] ;
    $errMsg="<font color=\"#00AAEA\"><b>Update Shipping Address Completed</b></font>&nbsp;&nbsp;";
	//header('Location : http://10.235.4.47/SignBoom/customer.php');
  }
  
  //Deletes the currently selected shipping address.
  function DeleteShipping() {
      
    if ($_POST['shipID'] == "0") return ;
    
    global $acctID, $AcctName, $errMsg;
    include ('Connections/DBConn.php');
    mysqli_select_db( $DBConn, $database_DBConn);
  
    $updQ  = "DELETE FROM signboom_shipto WHERE (ID='".$_POST['shipID']."')" ;
    mysqli_select_db( $DBConn, $database_DBConn);
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $updQ)  or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  
    $shipID = 0 ;
    $errMsg="<font color=\"#00AAEA\"><b>Shipping Address Deleted</b></font>&nbsp;&nbsp;";
  }
/*  
  function LoadInitalCustomerData()
  {
	  $loginUsername = $_SESSION['MM_Username'];
	  $RS__query=sprintf("SELECT * FROM signboom_user WHERE email='%s'",  get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername)); 
    mysql_select_db($database_DBConn, $DBConn);
    $RS = mysql_query($RS__query, $DBConn) or die(mysql_error());
    $FoundUser = !mysql_num_rows($RS);
      if ($FoundUser) {
      header("Location: ".$MM_redirectLoginFailed );
      exit();
    }
    $userLevel = mysql_result($RS,0,'userLevel');
    $acctID=mysql_result($RS,0,'ID');
    $firstName=mysql_result($RS,0,'firstName');
    $lastName=mysql_result($RS,0,'lastName');
    $email=mysql_result($RS,0,'email');
    $company=mysql_result($RS,0,'company');
    $AcctName=mysql_result($RS,0,'AcctName');
    $country=mysql_result($RS,0,'country');
    $url=mysql_result($RS,0,'url');
    $address=mysql_result($RS,0,'address');
    $provstate=mysql_result($RS,0,'provstate');
    $city=mysql_result($RS,0,'city');
    $phone1=mysql_result($RS,0,'phone1');
    $phone2=mysql_result($RS,0,'phone2');
    $htmlmail=mysql_result($RS,0,'htmlmail');
    $defcourier=mysql_result($RS,0,'defcourier');
    $courieracct=mysql_result($RS,0,'courieracct');
    $postalzip=mysql_result($RS,0,'postalzip');
    $hintq=mysql_result($RS,0,'hintq');
    $hinta=mysql_result($RS,0,'hinta');
    $dct=mysql_result($RS,0,'dct');
    $pstnum=mysql_result($RS,0,'pstnum');
    $userName=mysql_result($RS,0,'userName');
    $acctDisable=mysql_result($RS,0,'acctDisable');
    $htmlmail=mysql_result($RS,0,'htmlmail');
    
    $shipID = 0;
    $shipName = "";
    $shipAddress = "";
    $shipCity = "";
    $shipState = "";
    $shipZip = "";
    $shipCountry = "";
  }
*/
?>

<script language="JavaScript" type="text/JavaScript">
  
  var shiptoArray = new Array();
  var cities = new Array();
    
<?php  
  if ($shipcityerr) {
    echo ("var shipcityerr = true; ");
  } else {
    echo ("var shipcityerr = false; ");
  }

  foreach ($shipto as $sh) {
    $shiptonum = $sh->ID;
    echol ('shiptoArray['.$shiptonum.'] = new Array();');
    echol ('shiptoArray['.$shiptonum.'][0] = "'.$sh->ID.'";');
    echol ('shiptoArray['.$shiptonum.'][1] = "'.addcslashes($sh->name, "\0..\057\072..\100\133..\140\173..\377").'";');
    echol ('shiptoArray['.$shiptonum.'][2] = "'.addcslashes($sh->address, "\0..\057\072..\100\133..\140\173..\377").'";');
    echol ('shiptoArray['.$shiptonum.'][3] = "'.addcslashes($sh->city, "\0..\057\072..\100\133..\140\173..\377").'";');
    echol ('shiptoArray['.$shiptonum.'][4] = "'.$sh->state.'";');
    echol ('shiptoArray['.$shiptonum.'][5] = "'.addcslashes($sh->zip, "\0..\057\072..\100\133..\140\173..\377").'";');
    echol ('shiptoArray['.$shiptonum.'][6] = "'.$sh->country.'";');
  }
?>
  function ChangeAddr() {
    shipidx = document.getElementById("selectShip").value;
    document.getElementById("shipID").value = shipidx;
    document.getElementById("shipName").value = shiptoArray[shipidx][1];
    document.getElementById("shipAddress").value = shiptoArray[shipidx][2];
    document.getElementById("shipCity").value = shiptoArray[shipidx][3];
    document.getElementById("shipState").value = shiptoArray[shipidx][4];
    document.getElementById("shipZip").value = shiptoArray[shipidx][5];
    document.getElementById("lastshipcity").value = shiptoArray[shipidx][3];
    document.getElementById("lastshipst").value = shiptoArray[shipidx][4];
  }

  function SetupNewAddr() {
    document.getElementById("selectShip").selectedIndex = "0";
    document.getElementById("shipID").value = "0";
    document.getElementById("shipName").value = "";
    document.getElementById("shipAddress").value = "";
    document.getElementById("shipCity").value = "";
    document.getElementById("shipState").value = "";
    document.getElementById("shipZip").value = "";
    document.getElementById("lastshipcity").value = "";
    document.getElementById("lastshipst").value = "";
  }

  function ClearAddress() {
    document.getElementById("selectShip").selectedIndex = "0";
    document.getElementById("shipName").value = "";
    document.getElementById("shipAddress").value = "";
    document.getElementById("shipCity").value = "";
    document.getElementById("shipState").value = "";
    document.getElementById("shipZip").value = "";
    document.getElementById("lastshipcity").value = "";
    document.getElementById("lastshipst").value = "";
  }

  //Execute this when a new city is selected from the pick list
  function PickCity() {
    document.getElementById('txtselectedcity').value = document.getElementById('citylist').value;
  }

  function SelectCity() {
    hideme('citydiv');
    if (shipcityerr) {
      document.getElementById('shipCity').value = document.getElementById('txtselectedcity').value;
    } else {
      document.getElementById('city').value = document.getElementById('txtselectedcity').value;
    }
  }
  //Hides a div
  function hideme(divname) {
    if (document.getElementById) { 
      document.getElementById(divname).style.visibility = 'hidden';
    } 
  }
  //Shows a div
  function showCityDiv() {
    if (document.getElementById) { 
      document.getElementById('citydiv').style.visibility = 'visible';
    } 
  }

</script>


<?php
  if ($cityerr) {
    echo "<script language=\"javascript\">alert(\"That city/province combination is not valid.  Your changes have not been saved.  Please choose a correct city and province combination.\");</script>";
    echo "<script language=\"javascript\">showCityDiv();</script>";
  } else {
    echo "<script language=\"javascript\">hideme('citydiv');</script>";
  }
?>    

<body>

  <div id="page">
    <div id="wrapper">

    <?php
      include ('header.php');
    ?>

    <div id="content">
    <?php
      include ('sidebar.html');
    ?>

    <img src="images/title_my_account.gif" width="155" height="18" alt="MY ACCOUNT">
    <br><br>

    <div style="width: 430px;">
    <form name="form1" method="post" action="">
      <span class="highlighted"><?php echo $errMsg; ?></span><br><br>
      <b>Contact Information:</b>
      <hr>
      <br><br>
      <div style="text-align: right;">
      <input name="acctid" type="hidden" id="acctid" value="<?php echo $acctID; ?>">
      <input name="acctname" type="hidden" id="acctname" value="<?php echo $AcctName; ?>">
      <input name="oldcity" type="hidden" id="oldcity" value="<?php echo $city; ?>">
      <input name="oldst" type="hidden" id="oldst" value="<?php echo $provstate; ?>">
      Account ID: <?php echo stripslashes($AcctName); ?><br><br>
	  First Name: <input name="firstName" type="text" id="firstName" value="<?php echo stripslashes($firstName); ?>" size="32" maxlength="32"><br>
      Last Name: <input name="lastName" type="text" id="lastName" value="<?php echo stripslashes($lastName); ?>" size="32" maxlength="32"><br>
      Company: <input name="company" type="text" id="firstName22" value="<?php echo stripslashes($company); ?>" size="32" maxlength="35"><br>
      Address: <input name="address" type="text" id="xaddress" value="<?php echo stripslashes($address); ?>" size="32" maxlength="64"><br>
      City: <input name="city" type="text" id="city" value="<?php echo stripslashes($city); ?>" size="32" maxlength="25"><br>
      Province/State: 
      <select name="provstate"  id="provstate">
      <?php  if(count($arrProvState)){?>
      <?php    while( list($id, $val) = each($arrProvState) ){?>
      <?php      if($id == $provstate){?>
                <option value="<?php echo $id;?>" selected><?php echo $val;?></option>
      <?php      }else{?>
                <option value="<?php echo $id;?>"><?=$val;?></option>
      <?php      }?>
      <?php    }?>
      <?php  }else{?>
            <option value="">None</option>
      <?php  }?>
      </select><br>
      Postal/Zip Code: <input name="postalzip" type="text" id="postalzip2" value="<?php echo stripslashes($postalzip); ?>" size="20" maxlength="16"><br>
      <?php
      /*
      echo 'Country: ';
      <select name="country" id="select">
        <option value="USA"<? if ($country == "USA") echo " selected"?>>United States</option>
        <option value="CAN"<? if ($country == "CA") echo " selected"?>>Canada</option>
      </select>
      <br>
      */
      ?>
      Phone: <input name="phone1" type="text" id="phone1" value="<?php echo stripslashes($phone1); ?>" size="20" maxlength="16"><br>
      FAX Number: <input name="phone2" type="text" id="phone2" value="<?php echo stripslashes($phone2); ?>" size="20" maxlength="16"><br>
      Email Address: <input name="email" type="text" id="email" value="<?php echo trim($email); ?>" size="32" maxlength="64"><br>
      Web Site URL: <input name="url" type="text" id="url" value="<?php echo stripslashes($url); ?>" size="32" maxlength="64"><br>
      PST Number: <input name="pstnum" type="text" id="pstnum" value="<?php echo stripslashes($pstnum); ?>" size="32" maxlength="10"><br>
      </div>

      <br><br><br>
      <b>Security Settings:</b>
      <hr>
      <br>

      <div style="text-align: right;">
      Please choose a question, and type in your answer to it.<br>
      If you forget your password, you'll need to answer this question.<br><br>
      Security Question:
      <?php
        // Identify hint question to be shown in drop-down list.
        // $ohintq is retrieved from the database. $hintq is the one they have just selected.
        // Either $hintq or $ohintq will be blank.  The other will have the hint question that should be displayed.
        if (trim($hintq) != "") {
          $id = 0;
          while (list($i, $val) = each($arrHintQ2) ) {
            if ($hintq == $val) $hint_question = $i;
          }
        }
        else {
            $hint_question = $selectHintQ;
        }
      ?>

        <select name="selectHintQ">
        <?php
          //Let them choose new hint question if desired.
          if (count($arrHintQ)) {
            while (list($id, $val) = each($arrHintQ) ) {
              if ($id == $hint_question) {
                printf("<option value=\"$id\" selected>$val</option>\n");
              } else {
                printf("<option value=\"$id\">$val</option>\n");
              }
            }
          } else {
            printf("<option value=\"\">None</option>\n");
          }
        ?>
        </select>

      <br>
      Answer: <input name="hinta" type="text" id="hinta" value="<?php echo stripslashes($hinta); ?>" size="32" maxlength="32"><br><br>
      Please type your password twice, below.<br><br>
      Password: <input name="userPass" type="password" id="userPass" size="32" maxlength="20"><br>
      Confirm Password: <input name="confPass" type="password"  id="confPass" size="32" maxlength="20"><br>
      </div>

      <br><br><br>
      <b>Other Settings:</b>
      <hr>
      <br>

      <div style="text-align: right;">
      Would you like to receive order confirmations in HTML?<br>
      <input name="htmlmail" type="radio" value="Yes" <?php if ($htmlmail == "Yes") print " checked"; ?>>Yes
      <input name="htmlmail" type="radio" value="No"<?php if (!($htmlmail == "Yes")) print " checked"; ?>>No
      <br><br>
      Your discount level: <?php echo stripslashes(GetDiscount($dct)); ?><br>
      </div>

      <br><br>

      <div align="center">
      <input class="button" type="submit" name="Submit" value="Save Changes to Account">
      </div>
      <br><br>
      <b>Shipping Addresses:</b>
      <hr>
      <br>

      <div style="text-align: right;">

      <!--<input name="AddShipping" type="button" onClick="SetupNewAddr();" value="New Address" > OR -->
      Address: 
      <select name="selectShip" id="selectShip" onChange="ChangeAddr()">
        <?php
        if (count($shipto)) {
          foreach ($shipto as $s) {
            if($s->ID == $shipID){
              echol ('<option value="'.$s->ID.'" selected>'.$s->name.'</option>');
            } else {
              echol ('<option value="'.$s->ID.'">'.$s->name.'</option>');
            }
          }
        } else {
          echol ('<option value="">None</option>');
        }
        ?>
      </select><br>
      <input name="shipID" type="hidden" id="shipID" value="<?php echo $shipID; ?>">
      <input name="lastshipcity" type="hidden" id="lastshipcity" value="<?php echo $lastshipcity; ?>">
      <input name="lastshipst" type="hidden" id="lastshipcity" value="<?php echo $lastshipst; ?>">
      Name: <input name="shipName" type="text" id="shipName" value="<?php echo stripslashes($shipName); ?>" size="32" maxlength="64"><br>
      Address: <input name="shipAddress" type="text" id="shipAddress" value="<?php echo stripslashes($shipAddress); ?>" size="32" maxlength="64"><br>
      City: <input name="shipCity" type="text" id="shipCity" value="<?php echo stripslashes($shipCity); ?>" size="32" maxlength="64"><br>
      Province/State:
      <select name="shipState" id="shipState">
        <?php  
        if (count($arrProvState)) {
          reset($arrProvState);
          while (list($id, $val) = each($arrProvState) ){
            if($id == $shipState){
              echol ('<option value="'.$id.'" selected>'.$val.'</option>');
            } else {
              echol ('<option value="'.$id.'">'.$val.'</option>');
            }
          }
        } else {
          echol ('<option value="">None</option>');
        }
        ?>
      </select>
      <br>
      Postal/Zip Code: <input name="shipZip" type="text" id="shipZip" value="<?php echo stripslashes($shipZip); ?>" size="20" maxlength="64">
      <br><br>
      </div>
      <div style="text-align: center;">
      <input class="button" type="submit" name="UpdateShipping" value="Add / Update Shipping Address">&nbsp;&nbsp;
      <input class="button" type="submit" name="DeleteShipping" value="Delete Shipping Address" onClick="ClearAddress();">
      </div>

 
      <input name="dct" type="hidden" value="<?php echo $dct; ?>">
      </form>
      </div>

    </div>

    <?php
      include ('footer.html');
    ?>

  </div>
</div>

<div id="citydiv">
  <br><b>Signboom City Locator</b><br><br>

  <div id="citytxt">
    We are unable to locate <?php echo ($shipcityerr ? $lastshipcity : $city); ?> in our shipping database.  
    Please choose a new city from the list below, and then click "Select" to accept your city as entered. 
    <br><br>
    Please note that if the city is not in our database, we cannot compute freight charges.
    <br><br>
  </div>

  <div id="cityprovtxt">
    Cities within <?php echo ($shipcityerr ? $lastshipst : $provstate); ?>
    <br><br>
  </div>

  <select name="citylist" size="14" id="citylist" multiple onChange="PickCity()">
     <?php
     if ($cityerr) {
       for ($i = 0; $i <= count($Cities); $i++) { 
         echo ('<option value="'.$Cities[$i].'">'.$Cities[$i].'</option>');
       } 
     }
     ?>    
   </select>

  <input name="txtselectedcity" type="text" id="txtselectedcity" value="<?php echo ($shipcityerr ? $lastshipcity : $city); ?>" maxlength="32">
  <input name="Submit" type="submit" onClick="SelectCity();" value="Select">
</div>

</body>

</html>


