<?
  include('../admin/auth.php');

  //session_save_path("/home/users/web/b516/as.signboom/phpsessions");
  session_save_path("/opt/lampp/temp/");
  session_start();
  $_SESSION['page_forward'] = $_SERVER['REQUEST_URI'];
  if (!isset($_SESSION["MM_Username"])) {
    // redirect to login page
    header("Location: index.php");
    exit();
  }

  // level 1 is signboom admin; level 4 gives access to drag and drop, but not yet regular order form
  if ((!authenticate($_SESSION["MM_Username"], $_SESSION["MM_Password"], 1)) &&
      (!authenticate($_SESSION["MM_Username"], $_SESSION["MM_Password"], 4))) {
    // redirect to download page
    unset($_SESSION["MM_Username"]);
    unset($_SESSION["MM_Password"]);
    header("Location: admin/alogin.php");
    exit();
  }
  unset($_SESSION['page_forward']);

?>
