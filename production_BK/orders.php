<?php 
require('authprodn.php');
$this_is_orders_page = 1;  // tells include files to handle things differently; don't remove
$this_is_single_order_page = 0;
include('includes/orders_page_setup.php');
include('includes/handle_submit_orders.php');
include('../includes/getdetail.php');
include('includes/send_email.php');

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

  <title><?php echo $queue; ?></title>

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
  <div style="float: right;"><h1>Order Processing System: <?php echo $product . ' ' . $queue; ?> (Orders)</h1></div>
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
    <span class="lineitem_returning_customer">RETURNING</span>
    <br><br>
  </div>
</div>


<div style="margin: 0px auto; text-align: center;">
  <!-- Form which passes tickbox ticks back into PHP to be handled. -->
  <form name="main_form" action=<?php echo $_SERVER['PHP_SELF'] ?> method="POST">
    <input name="order_id" id="order_id" type="hidden" value="<?php echo $order_id; ?>">
    <input name="team" id="team" type="hidden" value="<?php echo $team; ?>">
    <input name="start" id="start" type="hidden" value="<?php echo $start; ?>">
    <input name="end" id="end" type="hidden" value="<?php echo $end; ?>">
    <input name="special_case" id="special_case" type="hidden" value="<?php echo $special_case; ?>">
    <input name="product" id="product" type="hidden" value="<?php echo $product; ?>">
    <input name="queue" id="queue" type="hidden" value="<?php echo $queue; ?>">

    <table class="evenodd" border="0" align="center" cellpadding="5">

  <?php 
    include('includes/orders_table_heading.html');

      $order_number = 0;
      $num_orders = 0;
      $today = date("m/d/Y");
      $todays_date = date('Y-m-d');

      if ($_POST['update_orders']) handle_submit_orders($queue);
      include('includes/query_queues.php');

      // All the orders are shown on one page.
      for ($j = 0, $i = 0; $i < $num_jobs; $i++) {

        $row_orders = mysql_fetch_assoc($jobs);
        if ($row_orders == FALSE) echo "Could not read order from database.";
    
        // Display each order only once.  Put all results on one page.
        $order_id = $row_orders['ID'];
        if ($order_id != $order_number) {
	  $j++;
          include('includes/orders_display_row.php');
          $num_orders++;
        }
        $order_number = $order_id;
      } 
    ?>

  </table>

  <?php include('includes/page_footer.php'); ?>

  </form>

</body>
</html>
<?php
mysql_free_result($orders);
?>
