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

    <img src="images/title_faq.gif" width="370" height="18" alt="FREQUENTLY ASKED QUESTIONS">

    <br><br>
    <p><b>Why does it take so long to upload my files?</b></p>

    <p>There are many factors to file upload times.  These include the number and size
    of your files, the speed of your 
    computer, the speed of your connection to the Internet, how busy the Internet is 
    at the time you are uploading the file, the speed of our connection to the Internet, 
    the speed of our computer, and how many other clients are uploading to our computer 
    at the time of your upload.</p>

    <p>We have a fast T1 connection to the Internet and a powerful server computer
    ready to accept your uploads, and are constantly working to improve upload times.</p>

    <p>You can test out the speed of your Internet connection using this online tool:</p>

    <div style="margin: 0px auto; text-align: center;">
    <a href="http://www.speakeasy.net/speedtest/" target="_blank"><img 
      src="http://www.speakeasy.net/images/speedtest/speedtest_120x60.gif" 
      width="120" height="60" border="0" alt="Speakeasy Speed Test" target="_blank"></a>
    </div>

    <br><br>
    <p><b>Why am I not receiving confirmation emails about my orders?</b></p>

    <p>Your spam filter may be rejecting our confirmation emails as potential spam.  To
    ensure that you receive confirmation emails in your inbox, please add signboom@signboom.com 
    into your email contacts list.</p>

    <!--
    <br>
    <p><b>The forms on the Rigid and Banner order pages do not have all the boxes laid out on one row. Will you be fixing this?</b></p>

    <p>Some clients are seeing rows in the order forms broken into multiple lines because the font on their system is too large 
    for the browser to fit the content of long rows into the width of our order form.  To prevent this, we are changing the code to 
    handle all the different combinations of browsers, operating systems and font sizes which our clients use. In the meantime,
    you may be able to work around this problem. Try setting the browser's font size or fixed-width font size smaller than its 
    current settings.  You can alway restore them when you are finished using the form.</p>

    <p>On Safari:  Click <i>Edit - Preferences - Appearance</i>, and then choose a smaller <i>Standard font</i> and/or a smaller 
    <i>Fixed-width font</i>.</p>

    <p>On Windows IE7: Click <i>Page - Text Size - Medium</i> or <i>Page - Text Size - Smaller</i> or <i>Page - Text Size - Smallest</i>.</p> 

    <p>On Firefox:  Click <i>View - Zoom - Zoom Out</i>.</p>

    <br>
    <p><b>The forms on the Rigid and Banner are displayed off to the right of the logo instead of beneath it. Will you be fixing this?</b></p>

    </p>This is related to the problem above with the font size.  Some browsers (like Firefox) react to the problem by moving the whole
    table off to the side.  We are working to correct this.  In the meantime, you can work around this using the suggestions in the question
    above.</p>
    -->

    <br><br><br>

    </div>

    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>
