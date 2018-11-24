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

    <img src="images/title_working_large.gif" width="189" height="18" alt="WORKING LARGE">

    <p>On very large projects you may run into some limitations on art board sizes.  When this occurs, 
    you will need to work at a scale of 2:1 or 4:1 etc. in order to meet your requirements.  All of 
    the usual PDF preparation rules apply, with the exception that when you select the resolution of 
    the images you are incorporating into the design, you must take into account that we will be 
    enlarging the images at our end, so you must include higher resolution images.</p>

    <p>For example, if you are designing at 50%, then for close up work you must begin with or down-sample 
    to no less than 360ppi.  Enlarged, this will give us 180ppi which works very well.  Given that this 
    only becomes an issue at very large output sizes, lower resolution images may be fine.  A billboard 
    with a viewing distance of 20+ feet looks fine at 45ppi finished size, so you would only require a 
    90ppi image if designing at 50%.</p>

    <p>Before uploading your order, view your PDF in Adobe Reader (not your design application).  Set the 
    view to 100% if designing at 1:1, 200% if designing at 2:1 etc.  Pan around and ensure your resolution 
    will be acceptable to your client.  Make sure all elements are present and appear as you expect them to.</p>

    </div>

    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>
