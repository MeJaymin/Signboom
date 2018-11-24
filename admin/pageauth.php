<?
	include('auth.php');
	//session_save_path("/home/users/web/b516/as.signboom/phpsessions");
	//session_save_path("C://xampp//tmp");
	session_save_path("/opt/lampp/temp/");
	session_start();
	$_SESSION['page_forward'] = $_SERVER['REQUEST_URI'];
	if (!isset($_SESSION["MM_UserName"])) {
		// redirect to login page
		header("Location: alogin.php");
		exit();
	}
	if (!authenticate($_SESSION["MM_UserName"], $_SESSION["MM_Password"], 1)) {
		// redirect to download page
		unset($_SESSION["MM_UserName"]);	
		unset($_SESSION["MM_Password"]);	
		header("Location: alogin.php");
		exit();
	}
	unset($_SESSION['page_forward']);
?>
