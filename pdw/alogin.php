<?php

require_once('../includes/globals.php');
require_once('../Connections/DBConn.php');
require_once('auth.php');

function loginmsg()
{
	echo '<blockquote>';
	echo '<p align="left" class="style1">The ID and password you have entered are in error or you do not have enough authority to use this feature.<br> Remember, both the Logon ID and your password are case sensitive. Please try again.</p>';
	echo '<p class="style1">Forgot your password?';
	echo ' Click <a href="#" onClick="popUpWindow(\'forgot.php\',30,30,400,150)">here</a>.</p>';
	echo '</blockquote>';
}

//session_save_path("/home/users/web/b516/as.signboom/phpsessions");
//session_save_path("C://xampp//tmp");
//session_save_path("/opt/lampp/temp/");
session_save_path("/var/www/html/");
session_start();

/*  
if (isset($email))
{
	$_SESSION["MM_UserName"] = $email;
	$_SESSION["MM_Password"] = $pw;
}
*/

if (isset($_POST['Submit']))
{
	$_SESSION["MM_UserName"] = $_POST['email'];
	$_SESSION["MM_Password"] = $_POST['pw'];
}

$invalidauth = false;

if (isset($_SESSION['MM_UserName']) && isset($_SESSION['MM_Password']))
{
    // level 1 is signboom admin, level 4 gives access to drag and drop system (but not yet regular order system)
    if(isset($_SESSION["MM_UserName"]))
    {
    	if ((authenticate($_SESSION["MM_UserName"], $_SESSION["MM_Password"], 1))  ||
        (authenticate($_SESSION["MM_UserName"], $_SESSION["MM_Password"], 4)))
		{
			if(isset($_SESSION['page_forward']))
			{
				$fwd = $_SESSION['page_forward'];
				unset($_SESSION['page_forward']);
				//if (strlen($fwd) == 0) $fwd = "admin.php";
				if (strlen($fwd) == 0) $fwd = "index.php";
			}
			else
			{
				$fwd = "index.php";
			}
			
			header("Location: ".$fwd);
			exit();
	    }
	    else
		{
			$invalidauth = true;
			unset($_SESSION['MM_UserName']);  
			unset($_SESSION['MM_Password']);
	    }
    }
}

include ('templates/login.html.php');
?>     
 
