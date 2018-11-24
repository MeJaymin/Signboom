<?php 
  require_once('Connections/DBConn.php');
  include('Connections/needLogin.php'); 
  setcookie("userid", $_SESSION['MM_Username'], time() + (3600 * 24 * 60)); 
  require_once( "includes/utils.php" );
  require_once( "includes/inc-signboom_new.php" );
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
    visibility:hidden; 
    top: 140px;
    left: 616px; 
    height:261px; 
    width:253px; 
    z-index:1; 
    padding: 10px 10px 10px 10px; 
    border: 1px solid black; 
  }
  </style>
</head>

<?
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
    

  if (isset($_POST['Submit'])) {                  // Handle "Save Changes to Account" button
    UpdateCustomer($loginUsername);
    $loginUsername = $_SESSION['MM_Username'];
  } else if (isset($_POST['UpdateShipping'])) {   // Handle "Add / Update Shipping Address" button
    UpdateShipping() ;
    $loginUsername = $_SESSION['MM_Username'];
  } else if (isset($_POST['DeleteShipping'])) {   // Handle "Delete Shipping Address" button
    DeleteShipping() ;
    $loginUsername = $_SESSION['MM_Username'];
  } else if (isset($_POST['TestMessaging'])) {    // Handle "Test Text Messaging Now" button
    TestTextMessaging($loginUsername) ;
    $loginUsername = $_SESSION['MM_Username'];
  } else {                                        // No submit button pressed.  Just display information on page.
    $RS__query=sprintf("SELECT * FROM signboom_user WHERE email='%s'",  
        get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername)); 
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

    $txtmssg =mysql_result($RS,0,'enableTxtMssg');
    $cellnumber=mysql_result($RS,0,'cellNumber');
    $cellprovider=mysql_result($RS,0,'cellProvider');
    
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
  }

  $Qry = sprintf("SELECT * FROM signboom_shipto WHERE acctid=".$acctID); 
  mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
  $result = mysql_query($Qry, $DBConn) or die(mysql_error());
  
  $i = 0;
  $shipto[$i]->ID = 0;
  $shipto[$i]->name = "New Address";
  $shipto[$i]->address = "";
  $shipto[$i]->city = "";
  $shipto[$i]->state = "";
  $shipto[$i]->zip = "";
  $shipto[$i]->country = "";
  while ($row = mysql_fetch_array($result, MYSQL_BOTH)) { 
    $i++;
    $shipto[$i]->ID = $row['ID'];
    $shipto[$i]->name = $row['name'];
    $shipto[$i]->address = $row['address'];
    $shipto[$i]->city = $row['city'];
    $shipto[$i]->state = $row['state'];
    $shipto[$i]->zip = $row['postalzip'];
    $shipto[$i]->country = $row['country'];
  } 
  mysql_free_result($result);

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
  function UpdateCustomer($UserEmail){
    global $arrUsers;

    global $selectUser;
    global $selectLevel;
    global $firstName, $lastName, $email;
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
    global $txtmssg, $cellnumber, $cellprovider;

    global $database_DBConn;

    //EMAIL GLOBALS
    global $global_sender;
    global $global_welcome_email_subject;
    global $global_welcome_email_footer;
    global $global_welcome_email_msg;

    //////////////////////////////
    //VALIDATION
    //////////////////////////////

    if ($firstName==""){
      $errMsg="Please enter a First Name.";
      return;
    }

    if ($lastName==""){
      $errMsg="Please enter a Last Name.";
      return;
    }

    if(ereg('[^_A-Za-z0-9-]', $userName)){
     $errMsg="User Name must contain only letters and numbers.";
     return;
    }

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

    // Do not allow letters in PST number.
    if (ereg('[^0-9]', trim($pstnum))) {
       $errMsg="That PST number is invalid. Please enter only the 6-digit numeric part of your PST number.";
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

    if ($txtmssg == "Yes") {
      // Require 10 digit, digits-only cell phone number.
      if ( (ereg('[^0-9]', $cellnumber)) || (strlen($cellnumber) != 10) ) {
         $errMsg="That cell phone number is invalid. Please enter all 10 digits of the number, without spaces or punctuation.";
         return;
      }
      // Require that they specify their cell phone provider.
      if ($cellprovider == "") {
         $errMsg="You must specify your cell phone provider, so that we can figure out which email address to use to reach your cell phone.";
         return;
      }
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
    $ocellnumber=mysql_result($RS,0,'cellNumber');
    $ocellprovider=mysql_result($RS,0,'cellProvider');
    $otxtmssg=mysql_result($RS,0,'enableTxtMssg');

    $ouserName=mysql_result($RS,0,'userName');
    $oacctDisable=mysql_result($RS,0,'acctDisable');

    // ************** UPDATE DATABASE HERE **************
    $myQuery  = "UPDATE signboom_user SET ";
    $myQuery .= "firstName='" . $firstName . "', ";
    $myQuery .= "lastName='" . $lastName . "', ";
    $myQuery .= "email='" . $email . "', ";
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
    $myQuery .= "pstnum='" . $pstnum . "', ";

    $myQuery .= "cellNumber='" . $cellnumber. "', ";
    $myQuery .= "cellProvider='" . $cellprovider. "', ";
    $myQuery .= "enableTxtMssg='" . $txtmssg. "' ";

    $myQuery .= sprintf("WHERE email='%s'",  get_magic_quotes_gpc() ? $UserEmail : addslashes($UserEmail)); 
    //echo "<script language=\"javascript\">alert(\"" . $myQuery. "\");</script>";
    mysql_select_db($database_DBConn, $DBConn);
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

      $msg.=bldmsg($otxtmssg, mysql_result($RS,0,'enableTxtMssg'), "Text Messaging");
      $msg.=bldmsg($ocellnumber, mysql_result($RS,0,'cellNumber'), "Cell Number");
      $msg.=bldmsg($ocellprovider, mysql_result($RS,0,'cellProvider'), "Cell Provider");

      $msg.="\n\nAll changes are indicated with an '*'";
      $msg.="\n".date("D M j G:i:s T Y");
      mail( "orders@signboom.com", "Customer Maintenance Notification", $msg, "From: " . $global_sender . "@{$_SERVER['SERVER_NAME']}\r\n");
      mail( "leonard@signboom.com", "Customer Maintenance Notification", $msg, "From: " . $global_sender . "@{$_SERVER['SERVER_NAME']}\r\n");
      mail( "usablewebdesigns@yahoo.com", "Customer Maintenance Notification", $msg, "From: " . $global_sender . "@{$_SERVER['SERVER_NAME']}\r\n");
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
    $rs = mysql_query($qry, $DBConn) or die(mysql_error());
    if (mysql_num_rows($rs) == 0) return "Loyalty discount is not active on this account.  Please contact us to discuss.";
    return mysql_result($rs,0,'Desc');
  }
  
  function valcity($city, $st) {
    global $database_DBConn, $DBConn;
    if (GetCountry($st) == "United States") return true;

    $qry = sprintf("SELECT * FROM signboom_rates WHERE City='%s' and Province='%s'", $city, $st); 
    mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
    $rs = mysql_query($qry, $DBConn) or die(mysql_error());
    $rscount = mysql_num_rows($rs);
    mysql_free_result($rs);
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
    mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
    $result = mysql_query($Qry, $DBConn) or die(mysql_error());
    $i = 0;
    while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
      $Cities[$i] = $row['City'];
      $i++;
    }
    mysql_free_result($result);
  }
  
  //Adds a new shipping address with the company ID passed in $id.
  function AddShipping() {
    
    global $acctID, $AcctName, $errMsg, $shipID;
    if (!(valshipaddr())) return;
    include ('Connections/DBConn.php');
    mysql_select_db($database_DBConn, $DBConn);
  
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
    mysql_select_db($database_DBConn, $DBConn);
    $result = mysql_query($updQ)  or die(mysql_error());
    $shipID = mysql_insert_id($DBConn);
    
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

  function TestTextMessaging($UserEmail) {
    global $txtmssg, $cellnumber, $cellprovider;
    global $arrCellDomain;

    $cell_address = "";
    if (count($arrCellDomain)) {
      while(list($providercode, $domain) = each($arrCellDomain) ){
        if ($providercode == $cellprovider) {
          $cell_address = $cellnumber . "@" . $domain;
        }
      }
    }

    if ($txtmssg == "Yes") {
      if ($cell_address == "") {
        echo "<script language=\"javascript\">alert(\"We do not have an email address on file for your cell phone provider at this time, so are not able to complete this test. Please contact us to have this resolved.\");</script>";
      }
      else {
        //echo "<script language=\"javascript\">alert(\"" . $cell_address. "\");</script>";
        mail($cell_address, "Signboom Test", "This is a test of the Signboom Text Messaging System\n",
          "From: signboom@signboom.com\r\n");
      }
    }
    else {
      echo "<script language=\"javascript\">alert(\"You have not enabled this feature of your Signboom account.  The test will not be carried out.\");</script>";
    }
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
    mysql_select_db($database_DBConn, $DBConn);
  
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
    mysql_select_db($database_DBConn, $DBConn);
    $result = mysql_query($updQ)  or die(mysql_error());
  
    $shipID = $_POST['shipID'] ;
    $errMsg="<font color=\"#00AAEA\"><b>Update Shipping Address Completed</b></font>&nbsp;&nbsp;";
  }
  
  //Deletes the currently selected shipping address.
  function DeleteShipping() {
      
    if ($_POST['shipID'] == "0") return ;
    
    global $acctID, $AcctName, $errMsg;
    include ('Connections/DBConn.php');
    mysql_select_db($database_DBConn, $DBConn);
  
    $updQ  = "DELETE FROM signboom_shipto WHERE (ID='".$_POST['shipID']."')" ;
    mysql_select_db($database_DBConn, $DBConn);
    $result = mysql_query($updQ)  or die(mysql_error());
  
    $shipID = 0 ;
    $errMsg="<font color=\"#00AAEA\"><b>Shipping Address Deleted</b></font>&nbsp;&nbsp;";
  }
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

</script>


<?php
  if ($cityerr) {
    echo ('<div id="citydiv" style="visibility:visible;"></div>');
  } else {
    echo ('<div id="citydiv" style="visibility:hidden;"></div>');
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
      <span class="highlighted"><? echo $errMsg; ?></span><br><br>
      <b>Contact Information:</b>
      <hr>
      <br><br>
      <div style="text-align: right;">
      <input name="acctid" type="hidden" id="acctid" value="<? echo $acctID; ?>">
      <input name="acctname" type="hidden" id="acctname" value="<? echo $AcctName; ?>">
      <input name="oldcity" type="hidden" id="oldcity" value="<? echo $city; ?>">
      <input name="oldst" type="hidden" id="oldst" value="<? echo $provstate; ?>">
      Account ID: <? echo stripslashes($AcctName); ?><br><br>
      First Name: <input name="firstName" type="text" id="firstName" value="<? echo stripslashes($firstName); ?>" size="32" maxlength="32"><br>
      Last Name: <input name="lastName" type="text" id="lastName" value="<? echo stripslashes($lastName); ?>" size="32" maxlength="32"><br>
      Company: <input name="company" type="text" id="firstName22" value="<? echo stripslashes($company); ?>" size="32" maxlength="35"><br>
      Address: <input name="address" type="text" id="xaddress" value="<? echo stripslashes($address); ?>" size="32" maxlength="64"><br>
      City: <input name="city" type="text" id="city" value="<? echo stripslashes($city); ?>" size="32" maxlength="25"><br>
      Province/State: 
      <select name="provstate"  id="provstate">
      <?  if(count($arrProvState)){?>
      <?    while( list($id, $val) = each($arrProvState) ){?>
      <?      if($id == $provstate){?>
                <option value="<?=$id;?>" selected><?=$val;?></option>
      <?      }else{?>
                <option value="<?=$id;?>"><?=$val;?></option>
      <?      }?>
      <?    }?>
      <?  }else{?>
            <option value="">None</option>
      <?  }?>
      </select><br>
      Postal/Zip Code: <input name="postalzip" type="text" id="postalzip2" value="<? echo stripslashes($postalzip); ?>" size="20" maxlength="16"><br>
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
      Phone: <input name="phone1" type="text" id="phone1" value="<? echo stripslashes($phone1); ?>" size="20" maxlength="16"><br>
      FAX Number: <input name="phone2" type="text" id="phone2" value="<? echo stripslashes($phone2); ?>" size="20" maxlength="16"><br>
      Email Address: <input name="email" type="text" id="email" value="<? echo $email; ?>" size="32" maxlength="64"><br>
      Web Site URL: <input name="url" type="text" id="url" value="<? echo stripslashes($url); ?>" size="32" maxlength="64"><br>
      PST Number: <input name="pstnum" type="text" id="pstnum" value="<? echo stripslashes($pstnum); ?>" size="32" maxlength="6"><br>
      </div>

      <br><br><br>
      <b>Security Settings:</b>
      <hr>
      <br>

      <div style="text-align: right;">
        Please choose a question, and type in your answer to it.<br>
        If you forget your password, you'll need to answer this question.<br><br>
        Security Question:
        <?
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
          <?
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
        Answer: <input name="hinta" type="text" id="hinta" value="<? echo stripslashes($hinta); ?>" size="32" maxlength="32">
        <br><br>
        Please type your password twice, below.
        <br><br>
        Password: <input name="userPass" type="password" id="userPass" size="32" maxlength="20"><br>
        Confirm Password: <input name="confPass" type="password"  id="confPass" size="32" maxlength="20"><br>
      </div>

      <br><br><br>
      <b>Other Settings:</b>
      <hr><br>
      <div style="text-align: right;">
        Would you like to receive order confirmations in HTML?<br>
        <input name="htmlmail" type="radio" value="Yes" <? if ($htmlmail == "Yes") print " checked"; ?>>Yes
        <input name="htmlmail" type="radio" value="No"<? if (!($htmlmail == "Yes")) print " checked"; ?>>No
        <br><br>
        Your discount level: <? echo stripslashes(GetDiscount($dct)); ?><br>
      </div>

      <br><br><br>
      <b>Receive Notifications on your Cell Phone:</b>
      <hr><br>
      <div style="text-align: left;">
        Would you like to receive order confirmations and progress updates as text messages sent to
        your cell phone?  
        <br><br>
        Note:  Signboom does not charge for this service.  However, your cell phone provider may 
        charge for receipt of text messages, or require you to sign up for a special plan to receive 
        text messages which originate from emails.  Please check with your cell provider.
      </div>
      <div style="text-align: right;">
        <br>
        Receive notifications on your cell phone?
        <input name="txtmssg" type="radio" value="Yes" <? if ($txtmssg == "Yes") print " checked"; ?>>Yes
        <input name="txtmssg" type="radio" value="No"  <? if (!($txtmssg == "Yes")) print " checked"; ?>>No
        <br><br>
        10 Digit Cell Number (digits only): 
        <input name="cellnumber" type="text" id="cellnumber" value="<? echo stripslashes($cellnumber); ?>" size="15" maxlength="10">
        <br><br>
        Cell Phone Provider:
        <select name="cellprovider"  id="cellprovider">
                <option value="">Please Select your Provider</option>
          <?  if(count($arrCellProvider)){?>
          <?    while( list($id, $val) = each($arrCellProvider) ){?>
          <?      if($id == $cellprovider){?>
                    <option value="<?=$id;?>" selected><?=$val;?></option>
          <?      }else{?>
                    <option value="<?=$id;?>"><?=$val;?></option>
          <?      }?>
          <?    }?>
          <?  }?>
        </select>
        <br><br>
      </div>
      <div style="text-align: left;">
        To start this service, please click the <b>Save Changes to Account</b> button below.  
        <br><br>
        If your cell phone provider is not listed, please 
        <a class="inline" href="mailto:usablewebdesigns@yahoo.com?subject=Signboom Doesnt Support my Cell Phone Provider">email us</a> your name, your cell phone provider, your cell number, and your account ID shown at the top of this page, and we 
        will follow up.
      </div>
      <br><br><br>

      <div style="width: 360px; background-color: #DC0A98; color: #FFFFFF; font-weight: bold; margin: 0px auto; text-align: center;">
        <br>
        To save changes made in any section above, click<br>
        <br>
        <input class="button" type="submit" name="Submit" value="Save Changes to Account">
        <br><br>
      </div>

      <br><br><br>
      <b>Test Receiving Notifications on your Cell Phone:</b>
      <hr><br>
      <div style="text-align: left;">
        To test that your cell phone is receiving our messages, click 
        <br><br>
        <div align="center">
          <input class="button" type="submit" name="TestMessaging" value="Test Text Messaging Now">
        </div>
        <br>
        This will cause a test message to be sent immediately to your cell phone.
        If that message doesn't arrive within five minutes, please check your
        cell phone information above for any errors, correct them, save, and try again.  If 
        you are sure your information is correct and you still aren't receiving a message, please
        <a class="inline" href="mailto:usablewebdesigns@yahoo.com?subject=Signboom Email to Text Not Working">email us</a>
        your name, cell phone number, cell carrier, and your account ID shown at 
        the top of this page, and we will follow up.
      </div>

      <br><br><br>
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
      <input name="shipID" type="hidden" id="shipID" value="<? echo $shipID; ?>">
      <input name="lastshipcity" type="hidden" id="lastshipcity" value="<? echo $lastshipcity; ?>">
      <input name="lastshipst" type="hidden" id="lastshipcity" value="<? echo $lastshipst; ?>">
      Name: <input name="shipName" type="text" id="shipName" value="<? echo stripslashes($shipName); ?>" size="32" maxlength="64"><br>
      Address: <input name="shipAddress" type="text" id="shipAddress" value="<? echo stripslashes($shipAddress); ?>" size="32" maxlength="64"><br>
      City: <input name="shipCity" type="text" id="shipCity" value="<? echo stripslashes($shipCity); ?>" size="32" maxlength="64"><br>
      Province/State:
      <select name="shipState" id="shipState">
        <?  
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
      Postal/Zip Code: <input name="shipZip" type="text" id="shipZip" value="<? echo stripslashes($shipZip); ?>" size="20" maxlength="64">
      <br><br>
      </div>
      <div style="text-align: center;">
      <input class="button" type="submit" name="UpdateShipping" value="Add / Update Shipping Address">&nbsp;&nbsp;
      <input class="button" type="submit" name="DeleteShipping" value="Delete Shipping Address" onClick="ClearAddress();">
      </div>

      <input name="dct" type="hidden" value="<? echo $dct; ?>">
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
    Please choose a new city from the list below press "Select" to accept your city as entered. Please note 
    that if the city is not in our database, we cannot compute freight charges.
  </div>

  <div id="cityprovtxt">
    Cities within <?php echo ($shipcityerr ? $lastshipst : $provstate); ?>
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


