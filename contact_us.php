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

    <img src="images/title_contact_us.gif" width="147" height="18" alt="CONTACT US">

    <div style="float: right; margin-left: 10px; margin-bottom: 10px; text-align: center;">
      <a href="images/signboom-building.jpg"><img src="images/signboom-building-vsmall.jpg" width="241" height="250" alt="Signboom Wholesale Printing in Langley"></a>
      <br>
      (Click to Enlarge)
    </div>

    <p><b>Address:</b><br>
    Signboom Industries Ltd.<br>
    B125 - 5525 272nd Street<br>
    Langley, BC, Canada<br>
    V4W 1P1</p>

    <p><b>Telephone:</b> 604-881-0363</p>

    <p><b>Fax:</b> 604-677-6652</p>

    <p><b>Email:</b> <a class="inline" href="mailto:info@signboom.com">info@signboom.com</a></p>

    <h2 style="margin-top: 40px;">Our Hours</h2>

    <p>We are open from 8:00 am to 4:00 pm, Monday to Friday.</p>
<!--
    <h2 style="margin-top: 30px;">Summer Hours</h2>

    <p>Please note our summer hours are in effect from June 1 to August 31, 2016.</p>
    <table>
      <tr><td>Monday</td><td>5:00am to 7:00pm</td></tr>
      <tr><td>Tuesday</td><td>5:00am to 7:00pm</td></tr>
      <tr><td>Wednesday</td><td>5:00am to 7:00pm</td></tr>
      <tr><td>Thursday</td><td>5:00am to 7:00pm</td></tr>
      <tr><td>Friday</td><td>7:00am to 4:00pm</td></tr>
    </table>

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

    <h2>How to Find Us</h2>

    <p>Reminder: We are now at a new location. We're less than 2 km from the 264th Street exit off Highway 1 in Langley.</p>
 
    <p>To reach us, take the 264 Street exit going north and then turn right on 56 Avenue and drive to 272 Street. Turn right onto 272 and then take the second driveway on your right. We're the last unit on your right-hand side.</p>
<!--
    <p><b><a href="https://www.google.ca/maps/place/5525+272+St,+Langley,+BC+V4W+1P1/@49.1033992,-122.4884083,14z/data=!4m5!3m4!1s0x5485cc92eb79d839:0xcd3e97cbb142b0d0!8m2!3d49.1033992!4d-122.4708988" target="_blank">Map to our Location</a></b>
-->

    <div style="text-align: center;">
    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d20896.94379877043!2d-122.4884083!3d49.1033992!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x5485cc92eb79d839%3A0xcd3e97cbb142b0d0!2s5525+272+St%2C+Langley%2C+BC+V4W+1P1!5e0!3m2!1sen!2sca!4v1464737928941" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
    </div>

    <div style="text-align: center; margin-top: 20px;">
      <a href="images/new-signboom-location.jpg"><img src="images/new-signboom-location-medium.jpg" width="600" height="446" alt="Signboom's New Location in Langley"></a>
      <br>
      Signboom's New Location<br>
      (Click to Enlarge)
    </div>

    </div>

    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>
