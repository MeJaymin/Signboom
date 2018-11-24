<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <title>Signboom Creative Services</title>

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

    <img src="images/title_creative_services.gif" width="231" height="18" alt="CREATIVE SERVICES">


    <p><b>cre·a·tive:</b> having the ability to create; original, expressive and imaginative</p>

    <p>Graphics are as important to your business as having the right plan, the right employees 
    and the right clientele. From the logo on your business card, to the decals on your vehicle, 
    to the sign above your door, graphics are the first thing a potential client will see about 
    your company.</p>

    <p>Effective design looks good and enhances and strengthens your message. Our in-house 
    creative department will work with you to develop the perfect image for your business, 
    conference, trade show or special event.</p>

    <p>Signboom Creative Services is here to assist you with all your creative needs.  We
    specialize in:</p>

    <ul>
    <li><b>Signage:</b></li>
    <ul>
      <li>Channel letters &bull; Fascia &bull; Pylons &bull; Banners</li>
    </ul>
    <li><b>Corporate Identity:</b></li>
    <ul>
      <li>Company logo · Stationary · Menus · Product Branding</li>
    </ul>
    <li><b>Environmental Graphics:</b></li>
    <ul>
      <li>Three-Dimensional Logos · Dedication Plaques · Donor recognition systems · Wayfinding Systems</li>
    </ul>
    <li><b>Marketing Materials:</b></li>
    <ul>
      <li>Email Fliers · Brochures · Point-of-Purchase Displays</li>
    </ul>
    </ul>

    <br>
    <p>For more information please call 604-881-0363 or 
    <a href="mailto:creative@signboom.com">email us</a>.</p>

    <br>
    <p><b>Creative Services: The Fine Print</b></p>
    <ul>
    <li>Uploading a file through this service DOES NOT constitute an order. This service is 
    strictly for items be supplied to assist with your creative design.</li>
    <li>If you require a file to be fixed, upload it to the creative team. They will fix it 
    and send it back to you. You will then need to re-upload the order through the online 
    ordering system.</li>
    <li>Creative Services are in addition to any charges related to your order. These 
    charges will be added to your invoice.</li>
    </ul>

    <br>
    <p><b>Creative Services File Upload</b></p>

    <iframe 
    style="margin-left:20px; background-color:#ffffff; overflow-x:hidden; display:block;" 
    id="file_upload_iframe" name="file_upload_iframe" 
    src="https://www.yousendit.com/v1/ibox.php?sitebox=2006805&sh=6f3a4aff5e84691f58560a40e2b668e4&send_notification=true" 
    width="350" height="380" marginwidth="0" align="middle" frameborder="0" allowtransparency="true">
    </iframe>    

    </div>
  
    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>
