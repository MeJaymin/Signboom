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

      <img src="images/title_wholesale_printing.gif" width="251" height="18" alt="WHOLESALE PRINTING">

      <p>Looking for fast turn-around, reliable quality, and low prices for your wholesale print jobs?
      You've come to the right place.</p>
  
      <p>At Signboom, we offer you the convenience of online ordering, superior quality, fast turnaround 
      and low prices, for all your large format print jobs.</p>

      <p>Our state-of-the-art machines can print your design onto a wide selection of rigid, flexible 
      and adhesive products.</p>

      <p>Our proprietary, streamlined web-based ordering 
      system (shown below) provides quality printing at some of the lowest prices available.</p>

<!--
      <h2 style="margin-top: 30px;">Holiday Hours 2017</h2>

<table>
<tr>
<td>Monday, December 18</td>
<td>8:00am  to  4:00pm  (standard hours)</td>
</tr>
<tr>
<td>Tuesday, December 19</td>
<td>8:00am  to  4:00pm  (standard hours)</td>
</tr>
<tr>
<td>Wednesday, December 20</td>
<td>8:00am  to  4:00pm  (standard hours)</td>
</tr>
<tr>
<td>Thursday, December 21</td>
<td>8:00am  to  12:00pm</td>
</tr>
<tr>
<td>Friday, December 22</td>
<td>Closed</td>
</tr>
<tr>
<td>Monday, December 25</td>
<td>Closed    (Statutory holiday)</td>
</tr>
<tr>
<td>Tuesday, December 26</td>
<td>Closed</td>
</tr>
<tr>
<td>Wednesday, December 27</td>
<td>Closed</td>
</tr>
<tr>
<td>Thursday, December 28</td>
<td>Closed</td>
</tr>
<tr>
<td>Friday, December 29</td>
<td>Closed</td>
</tr>
<tr>
<td>Monday, January 1</td>
<td>Closed    (Statutory holiday)<tr>
<td>Tuesday, January 2</td>
<td>8:00am  to  4:00pm  (standard hours)</td>
</tr>
</table>
-->


      <!--
      <h2 style="margin-top: 30px;">Our Hours Have Changed!</h2>
      <p>Starting December 1st, 2016, our new hours are 8:00 am to 4:00 pm, Monday to Friday. Please note that we will be opening one hour later and closing one hour earlier than previously.</p>
      -->

    <h2>How to Find Us</h2>

    <p>Reminder: We are now at a new location. We're less than 2 km from the 264th Street exit off Highway 1 in Langley.</p>
 
    <p>To reach us, take the 264 Street exit going north and then turn right on 56 Avenue and drive to 272 Street. Turn right onto 272 and then take the <b>second</b> driveway on your right. We're the last unit on your right-hand side. There's a map and photos on our <a href="http://signboom.com/contact_us.php">Contact Page</a>.</p>

      <h2>How our Proprietary Order Page Works</h2>

      <p>For each item in your order, choose from six product categories we offer: adhesive, rigid, banners, specialty, stands, and accessories.  Then choose from multiple product options within each category. Click the <b>Edit</b> button to display a list of the many finishing options we offer for the selected product (e.g. contour cutting, lamination, hanging hardware) and tick off the finishing options you want. After you've entered your sign dimensions and quantities, and ticked off the kind of packing, delivery and turnaround you need, the <b>Quote Order</b> button gives you a complete quote, including taxes. Then it's just a matter of browsing to the appropriate artwork files on your computer and a final click places your order.</p> 
  
      <p><a class="inline" href="signup.php">Sign-up for an account</a> to have instant
      access to online quoting and ordering.</p>
  
      <img border="0" src="images/order-form-screenshot.jpg" width="700" height="838" alt="Screenshot of Signboom's Proprietary Signage Ordering System">

    </div>
  
    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>
