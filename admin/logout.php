<?php
//error_reporting(0);
//session_save_path("/home/users/web/b516/as.signboom/phpsessions");
	//session_save_path("C://xampp//tmp");
	session_save_path("/opt/lampp/temp/");
	session_start(); 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.style1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;
}
-->
</style>
</head>
<body>
<p>&nbsp;</p>
<p align="center" class="style1">
<?php
//session_save_path("/home/users/web/b516/as.signboom/phpsessions");
session_save_path("C://xampp//tmp");
	session_start();
	//print_r($_SESSION);die;
	if (isset($_SESSION['MM_UserName']))
	{
		$user = $_SESSION['MM_UserName'];
 		//unregister the session variables
		
		unset($_SESSION['MM_Username']);
		unset($_SESSION["MM_Password"]);	
		unset($_SESSION["MM_UserGroup"]);	
    	//session_unregister("MM_Username");
    	//session_unregister("MM_UserGroup");
		//session_unregister("MM_Password");
		
		echo $user.' has been logged out.';
	} else {
		echo 'No one is logged on.';
	}
?>
</p>
</body>
</html>
