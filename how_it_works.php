<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <title>Signboom: Your Online Print Engine</title>

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

    <img src="images/title_how_it_works.gif" width="171" height="18" alt="HOW IT WORKS">

    <p>To achieve our extremely fast turnaround and excellent pricing, we have adopted a web-based PDF 
    workflow.  Our order/quote pages provide product descriptions and pricing interactively, allowing 
    us to process your order quickly.  You upload your PDF files through our web site, choose the 
    material and quantities you wish to print, select your shipping method, and we do the rest.</p>

    <p>You can count on us for a smooth order process, fast printing and dependable service.</p>

    <p>Here's how it works:</p>
    <ul>
    <li>You sign up online for a free account with us.</li>
    <li>Once you have an account, you log in to our site and use our convenient and powerful order/quote 
    pages to specify your order, upload your files, and obtain a quote and delivery date.  (We print most 
    orders the next business day.  If any delays are anticipated, we will notify you promptly.)</li>
    <li>We send you an email upon successful upload of your order.</li>
    <li>We send you a second email once your file has been reviewed and 
    is queued for printing.  (We do not send proofs of uploaded files.  If your file does not print as 
    viewed in Acrobat Reader we will reprint at no charge.)</li>
    <li>We print your job and inspect it.  We maintain our equipment to high standards to ensure quality.  
    We produce in-house colour ICC profiles for each media and ink to ensure consistency.</li>
    <li>We send you a third email when your job has printed, been inspected and is ready for shipping.</li>
    <li>We either hold your items for pickup or ship them according to your request.</li>
    <li>Out payment terms are net 15 days on approved credit, or we can process all oustanding amounts automatically 
    on your credit card on a weekly basis.  We will contact you prior to printing your first order, in order 
    to confirm your desired payment method.</li>
    <li>Flexible/adhesive products are delivered on a roll ready for trimming, mounting, finishing and 
    incorporating into your finished product.  Rigid orders are cut to predetermined sizes as ordered. 
    We also offer finished banners and accessory products to finish banners yourself.</li>
    </ul>


    <p>All you need to do is:</p>
    <ul>
    <li>Prepare your artwork and receive approval from your client.</li>
    <li>Save the approved art as a PDF file and review it in Adobe Reader before uploading.</li>
    <li>Use our order/quote pages to ensure you are getting the most useable product out of the width of the 
    print material.</li>
    <li>Submit your files, and select your materials and shipping method on our web site.</li>
    </ul>

    <p>We offer the following support to new clients:</p>

    <ul>
    <li>We discuss with you what to expect from CMYK printing.</li>
    <li>We provide detailed information explaining how to format your PDF files for predictable results 
    and fast turnaround.</li>
    </ul>

    <p>Visit our <a class="inline" href="how_to_order.php">How to Order</a> page for detailed instructions
    on how to use our order forms, or call or email us with your questions.</p>

    <p><a class="inline" href="signup.php">Sign up for an account</a> today to access our 
    Order and Quote pages.</p>
    <br>

    </div>
  
    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>
