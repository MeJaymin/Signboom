<?php
	//session_save_path("/home/users/web/b516/as.signboom/phpsessions");
	//session_save_path("C://xampp//tmp");
	//session_save_path("/opt/lampp/temp/");
	session_save_path("/var/www/html/");
	session_start();

	//print_r($_SESSION); die;
	
	if (isset($_SESSION['MM_UserName']))
	{
		$user = $_SESSION['MM_Username'];
		//unregister the session variables
		unset($_SESSION['MM_Username']);
		unset($_SESSION["MM_Password"]);  
		unset($_SESSION["MM_UserGroup"]);  
		//session_unregister("MM_Username");
		//session_unregister("MM_UserGroup");
		//session_unregister("MM_Password");
		//header("Location: index.php");
		header("Location: alogin.php");
		exit();
	} 
	else
	{
		echo 'No one is logged on.';
	}

?>
