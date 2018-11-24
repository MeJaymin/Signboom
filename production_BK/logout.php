<?php
  session_save_path("/home/users/web/b516/as.signboom/phpsessions");
  session_start(); 

  if (isset($_SESSION['MM_Username'])) {
    $user = $_SESSION['MM_Username'];
    //unregister the session variables
    unset($_SESSION['MM_Username']);
    unset($_SESSION["MM_Password"]);  
    unset($_SESSION["MM_UserGroup"]);  
    session_unregister("MM_Username");
    session_unregister("MM_UserGroup");
    session_unregister("MM_Password");
    header("Location: index.php");
    exit();
  } 
  else {
    echo 'No one is logged on.';
  }

?>
