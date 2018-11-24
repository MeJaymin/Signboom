<?php
require_once('../includes/mailord.php'); 

function handle_submit_orders($queue) {
  global $DBConn; 

  $num_orders = $_POST['number_of_orders'];

  // For each of the orders on that page....
  for ($k = 1; $k <= $num_orders; $k++) 
  {
    $the_order_id = $_POST['order_id_'.$k];

    // If there is an order on that row...
    if ($the_order_id != 0) 
    {

      // Read information on that order.
      $the_account_name = $_POST['account_name_'.$k];

      // Make note of whether the checkbox(es) were ticked.
      // Any checkbox that is ticked has been newly ticked. (Otherwise the order wouldn't
      // be displayed on the current page.)
      if (isset($_POST['done_'.$k]))
        $done = 1;
      else
        $done = 0;

      // Decide which queue to put the order in next. 
      $next_queue = '';
      switch($queue)
      {
        case 'Invoice':
          if ($done)
	    $next_queue = 'Complete'; // now currentqueue for FILE moves to Complete; ORDER already marked as completed when packed
	  break;

	case 'Ready':
          if ($done)
	  {
	    $next_queue = 'DateConfirmed'; // this isn't actually a queue; just an orderwide field we update 
	  }
	  break;

        default:
	  echo 'There is a bug in the software. I don\'t know what queue to put this item in next.';

      }

      // Create query to update order status.
      if ($next_queue == 'Complete')
      {
        $update_query = "UPDATE signboom_linedetail SET currentqueue = '$next_queue' WHERE orderid = $the_order_id";
        $result = mysql_query($update_query, $DBConn) or die(mysql_error());
      }
      else if ($next_queue == 'DateConfirmed')
      {
        $update_query = "UPDATE signboom_ordermast SET readydateconfirmed = '1' WHERE ID = $the_order_id";
        $result = mysql_query($update_query, $DBConn) or die(mysql_error());

	// We are also storing the information against the line items in signboom_linedetails because
	// the query for the dashboard totals of the Ready column was giving this error.
	// "#1104 - The SELECT would examine more than MAX_JOIN_SIZE rows; check your WHERE and use 
	// SET SQL_BIG_SELECTS=1 or SET MAX_JOIN_SIZE=# if the SELECT is okay"
	// I didn't want to enable the big selects, because that would slow down the Queues page.
        $update_query_2 = "UPDATE signboom_linedetail SET readydateconfirmed = '1' WHERE orderid = $the_order_id";
        $result_2 = mysql_query($update_query_2, $DBConn) or die(mysql_error());
      }

    } //end of if there is a job on that row

  }  // end of for loop

} // end of function handle_submit_orders()


?>
