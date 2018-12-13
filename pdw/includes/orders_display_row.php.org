<?php
      $order_id = $row_orders['ID'];
      $date_created = $row_orders['date_created'];
      $ready_date = $row_orders['readydate'];
      $reference_number = $row_orders['refnum'];
      $customer_notes= $row_orders['customernotes'];
      $file_uploaded = $row_orders['Uploaded'];
      $ship_type = $row_orders['shiptype'];
      $rush_type = $row_orders['rushtype'];
      $order_completed = $row_orders['ordercompleted'];

      $account_name_class = '';

      $special_chars =      array("&",     "\"",      "'",     "%",      "(",      ")");
      $replacement_values = array("&#038;", "&#034;", "&#039;", "&#037;", "&#040;", "&#041;");

      // Identify the rush status of the job, so it can be highlighted in the display.
      if ($rush_type == "RUSH") $lineitem_class = "lineitem_rush";
      elseif ($rush_type == "HOT") $lineitem_class = "lineitem_hot";
      else $lineitem_class = "lineitem_std";

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
    <td class="<?php echo $lineitem_class ?>">
     <a href="orderitem.php?order_id=<?php echo $order_id; ?>"> <?php echo $order_id; ?></a> 
    </td>
    <td class="<?php echo $lineitem_class ?>"><?php echo $order_date; ?></td>
    <td class="<?php echo $lineitem_class ?>"><?php echo $ready_date; ?></td>
    <td class="<?php echo $lineitem_class ?>">
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
    <td class="<?php echo $lineitem_class ?>">
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
    <td class="<?php echo $lineitem_class ?>">
    <?php 
    if ($file_uploaded == "Yes") {
      echo $file_uploaded; 
    }
    else {
      echo "<a href=\"mailto:$email\" target=\"_blank\"><b>No</b></a>";
    }
    ?>
    </td>
    <td class="<?php echo $lineitem_class ?>">
     <?php 
      echo $ship_type;
     ?>
    </td>
    <td class="<?php echo $lineitem_class ?>"><?php echo $rush_type; ?></td>
    <td class="<?php echo $lineitem_class ?>"><?php echo $order_completed; ?></td>
  </tr>
