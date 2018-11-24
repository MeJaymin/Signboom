  <input name="number_of_jobs" id="number_of_jobs" type="hidden" value="<?php echo $num_jobs; ?>">
  <input name="number_of_orders" id="number_of_orders" type="hidden" value="<?php echo $num_orders; ?>">
  <input name="this_is_single_order_page" id="this_is_single_order_page" type="hidden" value="<?php echo $this_is_single_order_page; ?>">
  <input name="order_id" id="order_id" type="hidden" value="<?php echo $order_id; ?>">

<div style="width: 1200px; height: 35px; margin: 20px auto 20px; text-align: center;">

  <?php
  // Show number of items displayed on this page.
  if ($this_is_orders_page) 
  {
    printf("%d Orders", $num_orders);
    if ($queue != 'Pending')
    {
      printf("<input style=\"margin-left: 20px;\" type=\"submit\" name=\"update_orders\" value=\"Update Order Statuses\" onclick=\"document.main_form.submit();\">");
    }
  }
  else  // this is files page
  {
    printf("%d Files", $num_jobs);
    if ((!$this_is_single_order_page) && ($queue != 'Orders') && ($queue != 'Today'))
      printf("<input style=\"margin-left: 20px;\" type=\"submit\" name=\"update_jobs\" value=\"Update Job Statuses\" onclick=\"document.main_form.submit();\">");
  }

  // Include 'delete order' buttonfor single-order job pages.
  if ($this_is_single_order_page)
  {
    include('includes/delete_order.php');  
    include('includes/reject_order.php');  
  }

  // Include 'mark as uploaded' button for single-order job pages when files originally failed to upload.
  if (($include_change_upload_in_footer) && ($uploaded != "Yes"))
    include('includes/change_upload_status.php');  
  ?>

</div>

