<?php

  /************** Load Setup Data *****************/
  $Qry = "SELECT * FROM signboom_parm WHERE ID = 1"; 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  /* just in case we can't read in the values below, have some reasonasble defaults in place */
  $setupfee = 20.0;
  $gfloorsetupfee = 75.0;
  $packfee = 10;
  $shipmult = 1.5;
  $wastefactor = 2.0;
  $inkcost = 0.0;
  $discountfactora = 0.01; // default is linear equation: discount in % = order cost / 100;
  $discountfactorb = 1.0;
  $sqfttimea = 175;
  $sqfttimeb = 750;
  while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) { 
    $setupfee = $row['setupfee'];
    $gfloorsetupfee = $row['gfloorsetupfee'];
    $packfee = $row['packagingfee'];
    $shipmult = $row['shipmultiplier'];
    $wastefactor = $row['wastefactor'];
    $inkcost = $row['inkcost'];
    $discountfactora = $row['discountfactora'];
    $discountfactorb = $row['discountfactorb'];
  } 
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

  /************** Get Zone Multiplier *****************/
  $Qry = sprintf("SELECT * FROM signboom_zone WHERE Zone='%s'", $defZone); 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $defZoneAdd = 0;
  while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) {;
    $defZoneAdd = $row['XTRA'];
  }
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

  /************** Load product categories *****************/
  $Qry = "SELECT * FROM signboom_category WHERE enabled = 1 ORDER BY displayorder"; 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $i = 0;
  while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) { 
    $i++;
    if(!isset($fcategory[$i]))
    {
      $fcategory[$i] = new stdClass();
      $fcategory[$i]->ID = "";
      $fcategory[$i]->code = "";
      $fcategory[$i]->shortname = "";
      $fcategory[$i]->description = "";
      $fcategory[$i]->printable = "";
    }
    $fcategory[$i]->ID = $row['ID'];
    $fcategory[$i]->code = $row['code'];
    $fcategory[$i]->shortname = $row['shortname'];
    $fcategory[$i]->description = $row['description'];
    $fcategory[$i]->printable = $row['printable'];
  } 
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

  /************** Load all products *****************/
  $i = 0;
  $Qry = "SELECT * FROM signboom_allproducts WHERE Enabled = 1 OR Enabled = 2 ORDER BY Category, SortGroup, SortOrder"; 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $first_product = true;
  $product_group = '';
  while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) { 

    // initialization
    if ($first_product)
    {
      $product_group = $row['SortGroup'];
      $first_product = false;
    }

    // handle switch to new group, by putting in separator, and remembering new group
    if ($row['SortGroup'] != $product_group) {
      $i++;
      if(!isset($fproduct[$i]))
      {
        $fproduct[$i] = new stdClass();
        $fproduct[$i]->category= $row['Category'];
        $fproduct[$i]->id = 0;
      }
      /* the values below let the javascript code know to put a separator into the list */
      $fproduct[$i]->category= $row['Category'];
      $fproduct[$i]->id = 0; 
      $product_group = $row['SortGroup'];
    }

    // add product to the selector
    $i++;
    if(!isset($fproduct[$i]))
    {
      $fproduct[$i] = new stdClass();
      $fproduct[$i]->category= $row['Category'];
      $fproduct[$i]->id = "";
      $fproduct[$i]->code = "";
      $fproduct[$i]->name = "";
      $fproduct[$i]->description = "";
      $fproduct[$i]->descr_image = "";
      $fproduct[$i]->descr_text = "";
      $fproduct[$i]->descr_finishing = "";
      $fproduct[$i]->descr_limitations = "";
      $fproduct[$i]->descr_extras = "";
      $fproduct[$i]->printwidth = "";
      $fproduct[$i]->printlength = "";
      $fproduct[$i]->costdisc = "";
      $fproduct[$i]->costnon = "";
      $fproduct[$i]->costwaste = "";
      $fproduct[$i]->costink = "";
      $fproduct[$i]->sort_group = "";
      $fproduct[$i]->sort_order = "";
      $fproduct[$i]->batch_day = "";
    }
    $fproduct[$i]->category= $row['Category'];
    $fproduct[$i]->id = $row['Id'];
    $fproduct[$i]->code = $row['Code'];
    $fproduct[$i]->name = $row['Name'];
    $fproduct[$i]->description = $row['Description'];
    $fproduct[$i]->descr_image = $row['DescriptionImage'];
    $fproduct[$i]->descr_text = $row['DescriptionText'];
    $fproduct[$i]->descr_finishing = $row['DescriptionFinishing'];
    $fproduct[$i]->descr_limitations = $row['DescriptionLimitations'];
    $fproduct[$i]->descr_extras = $row['DescriptionExtras'];
    $fproduct[$i]->printwidth = $row['Width'];
    $fproduct[$i]->printlength = $row['Length'];
    $fproduct[$i]->costdisc = $row['CostDisc'];
    $fproduct[$i]->costnon = $row['CostNon'];
    $fproduct[$i]->costwaste = $row['CostWaste'];
    $fproduct[$i]->costink = isset($row['CostInk'])?$row['CostInk']:"";
    $fproduct[$i]->sort_group = $row['SortGroup'];
    $fproduct[$i]->sort_order = $row['SortOrder'];
    $fproduct[$i]->batch_day = $row['BatchDay'];

  } 
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

  /************** Load finishing options categories *****************/
  $Qry = "SELECT * FROM signboom_finishing_sets ORDER BY Id";
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $i = 0;
  while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) { 
    $i++;
    if(!isset($foptioncategory[$i]))
    {
      $foptioncategory[$i] = new stdClass();
      $foptioncategory[$i]->id = "";
      $foptioncategory[$i]->code = "";
      $foptioncategory[$i]->name = "";
    }
    $foptioncategory[$i]->id = $row['Id'];
    $foptioncategory[$i]->code = $row['Code'];
    $foptioncategory[$i]->name = $row['Name'];
  } 
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

  /************** Load Finishing Options *****************/
  $Qry = "SELECT * FROM signboom_finishing ORDER BY Category, SortGroup, SortOrder"; 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $i = 0;
  while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) { 
    $i++;
    if(!isset($ffinishingoption[$i]))
    {
      $ffinishingoption[$i] = new stdClass();
      $ffinishingoption[$i]->id = "";
      $ffinishingoption[$i]->category = "";
      $ffinishingoption[$i]->sort_group= "";
      $ffinishingoption[$i]->option_set = "";
      $ffinishingoption[$i]->option_type = "";
      $ffinishingoption[$i]->sort_order = "";
      $ffinishingoption[$i]->option_name = "";
      $ffinishingoption[$i]->description = "";
      $ffinishingoption[$i]->code = "";
      $ffinishingoption[$i]->extra_time = "";
      $ffinishingoption[$i]->units = "";
      $ffinishingoption[$i]->units_per_hour = "";
      $ffinishingoption[$i]->reference = "";
      $ffinishingoption[$i]->fixed_cost = "";
      $ffinishingoption[$i]->variable_cost = "";
      $ffinishingoption[$i]->batch_day = "";
      $ffinishingoption[$i]->laminate_product_code = "";
    }
    $ffinishingoption[$i]->id = $row['Id'];
    $ffinishingoption[$i]->category = $row['Category'];
    $ffinishingoption[$i]->sort_group= $row['SortGroup'];
    $ffinishingoption[$i]->option_set = $row['OptionSet'];
    $ffinishingoption[$i]->option_type = $row['OptionType'];
    $ffinishingoption[$i]->sort_order = $row['SortOrder'];
    $ffinishingoption[$i]->option_name = $row['OptionName'];
    $ffinishingoption[$i]->description = $row['Description'];
    $ffinishingoption[$i]->code = $row['Code'];
    $ffinishingoption[$i]->extra_time = $row['ExtraTime'];
    $ffinishingoption[$i]->units = $row['Units'];
    $ffinishingoption[$i]->units_per_hour = $row['UnitsPerHour'];
    $ffinishingoption[$i]->reference = $row['Reference'];
    $ffinishingoption[$i]->fixed_cost = $row['Fixed'];
    $ffinishingoption[$i]->variable_cost = $row['Variable'];
    $ffinishingoption[$i]->batch_day = $row['BatchDay'];
    $ffinishingoption[$i]->laminate_product_code = $row['LaminateProductCode'];
  } 
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

  /************** Load Product vs Finishing Options Matrix *****************/
  /* Value 2 means this is a default finishing option for that product. */
  /* Value 1 means this is an allowed finishing option for that product. */
  $Qry = "SELECT signboom_product_finishing.Id as Id, signboom_product_finishing.ProductCode as ProductCode, signboom_product_finishing.FinishingOptionCode as FinishingOptionCode, signboom_product_finishing.Value as Value, signboom_finishing.SortOrder as SortOrder FROM signboom_product_finishing, signboom_finishing WHERE signboom_product_finishing.FinishingOptionCode = signboom_finishing.Code ORDER BY SortOrder"; 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $i = 0;
  while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) { 
    $i++;
    if(!isset($fproductoption[$i]))
    {
      $fproductoption[$i] = new stdClass();
    }
    $fproductoption[$i]->id = $row['Id'];
    $fproductoption[$i]->product_code = $row['ProductCode'];
    $fproductoption[$i]->finishing_option_code = $row['FinishingOptionCode'];
    $fproductoption[$i]->value = $row['Value'];
  } 
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);


  /************** Load category selector *****************/
  function loadCategories() {
    include ("orderglobals.php");
    global $fcategory;
    $bfound = false;
    foreach ( $fcategory as $p ) { 
      echo '<option value="';
          echo $p->code.'"';
      echo '>'.$p->shortname.'</option>';
    }
    echo '<option value="0" ';
    if (!($bfound)) echo 'selected';
    echo '>Select Category</option>';
  }
  
  /************** Load Item options selector *****************/
  function loadProducts() {
    include ("orderglobals.php");
    global $fproduct;
    $bfound = false;
    $product_group = $fproduct[1]->sort_group;
    foreach ( $fproduct as $p ) { 
      if ($p->sort_group != $product_group) {
        // Put in separator and switch to new group.
        echo '<option value="" disabled="disabled" class="select_separator">&nbsp;</option>';
        $product_group = $p->sort_group;
      }
      echo '<option value="';
      echo $p->ID.'"';
      echo '>'.$p->shortname.'</option>';
    }
    echo '<option value="" disabled="disabled" class="select_separator">&nbsp;</option>';
    echo '<option value="0" ';
    if (!($bfound)) echo 'selected';
    echo '>Select Product</option>';
  }

  /************** Load Item options description selector *****************/
  function loadOptions() {
    include ("orderglobals.php");
    global $foptset;
    $bfound = false;
    foreach ( $foptset as $p ) { 
      echo '<option value="';
        echo $p->ID.'"';
      echo '>'.$p->shortname.'</option>';
    }
    echo '<option value="0" ';
    if (!($bfound)) echo 'selected';
    echo '>Select Options</option>';
  }
?>
