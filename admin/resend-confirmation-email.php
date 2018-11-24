<?php 
  include ('authadmin.php'); 
  include ('helper-functions.php');
  include 'helpers/db_helper.php'; 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

  //require_once('../Connections/DBConn.php'); 
  require_once('../includes/mailord.php'); 
  include('../includes/utils.php');
  include('../includes/build_message.php');
  include('../includes/getdetail.php');

  if (!isset($_POST['order_id']))
  {
    $error_message = "You must enter the ID of the order whose confirmation email needs to be resent.";
  }
  else if (!isset($_POST['email_address']))
  {
    $error_message = "You must enter an email address to send the confirmation email to.";
  }
  else
  {
    $order_id = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['order_id']);
    $email_address = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['email_address']);

    if (!is_numeric($order_id))
    {
      $error_message = "'$order_id' is not a valid order ID. Only integers are allowed.";
    }
    else if (!isValidOrderId($order_id))
    {
      $error_message = "The order id '$order_id' does not exist.";
    }
    else if (!isValidEmailAddress($email_address))
    {
      $error_message = "'$email_address' is not a valid email address. Please correct it and try again.";
    }
    else
    {
      $Query = "SELECT * FROM signboom_ordermast WHERE ID = $order_id";
      $result = mysqli_query( $DBConn, $Query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $ref_num = mysqli_result($result, 0, 'refnum');
      $account_id = mysqli_result($result, 0, 'AcctName');
      $subject_line = "Signboom Order Entry #$order_id - $account_id - '$ref_num'";

      $query_rs = "SELECT * FROM signboom_user WHERE AcctName = '$account_id'";
      $rsUser = mysqli_query( $DBConn, $query_rs) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

      $detail = array();
      getOrderDetails($order_id, $detail);

      $msg = bldmsg($result, $detail, $rsUser);

      $message = "Resending confirmation email for order $order_id to $email_address<br>";
      mail($email_address, $subject_line, $msg, "From: " . mysqli_result($rsUser, 0, 'email') . "\r\n"); 
      $message .= "Email sent.";
    }

  }

  $order_id = '';
  $email_address = '';
  
  // Display the page.
  include ('templates/resend-confirmation-email.php'); 

?>
