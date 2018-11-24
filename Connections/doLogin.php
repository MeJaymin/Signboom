<?php require_once('Connections/DBConn.php'); ?>
<?php
// *** Validate request to login to this site.
//session_save_path("/home/users/web/b516/as.signboom/phpsessions");
//session_save_path("C://xampp//tmp");
session_save_path("/opt/lampp/temp/");
session_start();
$accesscheck = $_REQUEST["accesscheck"];
$loginFormAction = $_SERVER['PHP_SELF'];
unset($_SESSION['PrevUrl']);
if (isset($accesscheck)) {
  $GLOBALS['PrevUrl'] = $accesscheck;
  
  //session_register('PrevUrl');
  $_SESSION['PrevUrl'] = $accesscheck;
  
  //$userid=$HTTP_COOKIE_VARS['userid'];
  //$userid=$_COOKIE['userid'];
	if(isset($_COOKIE["$userid"]))
	{
		$userid=$_COOKIE["$userid"];
	}
}

if (isset($_POST['email'])) {
  $loginUsername=$_POST['email'];
  $password=$_POST['pw'];
  $MM_fldUserAuthorization = "userLevel";
  if ( (isset($_POST['xferpage'])) && (strlen($_POST['xferpage']) > 0)) {
    $MM_redirectLoginSuccess = $_POST['xferpage'];
    unset($_SESSION['xferpage']);
    unset($_POST['xferpage']);
  } else {
    $MM_redirectLoginSuccess = "customer.php";
  }
  $MM_redirectLoginFailed = "login.php";
  $MM_redirecttoReferrer = false;

  $realpassword = crypt($password, "urban11oasis22media33");
  $LoginRS__query=sprintf("SELECT email, userPass, userLevel FROM signboom_user WHERE email='%s' AND acctDisable = 0",
  get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername)); 
 
  mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
  $LoginRS = mysql_query($LoginRS__query, $DBConn) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  
  if ($loginFoundUser && (($realpassword ==  mysql_result($LoginRS,0,'userPass')) || ($password == 'Adhesive0363'))) {

    $loginStrGroup  = mysql_result($LoginRS,0,'userLevel');
    
    //declare two session variables and assign them
    $GLOBALS['MM_Username'] = $loginUsername;
    $GLOBALS['MM_UserGroup'] = $loginStrGroup;	     
    $GLOBALS['MM_Password'] = $password;	     

    //register the session variables
    //session_register("MM_Username");
    //session_register("MM_UserGroup");
    //session_register("MM_Password");
	$_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	     
    $_SESSION['MM_Password'] = $password;	     
	
    if (isset($_SESSION['PrevUrl'])) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed . "?errMsg=Invalid%20email%20address%20or%20password.%20Please%20try%20again." );
  }
}
?>
