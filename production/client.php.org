<?php 
  require('authprodn.php');
  require_once( "../includes/inc-mysql.php" );
  require_once( "../includes/dohash.php" );
  require_once( "../includes/utils.php" );
  require_once( "../includes/inc-signboom.php" );

  // SESSION HANDLER
  //session_save_path("/home/users/web/b516/as.signboom/phpsessions");
  session_save_path("/opt/lampp/temp/");
  session_start();

  if (isset($_GET['account'])) {
    $account = $_GET['account'];
  }
?>

<html>
<head>
  <title>Client Contact Information</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="production-new.css" rel="stylesheet" type="text/css">
</head>
<body>

  <div style="margin: 0px auto; width: 380px;">
    <div style="text-align: center;">
    <img src="../../images/logo3d.gif" width="308" height="54"><br>
    <p style="font-size:22px; font-weight:bold;">Client Contact Information<br>

  <?php
    if ($account) {

      echo "$account</p></div><br><br>";

      // Connect to the database.
      include('../Connections/DBConn.php');

      // Query the user database for all clients.
      $myQuery = "SELECT * FROM signboom_user WHERE AcctName='$account'";
      $result = mysql_query($myQuery);
      $num_rows = mysql_num_rows($result);
      if ($num_rows == 0) {
        echo "Could not find client $account in database.<br>";
      }
      else {
        while($myrow = mysql_fetch_array($result)) {
          echo "<b>Account Id:</b> " . $myrow['ID'] . "<br>";
          echo "<b>Account Name:</b> " . $myrow['AcctName'] . "<br>";
          echo "<b>First Name:</b> " . $myrow['firstName'] . "<br>";
          echo "<b>Last Name:</b> " . $myrow['lastName'] . "<br>";
          echo "<b>Email address:</b> <a href=\"mailto:" . $myrow['email'] . "\">" . $myrow['email'] . "</a><br>";
    
          echo "<b>Company Name:</b> " . $myrow['company'] . "<br>";
          echo "<b>Address:</b> " . $myrow['address'] . "<br>";
          echo "<b>City:</b> " . $myrow['city'] . "<br>";
          echo "<b>Province/State:</b> " . $myrow['provstate'] . "<br>";
          echo "<b>Country:</b> " . $myrow['country'] . "<br>";
          echo "<b>Postal/Zip Code:</b> " . $myrow['postalzip'] . "<br>";
          echo "<b>Default Courier:</b> " . $myrow['defcourier'] . "<br>";
          echo "<b>Courier Account:</b> " . $myrow['courieracct'] . "<br>";
  
          echo "<b>Telephone:</b> " . $myrow['phone1'] . "<br>";
          echo "<b>Cell Phone:</b> " . $myrow['phone2'] . "<br>";
  
          echo "<b>Discount Code:</b> " . $myrow['dct'] . "<br>";
          echo "<b>Promo Code:</b> " . $myrow['coupon'] . "<br>";
        } 
      }
  
      // Disconnect from the database.
      mysql_close ($DBConn);
    }
    else {
        echo "No client specified.</p></div>";
    }

    ?>

  </div>
</body>
</html>

