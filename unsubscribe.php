<?php
  require_once('Connections/DBConn.php'); 
  mysqli_select_db( $DBConn, $database_DBConn);

  $account_name = $_GET['account'];
  $email_address = $_GET['email'];

  $sql = "SELECT MailingList FROM signboom_user WHERE AcctName = '$account_name' AND email = '$email_address' AND acctDisable = 0";
  $result = mysqli_query( $DBConn, $sql);
  $found = mysqli_num_rows($result);
  if ($found < 1)
  {
    $message = "<span style=\"color: #cc0000;\">The software was unable to unsubscribe you as there was no account in the our database that matches both the account name and email address given.<br><br>Please call us at (604) 881-0363 so that we can locate your account and unsubscribe you. We apologize for the inconvenience.</span>";
  }
  else if ($found > 1)
  {
    $message = "<span style=\"color: #cc0000;\">The software was unable to unsubscribe you as there was more than one account in the our database that matches both the account name and email address given.<br><br>Please call us at (604) 881-0363 so that we can locate the correct account and unsubscribe you. We apologize for the inconvenience.</span>";
  }
  else // $found == 1
  {
    $row = mysqli_fetch_assoc($result);
    $mailing_list = $row['MailingList'];
    if (strtolower($mailing_list) == 'yes')
    {
      $sql = "UPDATE signboom_user SET MailingList = 'No' WHERE AcctName = '$account_name' AND email = '$email_address' AND acctDisable = 0";
      $result = mysqli_query( $DBConn, $sql);
      $rows_changed = mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
      if ($rows_changed)
      {
        $message = "You have been unsubcribed from the Signboom mailing list.<br><br>However, you will still receive confirmation emails for any orders you place with us.";
      }
      else
      {
        $message = "<span style=\"color: #cc0000;\">The software was unable to unsubscribe you due to an unexpected error.<br><br>Please call us at so that we can locate your account and unsubscribe you. We apologize for the inconvenience.</span>";
      }
    }
    else if (strtolower($mailing_list) != 'yes')
    {
        $message = "<span style=\"color: #cc0000;\">You were already unsubscribed.  If you receive another email after having unsubscribed, please let us know.</span>";
    }
  }
  
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <title>Signboom: Your Online Print Engine</title>

  <!--INTERNET EXPLORER SPECIFIC STYLING CODE FOLLOWS-->
  <?php
    include ('browser_detection.php');
    $my_browser = browser_detection('browser');
    if ($my_browser == 'msie6') 
    {
      echo '<link rel="stylesheet" href="ie6_specific.css" type="text/css" title="default_style">';
    }
    else
    {
      echo '<link rel="stylesheet" href="signboom.css" type="text/css" title="default_style">';
    }
  ?>

</head>

<body>

  <div id="page">
    <div id="wrapper">

    <?php
      include ('header.php');
    ?>

    <div id="content">
    <?php
       include ('sidebar.html');
    ?>

    <img src="images/title_unsubscribe.gif" width="392" height="18" alt="UNSUBSCRIBE FROM MAILING LIST">

    <br><br><br><br>
    <?php echo $message; ?>

    <br><br><br><br>

   </div>

    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>


