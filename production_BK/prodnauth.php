<?
  include('../admin/auth.php');

  session_save_path("/home/users/web/b516/as.signboom/phpsessions");
  session_start();
  $_SESSION['page_forward'] = $_SERVER['REQUEST_URI'];
  if (!isset($_SESSION["MM_Username"])) {
    // redirect to login page
    header("Location: index.php");
    exit();
  }

  if ((!authenticate($_SESSION["MM_Username"], $_SESSION["MM_Password"], 1)) &&
      (!authenticate($_SESSION["MM_Username"], $_SESSION["MM_Password"], 3))) {
    // redirect to download page
    unset($_SESSION["MM_Username"]);
    unset($_SESSION["MM_Password"]);
    header("Location: admin/alogin.php");
    exit();
  }
  unset($_SESSION['page_forward']);

?>
