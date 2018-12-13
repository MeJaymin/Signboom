<?php
include_once 'helpers/db_helper.php';
// text message build
  
  function bldmsg($rs, $dtl, $rsUser) {

    // Convert HTML codes back to special characters for displaying in an ASCII email.
    $customer_notes = mysqli_result($rs, 0, 'customernotes');
    $html_codes =     array("&#038;", "&#034;", "&#039;", "&#037;", "&#040;", "&#041;", "&#010;", "&#010;", "&#010;");
    $special_chars =  array("&",      "\"",     "'",      "%",      "(",      ")",      "\r\n",   "\r",     "\n");
    $customer_notes = str_replace($html_codes, $special_chars, $customer_notes);


    $msg = "Thank you for your order.\n\n";
    $msg .= "This email confirms that we have received the following order details and files.\n\n";
    $msg .= "\t1.\tYou will receive another confirmation once your file(s) have been reviewed and are queued for printing.\n";
    $msg .= "\t2.\tOnce printed and inspected you will receive an additional email letting you know your file is ready to ship or pick up per your original instructions.\n\n";

    $msg .= "Name:\t\t\t".mysqli_result($rsUser, 0, 'firstName')." ".mysqli_result($rsUser, 0, 'lastName')."\n\n";
    $msg .= "Company:\t\t".mysqli_result($rsUser, 0, 'company')."\n\n";
    $msg .= "Phone:\t\t".mysqli_result($rsUser, 0, 'phone1')." OR ".mysqli_result($rsUser, 0, 'phone2')."\n\n";
    $msg .= "Account:\t\t".mysqli_result($rsUser, 0, 'AcctName')."\n\n";
    $msg .= "Ship To:\t\t".mysqli_result($rs, 0, 'shipcompany')."\n";
    $msg .= "\t\t\t".mysqli_result($rs, 0, 'shipaddress')."\n";
    $msg .= "\t\t\t".mysqli_result($rs, 0, 'shipcity').', '.mysqli_result($rs, 0, 'shipprov').' '.mysqli_result($rs, 0, 'shipzip')."\n\n";

    if (strlen(trim(mysqli_result($rs,  0,  'documentname'))) > 0)
      $msg .= "\t\t\t*** SHIPPING LABEL HAS BEEN PROVIDED WITH ORDER ***\n\n";

    $order_cost = 0.0;
    for ($i = 1; $i <= 10; $i++) { 
      if (isset($dtl[$i]->code) && $dtl[$i]->code != "") {
        $msg = $msg . "Product: "  . $dtl[$i]->code     . "\n";
        $msg = $msg . "Options: "  . $dtl[$i]->options  . "\n";
        $msg = $msg . "Quantity: " . $dtl[$i]->quantity . "\n";
        $msg = $msg . "Size: "     . $dtl[$i]->width . " x " . $dtl[$i]->height . "\n";
        $order_cost += substr($dtl[$i]->total, 1);
        $tmp = substr(strrchr($dtl[$i]->filename, "\\"), 1);
        $msg .= "Filename: " . ($tmp ? $tmp : $dtl[$i]->filename) . " ";
        // If there is a second filename, put it on the same line.
        if(isset($dtl[$i]->filename2))
        {
          $tmp = substr(strrchr($dtl[$i]->filename2, "\\"), 1);
          if ($dtl[$i]->filename2 != "") $msg .= " and " . $tmp;
          $msg .= "\n\n";
        }
      }
    }

    $msg .= "Your order will be ready by: ".mysqli_result($rs, 0, 'readydate').".\n";
    $msg .= "Order Number: ".mysqli_result($rs, 0, 'ID')."\n";
    $msg .= "Your Reference Info: ".mysqli_result($rs, 0, 'refnum')."\n\n";
    $msg .= "Your Customer Notes: ".$customer_notes."\n\n";
    $msg .= "\n\n";

    // Calculate the percentage discount which has been applied to the order. 
    // We no longer grab the discount percentage from the user database, because that
    // discount is only applied to the DISCOUNTABLE amount of the media costs.
    // Instead, the javascript code calculates a dollar amount for the discount, choosing
    // either the customer's discount level, or an order-size based email, whichever is
    // a better deal for the customer. Then we convert that to a percentage (here) and 
    // then (below) apply that percentage evenly to each row in the billing summary.
    $discount_dollars = mysqli_result($rs, 0, 'discount');
    $discount_amount = substr($discount_dollars, 1);
    $discount_percentage = $discount_amount / $order_cost;

    $msg .= "Billing Summary:"."\n";
    $msg .= "----------------"."\n";

    // Total up the amount of material used in each medium and
    // display the square footage for each product.  We now group products by line item here
    // as we do in the bldhtml() function which sends html confirmation emails.
    $linecode = "";
    for ($i = 1; $i <= 10; $i++) { 
      if (isset($dtl[$i]->code) && $linecode != $dtl[$i]->code) {
        if ($linecode != "") {
          // This is a new medium.  Print out results for previous medium.
          $msg .= str_pad($linecode, 10);
          if ($linesfootage == 0.0) {
            $msg .= '';
          }
          else {
            $msg .= str_pad($linesfootage." sq ft", 14);
          }
          $msg .= str_pad(" ", 12); // where lineal footage used to be
          // Apply the percentage discount evenly across all items.
          $cost_net_discount = $lineamt * (1.0 - $discount_percentage);
          $msg .= getcurrency($cost_net_discount)."\n";

        }
        $linecode = $dtl[$i]->code;
        $linesfootage = 0;
        $lineamt = 0;
      }

      if (isset($dtl[$i]->code) && $dtl[$i]->code != "") {
        $lineamt += (substr($dtl[$i]->total, 1));
        $wsfootage = trim($dtl[$i]->sqfootage);
        $linesfootage += $wsfootage;
      }

    }

    // Print out results for final medium, if there is one.
    if ($linecode != "") {
      $msg .= str_pad($linecode, 10);
      if ($linesfootage == 0.0) {
        $msg .= '';
      }
      else {
        $msg .= str_pad($linesfootage." sq ft", 14);
      }
      $msg .= str_pad(" ", 12); // where lineal footage used to be
      // Apply the percentage discount evenly across all items.
      $cost_net_discount = $lineamt * (1.0 - $discount_percentage);
      $msg .= getcurrency($cost_net_discount)."\n";
    }

    $totsetup = substr(mysqli_result($rs, 0, 'setupfee'), 1);
    $totsetup = getcurrency($totsetup);
    $msg .= str_pad("Setup:",36).$totsetup."\n";

    $thepromocode = mysqli_result($rs, 0, 'promocode');
    $thepromodiscount = mysqli_result($rs, 0, 'promodiscount');
    if ($thepromodiscount != 0) {
      $thepromodiscount = getcurrency($thepromodiscount);
      $msg .= str_pad("Promo Discount:",27).$thepromodiscount." (".$thepromocode.")\n";
    }

    $therushfee = substr(mysqli_result($rs, 0, 'rushfee'), 1);
    if ($therushfee != 0) {
      $therushfee = getcurrency($therushfee);
      $msg .= str_pad("Rush:",36).$therushfee."\n";
    }

    // Handle special case where freight cost is given as 'Call'.
    // Ideally the getcurrency() function should do the substr (var, 1), not the calling function.
    // Then we wouldn't need this special case.  But I don't want to have to change all the
    // places in the code that call getcurrency().  Alison.  Feb 2014.
    if (strncmp(mysqli_result($rs, 0, 'freight'), '$', 1) == 0)
      $thefreight = substr(mysqli_result($rs, 0, 'freight'), 1); // strip off the $ sign
    else
      $thefreight = substr(mysqli_result($rs, 0, 'freight'), 0); // pass through the whole value

    $thefreight = getcurrency($thefreight);
    $msg .= str_pad("Freight:",10).str_pad(mysqli_result($rs, 0, 'shiptype'),26).getcurrency($thefreight)."\n";

    $thepst = substr(mysqli_result($rs, 0, 'pst'), 1);
    if ($thepst != 0) {
      $thepst = getcurrency($thepst);
      $msg .= str_pad("PST:",36).getcurrency($thepst)."\n";
    }
    $thegst = substr(mysqli_result($rs, 0, 'gst'), 1);
    if ($thegst != 0) {
      $thegst = getcurrency($thegst);
      $msg .= str_pad("GST:",36).getcurrency($thegst)."\n";
    }

    $msg .= str_pad("Total:",36).mysqli_result($rs, 0, 'ordertotal')."\n\n";

    $msg .= "\nThank you for your order.\n";
    $msg .= "\nwww.signboom.com.\n";
    $msg.="\n".date("D M j G:i:s T Y");
    return($msg);
  }   
?>
