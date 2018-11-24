<?php
  require_once('Connections/DBConn.php'); 
  require_once('includes/mailord.php'); 
  include('includes/testmode.php');
  include('includes/build_message.php');
  include('includes/getdetail.php');
  include('helpers/db_helper.php');

  if ($this_is_a_test == 1) {
    echo "<script language=\"javascript\">alert(\"The system is in test mode.  Your order has not been placed.\");</script>";
    header("Location: index.php");  // redirect to main site login page
    exit();
  }


  mysqli_select_db( $DBConn, $database_DBConn);

  $Query = 'SELECT * FROM signboom_ordermast WHERE ID = "' . $_GET["order"].'" AND Token = "'.$_GET['dbToken'].'"';
  $result = mysqli_query( $DBConn, $Query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $msg="nothing";
  $dispmsg = "You'll receive an acknowledgement in your email shortly.";   //ta 06/14/2014
  
  if (mysqli_num_rows($result) == 1) {
	if (mysqli_result($result, 0, 'Uploaded') == "Yes") {                                                      //ta 6/14/2014    
		$dispmsg = "Your acknowledgement was sent on " . mysqli_result($result, 0, 'UploadCompletionTime');    //ta 6/14/2014
	}                                                                                                       //ta 6/14/2014
	else                                                                                                    //ta 6/14/2014
	{                                                                                                       //ta 6/14/2014 
      $Qry = 'SELECT * FROM signboom_user WHERE ID = ' . mysqli_result($result, 0, 'UserID');
      $rsUser = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

      $refnum = mysqli_result($result, 0, 'refnum');
      $orderid = mysqli_result($result, 0, 'ID');
      $accountid = mysqli_result($result, 0, 'AcctName');
      $subjectline = "Signboom Order Confirmation #$orderid - $accountid -  '$refnum'";

      $detail = array();
      getOrderDetails($orderid, $detail);

      $customer_email = mysqli_result($result, 0, 'email');

      if (mysqli_result($rsUser, 0, 'htmlmail') != "Yes") {
        $msg = bldmsg($result, $detail, $rsUser);
        mail($customer_email, $subjectline, $msg, "From: " . "signboom@signboom.com\r\n");
      } else {
        $intro = ""; /* don't remove this; it is a cue to bldhtml to put in the "thank you for your order" information */
        $message[1]['content_type'] = 'text/html; charset=iso-8859-1';
        $message[1]['filename'] = '';
        $message[1]['no_base64'] = TRUE;
        $message[1]['data'] = bldhtml($result, $detail, $rsUser, $intro);
        $out = mp_new_message($message);
        mail($customer_email, $subjectline, $out[0], "From: signboom@signboom.com"."\r\n".$out[1]);
      }

      ((mysqli_free_result($rsUser) || (is_object($rsUser) && (get_class($rsUser) == "mysqli_result"))) ? true : false);                                                                 //ta 6/14/2014

      $Query = 'UPDATE signboom_ordermast SET Uploaded = "Yes", UploadCompletionTime = DATE_SUB(NOW(), INTERVAL 3 HOUR) WHERE ID = "' . $_GET["order"].'" AND Token = "'.$_GET['dbToken'].'"'; 
      $result = mysqli_query( $DBConn, $Query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

      // If current queue of a line item is Upload, change it to Proof. Some line items (stands, accessories) will already be in Finish queue.
      $Query2 = 'UPDATE signboom_linedetail SET currentqueue = "Proof" WHERE orderid = ' . $_GET["order"] . ' AND currentqueue = "Upload"';
      $result = mysqli_query( $DBConn, $Query2) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
	}                                                                                  //ta 6/14/2014
  } else {
    print ("Cannot find the order: ".$Query);
  }
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

  function BuildName($q, $w, $h, $f) {

  }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <title>Signboom: Your Online Print Engine</title>
  <script src="script/utility.js" LANGUAGE="JavaScript" TYPE="text/javascript"></script>

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

<?php require_once('Connections/DBConn.php'); ?>
<?php require_once('Connections/doLogin.php'); ?>

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

    <img src="images/title_thank_you_order.gif" width="344" height="18" alt="THANK YOU FOR YOUR ORDER">

    <br><br><br><br>
    <?php echo $dispmsg; 			//ta 06/14/2014 ?>
    <br><br><br><br>

   </div>

    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>


