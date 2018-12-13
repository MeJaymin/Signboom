<?php

      $account_name = $row_orders['AcctName'];
      $order_id = $row_orders['ID'];
      $date_created = $row_orders['date_created'];
      $ready_date_with_time = $row_orders['readydate'];
      sscanf($ready_date_with_time, "%s %s", $ready_date, $time);
      $readydateconfirmed = isset($row_orders['readydateconfirmed'])?$row_orders['readydateconfirmed']:"";
      $reference_number = $row_orders['refnum'];
      $customer_notes = $row_orders['customernotes'];
      $email = $row_orders['email'];
      $ship_type = $row_orders['shiptype'];
      $rush_type = $row_orders['rushtype'];
      $uploaded = $row_orders['Uploaded'];
      $cost = str_replace('$', '', $row_orders['cost']); // from subtotal field in ordermast database
      $time_completed = isset($row_orders['timecompleted'])?$row_orders['timecompleted']:"";

      $ship_attn = $row_orders['shipattn'];
      $ship_company = $row_orders['shipcompany'];
      $ship_address = $row_orders['shipaddress'];
      $ship_city = $row_orders['shipcity'];
      $ship_province = $row_orders['shipprov'];
      $ship_postal_code = $row_orders['shipzip'];
      $ship_country = $row_orders['shipcountry'];
      $remember_this_address = $row_orders['shiptoadd'];
      $shipping_label = $row_orders['documentname'];

      $first_order = $row_orders['firstorder'];
      $returning_customer = $row_orders['returningcustomer'];
      if ($first_order) $account_name_class = ' class="lineitem_first_order" ';
      else if ($returning_customer) $account_name_class = ' class="lineitem_returning_customer" ';
      else $account_name_class = '';

      $order_all_finished = 0;
      if ($queue == 'Pack') $order_all_finished = isOrderAllFinished($order_id);

      if (strlen(trim($shipping_label)) > 0)
        $shipping_information = "SHIP TO THIS ADDRESS:\\n\\n$ship_attn\\n$ship_company\\n$ship_address\\n$ship_city, $ship_province\\n$ship_country, $ship_postal_code\\n\\n===>>> Find label on P: drive\\n\\n";
      else
        $shipping_information = "SHIP TO THIS ADDRESS:\\n\\n$ship_attn\\n$ship_company\\n$ship_address\\n$ship_city, $ship_province\\n$ship_country, $ship_postal_code\\n\\n";

      $special_chars =      array("&",     "\"",      "'",     "%",      "(",      ")");
      $replacement_values = array("&#038;", "&#034;", "&#039;", "&#037;", "&#040;", "&#041;");

      // Identify the rush status of the job, so it can be highlighted in the display.
      if ($rush_type == "RUSH") $lineitem_class = "lineitem_rush";
      elseif ($rush_type == "HOT") $lineitem_class = "lineitem_hot";
      elseif ($ready_date == $today) $lineitem_class = "lineitem_today";
      else $lineitem_class = "lineitem_std";

      // If the order is late, highlight it red.
      // TO DO: This code won't work when database has 'Call' for readydate (for hot orders).
      sscanf($ready_date, "%02d/%02d/%4d", $month, $day, $year);
      $ready = sprintf("%4d-%02d-%02d", $year, $month, $day);
      if (($ready < $todays_date) && ($queue != 'Invoice')) $lineitem_class = "lineitem_late";

      if ($queue == 'Ready')
      {
        // If 24 hours have passed since the order was placed and
        // the ready date has not been confirmed, highlight the order as unconfirmed.
        $now_server_seconds = strtotime('now'); // Server is 3 hours ahead in Toronto time zone
        $now_vancouver_seconds = $now_server_seconds - 3 * 60 * 60; // need to be in Vancouver time zone
        $date_created_seconds = strtotime($date_created);
        if ((!$readydateconfirmed) && ($now_vancouver_seconds - $date_created_seconds > 24*60*60))
	  $lineitem_class = "lineitem_unconfirmed";

        // If the rush type is HOT and the ready date still says 'Call', highlight the order 
	// as unconfirmed.
	if (($rush_type == 'HOT') && ($ready_date_with_time == 'Call'))
	  $lineitem_class = "lineitem_unconfirmed";
      }
      $stage ="";
      if ($stage == "UPLOADING") {
        // On Uploading page want to include time, so don't do the parsing.
        $order_date = $date_created;
      }
      else {
        // Parse date out of date_created, and put in format mm/dd/yyyy.
        sscanf($date_created, "%d-%d-%d ", $year_created, $month_created, $day_created);
        $order_date = sprintf("%d/%d/%d", $month_created, $day_created, $year_created);
      }
  ?>
  <tr>
    <td>
      <?php if ($account_name == 'INCIDENT'): ?>
        <a <?php echo $account_name_class; ?> class="lineitem_incident" href="client.php?account=<?php echo urlencode($account_name); ?>" target="_blank"><?php echo $account_name; ?></a>
      <?php else: ?>
        <a <?php echo $account_name_class; ?> href="client.php?account=<?php echo urlencode($account_name); ?>" target="_blank"><?php echo $account_name; ?></a>
      <?php endif; ?>
    </td>
    <td>
     <?php if ($cost > $expensive): ?>
       <a class="lineitem_checklist" href="orderitem.php?order_id=<?php echo $order_id; ?>"> <?php echo $order_id; ?></a>
     <?php else: ?>
       <a href="orderitem.php?order_id=<?php echo $order_id; ?>"> <?php echo $order_id; ?></a> 
     <?php endif; ?>
    </td>
    <td><?php echo $order_date; ?></td>
    <td class="<?php echo $lineitem_class; ?>"><?php echo $ready_date; ?></td>
