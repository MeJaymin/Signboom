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

    <img src="images/title_how_to_order.gif" width="177" height="18" alt="HOW TO ORDER">

    <div style="width: 430px;">

    <p>To place an order, you will need to <a class="inline" href="signup.php">create an account</a>.</p> 

    <p>Once your account is created, log in using the large black <b>Log In</b> button at the top left of the 
    web site.<p>

    <p>Then click the large black button at the top left of the site, labelled with the type of product you 
    wish to buy: <b>Roll Stock</b>, <b>Rigid Signs</b> or <b>Banners</b>.  This will bring up an order page 
    for that type of material.</p>

    <p>Choose the products you wish to order, using the drop-down menus in the <b>Product</b> column.</p>

    <p>If you are purchasing Banners or Rigid Signs, choose the options you wish applied to that product, using 
    the drop-down menu under the <b>Finishing</b> column.  If you are purchasing Roll Stock, tick the check boxes
    that correspond to the finishing options you want (Lamination, Back Lighting, and/or Contour Cutting).
    The form will prevent you from choosing a finishing option that does not apply to that product.</p>

    <p>Enter the dimensions of your art work.</p>

    <p>Enter the quantity you wish to order.</p>

    <p>Specify the following information in the boxes at the bottom of the order form:</p>
    <ul>
    <li>Shipping Address</li>
    <li>Shipping Documents (optional)</li>
    <li>Print Services Speed</li>
    <li>Delivery Method</li>
    <li>A unique reference number of your own choice for this order</li>
    </ul>
    <br>

    <p>Click the large <b>Quote Order</b> button (which is half-way down the screen) to get a quote.</p>

    <p>Once you are happy with the quote, you can specify the art work using the <b>Browse</b> button in each row of 
    the order form. Once you have specified all the files for the art work, click the <b>Submit Order</b> button.
    (The <b>Quote Order</b> button turns into a <b>Submit Order</b> button, once the quote has been calculated.  
    It will turn back into a <b>Quote Order</b> button if you change any of the line items.)</p>

    <p>When you click the <b>Submit Order</b> button, a popup window will come up, which will load your files onto
    our server.  If you have large files, or many files, this could take five minutes or more.  The window will close
    when it is finished uploading the files, and you will be redirected to a "Thank you" page and an email confirming
    your order will be sent to you.  If the upload is taking much longer than you think it should, a temporary
    network disconnection may have interrupted your upload process.  In which case you can go back to the order form
    and submit the order again, or you can contact us for assistance.</p>
    </div>

    </div>

    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>
