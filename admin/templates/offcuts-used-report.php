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
      <h1>Offcuts Used</h1>
      <div style="width: 600px; margin: 20px auto;">

        <ul>
          <li style="margin-top: 5px;">Choose the starting and ending date for the period you want to see usage for. 
            End dates are included in the report.</li>
          <li style="margin-top: 5px;">The page will show all offcuts which were either CLAIMED and/or USED during that period.</li>
          <li style="margin-top: 5px;">NOTE: The system did not start tracking offcut usage until Nov 1, 2017.</li>
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

        <table border="0" align="center" cellpadding="10">
          <tr>
            <td class="heading">Product<br></td>
            <td class="heading" style="text-align:right;">Square Feet Used<br></td>
            <td class="heading" style="text-align:right;">Pieces Used<br></td>
            <td class="heading" style="text-align:right;">$ Value Used<br></td>
          </tr>
          <tr>
            <td colspan="4"><hr></td>
          </tr>

          <?php echo $data; // display the products and their ink costs ?>
  
          <tr>
            <td colspan="4" class="lineitem_std"><hr></td>
          </tr>
          <tr>
            <td class="lineitem_std" style="text-align: right; padding-right: 20px;">
              <?php printf("<b>Totals: </b><br><br>"); ?>
            </td>
            <td class="lineitem_std" style="text-align: right; padding-right: 10px;">
              <?php printf("<b>%.3f</b><br><br>", $total_sqft);
              ?>
            </td>
            <td class="lineitem_std" style="text-align: right;">
              <?php
              printf("<b>%d</b><br><br>", $total_items);
              ?>
            </td>
            <td class="lineitem_std" style="text-align: right;">
              <?php
              printf("<b>$%.2f</b><br><br>", $total_dollar_value);
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


