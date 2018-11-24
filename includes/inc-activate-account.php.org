<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <title>Signboom: Your Online Print Engine</title>
  <link rel="stylesheet" href="signboom.css" type="text/css" title="default_style">

  <!--INTERNET EXPLORER SPECIFIC STYLING CODE FOLLOWS-->
  <!--[if lte IE 7]>
  <link rel="stylesheet" href="ie6_specific.css" type="text/css" title="default_style">
  <!--[else]>
  <link rel="stylesheet" href="signboom.css" type="text/css" title="default_style">
  <![endif]-->

</head>

<body>
  <div id="page">
    <div id="wrapper">

    <?php include ('header.php'); ?>

      <div id="content">
        <?php include ('sidebar.html'); ?>

        <img src="images/title_create_account.gif" width="207" height="18" alt="CREATE ACCOUNT">
        <br><br>

        <div style="width: 430px;">
          <?php echo $error; ?>
        </div>

      </div>

      <?php
        include ('footer.html');
      ?>

    </div>
  </div>

</body>
</html>


