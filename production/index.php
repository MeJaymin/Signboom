<?php 

	require_once('../includes/globals.php');
	require_once('../Connections/DBConn.php');
	require_once('../admin/auth.php');

	function loginmsg()
	{
		echo '<blockquote>';
		echo '<p>Either the email address or the password you have entered is incorrect,'; 
		echo ' or you do not have enough authority to use this feature.</p>';
		echo '</blockquote>';
	}    

	//session_save_path("/home/users/web/b516/as.signboom/phpsessions");
	//session_save_path("C://xampp//tmp");
	//session_save_path("/opt/lampp/temp/");
	session_save_path("/var/www/html/");
	session_start();
	
	//if (isset($email))
	if(isset($_POST['email']))
	{
		//$_SESSION["MM_Username"] = $email;
		$_SESSION["MM_Username"] = $_POST['email'];
		//D$_SESSION["MM_Password"] = $pw;
		$_SESSION["MM_Password"] = $_POST['pw'];
	}

	$invalidauth = false;
	if(isset($_SESSION['MM_Username']))
	{
		//echo 'About to authenticate username and password: ';
		//echo $_SESSION["MM_Username"];
		//echo ' and ';
		//echo $_SESSION["MM_Password"];

		if ((authenticate($_SESSION["MM_Username"], $_SESSION["MM_Password"], 1)) || 
        (authenticate($_SESSION["MM_Username"], $_SESSION["MM_Password"], 3)))
		{
			//echo '<br>invalidauth = false';
			//print_r($_SESSION); die;
			if(isset($_SESSION['page_forward']))
			{
				$fwd = $_SESSION['page_forward'];
				unset($_SESSION['page_forward']);
				//if (strlen($fwd) == 0) $fwd = "admin.php";
				if(strlen($fwd) == 0) $fwd = "queues.php";
			}
			else
			{
				$fwd = "queues.php";
			}
			//echo $fwd; die;
			header("Location: ".$fwd);
			exit();
		}
		else
		{
			//echo '<br>invalidauth = true';
			$invalidauth = true;
			unset($_SESSION['MM_Username']);  
			unset($_SESSION['MM_Password']);
		}
	}

?>     

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>
  <title>Production Login</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="production-new.css" rel="stylesheet" type="text/css">
</head>

<body>

  <table border="0" align="center" cellpadding="7">
    <tr>
      <td rowspan="2" width="320"> <img src="../images/logo3d.gif" width="308" height="54"> </td>
      <td align="right" width="580"><h1>Order Processing System: Login</h1></td>
    </tr>
  </table>

  <div style="width: 350px; margin: 0px auto;">
  <br><br><br><br><br><br><br><br><br>
  <form name="form1" method="post" action="">
    Email Address: <input name="email" type="text" id="email">
    <br><br>
    Password: <input name="pw" type="password" id="pw">
    <br><br>
    <input name="Submit" type="submit" value="Log In">
  </form>
  <? 
    if ($invalidauth) {
      loginmsg();
    } 
  ?>

  </div>

</body>
</html>
