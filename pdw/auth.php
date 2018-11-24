<?
  function authenticate($un, $pw, $ulevel) {
    include('../Connections/DBConn.php');

    $realpassword = crypt($pw, "urban11oasis22media33");    
    $LoginRS__query=sprintf("SELECT email, userPass, userLevel, AcctName FROM signboom_user WHERE email='%s' AND userPass='%s'",
                            get_magic_quotes_gpc() ? $un : addslashes($un), 
                            get_magic_quotes_gpc() ? $realpassword : addslashes($realpassword)); 
 
    mysql_select_db($database_DBConn, $DBConn) or die(mysql_error());
    $LoginRS = mysql_query($LoginRS__query, $DBConn) or die(mysql_error());
    $loginFoundUser = mysql_num_rows($LoginRS);

    if ($loginFoundUser) {
      $loginStrGroup  = mysql_result($LoginRS,0,'userLevel');
      $acctName = mysql_result($LoginRS,0,'AcctName');
      //declare two session variables and assign them
      $GLOBALS['MM_Username'] = $un;
      $GLOBALS['MM_UserGroup'] = $loginStrGroup;        
      $GLOBALS['MM_Password'] = $pw;        
      $GLOBALS['MM_AcctName'] = $acctName;        

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
