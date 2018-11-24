<?php 
require('authprodn.php');

// Check whether user has requested that results be filtered.
if (isset($_GET['form_team'])) 
  $team = $_GET['form_team'];
else
  $team = "ALL";

/*
if (isset($_GET['form_printer'])) 
  $printer = $_GET['form_printer'];
else 
  $printer = "ALL";
*/

if (isset($_GET['form_custom_start'])) 
  $custom_start = $_GET['form_custom_start'];
else 
  $custom_start = "";

if (isset($_GET['form_custom_end'])) 
  $custom_end = $_GET['form_custom_end'];
else 
  $custom_end = "";

include('includes/date_picker.htm');
include('includes/dashboard_completed_orders.php');
include('includes/dashboard_ontime_orders.php');
mysql_select_db($database_DBConn, $DBConn);
$my_debug = 0;

calculateAllOnTime();
calculateAllCompleted();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
  <title>Dashboard</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="production-new.css" rel="stylesheet" type="text/css">
  <script type = "text/javascript">
    //Function that gets run whenever date_picker is closed.
    function datePickerClosed(dateField) {
      document.filter_controls.submit();
    }
  </script>
</head>

<body>

  <div style="width: 1200px; margin: 0px auto; text-align: center;">

    <div style="float: left; margin-top: 20px;"><img src="../../images/logo3d.gif" width="308" height="54"></div>
    <div style="float: right;"><h1>Order Processing System: Dashboard</h1></div>
    <?php include('menu.html');?>
    <br><br>

    <?php 
    date_default_timezone_set('America/Los_Angeles'); 
    $datetime = date("l M j, Y - g:i a"); 
    ?>

  <div style="margin: 0px auto; text-align: center;">

    <!-- Div to group the four inputs and the two tables those inputs apply to. -->
    <div style="display: inline-block; background-color: #EEEEEE; padding: 10px; text-align: center; border: solid 1px #CCCCCC;">

    <form name="filter_controls" method="get" action="<?php echo $PHP_SELF;?>">

      <div style="float: left; margin-bottom: 25px;">
      <b>Type:</b> 
      <select class="select160" name="form_team" id="form_team" onChange="this.form.submit();">
        <option value="ALL" <?php if ($team == "ALL") echo "SELECTED"; ?>>ALL</option>
        <option value="ONLINE"  <?php if ($team == "ONLINE")  echo "SELECTED"; ?>>ONLINE</option>
        <option value="OFFLINE"  <?php if ($team == "OFFLINE")  echo "SELECTED"; ?>>OFFLINE</option>
      </select>
      </div>


      <?php if (0): ?>
      <div style="float: left; margin-left: 25px; margin-bottom: 20px;">
      <b>Printer:</b> 
      <select class="select160" name="form_printer" id="form_printer" onChange="this.form.submit();">
        <option value="ALL"   <?php if ($printer == "ALL")   echo "ALL";      ?>>ALL</option>
        <option value="VUTEK" <?php if ($printer == "VUTEK") echo "SELECTED"; ?>>VUTEK</option>
        <option value="HP"    <?php if ($printer == "HP")    echo "SELECTED"; ?>>HP</option>
      </select>
      </div>
      <?php endif; ?>

      <div style="float: left; margin-left: 25px; margin-bottom: 20px;">
        <b>Custom Period:</b> 
        <input name="form_custom_start" size="10" value="<?echo $custom_start ?>" onChange="this.form.submit();"> 
        <input type=button value="Calendar" onclick="displayDatePicker('form_custom_start', this);">
      </div>

      <div style="float: left; margin-left: 5px; margin-bottom: 20px;">
        <b>to</b> 
        <input name="form_custom_end" size="10" value="<? echo $custom_end ?>" onChange="this.form.submit();"> 
        <input type=button value="Calendar" onclick="displayDatePicker('form_custom_end', this);">
      </div>

    </form>
    <br style="clear: both;">

    <table class="withborder" cellpadding="5">
      <tr>
        <td class="table_title" rowspan="2">Complete<br>Summary</td>
        <td class="heading" colspan="4">Total Complete</td>
      </tr>
      <tr>
        <td class="heading">Orders</td>
        <td class="heading">Files</td>
        <td class="heading">Sq Ft</td>
        <td class="heading">Waste</td>
      </tr>
      <tr>
        <td class="heading">Average Per Day</td>
        <td><?php echo $orders_complete_avg ?></td>
        <td><?php echo $files_complete_avg ?></td>
        <td><?php echo $sqft_complete_avg ?></td>
        <td><?php echo $waste_complete_avg ?></td>
      </tr>
      <tr>
        <td class="heading">Today</td>
        <td><?php echo $orders_complete_today ?></td>
        <td><?php echo $files_complete_today ?></td>
        <td><?php echo $sqft_complete_today ?></td>
        <td><?php echo $waste_complete_today ?></td>
      </tr>
      <tr>
        <td class="heading">Yesterday</td>
        <td><?php echo $orders_complete_yesterday ?></td>
        <td><?php echo $files_complete_yesterday ?></td>
        <td><?php echo $sqft_complete_yesterday ?></td>
        <td><?php echo $waste_complete_yesterday ?></td>
      </tr>
      <tr>
        <td class="heading">This Week</td>
        <td><?php echo $orders_complete_this_week ?></td>
        <td><?php echo $files_complete_this_week ?></td>
        <td><?php echo $sqft_complete_this_week ?></td>
        <td><?php echo $waste_complete_this_week ?></td>
      </tr>
      <tr>
        <td class="heading">This Month</td>
        <td><?php echo $orders_complete_this_month ?></td>
        <td><?php echo $files_complete_this_month ?></td>
        <td><?php echo $sqft_complete_this_month ?></td>
        <td><?php echo $waste_complete_this_month ?></td>
      </tr>
      <tr>
        <td class="heading">Last Month</td>
        <td><?php echo $orders_complete_last_month ?></td>
        <td><?php echo $files_complete_last_month ?></td>
        <td><?php echo $sqft_complete_last_month ?></td>
        <td><?php echo $waste_complete_last_month ?></td>
      </tr>
      <tr>
        <td class="heading">Custom Period</td>
        <td><?php echo $orders_complete_custom ?></td>
        <td><?php echo $files_complete_custom ?></td>
        <td><?php echo $sqft_complete_custom ?></td>
        <td><?php echo $waste_complete_custom ?></td>
      </tr>
    </table>

    <br style="clear: both;">
  </div> <!-- end of div which groups inputs and tables they apply to -->

  <br><br>

  <table class="withborder" cellpadding="5">
  <tr>
    <td class="table_title">% On Time<br>Summary</td>
    <td class="dark_grey_cell">Target</td>
    <td class="dark_grey_cell">MTD</td>
    <td class="dark_grey_cell">AVG</td>
    <?php
    for ($j = 1; $j <= 12; $j++) {
      printf("<td class=\"heading\">%s</td>", $month_text[$j]); 
    }
    ?>
  </tr>
  <tr>
    <td class="heading">On Time</td>
    <td class="inverse"><?php printf("%.0f", $target_ot); ?></td>
    <td class="inverse"><?php printf("%.0f", $mtd_ot); ?></td>
    <td class="inverse"><?php printf("%.0f", $avg_ot); ?></td>
    <?php
    for ($j = 1; $j <= 12; $j++) {
      printf("<td class=\"grey_col\">%.0f</td>", $month_ot[$j]); 
    }
    ?>
  </tr>
  <tr>
    <td class="heading">Late</td>
    <td class="inverse"><?php printf("%.0f", $target_late); ?></td>
    <td class="inverse"><?php printf("%.0f", $mtd_late); ?></td>
    <td class="inverse"><?php printf("%.0f", $avg_late); ?></td>
    <?php
    for ($j = 1; $j <= 12; $j++) {
      printf("<td class=\"grey_col\">%.0f</td>", $month_late[$j]); 
    }
    ?>
  </tr>
  <tr>
    <td class="heading">Very Early</td>
    <td class="dark_grey_cell"><?php printf("%.0f", $target_ve); ?></td>
    <td class="dark_grey_cell"><?php printf("%.0f", $mtd_ve); ?></td>
    <td class="dark_grey_cell"><?php printf("%.0f", $avg_ve); ?></td>
    <?php
    for ($j = 1; $j <= 12; $j++) {
      printf("<td>%.0f</td>", $month_ve[$j]); 
    }
    ?>
  </tr>
  <tr>
    <td class="heading">Two Days Early</td>
    <td class="dark_grey_cell"><?php printf("%.0f", $target_2de); ?></td>
    <td class="dark_grey_cell"><?php printf("%.0f", $mtd_2de); ?></td>
    <td class="dark_grey_cell"><?php printf("%.0f", $avg_2de); ?></td>
    <?php
    for ($j = 1; $j <= 12; $j++) {
      printf("<td>%.0f</td>", $month_2de[$j]); 
    }
    ?>
  </tr>
  <tr>
    <td class="heading">One Day Early</td>
    <td class="dark_grey_cell"><?php printf("%.0f", $target_1de); ?></td>
    <td class="dark_grey_cell"><?php printf("%.0f", $mtd_1de); ?></td>
    <td class="dark_grey_cell"><?php printf("%.0f", $avg_1de); ?></td>
    <?php
    for ($j = 1; $j <= 12; $j++) {
      printf("<td>%.0f</td>", $month_1de[$j]); 
    }
    ?>
  </tr>
  <tr>
    <td class="heading_grey">On Date</td>
    <td class="dark_grey_cell"><?php printf("%.0f", $target_ond); ?></td>
    <td class="dark_grey_cell"><?php printf("%.0f", $mtd_ond); ?></td>
    <td class="dark_grey_cell"><?php printf("%.0f", $avg_ond); ?></td>
    <?php
    for ($j = 1; $j <= 12; $j++) {
      printf("<td class=\"grey_col\">%.0f</td>", $month_ond[$j]); 
    }
    ?>
  </tr>
  <tr>
    <td class="heading">One Day Late</td>
    <td class="dark_grey_cell"><?php printf("%.0f", $target_1dl); ?></td>
    <td class="dark_grey_cell">
    <?php 
      if ($mtd_1dl > 0) 
        printf("<a class=\"red\" href=\"orders.php?order_page=LATES&start=%s&end=%s&special_case=ONEDAYLATE\">%.0f</a>", 
              $start_current_month, $end_current_month, $mtd_1dl); 
      else
        printf("<a href=\"orders.php?order_page=LATES&start=%s&end=%s&special_case=ONEDAYLATE\">%.0f</a>", 
              $start_current_month, $end_current_month, $mtd_1dl); 
    ?> 
    </td>
    <td class="dark_grey_cell"><?php printf("%.0f", $avg_1dl); ?></td>
    <?php
    for ($j = 1; $j <= 12; $j++) {
      if ($month_1dl[$j] > 0)
        printf("<td><a class=\"red\" href=\"orders.php?order_page=LATES&start=%s&end=%s&special_case=ONEDAYLATE\">%.0f</a></td>", 
                $start_month[$j], $end_month[$j], $month_1dl[$j]); 
      else
        printf("<td><a href=\"orders.php?order_page=LATES&start=%s&end=%s&special_case=ONEDAYLATE\">%.0f</a></td>", 
                $start_month[$j], $end_month[$j], $month_1dl[$j]); 
    }
    ?>
  </tr>
  <tr>
    <td class="heading">Two Days Late</td>
    <td class="dark_grey_cell"><?php printf("%.0f", $target_2dl); ?></td>
    <td class="dark_grey_cell">
    <?php 
      if ($mtd_2dl > 0)
        printf("<a class=\"red\" href=\"orders.php?order_page=LATES&start=%s&end=%s&special_case=TWODAYSLATE\">%.0f</a>", 
                $start_current_month, $end_current_month, $mtd_2dl); 
      else
        printf("<a href=\"orders.php?order_page=LATES&start=%s&end=%s&special_case=TWODAYSLATE\">%.0f</a>", 
                $start_current_month, $end_current_month, $mtd_2dl); 
    ?> 
    </td>
    <td class="dark_grey_cell"><?php printf("%.0f", $avg_2dl); ?></td>
    <?php
    for ($j = 1; $j <= 12; $j++) {
      if ($month_2dl[$j] > 0)
        printf("<td><a class=\"red\" href=\"orders.php?order_page=LATES&start=%s&end=%s&special_case=TWODAYSLATE\">%.0f</a></td>", 
                $start_month[$j], $end_month[$j], $month_2dl[$j]); 
      else
        printf("<td><a href=\"orders.php?order_page=LATES&start=%s&end=%s&special_case=TWODAYSLATE\">%.0f</a></td>", 
                $start_month[$j], $end_month[$j], $month_2dl[$j]); 
    }
    ?>
  </tr>
  <tr>
    <td class="heading">Very Late</td>
    <td class="dark_grey_cell"><?php printf("%.0f", $target_vl); ?></td>
    <td class="dark_grey_cell">
    <?php 
      if ($mtd_vl > 0)
        printf("<a class=\"red\" href=\"orders.php?order_page=LATES&start=%s&end=%s&special_case=VERYLATE\">%.0f</a>", 
               $start_current_month, $end_current_month, $mtd_vl); 
      else
        printf("<a href=\"orders.php?order_page=LATES&start=%s&end=%s&special_case=VERYLATE\">%.0f</a>", 
               $start_current_month, $end_current_month, $mtd_vl); 
    ?> 
    </td>
    <td class="dark_grey_cell"><?php printf("%.0f", $avg_vl); ?></td>
    <?php
    for ($j = 1; $j <= 12; $j++) {
      if ($month_vl[$j] > 0)
        printf("<td><a class=\"red\" href=\"orders.php?order_page=LATES&start=%s&end=%s&special_case=VERYLATE\">%.0f</a></td>", 
                $start_month[$j], $end_month[$j], $month_vl[$j]); 
      else
        printf("<td><a href=\"orders.php?order_page=LATES&start=%s&end=%s&special_case=VERYLATE\">%.0f</a></td>", 
                $start_month[$j], $end_month[$j], $month_vl[$j]); 
    }
    ?>
  </tr>
  </table>

  <br><br>

  </div>

</body>
</html>
<?php
mysql_free_result($orders);
mysql_free_result($jobs);
mysql_free_result($completed_jobs);
?>
