<?php

  require_once('../../Connections/DBConn.php');

  /********************************************************************************************
  *  Minor Functions
  *
  ********************************************************************************************/

  function intdiv($numerator, $denominator)
  {
    return floor(($numerator * 1.0) / ($denominator * 1.0));
  }

  function isValidDate($date_yymmdd)
  {
    if (!preg_match("/^[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])$/", $date_yymmdd))
      return false;

    sscanf($date_yymmdd, "%2d%2d%2d", $year, $month, $day);
    $year += 2000; // switch to 4 digit year
    return checkdate($month, $day, $year);
  }

  function isValidDueDate($due_date_yymmdd)
  {
    global $DBConn;
    global $hostname_DBConn;
    global $database_DBConn;
    global $username_DBConn;
    global $password_DBConn;

    // For the TED project, we can ignore extra_days.  Later we may want to take them into consideration.
    // If we do that, we would compare due_date against (today + cutoff_days + extra_days)

    $query = "SELECT cutofftime FROM signboom_parm WHERE ID = 1"; 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) 
    { 
      $cutoff_time = $row['cutofftime'];
    }

    $order_date_yymmdd = getEffectiveOrderDate($cutoff_time);

    sscanf($due_date_yymmdd, "%2d%2d%2d", $year, $month, $day);
    $year += 2000; // switch to 4 digit year
    $due_date_timestamp = mktime(0, 0, 0, $month, $day, $year);

    sscanf($order_date_yymmdd, "%2d%2d%2d", $year, $month, $day);
    $year += 2000; // switch to 4 digit year
    $order_date_timestamp = mktime(0, 0, 0, $month, $day, $year);

    // item due before it was ordered
    if ($due_date_timestamp < $order_date_timestamp)
    {
      return false;
    }

    $date_diff = floor(($due_date_timestamp - $order_date_timestamp) / (60 * 60 * 24));

    return true;
  }

  function isStandardFinishing($product, $finishing_options_array)
  {
    global $DBConn;
    global $hostname_DBConn;
    global $database_DBConn;
    global $username_DBConn;
    global $password_DBConn;

    // If any one of the options specified is not a default option (value "2") for that
    // product, then the finishing is NOT standard.

    // Check each finishing option to see if it is a standard option for that product.
    for ($i = 0; $i < count($finishing_options_array); $i++)
    {
      $finishing_option_code = $finishing_options_array[$i];
      $query = "SELECT * FROM signboom_product_finishing WHERE ProductCode = '$product' AND FinishingOptionCode = '$finishing_option_code' AND Value = 2";
      mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      if (mysqli_num_rows($result) == 0)
        return false;
    }

    return true;
  }

  /********************************************************************
  * getEffectiveOrderDate:
  *
  * We don't look at square footage
  ********************************************************************/

  function getEffectiveOrderDate($cutoff_time) 
  {
    global $hostname_DBConn;
    global $database_DBConn;
    global $username_DBConn;
    global $password_DBConn;
    global $DBConn;

    // Once we have PHP 5.1 and can set the default time zone, we can get rid of the -3 in the line below.
    $today_vancouver = mktime(date("H")-3, date("i"), date("s"), date("m"), date("d"), date("Y"));
    $today = date("Y-m-d H:i:s", $today_vancouver);   // e.g.2001-03-10 17:16:18 (the mysql DATETIME format)
    $hour = date("H", $today_vancouver);
    $day = date("d", $today_vancouver);
    $month = date("m", $today_vancouver);
    $year = date("Y", $today_vancouver);
  
    // If order is placed past the cutoff time, treat the order as if it came in on the
    // following day at 6 am.  
    // cutoff_time is the cutoff time for orders, in 24 hour clock.  This is stored in the database. 
    // It is set through that admin website in Vancouver time zone.  Usually something like 12 or 13.
    if ($hour >= $cutoff_time)
      $effective_order_date = mktime(6, 0, 0, $month, $day + 1, $year);
    else
      $effective_order_date = $today_vancouver;
    $day = date("d", $effective_order_date);
    $month = date("m", $effective_order_date);
    $year = date("Y", $effective_order_date);

    // For the TED project, we can assume 5 day workweeks (Mon - Fri) with no vacation/shutdown days.
    // If order is placed on a weekend, treat it as if it is placed on the following Monday.
    // Later we can add in handling for stat holidays and shutdowns.
    $sunday = 0;
    $saturday = 6;
    $day_of_week = date("w", $effective_order_date);
    if ($day_of_week == $saturday) 
      $effective_order_date = mktime(6, 0, 0, $month, $day + 2, $year);
    else if ($day_of_week == $sunday)   
      $effective_order_date = mktime(6, 0, 0, $month, $day + 1, $year);

    // convert effective order date to yymmdd format
    $effective_order_date = date("ymd", $effective_order_date);   // e.g. 150309
  
    return $effective_order_date;

  }

  /********************************************************************************************
  *  isValidFileName
  ********************************************************************************************/

  function isValidFileName(&$error_message, &$location, &$finishing, &$height, &$width, &$quantity, &$due_date, &$sides, &$double_sided, &$hardware, &$reference, &$notes)
  {
    global $hostname_DBConn;
    global $database_DBConn;
    global $username_DBConn;
    global $password_DBConn;
    global $DBConn;

    // Initialize variables.
    $error_message = '';
    $location = '';
    $finishing = '';
    $height = 0;
    $width = 0;
    $quantity = 0;
    $due_date = '';
    $sides = '';
    $double_sided = '';
    $hardware = '';
    $reference = '';
    $notes = '';

    $account_name = $_GET['account_name'];
    $product_code = $_GET['product_code'];
    $original_file_name = $_GET['file_name'];

    $error_message_end = "\n\nAny other files you dropped along with this file have NOT been added to the order.";

    // Parse file name. 
    $details = explode("_", $original_file_name);
    if (count($details) >= 8)
    {
      $event = $details[0]; // They put something like TS18 for TED2018 in here. 
      $department_code = $details[1];
      $associate = $details[2];
      $reference = $details[3];
      $sign_type = $details[4];
      $dimensions = $details[5];
      $quantity = $details[6];
      $sides = $details[7];
      $finishing = $details[8];
      if (count($details) == 10) 
      {
        $notes = $details[9];
        // Strip off .pdf from end of notes.
        $notes = substr($notes, 0, -4);
      }
      else
      {
	$notes = '';
        // Strip off .pdf from end of finishing
        $finishing = substr($finishing, 0, -4);
      }

      if ($product_code == 'SIN03ESL') // drop box they dropped into
        $hardware = 'EASEL';
      else
        $hardware = 'NONE';

      // At the moment there are not multiple locations to deliver to, so this is hardcoded to blank.
      $location = '';

      // Due date used to be given in each file name and parsed out by AJAX filename validation.
      // At the moment it isn't, so we hardcode it here.
      $due_date = '180330'; 

    }
    else
    {
      $error_message = "There is an error in this file name:\n\n" . $original_file_name . 
            "\n\nIt must be in the format (where notes are optional):" .
            "\n\nconference_dept_ associate_reference_signtype_dimensions_quantity_sides_finishing_notes.pdf" .
            "\n\nThere must be underscores (not dashes) between each piece of information." .
            "\n\nUnderscores are NOT permitted WITHIN the notes string, but you may use dashes within it." . 
            "\n\nIt has " . count($details) . " parts to it. It should have either 9 or 10 parts." . 
            $error_message_end . "\n\n";
      return false;
    }

    // Validate elements of original filename. 

    $location = strtoupper($location);

    if (sscanf($dimensions, "%fx%f", $width, $height) != 2)
    {
      if (sscanf($dimensions, "%fX%f", $width, $height) != 2)
      {
        $width = 0;
        $height = 0;
        if (sscanf($dimensions, "%f", $diameter) != 1)
        {
          $error_message = "There is an error in file name '" . $original_file_name . 
                "'.\n\nDimensions must be given as widthxheight (eg 18.5x30.25) or diameter (eg 24)." . $error_message_end;
          return false;
        }
      }
    }
    if (($width == 0) && ($height == 0) && ($diameter > 0))
    {  
      $width = $diameter;
      $height = $diameter;
    }

    if (sscanf($quantity, "q%d", $quantity_number) == 1)
    {
      $quantity = $quantity_number;
    }
    else if (sscanf($quantity, "Q%d", $quantity_number) == 1)
    {
      $quantity = $quantity_number;
    }
    else
    {
      $error_message = "There is an error in file name '" . $original_file_name . 
            "'.\n\nThe quantity '" . quantity . "' must be an integer (e.g. 1, 23) prefixed with a Q or a q (e.g. Q5)." . $error_message_end;
      return false;
    }

    $error_sides = false;
    $sides = strtoupper($sides);
    if ($sides == "S1")
    {
      $sides = 1;
      $double_sided = "";
    }
    else if ($sides == "S2S")
    {
      $sides = 2;
      $double_sided = "same";
    }
    else if ($sides == "S2D")
    {
      $sides = 2;
      $double_sided = "different";
    }
    else
    {
      $error_sides = true;
    }

    if ($error_sides)
    {
      $error_message = "There is an error in file name '" . $original_file_name . 
            "'.\n\nThe number of sides must be specified as S1 or S2S or S2D. (s1, s2s and s2d are also acceptable.)" . $error_message_end;
      return false;
    }

    /* COMMENTED OUT BECAUSE KIM THINKS THEY MAY WANT MORE FLEXIBILITY IN MEDIA CHOICE.
    // Check that the dropbox they dropped the file in (product_code) is right for the
    // product code given in the filename (sign_type).
    if ((strtoupper($sign_type) == 'FS') && ($product_code != 'GAT50'))
    {
      $error_message = "You have dropped the floor sign (FS) file '$original_file_name' into the wrong box. All floor signs should be dropped into the 'Gatorboard 1/2 inch' box." . $error_message_end;
      return false;
    }
    else if ((strtoupper($sign_type) == 'TT') && ($product_code != 'SIN03ESL'))
    {
      $error_message = "You have dropped the table top (TT) file '$original_file_name' into the wrong box. All table top signs should be dropped into the 'Sintra with Easel' box." . $error_message_end;
      return false;
    }
    else if ((strtoupper($sign_type) == 'LOL') && ($product_code != 'LOL'))
    {
      $error_message = "You have dropped the lollipop (LOL) file '$original_file_name' into the wrong box. All lollipop signs should be dropped into the 'Lollipop Signs' box." . $error_message_end;
      return false;
    }
    else if ((strtoupper($sign_type) == 'SB') && ($product_code != 'COR04'))
    {
      $error_message = "You have dropped the sandwich board (SB) file '$original_file_name' into the wrong box. All sandwich board signs should be dropped into the 'Coroplast 4mm' box." . $error_message_end;
      return false;
    }
    else if ((strtoupper($sign_type) == 'TD') && ($product_code != 'RAV'))
    {
      $error_message = "You have dropped the tear drop (TD) file '$original_file_name' into the wrong box. All tear drop signs should be dropped into the 'Removable Vinyl' box." . $error_message_end;
      return false;
    }
    */

    if (($product_code == "SSB") && ($sides == 2))
    {
      $error_message = "This interface does not currently support ordering of Double Sided Scrim Banner. Please place this order offline or through our regular order form."  . $error_message_end;
      return false;
    }
    else if (($product_code != "ACP03") && ($product_code != "SIN03") && ($product_code != "COR04") && 
             ($product_code != "LOL")   && ($product_code != "GAT50") && ($product_code != "GAT18") &&
             ($sides == 2))
    {
      $error_message = "Double-sided printing is not available on the product " . $product_code . "." . $error_message_end;
      return false;
    }

    // TO DO: Later this should be modified so that valid finishing options are retrieved
    // from the database, rather than hard-coded.
    $finishing = strtoupper($finishing);

    // Check that this product/finishing code combination is valid for that client.
    $query = "SELECT Id FROM signboom_user_products WHERE AcctName = '$account_name' AND ProductCode = '$product_code' AND FinishingCode = '$finishing' AND (Enabled = 1 OR Enabled = 2)"; 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    if (mysqli_num_rows($result) == 0)
    {
      $error_message = "There is an error in file name '" . $original_file_name . 
            "'.\n\nYou've specified a finishing option which is not valid, or which is not valid for that product. Please review the instructions at the top of this page for valid options." . $error_message_end;
      return false;
    }

    /*********** TO DO: Code allowable hardware options for client-product pairs into database and php. **********/
    $hardware = strtoupper($hardware);
    /*
    if (($hardware != "NONE") && ($hardware != "EASEL"))
    {
      $error_message = "There is an error in file name '" . $original_file_name . 
            "'.\n\nThe only valid hardware options are STD, HG, GO, CNC and MAT. (lower case std, hg, go, cnc and mat are also acceptable.)" . $error_message_end;
      return false;
    }
    */

    if (strlen($reference) == 0)
    {
      $error_message = "There is an error in file name '" . $original_file_name . 
            "'.\n\nA reference string of your choosing MUST be included.  It may not be left blank. This string should be unique for each file you upload." . $error_message_end;
      return false;
    }

    // Alison uses notes for her testing. Client can use it too if desired.
    /*
    if (strlen($notes) > 0)
    {
      $reference = $reference . "-" . $notes;
    }
    */
  
    return true;

  }

  /********************************************************************************************
  *  computeWaste
  ********************************************************************************************/

  function computeWaste($printlength, $printwidth, $heightin, $widthin) 
  {

    $hmultiple = -1.0;
    $vmultiple = -1.0;

    if ($heightin > $widthin) 
    {
      $sign_large_dim = $heightin;
      $sign_small_dim = $widthin;
    }
    else 
    {
      $sign_large_dim = $widthin;
      $sign_small_dim = $heightin;
    }
    if ($printlength > $printwidth) 
    {
      $media_large_dim = $printlength;
      $media_small_dim = $printwidth;
      $unprintable_scrap_large_dim = 0;
      $unprintable_scrap_small_dim = 4;
    }
    else 
    {
      $media_large_dim = $printwidth;
      $media_small_dim = $printlength;
      $unprintable_scrap_large_dim = 4;
      $unprintable_scrap_small_dim = 0;
    }

    // CHECK IF WE NEED TO TILE MEDIA to print the sign.  If we do, call a function that determines 
    // whether we need to put multiple tiles side by side and/or end to end. This function will 
    // choose the option that results in the fewest seams/tiles.
    if (($sign_large_dim > $media_large_dim) || ($sign_small_dim > $media_small_dim)) 
    {
      // Calculate how many tiles are required in each dimension.
      computeTilingMultiples($media_large_dim, $media_small_dim, 
                             $sign_large_dim, $sign_small_dim,
                             $tiling_multiples_0, $tiling_multiples_1);
      // Update the width and/or length of the media and the unprintable scrap.
      $media_large_dim = $media_large_dim * $tiling_multiples_0;
      $unprintable_scrap_large_dim = $unprintable_scrap_large_dim * $tiling_multiples_0;
      $media_small_dim = $media_small_dim * $tiling_multiples_1;
      $unprintable_scrap_small_dim = $unprintable_scrap_small_dim * $tiling_multiples_1;

      // Swap the small and large dimensions if required.
      if ($media_small_dim > $media_large_dim) 
      {
        $temp_dim = $media_large_dim;
        $temp_scrap = $unprintable_scrap_large_dim;
        $media_large_dim = $media_small_dim;
        $unprintable_scrap_large_dim = $unprintable_scrap_small_dim;
        $media_small_dim = $temp_dim;
        $unprintable_scrap_small_dim = $temp_scrap;
      }
    }

    // ***** HORIZONTAL ORIENTATION: compute the waste if the small dimension of the sign is parallel 
    // to the small dimension of the media, and the optimal number of signs are ordered
    if (($sign_large_dim <= $media_large_dim) && ($sign_small_dim <= $media_small_dim)) 
    {
      // Figure out the optimum multiples in this orientation.
      $hmultiple = intdiv($media_small_dim, $sign_small_dim);
      $vmultiple = intdiv($media_large_dim, $sign_large_dim);
      $number_of_signs = $hmultiple * $vmultiple;

      // Calculate the area of the signs printed.
      $area_printed = $sign_small_dim * $sign_large_dim * $number_of_signs;

      // Calculate the amount of media used.   
      $media_used = ($media_small_dim + $unprintable_scrap_small_dim) * 
                    ($media_large_dim + $unprintable_scrap_large_dim);

      // Calculate amount of waste per sign in square feet
      $waste = ($media_used - $area_printed) / $number_of_signs;
      $waste_sqft_horizontal = $waste / 144.0;
    }
    else 
    {
      $waste_sqft_horizontal = -1;
    }

    // ***** VERTICAL ORIENTATION: compute the waste if the large dimension of the sign is parallel 
    // to the small dimension of the media, and the optimal number of signs are ordered
    if (($sign_small_dim <= $media_large_dim) && ($sign_large_dim <= $media_small_dim)) 
    {
      // Figure out the optimum multiples in this orientation.
      $hmultiple = intdiv($media_small_dim, $sign_large_dim);
      $vmultiple = intdiv($media_large_dim, $sign_small_dim);
      $number_of_signs = $hmultiple * $vmultiple;

      // Calculate the area of the signs printed.
      $area_printed = $sign_small_dim * $sign_large_dim * $number_of_signs;

      // Calculate the amount of media used.
      $media_used = ($media_small_dim + $unprintable_scrap_small_dim) * 
                    ($media_large_dim + $unprintable_scrap_large_dim);

      // Calculate amount of waste per sign in square feet
      $waste = ($media_used - $area_printed) / $number_of_signs;
      $waste_sqft_vertical = $waste / 144.0;
    }
    else 
    {
      $waste_sqft_vertical = -1;
    }


    // ***** ALIGNMENT SPACING: Calculate area of 0.5 inch perimeter around each
    // sign; this is waste due to spacing required for registration and cutting.
    $alignment_waste_sqft = ((($sign_small_dim + 1) * ($sign_large_dim + 1)) - ($sign_small_dim * $sign_large_dim)) / 144.0;

    // ***** CHOOSE THE ORIENTATION that gives the least waste.
    if ($waste_sqft_horizontal == -1) 
    {
      $waste_sqft = $waste_sqft_vertical + $alignment_waste_sqft;
    }
    else if ($waste_sqft_vertical == -1) 
    {
      $waste_sqft = $waste_sqft_horizontal + $alignment_waste_sqft;
    }
    else if ($waste_sqft_horizontal <= $waste_sqft_vertical) 
    {
      $waste_sqft = $waste_sqft_horizontal + $alignment_waste_sqft;
    }
    else 
    {
      $waste_sqft = $waste_sqft_vertical + $alignment_waste_sqft;
    }

    return($waste_sqft);
  }

  /********************************************************************************************
  *  computeTilingMultiples(media_large_dim, media_small_dim, sign_large_dim, sign_small_dim);
  *
  *  Determine whether we need to put multiple tiles side by side or end to end, and return
  *  the number of tiles in each dimension.
  *
  ********************************************************************************************/

  function computeTilingMultiples($media_large_dim, $media_small_dim, $sign_large_dim, $sign_small_dim, 
                                  &$tiling_multiples_0, &$tiling_multiples_1)
  {
    // HORIZONTAL ORIENTATION: Compute the number of tiles needed if we line up the
    // long side of the sign with the long side of the media.
    $htilecount_horizontal = intdiv($sign_large_dim, $media_large_dim) + (($sign_large_dim % $media_large_dim > 0) ? 1 : 0);
    $vtilecount_horizontal = intdiv($sign_small_dim, $media_small_dim) + (($sign_small_dim % $media_small_dim > 0) ? 1 : 0);

    // VERTICAL ORIENTATION: Compute the number of tiles needed if we line up the
    // short side of the sign with the long side of the media.
    $htilecount_vertical = intdiv($sign_small_dim, $media_large_dim) + (($sign_small_dim % $media_large_dim > 0) ? 1 : 0);
    $vtilecount_vertical = intdiv($sign_large_dim, $media_small_dim) + (($sign_large_dim % $media_small_dim > 0) ? 1 : 0);

    // CHOOSE THE ORIENTATION with the least seams/tiles.
    if ($htilecount_horizontal * $vtilecount_horizontal <= $htilecount_vertical * $vtilecount_vertical) 
    {
      $tiling_multiples_0 = $htilecount_horizontal;
      $tiling_multiples_1 = $vtilecount_horizontal;
    }
    else 
    {
      $tiling_multiples_0 = $htilecount_vertical;
      $tiling_multiples_1 = $vtilecount_vertical;
    }

  }

  /********************************************************************************************
  *  computeFinishingCosts
  ********************************************************************************************/

  function computeFinishingCosts($finishing_options_array, $wswidth, $wsheight, $wsperimeter, $quantity, 
                                 $cost_discountable, $cost_nondiscountable, $cost_ink, &$printed_sqfootage)
  {
    global $DBConn;
    global $hostname_DBConn;
    global $database_DBConn;
    global $username_DBConn;
    global $password_DBConn;

    $cost_options = 0;
    $number_of_options = 0;

    // Identify each finishing option which has been requested.
    for ($i = 0; $i < count($finishing_options_array); $i++)
    {
      $number_of_options++;
      $option_name = $finishing_options_array[$i];  // e.g. AF-X

      $query = "SELECT * FROM signboom_finishing WHERE Code = '$option_name'"; 
      mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) 
      { 
        $variable_cost = $row['Variable'];
        $fixed_cost = $row['Fixed'];
        $equation = $row['Units'];       // this is a poorly named database column
        $extra_days = $row['ExtraTime']; // so far, we don't use this in event order form

        // Calculate cost of that option and add to the total cost of options.
        switch ($equation) 
        {
          case "PF":    // Perimeter Footage
            $option_cost = (($wsperimeter * $variable_cost) + $fixed_cost) * $quantity;
            $cost_options += $option_cost;
            break;
          case "SF":    // Square Footage
            $option_cost = (($wswidth/12.0 * $wsheight/12.0 * $variable_cost) + $fixed_cost) * $quantity;
            $cost_options += $option_cost;
            break;
          case "BS":    // Back Side
            // Charge the printing and ink cost for the back side, but not the media cost.
            // Don't multiply cost_disc + cost_non + cost_ink by quantity, as they already include quantity in them.
            $option_cost = (($cost_discountable + $cost_nondiscountable) * $variable_cost) + ($fixed_cost * $quantity) + $cost_ink;
            $cost_options += $option_cost;
            $printed_sqfootage = $printed_sqfootage * 2.0;
            break;
          case "EA":    // Each
            // Calculate the number of (fixed) and the cost of (variable) an item added to a sign.  
            $option_cost = $fixed_cost * $variable_cost * $quantity;
            $cost_options += $option_cost;
            break;
          default:
            exit(-1);
            break;
        }  // end switch

      } // end of if that option is checked

    } // end of for all options

    return $cost_options;

  }



  /********************************************************************
   * calculateLineItemCost:
   *
   * Variables in Function:
   *   printwidth;           // the width of the media in inches
   *   printlength;          // the length of the media in inches
   *   wsheight;             // the height of the printed sign in inches
   *   wswidth;              // the width of the printed sign in inches
   *   wsperimeter;          // the linear dimension of the sign perimeter in ft
   *   wsarea;               // the area of one side of the sign in sqft
   *   quantity;             // the number of signs to print
   *   waste_sqft_per_sign;  // material wasted per sign (if optimum quantity was purchased)
   *   cost_discountable;    // discountable cost of this particular item
   *   cost_nondiscountable; // non-discountable cost of this particular item
   *   cost_waste;           // cost of waste for this particular item
   *   cost_options;         // cost of finishing options for this particular item
   *   sign_sqfootage;       // sq ft of material used for this line item (factoring in quantity)
   *   printed_sqfootage;    // sq ft of material actually printed (factoring in double sided)
   *   waste_sqfootage;      // sq ft of material wasted for this item (factoring in quantity)
   *   total_sqfootage;      // total square footage of media used (sign + waste)
   *   cost_line_total;      // cost for line, including waste, options and ink, but not discount and taxes
   *   cost_per_piece;       // cost per piece, including waste, options and ink, but not discount and taxes 
   *
   *  The function below is based on the CalculateLineCost from all_order.js of the traditional Signboom
   *  order system. We need to keep costing of the two function in sync.  Unfortunately, I could not
   *  use the original function as-is, as it grabs data directly from the 10 rows in the traditional
   *  order system, and the new TED event system has to be able to price out more than ten line items
   *  per order.
   *
   *  Differences between traditional Signboom order System and TED/Event order system.
   *
   *  1. In the traditional order system, we identify whether any fixtures have been ordered (by 
   *     looking at product categories) and add an extra day if they are. In TED, we don't add that day.
   *  2. In traditional system, some line items are fixtures only. With TED, all items have 
   *     printable component.
   *  3. In traditional system, rush/hot is applied order-wide.  With TED, it is applied to individual
   *     line items.
   *  4. In traditonal system, we calculate shipping costs and taxes.  With TED, we don't.
   *  5. In traditional system, we charge order-wide setup costs.  With TED, we don't.
   *  6. With traditional system, we give the client the better discount of either a) order size
   *     discount or b) their special customer discount.  With TED we apply just the customer discount, 
   *     so we don't need to keep track of square footage of the order.
   *
   ********************************************************************/

  function calculateLineItemCost(&$message, $event_location_code, $finishing_code, $wsheight, $wswidth, $quantity, $due_date_yymmdd, $sides, $double_sided, $hardware, $reference, $notes)
  {
    global $DBConn;
    global $hostname_DBConn;
    global $database_DBConn;
    global $username_DBConn;
    global $password_DBConn;

    $message = '';

    $account_name = $_GET['account_name'];
    $product_code = $_GET['product_code'];
    $file_name = $_GET['file_name'];
    if (isset($_GET['order_id'])) $order_id = $_GET['order_id'];

//echo "account = $account_name<br>file=$file_name<br>location=$event_location_code<br>height=$wsheight<br>width=$wswidth<br>quantity=$quantity<br>due_date=$due_date_yymmdd<br>save=$save_to_database<br>order id=$order_id<br><br>";

    //echo 'About to read parameters from database.<br><br>';

    // READ INFORMATION FROM DATABASE
    // ==============================

    // These are system wide. Start by hardcoding some decent default values.
    $cutoff_time = 12;
    $ink_cost = 0.0;
    $waste_factor = 1.0;
    $cutoff_days_for_hot = 3;
    $cutoff_days_for_rush = 6;
    $query = "SELECT cutofftime, inkcost, wastefactor, cutoffdaysrush, cutoffdayshot FROM signboom_parm WHERE ID = 1"; 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) 
    { 
      $cutoff_time = $row['cutofftime'];
      $ink_cost = $row['inkcost'];
      $waste_factor = $row['wastefactor'];
      $cutoff_days_for_rush = $row['cutoffdaysrush'];
      $cutoff_days_for_hot = $row['cutoffdayshot'];
    } 
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

    //echo "cutoff time: $cutoff_time, ink cost: $ink_cost, waste factor: $waste_factor, cutoff days: rush = $cutoff_days_for_rush, hot = $cutoff_days_for_hot<br><br>";

    // These are customer specific. 
    // e.g. S70 = 70%,  S40 = 40%, 100 = 10%
    $query = "SELECT dct FROM signboom_user WHERE AcctName = '$account_name'"; 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    if (mysqli_num_rows($result) == 0)
      //return -1; // indicate error
      exit(-1);
    while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) 
    { 
      $discount_code = $row['dct'];
    } 
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

    //echo "discount code = $discount_code<br><br>";

    $discount_percent = 0;
    $query = "SELECT Dct FROM signboom_discount WHERE ID = '$discount_code'"; 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) 
    { 
      $discount_percent = $row['Dct'];
    } 
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

    //echo "discount percent = $discount_percent<br><br>";

    // Map this customer's 'product code' and 'finishing code' to the usual products and finishing options.
    $query = "SELECT * FROM signboom_user_products WHERE AcctName = '$account_name' AND ProductCode = '$product_code' AND FinishingCode = '$finishing_code'"; 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    if (mysqli_num_rows($result) == 0)
      //return -1; // indicate error
      exit(-1);
    $finishing_options_array = array();
    while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) 
    { 
      $product = $row['Code']; // this maps LOL to COR04, for example
      $category = $row['Category']; // Adhesive Media, Flexible Media or Rigid Media.
      $product_name = $row['ShortName']; // this maps LOL to Lollipop Signs, for example
      $other_finishing = '';
      if ($category == 'Adhesive Media')
      {
        $af = $row['AF'];
        if ($af != '') { $finishing_options_array[] = $af; $other_finishing .= "$af^"; }

        $al = $row['AL']; 
        if ($al != '') { $finishing_options_array[] = $al; $other_finishing .= "$al^"; $lamination = $al; }
        else { $finishing_options_array[] = 'AL-X'; $other_finishing .= 'AL-X^'; $lamination = 'AL-X'; }

        $ai = $row['AI']; 
        if ($ai != '') { $finishing_options_array[] = $ai; $other_finishing .= "$ai^"; $ink_layers = $ai; }
        else { $finishing_options_array[] = 'AI-X'; $other_finishing .= 'AI-X^'; $ink_layers = 'AI-X'; }

        $ap = $row['AP'];
        if ($ap != '') { $finishing_options_array[] = $ap; $other_finishing .= "$ap^"; }

        $ak = $row['AK'];
        if ($ak != '') { $finishing_options_array[] = $ak; $other_finishing .= "$ak^"; }
      }
      if ($category == 'Flexible Media')
      {
        $bf = $row['BF']; 
        if ($bf != '') { $finishing_options_array[] = $bf; $other_finishing .= "$bf^"; }

        $bb = $row['BB']; 
        if ($bb != '') { $finishing_options_array[] = $bb; } // this gets populated in "sides" part of user's filename

        $bi = $row['BI']; 
        if ($bi != '') { $finishing_options_array[] = $bi; $other_finishing .= "$bi^"; $ink_layers = $bi; }
        else { $finishing_options_array[] = 'BI-X'; $other_finishing .= 'BI-X^'; $ink_layers = 'BI-X'; }

        $bp = $row['BP'];
        if ($bp != '') { $finishing_options_array[] = $bp; $other_finishing .= "$bp^"; }

        $bk = $row['BK'];
        if ($bk != '') { $finishing_options_array[] = $bk; $other_finishing .= "$bk^"; }
      }
      if ($category == 'Rigid Media')
      {
        $rf = $row['RF']; 
        if ($rf != '') { $finishing_options_array[] = $rf; $other_finishing .= "$rf^"; }

        $rl = $row['RL']; 
        if ($rl != '') { $finishing_options_array[] = $rl; $other_finishing .= "$rl^"; $lamination = $rl; }
        else { $finishing_options_array[] = 'RL-X'; $other_finishing .= 'RL-X^'; $lamination = 'RL-X'; }

        $rb = $row['RB']; 
        if ($rb != '') { $finishing_options_array[] = $rb; } // this gets populated in "sides" part of user's filename 

        $rh = $row['RH']; 
        if ($rh != '') { $finishing_options_array[] = $rh; $other_finishing .= "$rh^"; }

        $re = $row['RE']; 
        if ($re != '') { $finishing_options_array[] = $re; $other_finishing .= "$re^"; }

        $ri = $row['RI']; 
        if ($ri != '') { $finishing_options_array[] = $ri; $other_finishing .= "$ri^"; $ink_layers = $ri; }
        else { $finishing_options_array[] = 'RI-X'; $other_finishing .= 'RI-X^'; $ink_layers = 'RI-X'; }

        $rp = $row['RP'];
        if ($rp != '') { $finishing_options_array[] = $rp; $other_finishing .= "$rp^"; }

        $rk = $row['RK'];
        if ($rk != '') { $finishing_options_array[] = $rk; $other_finishing .= "$rk^"; }

        $ro = $row['RO']; 
        if ($ro != '') { $finishing_options_array[] = $ro; $other_finishing .= "$ro^"; }
      }
    }

    //echo "product = $product, AF = $af, AL = $al, AI = $ai, AP = $ap, AK = $ak, BF = $bf, BB = $bb, BI = $bi, BP = $bp, BK = $bk, RF = $rf, RL = $rl, RB = $rb, RH = $rh, RE = $re, RI = $ri, RP = $rp, $RK = $rk, RO = $ro<br><br>";

    // Get product specific costing information.
    $printwidth =  48.0; // default
    $printlength = 96.0; // default
    $query = "SELECT * FROM signboom_allproducts WHERE Code = '$product'";
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    if (mysqli_num_rows($result) == 0)
      //return -1; // indicate error
      exit(-1);
    while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) 
    { 
      $category = $row['Category'];
      $printwidth = $row['Width'];
      $printlength = $row['Length'];
      $product_cost_waste = $row['CostWaste'];
      $product_cost_nondiscountable = $row['CostNon'];
      $product_cost_discountable = $row['CostDisc'];
    } 
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

    //echo "product $product: width = $printwidth, length = $printlength, cost waste = $product_cost_waste, cost nondisc = $product_cost_nondiscountable, cost disc = $product_cost_discountable<br><br>";

    // If double side has been requested (_S2S or _S2D), add the relevant finishing option into the array.
    if (($sides == 2) && ($double_sided == 'same') && ($category == 'RIGID'))
    {
      $finishing_options_array[] = 'RB-SF'; 
      $rb = 'RB-SF';
    }
    else if (($sides == 2) && ($double_sided == 'different') && ($category == 'RIGID'))
    {
      $finishing_options_array[] = 'RB-DF'; 
      $rb = 'RB-DF';
    }
    else if (($sides == 2) && ($double_sided == 'same') && ($category == 'BANNER'))
    {
      $finishing_options_array[] = 'BB-SF';
      $bb = 'BB-SF';
    }
    else if (($sides == 2) && ($double_sided == 'different') && ($category == 'BANNER'))
    {
      $finishing_options_array[] = 'BB-DF';
      $bb = 'BB-DF';
    }

    // COMPUTE SQUARE FOOTAGE, WASTE AND PERIMETER AMOUNTS
    // ===================================================

    // Work out perimeter (in linear feet) and square footage of the printed sign
    $wsperimeter = (($wsheight + $wswidth) * 2) / 12; // gets multiplied by quantity when option cost calculated
    $wsarea = ($wsheight/12) * ($wswidth/12);

    // Work out square footage.
    $sign_sqfootage = $wsarea * $quantity;
    $printed_sqfootage = $sign_sqfootage; // gets doubled in computeFinishingCosts() below, 
                                          // if sign is printed on back side (BS option)

    // Calculate amount of material wasted (in sq ft) per sign.  Assume for this calculation
    // that the customer ordered the optimum multiples of their sign.
    $waste_sqft_per_sign = computeWaste($printlength, $printwidth, $wsheight, $wswidth);  
    $waste_sqfootage = $waste_sqft_per_sign * $quantity * $waste_factor;

    //echo "waste per sign = $waste_sqft_per_sign sqft, quantity = $quantity, factor= $waste_factor, waste total = $waste_sqfootage sqft<br>";

    // Work out total square footage of media used (sign + waste).
    $total_sqfootage = $sign_sqfootage + $waste_sqfootage;

    //echo "perimenter = $wsperimeter, area = $wsarea, square footage = $sign_sqfootage<br><br>";

    // COMPUTE COST OF MEDIA AND PRINTING
    // ==================================

    //This is the discountable portion of the cost. 
    $cost_discountable = round($product_cost_discountable * $sign_sqfootage, 2);

    //This is the non-discountable portion of the cost 
    $cost_nondiscountable = round($product_cost_nondiscountable * $sign_sqfootage, 2);

    //Waste cost.
    $cost_waste = round($product_cost_waste * $waste_sqfootage, 2);

    //This is the cost of the ink. It is non-discountable and system-wide (not product-specific).
    $cost_ink = round($ink_cost * $sign_sqfootage, 2);

    //echo "discountable = $cost_discountable, non-discountable = $cost_nondiscountable, waste = $cost_waste, ink = $cost_ink<br><br>";

    // COMPUTE COST OF FINISHING OPTIONS
    // =================================

    // Identify whether finishing is standard or custom.  
    //$af, $al, $ai, $bf, $bb, $bi, $rf, $rl, $rb, $rh, $re, $ri, $ro) 
    $options = "STD";
    if (!isStandardFinishing($product, $finishing_options_array)) 
    {
      $options = "CUS";
    }
    $cost_options = computeFinishingCosts($finishing_options_array, $wswidth, $wsheight, $wsperimeter, $quantity, $cost_discountable, $cost_nondiscountable, $cost_ink, &$printed_sqfootage);
    $cost_options = round($cost_options, 2);

    //echo "options are $options, cost = $cost_options<br><br>";

    // This line was in javascript of regular order system. It looks like discounts get applied to finishing
    // options.  Follow up on this later.  For TED2015/2016/2017 events, they are not getting discounts.
    // line_discountable_cost = cost_discountable + cost_options;

    // COMPUTE COST OF MEDIA AND PRINTING
    // ==================================

    //Line cost. Hardware is going to get its own line in the database, so don't include it here.
    $cost_line_total = $cost_discountable + $cost_nondiscountable + $cost_waste + $cost_options + $cost_ink;
    $cost_per_piece = round($cost_line_total / $quantity, 2);

    //echo "line cost = $cost_line_total, per piece = $cost_per_piece (w/o hardware)<br><br>";

    // APPLY CUSTOMER DISCOUNT
    // =======================

    $discount_in_dollars = round($discount_percent * $cost_discountable / 100.0, 2);
    //echo "discount = $discount_in_dollars<br><br>";

    // COMPUTE RUSH/HOT COST FOR LINE ITEM
    // ===================================

    // For the TED project, we can ignore extra_days.  Later we may want to take them into consideration.
    // If we do that, we would compare (due_date + extra_days) against (today + cutoff_days)
    $order_date_yymmdd = getEffectiveOrderDate($cutoff_time);

    sscanf($due_date_yymmdd, "%2d%2d%2d", $year, $month, $day);
    $year += 2000; // switch to 4 digit year
    // Convert date formats.  Assume 3PM time for daily deliveries.
    $readydate     = sprintf("%02d/%02d/%4d 3PM", $month, $day, $year);
    $readydatetime = sprintf("%4d-%02d-%02d 15:00:00", $year, $month, $day);
    //$readydate = $month . '/' . $day . '/' . $year . ' 3PM';
    //$readydatetime = $year . '-' . $month . '-' . $day . ' 15:00:00';

    $due_date_timestamp = mktime(0, 0, 0, $month, $day, $year);
    sscanf($order_date_yymmdd, "%2d%2d%2d", $year, $month, $day);
    $year += 2000; // switch to 4 digit year
    $order_date_timestamp = mktime(0, 0, 0, $month, $day, $year);

    $date_diff = floor(($due_date_timestamp - $order_date_timestamp) / (60 * 60 * 24));

