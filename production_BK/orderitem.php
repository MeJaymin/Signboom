<?php
require('authprodn.php');
$this_is_orders_page = 0;
$this_is_single_order_page = 1;
include('includes/single_order_page_setup.php');
include('includes/query_single_order_page.php');
include('includes/query_finishing_options.php');

// Handle any ticks user has made in checkboxes.
include('includes/javascript_delete_functions.htm');
include('includes/date_picker.htm');

// Look up price above which an order is considered "expensive" and should be flagged in production.
$query_expensive = "SELECT expensive FROM signboom_parm WHERE ID = 1";
$result_expensive = mysql_query($query_expensive, $DBConn);
if (!$result_expensive)
{
  $message = "I can't find the parameter to use for flagging orders as 'expensive'.";
}
else
{
  $row = mysql_fetch_array($result_expensive); 
  $expensive = $row['expensive'];
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>

  <title>Order Details</title>

  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="production-new.css" rel="stylesheet" type="text/css">

  <script type="text/javascript" src="../script/encoder.js"></script>
  <script type="text/javascript">
  function displayMessage(the_message) {
    Encoder.EncodeType = "entity";
    var decoded = Encoder.htmlDecode(the_message);
    alert(decoded);
  }
  </script>
</head>

<body>

<div style="width: 1200px; margin: 0px auto; text-align: center;">

  <div style="float: left; margin-top: 20px;"><img src="../../images/logo3d.gif" width="308" height="54"></div>
  <div style="float: right;"><h1>Order Processing System: Order <?php echo $_GET['order_id']; ?></h1></div>
  <br style="clear: both;">
  <?php include('menu.html');?>
  <br><br>

  <div class="legend">
    COLOUR LEGEND: &nbsp;&nbsp;&nbsp;&nbsp;
    <span class="lineitem_rush">RUSH</span> &nbsp;&nbsp;&nbsp;&nbsp;
    <span class="lineitem_hot">HOT</span> &nbsp;&nbsp;&nbsp;&nbsp;
    <span class="lineitem_today">TODAY</span> &nbsp;&nbsp;&nbsp;&nbsp;
    <span class="lineitem_late">LATE</span> &nbsp;&nbsp;&nbsp;&nbsp;
    <span class="lineitem_checklist">EXPENSIVE</span> &nbsp;&nbsp;&nbsp;&nbsp;
    <span class="lineitem_first_order">FIRST ORDER</span> &nbsp;&nbsp;&nbsp;&nbsp;
    <span class="lineitem_returning_customer">RETURNING</span> &nbsp;&nbsp;&nbsp;&nbsp;
    <span class="lineitem_custom_finish">CUSTOM FINISH</span>

    <br><br>
    <?php 
      /* TO DO: display the filters that were chosen on the dashboard to get to this page. 
      echo "Type $team, Printer $printer, $stage<br>";
      */
    ?>
  </div>

</div>

<div style="margin: 0px auto; text-align: center;">
  <!-- Form which passes tickbox ticks back into PHP to be handled. -->
  <form name="main_form" action=<?php echo $_SERVER['PHP_SELF'] ?> method="POST">
    <input name="my_order_id" id="my_order_id" type="hidden" value="<?php echo $my_order_id; ?>">

    <table class="evenodd" border="0" align="center" cellpadding="5">

    <?php
      include('includes/jobs_table_heading.html');
      $today = date("m/d/Y");
      $todays_date = date('Y-m-d');

      // Display the files in this order.
      for ($i = 0; $i < $num_jobs; $i++) {
        $row = mysql_fetch_assoc($jobs);
        if ($row == FALSE)
          echo "Could not read job from database.";
        else
          include('includes/jobs_display_row.php');
      }
      ?>

    </table>

    <?php include('includes/page_footer.php'); ?>

  </form>

  <?php include('includes/change_ready_date.php'); ?>

</div>
</body>
</html>

<?php
mysql_free_result($orders);
mysql_free_result($jobs);
mysql_free_result($result);
?>
