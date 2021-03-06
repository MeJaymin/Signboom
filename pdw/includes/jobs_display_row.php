  <?php 

    // Calculate which row on the page we are dealing with.
    $start_row="0";
    $j = $i - $start_row + 1;  // index from 1 up

    $order_id = $row['ID'];
    echo "<input name=\"order_id_$j\" id=\"order_id_$j\" type=\"hidden\" value=\"$order_id\"> ";
    $job_id = $row['jobid'];
    echo "<input name=\"job_id_$j\" id=\"job_id_$j\" type=\"hidden\" value=\"$job_id\"> ";
    $order_type = $row['ordertype'];
    echo "<input name=\"order_type_$j\" id=\"order_type_$j\" type=\"hidden\" value=\"$order_type\"> ";
    $rush_type = $row['rushtype'];
    $ready_date = $row['readydate'];
    $customer_notes = $row['customernotes'];
    $reference_number = $row['refnum'];
    $shipping_type = $row['shiptype'];
    $account_name = $row['AcctName'];
    $email_proofed = isset($row['emailproofed'])?$row['emailproofed']:"";
    $email_packed = isset($row['emailpacked'])?$row['emailpacked']:"";

    $ship_attn = $row['shipattn'];
    $ship_company = $row['shipcompany'];
    $ship_address = $row['shipaddress'];
    $ship_city = $row['shipcity'];
    $ship_province = $row['shipprov'];
    $ship_postal_code = $row['shipzip'];
    $ship_country = $row['shipcountry'];
    $remember_this_address = $row['shiptoadd'];
    $shipping_label = $row['documentname'];

    $first_order = $row['firstorder'];
    $returning_customer = $row['returningcustomer'];
    if ($first_order) $account_name_class = ' class="lineitem_first_order" ';
    else if ($returning_customer) $account_name_class = ' class="lineitem_returning_customer" ';
    else $account_name_class = '';

    if (strlen(trim($shipping_label)) > 0)
      $shipping_information = "SHIP TO THIS ADDRESS:\\n\\n$ship_attn\\n$ship_company\\n$ship_address\\n$ship_city, $ship_province\\n$ship_country, $ship_postal_code\\n\\n===>>> Find label on P: drive\\n\\n";
    else
      $shipping_information = "SHIP TO THIS ADDRESS:\\n\\n$ship_attn\\n$ship_company\\n$ship_address\\n$ship_city, $ship_province\\n$ship_country, $ship_postal_code\\n\\n";

    $special_chars =      array("&",     "\"",      "'",     "%",      "(",      ")");
    $replacement_values = array("&#038;", "&#034;", "&#039;", "&#037;", "&#040;", "&#041;");

    echo "<input name=\"account_name_$j\" id=\"account_name_$j\" type=\"hidden\" value=\"$account_name\"> ";
    // Identify the rush status of the job, so it can be highlighted in the display.
    if ($rush_type == "RUSH") $lineitem_class = "lineitem_rush";
    elseif ($rush_type == "HOT") $lineitem_class = "lineitem_hot";
    else $lineitem_class = "lineitem_std";

    // Grab the details of the job.
    $query = "SELECT * FROM signboom_linedetail WHERE id = '$job_id'";
    $result = mysqli_query( $DBConn, $query) or die();
    $details = mysqli_fetch_assoc($result);
    // These fields are particular to type of job.
    $media = $details['product'];
    $options = $details['options'];
    $cost = $details['cost'];  // or should it be dctcost?
    $height = $details['itemheight'];  
    $width = $details['itemwidth'];  
    $square_footage = $details['printedarea'];  // for production we want to display printed area, not media area

    $the_filename = $details['filename'];
    $option_list = "FINISHING OPTIONS:\\n\\n Job Id: $job_id\\n\\nMedia: $media\\n\\nFile name: $the_filename\\n\\n----------------------\\n\\n";
    $option_codes = 
      $details['AF'] . " " .
      $details['AL'] . " " .
      $details['AI'] . " " .
      $details['BF'] . " " .
      $details['BB'] . " " .
      $details['BI'] . " " .
      $details['RF'] . " " .
      $details['RL'] . " " .
      $details['RB'] . " " .
      $details['RH'] . " " .
      $details['RE'] . " " .
      $details['RI'] . " " .
      $details['RO'];
    $the_options = explode(" ", $option_codes);
    for ($array_index = 0; $array_index < count($the_options); $array_index++) {
      $the_finishing_code = $the_options[$array_index];
      if ($the_finishing_code != "") {
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

    $cutting = "";
    if (strlen(trim($details['AF'])) > 0)
      $cutting = $details['AF'];
    else if (strlen(trim($details['BF'])) > 0)
      $cutting = $details['BF'];
    else if (strlen(trim($details['RF'])) > 0)
      $cutting = $details['RF'];
    ?>

    <tr class="<?php echo $lineitem_class; ?>">
      <td><?php echo $media ?></td>
      <td><a href="#" onClick="alert('<?php echo htmlspecialchars($option_list); ?>'); return false;">Click</a></td>
      <td><?php echo $job_id ?></td>
      <td><a href="orderitem.php?order_id=<?php echo $order_id ?>&rush=<?php echo $rush_type ?>&account=<?php echo $account_name ?>&ready=<?php echo $ready_date ?>"> <?php echo $order_id ?></a></td>
      <td><?php echo $details['linenum'] ?></td>
      <td><?php echo $ready_date ?></td>
      <td>
      <?php 
        $temp_filename = $details['filename'];
        if (strlen($temp_filename) > 20) {
          echo "<a href=\"#\" onClick=\"alert('$temp_filename'); return false;\">";
          echo substr($temp_filename, 0, 16); 
          echo "...";
          echo "</a>";
        }
        else {
          echo $temp_filename;
        }
      ?>
      </td>
      <td><?php echo $details['quantity'] ?></td>
      <td align="center"><?php echo $cost ?></td>
      <td align="center">
        <?php 
        $myProofed = $details['proofed'];
        $myPrinted = $details['printed'];
        $myFinished = $details['finished'];
        $myPacked = $details['packed'];

        if (($email_proofed == "yes") || ($email_packed == "yes"))
          echo "<input name=\"proofed_$j\" type=\"checkbox\" id=\"proofed_$j\" value=\"yes\" class=\"myinput\" CHECKED DISABLED>";
        else if ($myProofed == "yes") 
          echo "<input name=\"proofed_$j\" type=\"checkbox\" id=\"proofed_$j\" value=\"yes\" class=\"myinput\" CHECKED DISABLED>";
        else 
          echo "<input name=\"proofed_$j\" type=\"checkbox\" id=\"proofed_$j\" value=\"yes\" class=\"myinput\" DISABLED>";
        ?>
      </td>
      <td align="center">
        <?php 
        if ($email_packed == "yes") 
          echo "<input name=\"printed_$j\" type=\"checkbox\" id=\"printed_$j\" value=\"yes\" class=\"myinput\" CHECKED DISABLED>";
        else if ($myPrinted == "yes") 
          echo "<input name=\"printed_$j\" type=\"checkbox\" id=\"printed_$j\" value=\"yes\" class=\"myinput\" CHECKED DISABLED>";
        else
          echo "<input name=\"printed_$j\" type=\"checkbox\" id=\"printed_$j\" value=\"yes\" class=\"myinput\" DISABLED>";
        ?>
      </td>
      <td align="center">
        <?php 
        if ($email_packed == "yes") 
          echo "<input name=\"packed_$j\" type=\"checkbox\" id=\"packed_$j\" value=\"yes\" class=\"myinput\" CHECKED DISABLED>";
        else if ($myPacked == "yes") 
          echo "<input name=\"packed_$j\" type=\"checkbox\" id=\"packed_$j\" value=\"yes\" class=\"myinput\" CHECKED DISABLED>";
        else 
          echo "<input name=\"packed_$j\" type=\"checkbox\" id=\"packed_$j\" value=\"yes\" class=\"myinput\" DISABLED>";
        ?>
      </td>
    </tr>


