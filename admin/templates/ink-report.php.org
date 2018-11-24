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
      <h1>Ink Report</h1>
      <div style="width: 600px; margin: 20px auto;">

        <ul>
          <li style="margin-top: 5px;">Choose the starting and ending date for the period you want totals for. 
            End dates are included in the report.</li>
          <li style="margin-top: 5px;">The page will show all orders which were COMPLETED during that period.</li>
          <li style="margin-top: 5px;">If a file that is completed is part of an order that is NOT completed, that 
            file will NOT be included here.</li>
          <li style="margin-top: 5px;">NOTE: The system did not start tracking the cost of ink until <b>DATE TBD - INK TRACKING NOT LAUNCHED AS OF May 14, 2013</b>
            . So the ink cost for all orders prior to then will show as zero.</li>
        </ul>
        <br>
        <form id="my_form" name="my_form" action="" method="get">
          <b>Start Date:</b> <input style="width: 100px;" name="StartDate" value="<?echo $start_date ?>" > 
          <input type=button value="Calendar" onclick="displayDatePicker('StartDate', this);">
          &nbsp;&nbsp;&nbsp;&nbsp;
          <b>EndDate:</b> <input style="width: 100px;" name="EndDate" value="<? echo $end_date ?>" > 
          <input type=button value="Calendar" onclick="displayDatePicker('EndDate', this);">
          <br><br>
          <input style="float: right;" class="button" type="submit" name="Display Report" value="Display Report">
          <br style="clear: both;"><br><br>
          <?php echo "<b>Orders Completed between $start_date to $end_date</b><br><br>";?>
        </form>

        <table border="0" align="center" cellpadding="5">
          <tr>
            <td class="heading">Product<br></td>
            <td class="heading" style="text-align:right;">Printed Area<br></td>
            <td class="heading" style="text-align:right;">Ink Cost ($)<br></td>
          </tr>
          <tr>
            <td colspan="5"><hr></td>
          </tr>

          <?php echo $data; // display the products and their ink costs ?>
  
          <tr>
            <td colspan="5" class="lineitem_std"><hr></td>
          </tr>
          <tr>
            <td class="lineitem_std" style="text-align: right; padding-right: 20px;">
              <?php printf("<b>Totals: </b><br><br>"); ?>
            </td>
            <td class="lineitem_std" style="text-align: right; padding-right: 10px;">
              <?php printf("<b>%.0f</b><br><br>", $total_printed_area_sqft);
              ?>
            </td>
            <td class="lineitem_std" style="text-align: right;">
              <?php
              printf("<b>%.2f</b><br><br>", $total_ink_cost);
              ?>
            </td>
          </tr>
        </table>

      </div>
    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


