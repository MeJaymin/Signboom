<?php

function addToOrdersList($the_order_id, &$orders_list)
{
  // Add this order to a list of all orders which have just had a file proofed/packed.
  // After all the order queues have been saved to the database, we'll check this list
  // and send emails to users whose orders have just been completely proofed/packed.
  if (!in_array($the_order_id, $orders_list))
    $orders_list[] = $the_order_id;
}

function sendEmails($queue, $orders_list)
{
  global $DBConn;

  // Check all the orders that contain a file that has just been checked off as proofed/packed.
  // If the order is fully proofed/packed, then email the customer.
  if (!empty($orders_list))
  {
    foreach ($orders_list as $the_order_id)
    {
      // Check whether all files in this order have been proofed/packed.
      $query_jobs_test = "SELECT id, currentqueue FROM signboom_linedetail WHERE orderid = $the_order_id";
      $jobs_test = mysql_query($query_jobs_test, $DBConn);
      if ($jobs_test === false)
      {
        echo "Query for current queue Failed!<br>";
      }
      while ($row_jobs = mysql_fetch_assoc($jobs_test))
      {
        if ($row_jobs['currentqueue'] == $queue)
        {
          $id = $row_jobs['id'];
          return; 
        }
      }

      // If you get to here, then all none of the files were still in the Proof/Pack queue,
      // so they have all been Proofed/Packed. Send the user an email.
  
      // Check if email for this stage has been sent
      $query_order_test = "SELECT * FROM signboom_ordermast WHERE ID = $the_order_id";
      $result = mysql_query($query_order_test, $DBConn); 
      if ($result === false)
        echo "Query for proofed email Failed!<br>";
      $row_order = mysql_fetch_array($result);
      $refnum = $row_order['refnum'];

      if ($queue == 'Proof')
      {
        $field = 'emailproofed';
	$what_we_did = 'proofed';
        $the_email_title = "Your Signboom order reference '$refnum' has been proofed.";
        $intro = "Your order has been successfully proofed and is ready to print. You will receive an additional email once your order has been completed.";
      }
      else if ($queue == 'Pack')
      {
        $field = 'emailpacked';
	$what_we_did = 'packed';
        $the_email_title = "Your Signboom order reference '$refnum' has been packed.";
        $intro = "Your order has been completed and packed and is ready for delivery/pickup.";

	$now = mktime(); // given in seconds
        $vancouver_time = $now - (3 * 60 * 60); // subtract 3 hours off for time difference
        $order_completion_time = date('Y-m-d H:i:s', $vancouver_time);
        $update_query = "UPDATE signboom_ordermast SET timecompleted = '$order_completion_time', ordercompleted = 'yes' WHERE ID = $the_order_id";
        $result = mysql_query($update_query, $DBConn) or die(mysql_error());
      }

      if ($row_order[$field] == 'no')
      {
	$detail = array();
	getOrderDetails($the_order_id, $detail);

        // Prepare and send an email to customer.
        $account_name = $row_order['AcctName'];
	$query_user = "SELECT * FROM signboom_user WHERE AcctName = '$account_name'";
        $info_user = mysql_query($query_user, $DBConn);
    
	$message = array();
        $message[1]['content_type'] = 'text/html; charset=iso-8859-1';
        $message[1]['filename'] = '';
        $message[1]['no_base64'] = TRUE;
        $message[1]['data'] = bldhtml($result, $detail, $info_user, $intro);
        $out = mp_new_message($message);
        mail(mysql_result($result,0,'email'), $the_email_title, $out[0], "From: signboom@signboom.com"."\r\n".$out[1]);
        //mail('alison@usablewebdesigns.com', $the_email_title, $out[0], "From: signboom@signboom.com"."\r\n".$out[1]);
        mysql_free_result($rsUser);
        mysql_free_result($result);

        echo "<div style=\"text-align: center; font-weight: bold; color:#00AAEA; padding-top: 15px; padding-bottom: 15px;\">The customer has been sent an email indicating that all jobs in order " . $the_order_id . " have been $what_we_did.</div>";

        // Update status of that order in the database.
        $set_query = "UPDATE signboom_ordermast SET $field = 'yes' WHERE ID = $the_order_id";
        mysql_query($set_query, $DBConn) or die(mysql_error());
      }

    } //end foreach loop
    unset($the_order_id);

  } // end of if array is not empty

  // Refresh the page.
  $this_page = $_SERVER["PHP_SELF"] . 
  $the_queue = $_REQUEST["queue"];
  $the_product = $_REQUEST["product"];
  $my_href = "window.location.href=". $this_page . "?product=" . $product. "&queue=" . $queue;
  echo '<script type="text/javascript">';
  echo '$my_href';
  echo '</script>';
}
?>
