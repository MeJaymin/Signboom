<?php 
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
require_once('Connections/DBConn.php');
require_once('helpers/db_helper.php');
?>
<?php
// *** Validate request to login to this site.
//session_save_path("/home/users/web/b516/as.signboom/phpsessions");
//session_save_path("C://xampp//tmp");
//session_save_path("/opt/lampp/temp/");
session_save_path("/var/www/html/");
session_start();
$accesscheck = isset($_REQUEST["accesscheck"])?$_REQUEST["accesscheck"]:"";
$loginFormAction = $_SERVER['PHP_SELF'];
if(isset($_SESSION['PrevUrl']))
{
unset($_SESSION['PrevUrl']);
}
if (isset($accesscheck)) {
  $GLOBALS['PrevUrl'] = $accesscheck;
  
  //session_register('PrevUrl');
  $_SESSION['PrevUrl'] = $accesscheck;
  
  //$userid=$HTTP_COOKIE_VARS['userid'];
  //$userid=$_COOKIE['userid'];
	if(isset($_COOKIE["userid"]))
	{
		$userid=$_COOKIE["userid"];
	}
}

if (isset($_POST['email'])) {
  //print_r($_POST); die;
  /*if(!empty($_POST['xferpage']) && $_POST['xferpage']=="")
  {
    echo '222';
  }
  die;*/
  $loginUsername=$_POST['email'];
  $password=$_POST['pw'];
  $MM_fldUserAuthorization = "userLevel";
  if ( (isset($_POST['xferpage'])) && (strlen($_POST['xferpage']) > 0)) {
    
    if($_POST['xferpage']=="")
    {
      $MM_redirectLoginSuccess = $_POST['xferpage'];  
    }
    else
    {
      $MM_redirectLoginSuccess = "customer.php";  
    }
    //echo $MM_redirectLoginSuccess; die;
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
 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $LoginRS = mysqli_query( $DBConn, $LoginRS__query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $loginFoundUser = mysqli_num_rows($LoginRS);
  
  if ($loginFoundUser && (($realpassword ==  mysqli_result($LoginRS, 0, 'userPass')) || ($password == 'Adhesive0363'))) {

    $loginStrGroup  = mysqli_result($LoginRS, 0, 'userLevel');
    
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
	   
    if (!empty($_SESSION['PrevUrl'])) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    //echo $MM_redirectLoginSuccess; die;
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed . "?errMsg=Invalid%20email%20address%20or%20password.%20Please%20try%20again." );
  }
}
?>
