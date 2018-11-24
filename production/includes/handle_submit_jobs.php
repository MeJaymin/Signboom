<?php
require_once('../includes/mailord.php'); 

function getOptionQueue($finishing_option)
{
  global $DBConn;
  $query = "SELECT Queue FROM signboom_finishing WHERE Code = '$finishing_option'";
  $result = mysql_query($query, $DBConn) or die();
  $info = mysql_fetch_assoc($result);
  $queue = $info['Queue'];
  return $queue;
}


function handle_submit_jobs($queue) {
  global $DBConn;
  $orders_proofed = array(); // Keep track of orders which have just had a file proofed.
  $orders_packed = array(); // Keep track of orders which have just had a file packed`.

  // $order_id_# $job_id_# $order_type_# $account_name_# $proofed_# $printed_# $finished_# $packed_#
  $num_jobs = $_POST['number_of_jobs'];

  // For each of the jobs=files on that page....
  for ($k = 1; $k <= $num_jobs; $k++) 
  {
    $the_order_id = $_POST['order_id_'.$k];

    // If there is a job on that row...
    if ($the_order_id != 0) 
    {
      // Read information on that job.
      $the_job_id = $_POST['job_id_'.$k];
      $the_order_type = $_POST['order_type_'.$k];
      $the_account_name = $_POST['account_name_'.$k];

      // Make note of whether the checkbox(es) were ticked.
      // With the exception of the Proof queue, which shares a page with the RIP queue,
      // any checkbox that is ticked has been newly ticked. (Otherwise the file wouldn't
      // be displayed on the current page.
      if ($queue == 'RIP')
      {
	// This queue has an extra checkbox for each file.
	if (isset($_POST['proofed_'.$k]))
          $proofed = 1;
        else
          $proofed = 0;
      }
      if (isset($_POST['done_'.$k]))
        $done = 1;
      else
        $done = 0;

      // Make note of what finishing options are required.
$query = <<< End_Of_Query
SELECT signboom_allproducts.Category, signboom_linedetail.currentqueue, 
       signboom_linedetail.product,
       signboom_linedetail.AF, signboom_linedetail.AL, signboom_linedetail.AI, 
       signboom_linedetail.AP, signboom_linedetail.AK, 
       signboom_linedetail.BF, signboom_linedetail.BB, signboom_linedetail.BI, 
       signboom_linedetail.BP, signboom_linedetail.BK, 
       signboom_linedetail.RF, signboom_linedetail.RL, signboom_linedetail.RB, 
       signboom_linedetail.RH, signboom_linedetail.RE, signboom_linedetail.RI, 
       signboom_linedetail.RP, signboom_linedetail.RK, signboom_linedetail.RO 
       FROM signboom_linedetail, signboom_allproducts 
       WHERE (signboom_linedetail.id = $the_job_id) AND signboom_linedetail.product = signboom_allproducts.Code
End_Of_Query;
      $result = mysql_query($query, $DBConn) or die();
      $details = mysql_fetch_assoc($result);
      $current_queue = $details['currentqueue'];
      $category = $details['Category'];
      $product = $details['product'];
      $AF = $details['AF'];
      $AL = $details['AL'];
      $AI = $details['AI'];
      $AP = $details['AP'];
      $AK = $details['AK'];
      $BF = $details['BF'];
      $BB = $details['BB'];
      $BI = $details['BI'];
      $BP = $details['BP'];
      $BK = $details['BK'];
      $RF = $details['RF'];
      $RL = $details['RL'];
      $RB = $details['RB'];
      $RH = $details['RH'];
      $RE = $details['RE'];
      $RP = $details['RP'];
      $RK = $details['RK'];
      $RO = $details['RO'];

      // Decide which queue to put the file in, based on what finishing options are required.
      $next_queue = '';
      switch($queue)
      {
        case 'RIP':
	  // If it has been proofed, but we're not ready to send it to the RIP yet...
	  if ($proofed && !$done)
	    $next_queue = 'RIP';

          // Check whether proofed has been newly ticked. If so, we may need to send an email to the client.
	  if (($current_queue == 'Proof') and ($proofed))
	  {
            // Add this order to a list of orders to be checked later in sendProofedEmails().
	    // That will send an email to the customer if their order has just been completely proofed.
	    addToOrdersList($the_order_id, $orders_proofed);
          }

	  // If we're ready to send it to the RIP...
          if ($done)
	    if ($product == 'KISS')
	      $next_queue = 'Kiss';
	    else if ($product == 'CNC')
	      $next_queue = 'CNC';
	    else
	    $next_queue = 'Print';
	  break;

        case 'Print':
          if ($done)
	  {
	    if ($category == 'ADHESIVE') 
	    {
	      $next_queue = getOptionQueue($AL); // check for lamination (Lam queue)
	      if ($next_queue == '')
	        $next_queue = getOptionQueue($AF); // check for cutting (Kiss or CNC queue)
	      if ($next_queue == '')
	        $next_queue = 'Pack'; // We skip past Finish queue; there are no finish options for adhesive.
	    }

	    if ($category == 'RIGID') 
	    {
	      $next_queue = getOptionQueue($RL); // check for lamination (Lam queue)
	      if ($next_queue == '')
	        $next_queue = getOptionQueue($RF); // check for cutting (Kiss or CNC queue)

	      // Note: All rigid orders have to go to CNC for cutting, so the functions below will
	      // never get invoked.
	      if ($next_queue == '')
	        $next_queue = getOptionQueue($RE); // check for edges (CNC queue)
	      if ($next_queue == '')
	        $next_queue = getOptionQueue($RH); // check for mounting/hanging (CNC or Finish queue)
	      if ($next_queue == '')
	        $next_queue = 'Pack';
	    }

	    if ($category == 'BANNER') 
	    {
	      //skip lamination; not an option for banner; 
	      $next_queue = getOptionQueue($BF); // for legacy reasons the BF field includes hem and grommets and rod pockets
	      if ($next_queue == '')
	        $next_queue = getOptionQueue($BE); // check for edges (Finish queue)
	      if ($next_queue == '')
	        $next_queue = 'Pack';
	    }

	  }
	  break;

	case 'Lam':
          if ($done)
	  {
	    if ($category == 'ADHESIVE') 
	    {
	      $next_queue = getOptionQueue($AF); // check for cutting (Kiss or CNC queue)
	      if ($next_queue == '')
	        $next_queue = 'Pack'; // We skip past Finish queue; there are no finish options for adhesive.
	    }

	    if ($category == 'RIGID') 
	    {
	      $next_queue = getOptionQueue($RF); // check for cutting (Kiss or CNC queue)

	      // Note: All rigid orders have to go to CNC for cutting, so the functions below will
	      // never get invoked.
	      if ($next_queue == '')
	        $next_queue = getOptionQueue($RE); // check for edges (Finish queue)
	      if ($next_queue == '')
	        $next_queue = getOptionQueue($RH); // check for mounting/hanging (Finish queue)
	      if ($next_queue == '')
	        $next_queue = 'Pack';
	    }

	    if ($category == 'BANNER') 
	    {
	      $next_queue = getOptionQueue($BF); //check for cutting 
	      if ($next_queue == '')
	        $next_queue = getOptionQueue($BE); // check for edges (Finish queue)
	      if ($next_queue == '')
	        $next_queue = 'Pack';
	    }

	  }
	  break;

	case 'Kiss':
	case 'CNC':
          if ($done)
	  {
	    if ($category == 'ADHESIVE') 
	    {
	      $next_queue = 'Pack'; // We skip past Finish queue; there are no finish options for adhesive.
	    }

	    if ($category == 'RIGID') 
	    {
	      $next_queue = getOptionQueue($RH); // check for mounting/hanging (CNC or Finish queue)
	      // Add in extra check to avoid looping infinitely in CNC queue when RH is for
	      // holes, which are done at the CNC station.
	      if (($next_queue == '') || ($next_queue == 'CNC'))
	        $next_queue = 'Pack';
	    }

	    if ($category == 'BANNER') 
	    {
	      $next_queue = getOptionQueue($BE); // check for edges (Finish queue)
	      if ($next_queue == '')
	        $next_queue = 'Pack';
	    }

	  }
	  break;

	case 'Finish':
          if ($done)
	  {
	    $next_queue = 'Pack'; 
	  }
	  break;


        case 'Pack': 
          if ($done)
	  {
	    $next_queue = 'Invoice'; 
            // Add this order to a list of orders to be checked later in sendPackedEmails().
	    // That will send an email to the customer if their order has just been completely packed.
	    // It will also update the ORDERS table so that completed = 'yes' and timecompleted is populated
	    addToOrdersList($the_order_id, $orders_packed);
	  }
	  break;

        default:
	  echo 'There is a bug in the software. I don\'t know what queue to put this item in next.';

      }

      // TO DO: Code this if staff still want it.
      // User is allowed to untick Printed status on an INDIVIDUAL order page, because no email to customer was sent 
      // when it was ticked. We don't allow it to be unticked on a page with MULTIPLE orders because this was causing
      // issues where a user with a "stale" production page open could accidentally - by submitting their page - reverse 
      // the ticks that had been made earlier by someone else.
      // Create query to update job status.
      if ($next_queue != '')
      {
        $update_query = "UPDATE signboom_linedetail SET currentqueue = '$next_queue' WHERE id = $the_job_id";
        $result = mysql_query($update_query, $DBConn) or die(mysql_error());
      }

    } //end of if there is a job on that row

  }  // end of for loop

  // Now we need to email customers regarding orders which have just been completedly proofed or packed.
  if ($queue == 'RIP')
    sendEmails('Proof', $orders_proofed);
  else if ($queue == 'Pack') 
    sendEmails('Pack', $orders_packed);

} // end of function handle_submit_jobs()


?>
