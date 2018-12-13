  <?php 

    // Used later for customer_notes and reference_number. 
    $special_chars =      array("&",     "\"",      "'",     "%",      "(",      ")");
    $replacement_values = array("&#038;", "&#034;", "&#039;", "&#037;", "&#040;", "&#041;");

    $j = $i + 1;  // index from 1 up
    $order_id = $row['ID'];
    echo "<input name=\"order_id_$j\" id=\"order_id_$j\" type=\"hidden\" value=\"$order_id\"> ";
    $job_id = $row['jobid'];
    echo "<input name=\"job_id_$j\" id=\"job_id_$j\" type=\"hidden\" value=\"$job_id\"> ";
    $order_type = $row['ordertype'];
    echo "<input name=\"order_type_$j\" id=\"order_type_$j\" type=\"hidden\" value=\"$order_type\"> ";
    $rush_type = $row['rushtype'];
    $ready_date_with_time = $row['readydate'];
    sscanf($ready_date_with_time, "%s %s", $ready_date, $time);
    $customer_notes = $row['customernotes'];
    $reference_number = $row['refnum'];
    $shipping_type = $row['shiptype'];
    $account_name = $row['AcctName'];
    $uploaded = isset($row['uploaded'])?$row['uploaded']:"";

    $ship_attn = isset($row['shipattn'])?$row['shipattn']:"";
    $ship_company = isset($row['shipcompany'])?$row['shipcompany']:"";
    $ship_address = isset($row['shipaddress'])?$row['shipaddress']:"";
    $ship_city = isset($row['shipcity'])?$row['shipcity']:"";
    $ship_province = isset($row['shipprov'])?$row['shipprov']:"";
    $ship_postal_code = isset($row['shipzip'])?$row['shipzip']:"";
    $ship_country = isset($row['shipcountry'])?$row['shipcountry']:"";
    $remember_this_address = isset($row['shiptoadd'])?$row['shiptoadd']:"";
    $shipping_label = isset($row['documentname'])?$row['documentname']:"";

    $first_order = $row['firstorder'];
    $returning_customer = $row['returningcustomer'];
    if ($first_order) $account_name_class = ' class="lineitem_first_order" ';
    else if ($returning_customer) $account_name_class = ' class="lineitem_returning_customer" ';
    else $account_name_class = '';

    if (strlen(trim($shipping_label)) > 0)
      $shipping_information = "SHIP TO THIS ADDRESS:\\n\\n$ship_attn\\n$ship_company\\n$ship_address\\n$ship_city, $ship_province\\n$ship_country, $ship_postal_code\\n\\n===>>> Find label on P: drive\\n\\n";
    else
      $shipping_information = "SHIP TO THIS ADDRESS:\\n\\n$ship_attn\\n$ship_company\\n$ship_address\\n$ship_city, $ship_province\\n$ship_country, $ship_postal_code\\n\\n";


    echo "<input name=\"account_name_$j\" id=\"account_name_$j\" type=\"hidden\" value=\"$account_name\"> ";
    if ($my_debug) {
      echo "Line $i: $order_id, $job_id, $order_type, $rush_type $ready_date<br>";
    }

    // Identify the rush status of the job, so it can be highlighted in the display.
    if ($rush_type == "RUSH") $lineitem_class = "lineitem_rush";
    elseif ($rush_type == "HOT") $lineitem_class = "lineitem_hot";
    elseif ($ready_date == $today) $lineitem_class = "lineitem_today";
    else $lineitem_class = "lineitem_std";

    // If the order is late, highlight it red.
    sscanf($ready_date, "%02d/%02d/%4d", $month, $day, $year);
    $ready = sprintf("%4d-%02d-%02d", $year, $month, $day);
    if ($ready < $todays_date) $lineitem_class = "lineitem_late";

    // Grab the details of the job.
    $query = "SELECT * FROM signboom_linedetail WHERE id = '$job_id'";
    $result = mysqli_query( $DBConn, $query) or die();
    $details = mysqli_fetch_assoc($result);
    // These fields are particular to type of job.
    $media = $details['product'];
    $options = $details['options'];
    $cost = $details['cost'];  // or should it be dctcost?
    $cost = ltrim($cost, "$"); // remove $ sign so we can do greater-than check
    $quantity = $details['quantity'];  
    $height = $details['itemheight'];  
    $width = $details['itemwidth'];  
    $square_footage = $details['printedarea'];  // for production we want to display printed area, not media area
    $current_queue = $details['currentqueue'];

    $the_filename = $details['filename'];
    $option_list = "FINISHING OPTIONS:\\n\\n Job Id: $job_id\\n\\nMedia: $media\\n\\nFile name: $the_filename\\n\\n----------------------\\n\\n";
    $option_codes = 
      $details['AF'] . " " .
      $details['AL'] . " " .
      $details['AI'] . " " .
      $details['AP'] . " " .
      $details['AK'] . " " .
      $details['BF'] . " " .
      $details['BB'] . " " .
      $details['BI'] . " " .
      $details['BP'] . " " .
      $details['BK'] . " " .
      $details['RF'] . " " .
      $details['RL'] . " " .
      $details['RB'] . " " .
      $details['RH'] . " " .
      $details['RE'] . " " .
      $details['RI'] . " " .
      $details['RP'] . " " .
      $details['RK'] . " " .
      $details['RO'];
    $the_options = explode(" ", $option_codes);
    for ($array_index = 0; $array_index < count($the_options); $array_index++) {
      $the_finishing_code = $the_options[$array_index];
      if ($the_finishing_code != "") {
        // TO DO: Replace query below with use of the array_options table built in query_finishing_options.php
        $query = "SELECT * FROM signboom_finishing WHERE Code = '$the_finishing_code'";
        $result = mysqli_query( $DBConn, $query) or die();
        $option_information = mysqli_fetch_assoc($result);
        $option_name = $option_information['OptionName'];
        $option_list .= $option_name . "\\n\\n";
      }
    }

    $lamination = "";
    if (strlen(trim($details['AL'])) > 0)
      $lamination = $details['AL'];
    else if (strlen(trim($details['RL'])) > 0)
      $lamination = $details['RL'];

    if ($lamination != "")
      $lamination_name = $array_options[$lamination];
    else
      $lamination_name = "";

    $cutting = "";
    if (strlen(trim($details['AF'])) > 0)
      $cutting = $details['AF'];
    else if (strlen(trim($details['BF'])) > 0)
      $cutting = $details['BF'];
    else if (strlen(trim($details['RF'])) > 0)
      $cutting = $details['RF'];

    if ($cutting != "")
      $cutting_name = $array_options[$cutting];
    else
      $cutting_name = "";

    $ink_layers = "";
    if (strlen(trim($details['AI'])) > 0)
      $ink_layers = $details['AI'];
    else if (strlen(trim($details['BI'])) > 0)
      $ink_layers = $details['BI'];
    else if (strlen(trim($details['RI'])) > 0)
      $ink_layers = $details['RI'];

    if ($ink_layers != "")
      $ink_layers_name = $array_options[$ink_layers];
    else
      $ink_layers_name = "";

    $mounting = "";
    if (strlen(trim($details['RH'])) > 0)
      $mounting = $details['RH'];

    if ($mounting != "")
      $mounting_name = $array_options[$mounting];
    else
      $mounting_name = "";

    /*
    $print_speed = "";
    if (strlen(trim($details['AP'])) > 0)
      $print_speed = $details['AP'];
    else if (strlen(trim($details['BP'])) > 0)
      $print_speed = $details['BP'];
    else if (strlen(trim($details['RP'])) > 0)
      $print_speed = $details['RP'];

    if ($print_speed != "")
      $print_speed_name = $array_options[$print_speed];
    else
      $print_speed_name = "";

    $ink_finish = "";
    if (strlen(trim($details['AK'])) > 0)
      $ink_finish = $details['AK'];
    else if (strlen(trim($details['BK'])) > 0)
      $ink_finish = $details['BK'];
    else if (strlen(trim($details['RK'])) > 0)
      $ink_finish = $details['RK'];

    if ($ink_finish != "")
      $ink_finish_name = $array_options[$ink_finish];
    else
      $ink_finish_name = "";
    */

    $sides = "";
    if (strlen(trim($details['RB'])) > 0)
      $sides = $details['RB'];
    else if (strlen(trim($details['BB'])) > 0)
      $sides = $details['BB'];

    if ($sides != "")
      $sides_name = $array_options[$sides];
    else
      $sides_name = "";

    // RE and RO will never happen simultenaously for a product.
    $other = "";
    if (strlen(trim($details['RE'])) > 0)
      $other = $details['RE'];
    else if (strlen(trim($details['RO'])) > 0)
      $other = $details['RO'];

    if ($other != "")
      $other_name = $array_options[$other];
    else
      $other_name = "";
      $queue="";
     if (($queue == 'Files') || ($queue == 'Pack'))
     {
       if ($ready_date != $current_ready_date) 
       {
         if ($current_ready_date != '') 
	 {
           // Display totals for that ready date.
           $ready_date_sq_ft = round($ready_date_sq_ft, 0); 
           $ready_date_cost = round($ready_date_cost, 0); 
	   echo '<tr class="subtotals"><td colspan="7">&nbsp;</td><td>' . $current_ready_date . '</td><td colspan="6"></td><td>' . $ready_date_quantity . '</td><td colspan="2">&nbsp;</td><td>' . $ready_date_sq_ft . '</td><td>$' . $ready_date_cost . '</td><td>&nbsp;</td></tr>';
         }

         // Reset totals for next ready date.
         $current_ready_date = $ready_date;
         $ready_date_quantity = 0;
         $ready_date_sq_ft = 0;
         $ready_date_cost = 0;
       }

       // Calculate subtotals for each ready date.
       $ready_date_quantity += $quantity;
       $ready_date_sq_ft += $square_footage;
       $ready_date_cost += $cost;
    }
    ?>

    <tr>
      <td><?php echo $other_name ?></td>
      <td><?php echo $mounting_name ?></td>
      <td><?php echo $sides_name ?></td>
      <td><?php echo $ink_layers_name ?></td>
      <td><?php echo $cutting_name ?></td>
      <td><?php echo $lamination_name ?></td> 
      <td><a <?php if ($options == 'CUS') echo 'class="lineitem_custom_finish"'; ?> 
             href="#" 
             onClick="alert('<?php echo htmlspecialchars($option_list); ?>'); return false;"><?php echo $media?></a></td>
      <!--<td><?php echo $job_id ?></td>-->
      <!--<td><?php echo $details['linenum'] ?></td>-->
      <td class="<?php echo $lineitem_class; ?>"><?php echo $ready_date ?></td>
      <td>
        <?php 
          if (strlen(trim($customer_notes)) > 0) {
            $output = str_replace($special_chars, $replacement_values, $customer_notes);
            echo "<a href=\"#\" style=\"color: #CC0000; font-weight: bold;\" onClick=\"displayMessage('$output'); return false;\">YES</a>";
          }
          else {
            echo "NO";
          }
        ?>
      </td>

      <td>
        <?php if ($cost > $expensive): ?>
          <a class="lineitem_checklist" href="orderitem.php?order_id=<?php echo $order_id ?>&rush=<?php echo $rush_type ?>&account=<?php echo $account_name ?>&ready=<?php echo $ready_date ?>"> <?php echo $order_id ?></a>
        <?php else: ?>
          <a href="orderitem.php?order_id=<?php echo $order_id ?>&rush=<?php echo $rush_type ?>&account=<?php echo $account_name ?>&ready=<?php echo $ready_date ?>"> <?php echo $order_id ?></a>
        <?php endif; ?>
      </td>

        <?php 
	/*
	// Not using this right now.
        $temp_reference = $reference_number;
        if (strlen($temp_reference) > 20) {
          $output2 = str_replace($special_chars, $replacement_values, $reference_number);
          echo "<a href=\"#\" onClick=\"alert('$output2'); return false;\">";
          echo substr($temp_reference, 0, 16); 
          echo "...";
          echo "</a>";
        }
        else {
          echo $reference_number;
        }
	*/
        ?>

      <td>
     <?php 
        if (strlen(trim($shipping_label)) > 0) {
         echo "<a href=\"#\" style=\"color: #CC0000; font-weight: bold;\" onClick=\"alert('$shipping_information')\">$shipping_type</a>";
       }
       else {
         echo "<a href=\"#\" onClick=\"alert('$shipping_information')\">$shipping_type</a>";
       }
     ?>
      </td>
      <td><?php echo $ship_city?></td> 
      <td>
       <?php if ($account_name == 'INCIDENT'): ?>
        <a <?php echo $account_name_class; ?> class="lineitem_incident" href="client.php?account=<?php echo urlencode($account_name); ?>" target="_blank"><?php echo $account_name; ?></a>
      <?php else: ?>
        <a <?php echo $account_name_class; ?> href="client.php?account=<?php echo urlencode($account_name); ?>" target="_blank"><?php echo $account_name; ?></a>
      <?php endif; ?>
      </td>
      <td>
      <?php 
        $temp_filename = $details['filename'];
        if (strlen($temp_filename) > 26) {
          echo "<a href=\"#\" onClick=\"alert('$temp_filename'); return false;\">";
          echo substr($temp_filename, 0, 22); 
          echo "...";
          echo "</a>";
        }
        else {
          echo $temp_filename;
        }
      ?>
      </td>
      <td class="number"><?php printf("%d", $quantity); ?></td>
      <td class="number"><?php printf("%.2f", round($height, 2)); ?></td>
      <td class="number"><?php printf("%.2f", round($width, 2));  ?></td>
      <td class="number"><?php echo round($square_footage); ?></td>
      <td class="number"><?php echo '$' . round($cost); ?></td>
      <?php 
        if ($this_is_single_order_page)
        {
            echo '<td><a href="http://signboom.com/production/orders.php?product=ALL&queue=' . $current_queue . '">' . $current_queue . '</a></td>';
        }
        else if (($queue == 'Files') || ($queue == 'Today'))
        {
            echo '<td><a href="http://signboom.com/production/orders.php?product=ALL&queue=' . $current_queue . '">' . $current_queue . '</a></td>';
        }
        else
        {
          if ($queue == 'RIP')
          {
            echo '<td align="center">';
	    // Display extra checkbox for 'Proof'. Don't allow this to be unchecked as checking it sends an email to the customer.
            if ($current_queue == 'RIP')
              echo "<input name=\"proofed_$j\" type=\"checkbox\" id=\"proofed_$j\" value=\"yes\" class=\"myinput\" CHECKED DISABLED>";
            else 
              echo "<input name=\"proofed_$j\" type=\"checkbox\" id=\"proofed_$j\" value=\"yes\" class=\"myinput\" onClick=\"CheckboxTicked($j, 'Proof', $job_id)\">";
            echo '</td>';
          }
          echo '<td align="center">';
	  // Display checkbox for the queue associated with this page. 
          echo "<input name=\"done_$j\" type=\"checkbox\" id=\"done_$j\" value=\"yes\" class=\"myinput\" onClick=\"CheckboxTicked($j, '$queue', $job_id)\">";
          echo '</td>';
        }
      ?>
    </tr>


