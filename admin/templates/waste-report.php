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
      <h1>Waste Report</h1>
      <div style="width: 600px; margin: 20px auto;">

        <ul>
          <li style="margin-top: 5px;">Choose the starting and ending date for the period you want totals for. 
            End dates are included in the report.</li>
          <li style="margin-top: 5px;">The page will show all orders which were COMPLETED during that period.</li>
          <li style="margin-top: 5px;">If a file that is completed is part of an order that is NOT completed, that 
            file will NOT be included here.</li>
          <li style="margin-top: 5px;">NOTE: The system did not start tracking the COST of waste until Sunday 
            September 23, 2012. So the total waste cost for all orders prior to then will show as zero.</li>
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
          <?php echo "<b>Orders Completed between $start_date to $end_date</b><br><br>";?>
        </form>

        <table border="0" align="center" cellpadding="5">
          <tr>
            <td class="heading">Product<br></td>
            <td class="heading" style="text-align:right;">Printed Area<br></td>
            
            <td class="heading" style="text-align:right;">Waste Area<br></td>
            <td class="heading" style="text-align:right;">Waste %<br></td>
            <td class="heading" style="text-align:right;">Waste Cost ($)<br></td>
          </tr>
          <tr>
            <td colspan="5"><hr></td>
          </tr>

          <?php echo $data; // display the products and their printed/waste totals ?>
  
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
              printf("<b>%.0f</b><br><br>", $total_waste_area_sqft);
              ?>
            </td>
            <td></td>
            <td class="lineitem_std" style="text-align: right;">
              <?php
              printf("<b>%.2f</b><br><br>", $total_waste_cost);
              ?>
            </td>
          </tr>
        </table>

        <?php if ($display_notes): ?>
        <b>*** NOTES</b>:
        <br><br>
        The following orders were placed prior to Sept 23, 2012, when tracking of waste costs 
        (with new waste area calculations) started.  The waste area for those orders was included 
        in the <b>Waste Area</b> column and the <b>Waste %</b> column, but it was calculated with 
        the <span style="color: #cc0000; font-weight: bold;">old</span> waste code.  The waste cost 
        is not available for these orders, so the <b>Waste Cost</b> column for that product will 
        be lower than the correct value.
        <br><br>
        <table align="center" cellpadding="8">
          <tr>
            <td class="heading">Order Id</td>
            <td class="heading">File Id</td>
            <td class="heading">Product</td>
            <td class="heading">Printed Area</td>
            <td class="heading">Waste Area</td>
            <td class="heading"><!-- Notes Column--></td>
          </tr>
          <tr>
            <td colspan="6"><hr></td>
          </tr>
          <?php echo $notes; // display the notes; ?>
        </table>
        <?php endif; ?>

      </div>
    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


