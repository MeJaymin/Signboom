<?php require_once('Connections/DBConn.php'); ?>
<?php require_once('Connections/doLogin.php');?>
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

    <img src="images/title_log_in.gif" width="76" height="18" alt="LOG IN">
    <?php
    if (strlen($errMsg) > 0) {
      printf("<br><br><span class=\"highlighted\">\n");
      echo $errMsg; 
      printf("</span>\n");
    }
    ?>

    <br><br>
    <form name="form1" method="POST" action="<?php echo $loginFormAction; ?>">
      Email Address: <input name="email" type="text" id="email" value="<?php echo $userid; ?>"><br><br>
      Password: <input name="pw" type="password" id="pw"><br><br>
      <input name="Submit" type="submit" value="Log In">
      <input name="xferpage" type="hidden" id="xferpage" value="<? print $accesscheck; ?>"><br><br>

      Don't have an account?
      <a href="signup.php">Create One Here</a><br><br>
      Forgot your password?
      <a href="#"  onClick="popUpWindow('forgot.php', 30, 30, 400, 250)">Reset It Here</a>
    </form>

   </div>

    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>


