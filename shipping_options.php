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

    <img src="images/title_shipping_options.gif" width="214" height="18" alt="SHIPPING OPTIONS">

    <p>We offer you the option of picking up your signs yourself, having your designated courier pick them
    up, or or having us arrange shipping for you. You can select your choice in the order form. A map
    to our location is available on the <a href="contact_us.php">Contact Us</a> page.</p>

    <p>If you are picking up the signs yourself, you can choose to have them unpackaged, to reduce waste.  
    Or we can package them, as we would for courier pickup or standard shipping.</p>

    <p>The order form will display the shipping cost, when you use it to calculate the cost of your order.</p>

    </div>
  
    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>