/* We aren't charging rush/hot for TED 2017.
    if ($date_diff < $cutoff_days_for_hot) 
    {
      //order is hot: charge 20% (we don't enforce a minimum of $75 because we are charging file by file)
      $service_type = "HOT";
      $service_cost = (($cost_line_total - $discount_in_dollars) * .2);
      //$service_cost = (75.00 > $service_cost) ? 75.00 : $service_cost;
    }
    else if ($date_diff < $cutoff_days_for_rush)
    {
      //order is rush : charge 10% (we don't enforce minimum of $25 because we are charging file by file)
      $service_type = "RUSH";
      $service_cost = (($cost_line_total - $discount_in_dollars) * .1);
      //$service_cost = (25.00 > $service_cost) ? 25.00 : $service_cost;
    }
    else
    {
*/
      // order is standard
      $service_type = "STD";
      $service_cost = 0;
/*
    }
*/

    //echo "effective order date = $order_date_yymmdd, service type = $service_type, service cost = $service_cost<br><br>";

    // COMPUTE FINAL COST FOR LINE ITEM
    // ================================
    // TO DO: Why is this line repeated here?  It is a few paragraphs above...
    $cost_line_total = $cost_discountable + $cost_nondiscountable + $cost_waste + $cost_options + $cost_ink;
    $net_cost = $cost_line_total - $discount_in_dollars + $service_cost;

    $freight = 0;
    //$freight = calculateFreight(?);
    $pst = 0;
    $gst = 0.05 * $net_cost;
    //calculateTax(net_cost, freight, $pst, $gst);

    //echo "cost before discount and rush = $cost_line_total<br>";
    //echo "cost after discount and rush = $net_cost<br><br>";

    // SAVE LINE ITEM INFORMATION TO DATABASE
    // ======================================
    // cost is total cost (nondisc + disc)
    // dct is discountABLE cost only (not the actual discount extended)

      $cost_discountable_formatted = sprintf('$%.2f', $cost_discountable);

    if (isset($_GET['order_id']) && (ctype_digit($_GET['order_id'])) && ($_GET['order_id'] > 0))
    {
      // This is situation where line item is to be put into database.
      $order_id = $_GET['order_id'];

      // Add $ signs into those columns which have historically had them. Control digits after decimal.
      $cost_line_total_formatted   = sprintf('$%.2f', $cost_line_total);
      $cost_discountable_formatted = sprintf('$%.2f', $cost_discountable);
      $sign_sqfootage_formatted    = sprintf('%.3f', $sign_sqfootage);
      $printed_sqfootage_formatted = sprintf('%.3f', $printed_sqfootage);
      $waste_sqfootage_formatted   = sprintf('%.3f', $waste_sqfootage);
      $service_cost_formatted      = sprintf('%.2f', $service_cost);

      // Get most recently used (highest) linenum for that orderid from database
      $query = "SELECT linenum FROM signboom_linedetail WHERE orderid = $order_id ORDER BY linenum DESC";
      mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      if (mysqli_num_rows($result) == 0)
      {
        $line_number = 1;
      }
      else
      {
        // first response contains highest line number used so far
        $row = mysqli_fetch_array($result,  MYSQLI_BOTH); 
        $line_number = $row['linenum'] + 1; // add one to get next line number to use
      }

      $confirmation_email_text = 
        "Customer $account_name has placed order $order_id.\n\n" .
        "Note: This email is sent BEFORE the files have finishing uploading.\n\n" .
        "Line Number: $line_number\nProduct: $product\nFile: $file_name\nReady Date: $readydate\nService: $service_type\n" .
        "Finishing: $options\nQuantity: $quantity\nHeight: $wsheight\nWidth: $wswidth\n" .
        "Product Cost: $cost_line_total_formatted\nService Cost: \$$service_cost_formatted\n\n";

      // Add new line item into linedetail database table
      $query = "INSERT INTO signboom_linedetail SET orderid = $order_id, linenum = $line_number, product = '$product', options = '$options', quantity = $quantity, itemheight = $wsheight, itemwidth = $wswidth, filename = '$file_name', cost = '$cost_line_total_formatted', dctcost = '$cost_discountable_formatted', proofed = 'no', printed = 'no', finished = 'no', packed = 'no', squarefootage = $sign_sqfootage_formatted, printedarea = $printed_sqfootage_formatted, wastearea = $waste_sqfootage_formatted, wastecost = $cost_waste, inkcost = $cost_ink, AF = '$af', AL = '$al', AI = '$ai', AP = '$ap', AK = '$ak', BF = '$bf', BB = '$bb', BI = '$bi', BP = '$bp', BK = '$bk', RF = '$rf', RL = '$rl', RB = '$rb', RH = '$rh', RE = '$re', RI = '$ri', RP = '$rp', RK = '$rk', RO = '$ro', EventLocationCode = '$event_location_code', readydate = '$readydate', readydatetime = '$readydatetime', rushtype = '$service_type', rushcost = '$service_cost_formatted'";
      mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
      $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

      if ($result)
      {
        echo "OK~$file_name"; // insert succeeded
        $sql_error = false;
      }
      else
      {
        echo "ERROR~$file_name"; // insert failed
        $sql_error = true;
      }

      if (!$sql_error)
      {
        //Database inserts worked, so email Kim about order.
        mail("alison@usablewebdesigns.com", "TED2018 has ordered a file", $confirmation_email_text, "From: signboom@signboom.com" . "@{$_SERVER['SERVER_NAME']}\r\n");
        mail("kim@signboom.com", "TED2018 has ordered a file", $confirmation_email_text, "From: signboom@signboom.com");
      }
    }
    else
    {
      // This is situation where the file name is just being validated and costed.
      // Return the pieces of information the javascript needs to sum up in order to populate
      // the ordermast table with a line for this order. Make it a string separated by ~'s.

      sscanf($due_date_yymmdd, "%2d%2d%2d", $year, $month, $day);
      $year += 2000; // switch to 4 digit year
      $due_date_timestamp = mktime(0, 0, 0, $month, $day, $year);
      //$due_date = date("D, M j, Y", $due_date_timestamp); // including year in 4 digits
      $due_date = date("D, M j", $due_date_timestamp);

      if ($sides == 2)
        $sides_formatted = $sides . ' (' . $double_sided . ')';
      else 
        $sides_formatted = $sides;

      $cost_line_total_w_hardware = $cost_line_total;

      $message = $order_id . '~' .
                 $line_number . '~' .
                 $product_code . '~' .
                 $product_name . '~' .
                 $event_location_code . '~' .
                 $wswidth . '~' .
                 $wsheight . '~' .
                 $due_date_yymmdd . '~' .
                 $due_date . '~' .
                 $quantity . '~' .
                 $sides_formatted . '~' .
                 $finishing_code . '~' .
                 $hardware . '~' .
                 $reference. '~' .
                 $cost_line_total_w_hardware . '~' .
                 $cost_discountable . '~' .
                 $cost_nondiscountable . '~' .
                 $cost_waste . '~' .
                 $cost_ink . '~' .
                 $options . '~' .
                 $cost_options. '~' .  
                 $printed_sqfootage . '~' .
                 $discount_in_dollars . '~' .
                 $service_type . '~' .
                 $service_cost . '~' .
                 $gst . '~' .
                 $lamination . '~' .
                 $ink_layers . '~' .
                 $other_finishing . '~' .
		 $notes;

      //echo 'order_id line_number line_total discountable non_discountable cost_waste cost_ink options cost_options printed_sq_ft discount_dollars<br>';
      // echo "$message<br>";

      echo "OK~$file_name~$message";
    }

    return;
  }

  /********************************************************************************************/

  // isValidFileName populates the variables passed to it.
  if (isValidFileName($error_message, $event_location_code, $finishing_code, $height, $width, $quantity, $due_date_yymmdd, $sides, $double_sided, $hardware, $reference, $notes))
  {
    // calculateLineItemCost populates $message
    calculateLineItemCost($message, $event_location_code, $finishing_code, $height, $width, $quantity, $due_date_yymmdd, $sides, $double_sided, $hardware, $reference, $notes);
  }
  else 
  {
    $file_name = $_GET['file_name'];
    echo "ERROR~$file_name~$error_message";
  }
  return true;
?>
