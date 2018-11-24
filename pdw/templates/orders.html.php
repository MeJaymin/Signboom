<!doctype html>

<html>

<head>
  <meta charset="utf-8">
  <title>Signboom: PWD Order System</title>
  <?php include ('head.html'); ?>
  <link rel="stylesheet" type="text/css" media="all" href="styles.css" />
</head>

<body>

<div id="page_wide">

  <?php include ('banner-menu.html'); ?>

  <div id="content_wide">

    <div style="text-align: center;">
    Click numeric ID for order details. (Orders which haven't finished loading are not displayed here.)
    <br><br>
    <b>
    COLOUR LEGEND: &nbsp;&nbsp;&nbsp;&nbsp;
    <span class="lineitem_rush">RUSH</span> &nbsp;&nbsp;&nbsp;&nbsp;
    <span class="lineitem_hot">HOT</span> &nbsp;&nbsp;&nbsp;&nbsp;
    </span>
    </b>
    </div>
    <br>

    <table border="0" align="center" cellpadding="5">

    <?php 
      include('includes/orders_table_heading.html');

      $order_number = 0;
      $num_orders = 0;

      // All the orders are shown on one page.
      for ($i = 0; $i < $num_jobs; $i++) {

        $row_orders = mysql_fetch_assoc($jobs);
        if ($row_orders == FALSE) echo "Could not read order from database.";
    
        // Display each order only once.  Put all results on one page.
        $order_id = $row_orders['ID'];
        if ($order_id != $order_number) {
          include('includes/orders_display_row.php');
          $num_orders++;
        }
        $order_number = $order_id;
      } 
    ?>

    </table>

  </div>

</div>

</body>

</html>
