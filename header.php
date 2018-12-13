<?php
error_reporting(0);
ini_set("url_rewriter.tags","");
ini_set(session.use_trans_sid, false);
//session_save_path("/home/users/web/b516/as.signboom/phpsessions");
//session_save_path("C:\xampp\tmp");
//session_save_path("/opt/lampp/temp/");
session_save_path("/var/www/html/");
session_start();
 ?>
<html>
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
</head>
<body>
<div id="header">

  <div id="header_left">
    <div id="logo">
      <a href="index.php"><img border="0" src="images/logo3d.jpg" width="308" height="54" alt="Signboom.com: Your online print engine"></a>
    </div>
    <div class="spacer"></div>
    <div id="buttons">
      <br><br>
      <a href="all_order.php" onClick="this.blur()" 
        onMouseOver="document.order_image.src='images/order_form_large_hover.gif';" 
        onMouseOut="document.order_image.src='images/order_form_large.gif';"> 
        <img border="0" src="images/order_form_large.gif" name="order_image" width="230" height="38" alt="Order Page">
      </a>
    </div>
  </div>

  <div id="header_right">
    <div id="header_image">
      <img border="0" src="images/gears_bw_sm_ds.jpg" width="350" height="165" alt="">
    </div>
  </div>

  <span class="clearboth"></span>
  <div class="spacer"></div>

  <div id="main_menu">
    <br>
      <a href="index.php" onClick="this.blur()" 
        onMouseOver="document.home_image.src='images/menu_home_hover.jpg';" 
        onMouseOut="document.home_image.src='images/menu_home.jpg';"> 
        <img border="0" src="images/menu_home.jpg" name="home_image" width="94" height="108" alt="Home">
      </a> 
      <a href="how_it_works.php" onClick="this.blur()" 
        onMouseOver="document.works_image.src='images/menu_how_it_works_hover.jpg';" 
        onMouseOut="document.works_image.src='images/menu_how_it_works.jpg';"> 
        <img border="0" src="images/menu_how_it_works.jpg" name="works_image" width="94" height="108" alt="How It Works">
      </a> 
      <a href="how_to_order.php" onClick="this.blur()" 
        onMouseOver="document.order_image.src='images/menu_how_to_order_hover.jpg';" 
        onMouseOut="document.order_image.src='images/menu_how_to_order.jpg';"> 
        <img border="0" src="images/menu_how_to_order.jpg" name="order_image" width="94" height="108" alt="How To Order">
      </a> 
      <a href="green_inks.php" onClick="this.blur()" 
        onMouseOver="document.green_image.src='images/menu_green_inks_hover.jpg';" 
        onMouseOut="document.green_image.src='images/menu_green_inks.jpg';"> 
        <img border="0" src="images/menu_green_inks.jpg" name="green_image" width="94" height="108" alt="Green Inks">
      </a> 
      <?php
      if (isset($_SESSION['MM_Username'])) {  
        printf("<a href=\"customer.php\" onClick=\"this.blur()\" ");
          printf("onMouseOver=\"document.create_image.src='images/menu_my_account_hover.jpg';\" ");
          printf("onMouseOut=\"document.create_image.src='images/menu_my_account.jpg';\"> ");
          printf("<img border=\"0\" src=\"images/menu_my_account.jpg\" name=\"create_image\" width=\"94\" height=\"108\" alt=\"Create Account\"> ");
        printf("</a> ");
      }
      else {
        printf("<a href=\"signup.php\" onClick=\"this.blur()\" ");
          printf("onMouseOver=\"document.create_image.src='images/menu_signup_hover.jpg';\" ");
          printf("onMouseOut=\"document.create_image.src='images/menu_signup.jpg';\"> ");
          printf("<img border=\"0\" src=\"images/menu_signup.jpg\" name=\"create_image\" width=\"94\" height=\"108\" alt=\"Create Account\"> ");
        printf("</a> ");
      }
      ?>
      <a href="faq.php" onClick="this.blur()" 
        onMouseOver="document.faq_image.src='images/menu_faq_hover.jpg';" 
        onMouseOut="document.faq_image.src='images/menu_faq.jpg';"> 
        <img border="0" src="images/menu_faq.jpg" name="faq_image" width="94" height="108" alt="Frequently Asked Questions">
      </a> 
      <a href="contact_us.php" onClick="this.blur()" 
        onMouseOver="document.contact_image.src='images/menu_contact_us_hover.jpg';" 
        onMouseOut="document.contact_image.src='images/menu_contact_us.jpg';"> 
        <img border="0" src="images/menu_contact_us.jpg" name="contact_image" width="94" height="108" alt="Contact Us">
      </a>
  </div>

</div>  <!-- end of header -->

<br class="clearboth">
<div class="spacer"></div>
</body>
</html>
