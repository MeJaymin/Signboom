  <table border="0" align="center" cellpadding="5">
    <tr class="heading">
      <td>Product</td>
      <td>Finishing</td>
      <td>File Id</td>
      <td>Line #</td>
      <td>Ready Date</td>
      <td>File Name</td>
      <td>Quantity</td>
      <td>Cost</td>
      <td>Proof Done</td>
      <td>Print Done</td>
      <td>Finish Done</td>
    </tr>
    <tr>
      <td colspan="15"><hr></td>
    </tr>

  <?php
    $reference_number = $row['refnum'];
    $customer_notes = $row['customernotes'];
    echo "<h2>Order Id: $order_id</h2>";
    if (strlen(trim($customer_notes)) > 0) {
      $special_chars =      array("&",     "\"",      "'",     "%",      "(",      ")");
      $replacement_values = array("&#038;", "&#034;", "&#039;", "&#037;", "&#040;", "&#041;");
      $output = str_replace($special_chars, $replacement_values, $customer_notes);
      echo "<b>Customer Notes:</b>$output<br><br>";
    }
    if (strlen(trim($reference_number)) > 0) 
      echo "<b>Customer Reference:</b>$reference_number<br><br>";
  ?>

  <form name="main_form" action=<?php echo $_SERVER['PHP_SELF'] ?> method="POST">

  <?php
  // Skip the rows we don't need to look at.
  for ($i = 0; $i < $start_row; $i++) {
    $row = mysql_fetch_assoc($jobs);
  }
  for ($i = $start_row; ($i < $start_row + $rows_per_page) && ($i < $num_jobs); $i++) {
    // Calculate which row on the page we are dealing with.
    $j = $i - $start_row + 1;  // index from 1 up

    // Grab information about that job 
    $row = mysql_fetch_assoc($jobs);
    if ($row == FALSE) echo "Could not read job from database.";
    $order_id = $row['orderid'];
    echo "<input name=\"order_id_$j\" id=\"order_id_$j\" type=\"hidden\" value=\"$order_id\"> ";
    $job_id = $row['jobid'];
    echo "<input name=\"job_id_$j\" id=\"job_id_$j\" type=\"hidden\" value=\"$job_id\"> ";
    $order_type = $row['ordertype'];
    echo "<input name=\"order_type_$j\" id=\"order_type_$j\" type=\"hidden\" value=\"$order_type\"> ";
    $rush_type = $row['rushtype'];
    $ready_date = $row['readydate'];
    $reference_number = $row['refnum'];
    $customer_notes = $row['customernotes'];
    $shipping_type = $row['shiptype'];
    $account_name = $row['accountname'];
    $uploaded = $row['uploaded'];
    $email_proofed = $row['emailproofed'];
    $email_packed = $row['emailpacked'];
    $first_order = $row['firstorder'];
    $returning_customer = $row['returningcustomer'];
    if ($first_order) $account_name_class = ' class="lineitem_first_order" ';
    else if ($returning_customer) $account_name_class = ' class="lineitem_returning_customer" ';
    else $account_name_class = '';

    echo "<input name=\"account_name_$j\" id=\"account_name_$j\" type=\"hidden\" value=\"$account_name\"> ";
    if ($my_debug) {
      echo "Line $i: $order_id, $job_id, $order_type, $rush_type $ready_date<br>";
    }

    // Identify the rush status of the job, so it can be highlighted in the display.
    if ($rush_type == "RUSH") $lineitem_class = "lineitem_rush";
    elseif ($rush_type == "HOT") $lineitem_class = "lineitem_hot";
    else $lineitem_class = "lineitem_std";

    // Grab the details of the job.
    $query = "SELECT * FROM signboom_linedetail WHERE id = '$job_id'";
    $result = mysql_query($query, $DBConn) or die();
    $details = mysql_fetch_assoc($result);
    $media = $details['product'];
    $options = $details['options'];
    $cost = $details['cost'];  // or should it be dctcost?
    $height = $details['itemheight'];  
    $width = $details['itemwidth'];  
    $square_footage = $details['printedarea'];  // want to display printed area not media area

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
        $result = mysql_query($query, $DBConn) or die();
        $option_information = mysql_fetch_assoc($result);
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

    // Display the details of the job.
    if (mysql_num_rows($result) == 0) {
    ?>
    <tr class="<?php echo $lineitem_class ?>">
      <td><?php echo $order_id ?></td>
      <td><?php echo $job_id ?></td>
      <td colspan="14" align="left"><b>Order <?php echo $job_id ?>: Could not find job.</b></td>
    </tr>
    <?
    }
    else {
    ?>
    <tr class="<?php echo $lineitem_class; ?>">
      <td><?php echo $media ?></td>
      <td><a href="#" onClick="alert('<?php echo $option_list; ?>'); return false;">Click</a></td>
      <td><?php echo $job_id ?></td>
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
      <td><?php echo $cost ?></td>
      <td align="center">
        <?php 
        $myProofed = $details['proofed'];
        $myPrinted = $details['printed'];
        $myFinished = $details['finished'];
        $myPacked = $details['packed'];

        if (($email_proofed == "yes") || ($email_packed == "yes"))
          echo "<input name=\"proofed_$j\" type=\"checkbox\" id=\"proofed_$j\" value=\"yes\" class=\"myinput\" CHECKED DISABLED>";
        else 
          echo "<input name=\"proofed_$j\" type=\"checkbox\" id=\"proofed_$j\" value=\"yes\" class=\"myinput\" DISABLED>";
        ?>
      </td>
      <td align="center">
        <?php 
        if ($email_packed == "yes") 
          echo "<input name=\"printed_$j\" type=\"checkbox\" id=\"printed_$j\" value=\"yes\" class=\"myinput\" CHECKED DISABLED>";
        else
          echo "<input name=\"printed_$j\" type=\"checkbox\" id=\"printed_$j\" value=\"yes\" class=\"myinput\" DISABLED>";
        ?>
      </td>
      <td align="center">
        <?php 
        if ($email_packed == "yes") 
          echo "<input name=\"packed_$j\" type=\"checkbox\" id=\"packed_$j\" value=\"yes\" class=\"myinput\" CHECKED DISABLED>";
        else 
          echo "<input name=\"packed_$j\" type=\"checkbox\" id=\"packed_$j\" value=\"yes\" class=\"myinput\" DISABLED>";
        ?>
      </td>
    </tr>
    <?
    }
  } // end of for loop
?>
    </form>  <!-- end of <form name="main_form"> -->
    <tr>
      <td colspan="15"><hr></td>
    </tr>
  </table>