<?php 
if ($order_page == "LATES") { 
    echo '<td><?php echo $time_completed; ?></td>';
} 
?>
    <td>
     <?php 
       if (strlen($reference_number) > 20) {
         echo "<a href=\"#\" onClick=\"alert('$reference_number')\">";
         echo substr($reference_number, 0, 16); 
         echo "...";
         echo "</a>";
       }
       else {
         echo $reference_number;
       }
     ?>
    </td>
    <td>
     <?php 
       if (strlen(trim($customer_notes)) > 0) {
         $output = str_replace($special_chars, $replacement_values, $customer_notes);
         echo "<a href=\"#\" style=\"color: #CC0000; font-weight: bold;\" onClick=\"displayMessage('$output')\">YES</a>";
       }
       else {
         echo "NO";
       }
     ?>
    </td>
    <td>
     <?php 
        if (strlen(trim($shipping_label)) > 0) {
         echo "<a href=\"#\" style=\"color: #CC0000; font-weight: bold;\" onClick=\"alert('$shipping_information')\">$ship_type</a>";
       }
       else {
         echo "<a href=\"#\" onClick=\"alert('$shipping_information')\">$ship_type</a>";
       }
     ?>
    </td>
    <td>
      <?php echo $ship_city; ?>
    </td>
    <?php 
     if (($queue == 'Pack') || ($queue == 'Ready') || ($queue == 'Invoice'))
     {
       echo '<td>';
       echo "<input name=\"order_id_$j\" id=\"order_id_$j\" type=\"hidden\" value=\"$order_id\"> ";
       echo "<input name=\"account_name_$j\" id=\"account_name_$j\" type=\"hidden\" value=\"$account_name\"> ";
       // Display checkbox for the queue associated with this page. 
       if (($queue == 'Pack') && (!$order_all_finished))
         echo "<input name=\"done_$j\" type=\"checkbox\" id=\"done_$j\" disabled value=\"yes\" class=\"myinput\" >";
       else
         echo "<input name=\"done_$j\" type=\"checkbox\" id=\"done_$j\" value=\"yes\" class=\"myinput\" >";
       echo '</td>';
     }
     else if ($queue == 'Pending')
     {
       // Label order as either Upload or Rejected. TO DO: Implement Hold later.
       if ($uploaded != 'Yes') 
         $order_label = 'Upload';
       else 
         $order_label = 'Rejected';
       echo "<td>$order_label</td>";
     }
     ?>
  </tr>
