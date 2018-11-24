<?php
 //session_save_path("/home/users/web/b516/as.signboom/phpsessions");
 //session_save_path("C://xampp//tmp");
 session_save_path("/opt/lampp/temp/");
 session_start(); 
 //register the session variables
 //session_unregister("MM_Username");
 //session_unregister("MM_UserGroup");
 unset($_SESSION["MM_Username"]);
 unset($_SESSION["MM_Password"]);
 header("Location: ../login.php");

?>
