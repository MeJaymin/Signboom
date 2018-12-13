<!doctype html>

<html>

  <head>
    <meta charset="utf-8">
    <title>Signboom: Version 1.5 Administration</title>
    <?php include ('head.html'); ?>
  </head>

<body>

  <div id="page">

    <?php include ('banner-menu.php'); ?>

    <div id="content">
      <h1>Order Report</h1>
      <div style="width: 600px; margin: 20px auto;">

        <ul>
          <li style="margin-top: 5px;">Choose the starting and ending date for the period you want data for. 
            Orders placed on the end date are included in the report.</li>
          <li style="margin-top: 5px;">The spreadsheet will include all orders which were PLACED during that period.</li>
        </ul>
        <br>
        <form id="my_form" name="my_form" action="" method="get">
          <b>Start Date:</b> <input style="width: 100px;" name="StartDate" value="<?php echo $start_date ?>" > 
          <input type=button value="Calendar" onclick="displayDatePicker('StartDate', this);">
          &nbsp;&nbsp;&nbsp;&nbsp;
          <b>EndDate:</b> <input style="width: 100px;" name="EndDate" value="<?php echo $end_date ?>" > 
          <input type=button value="Calendar" onclick="displayDatePicker('EndDate', this);">
          <br><br>
          <input style="float: right;" class="button" type="submit" name="Display Report" value="Display Report">
          <br style="clear: both;"><br><br>
        </form>

      </div>
    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


