<?php
include 'helpers/db_helper.php';
  function authenticate($un, $pw, $ulevel) {
	//echo "Inside authenticate function.";
    include('../Connections/DBConn.php');

    $realpassword = crypt($pw, "urban11oasis22media33");
	
    $LoginRS__query=sprintf("SELECT email, userPass, userLevel FROM signboom_user WHERE email='%s' AND userPass='%s'",
    get_magic_quotes_gpc() ? $un : addslashes($un), get_magic_quotes_gpc() ? $realpassword : addslashes($realpassword));
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"])); 
    $LoginRS = mysqli_query( $DBConn, $LoginRS__query) or die(mysqli_error($GLOBALS["___mysqli_ston"])); 
    
     $loginFoundUser = mysqli_num_rows($LoginRS);
    if ($loginFoundUser) {
      $loginStrGroup  = mysqli_result($LoginRS,0,'userLevel');
      //print_r($loginStrGroup); die;
      //declare two session variables and assign them
      $GLOBALS['MM_Username'] = $un;
      $GLOBALS['MM_UserGroup'] = $loginStrGroup;        
      $GLOBALS['MM_Password'] = $pw;        

      //register the session variables
      /*
	  session_register("MM_Username");
      session_register("MM_UserGroup");
      session_register("MM_Password");
	  */

      if ($loginStrGroup != $ulevel) {
        $rc = -0;
      } else {
          $rc = -1;
      }
    }
    else {
        $rc = -0;
    }    
    //mysql_close ($DBConn);
    return $rc;
  }
?>
