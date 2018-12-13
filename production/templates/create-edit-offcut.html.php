<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
  <title>Edit Offcut</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="production-new.css" rel="stylesheet" type="text/css">
  <script type = "text/javascript">
    //Function that gets run whenever date_picker is closed.
    function datePickerClosed(dateField) {
      document.filter_controls.submit();
    }
  </script>
</head>


<body>

<div style="width: 1200px; margin: 0px auto; text-align: center;">


  <div style="float: left; margin-top: 20px;"><img src="../images/logo3d.gif" width="308" height="54"></div>
  <div style="float: right;"><h1>Order Processing System: Offcuts</h1></div>
  <?php include('menu.html');?>

  <div style="width: 800px; margin: 15px auto;">

    <h1><?php echo $button_value; ?></h1>

    <?php
    $message=""; 
      if (strlen(trim($message)) > 0)
      {
        echo "<script language=\"javascript\">alert(\"$message\");</script>";
	$message = '';
      }
    ?>

    <form id="offcut_form" name="offcut_form" method="post" action="">

      <input type="hidden" name="offcut_id" value="<?php echo $offcut_id; ?>">
	  
      <ul class="vertical">

        <li>
          <label for="date_added">Date Added:</label> 
          <input name="date_added" id="date_added" value="<?php echo isset($date_added)?$date_added:""; ?>" disabled> (this will be populated automatically)
        </li>

        <li>
          <label for="person_added">Person Added:</label> 
          <input name="person_added" id="person_added" value="<?php echo isset($person_added)?$person_added:""; ?>" disabled> (this will be populated automatically)
        </li>

        <li>
          <label for="material">Product (Media):</label>
          <select id="material" name="material">
	    <?php 
	    foreach ($products as $key => $value) 
	    {
	      if ($value == '') 
	        $display_text = 'Choose Product';
	      else
	        $display_text = $value;
	      if ($material == $value) 
	        $this_is_selected = 'selected';
              else
	        $this_is_selected = '';
              echo '<option value="' . $value . '" ' . $this_is_selected . '  >' . $display_text . '</option>';
            }
	    ?>
          </select>
        </li>

        <li>
          <label for="width">Width:</label>
          <input name="width" id="width" value="<?php echo isset($width)?$width:""; ?>">
        </li>

        <li>
          <label for="length">Length:</label>
          <input name="length" id="length" value="<?php echo isset($length)?$length:""; ?>">
        </li>

        <li>
          <label for="quantity">Quantity:</label>
          <input name="quantity" id="quantity" value="<?php echo isset($quantity)?$quantity:""; ?>">
        </li>

        <li>
          <label for="paid_for">Paid for (by Customer):</label>
          <select id="paid_for" name="paid_for">
            <option value=""   <?php if (isset($paid_for)?$paid_for:"" == "")   echo "selected"; ?>>Choose</option>
            <option value="0"  <?php if (isset($paid_for)?$paid_for:"" == "0")  echo "selected"; ?>>No</option>
            <option value="1"  <?php if (isset($paid_for)?$paid_for:"" == "1")  echo "selected"; ?>>Yes</option>
          </select>
        </li>

        <li>
          <label for="description">Description:</label>
          <textarea name="description" id="description"><?php echo isset($description)?$description:""; ?></textarea>
        </li>

        <li>
          <label for="claimed">Claimed:</label>
          <select id="claimed" name="claimed" disabled>
            <option value=""   <?php if (isset($claimed)?$claimed:"" == "")   echo "selected"; ?>>Choose</option>
            <option value="0"  <?php if (isset($claimed)?$claimed:"" == "0")  echo "selected"; ?>>No</option>
            <option value="1"  <?php if (isset($claimed)?$claimed:"" == "1")  echo "selected"; ?>>Yes</option>
          </select>
        </li>

        <li>
          <label for="date_claimed">Date Claimed:</label>
          <input name="date_claimed" id="date_claimed" value="<?php echo isset($date_claimed)?$date_claimed:""; ?>" disabled> 
        </li>

        <li>
          <label for="person_claimed">Person Claimed:</label>
          <input name="person_claimed" id="person_claimed" value="<?php echo isset($person_claimed)?$person_claimed:""; ?>" disabled> 
        </li>

        <li>
          <label for="used">Used:</label>
          <select id="used" name="used" disabled>
            <option value=""   <?php if (isset($used)?$used:"" == "")   echo "selected"; ?>>Choose</option>
            <option value="0"  <?php if (isset($used)?$used:"" == "0")  echo "selected"; ?>>No</option>
            <option value="1"  <?php if (isset($used)?$used:"" == "1")  echo "selected"; ?>>Yes</option>
          </select>
        </li>

        <li>
          <label for="date_used">Date Used:</label>
          <input name="date_used" id="date_used" value="<?php echo isset($date_used)?$date_used:""; ?>" disabled> 
        </li>

        <li>
          <label for="person_used">Person Used:</label>
          <input name="person_used" id="person_used" value="<?php echo isset($person_used)?$person_used:""; ?>" disabled> 
        </li>

        <li>
          <input style="float: right;" type="submit" name="<?php echo isset($button_name)?$button_name:""; ?>" value="<?php echo isset($button_value)?$button_value:""; ?>">
        </li>

      </ul>
    </form>

    <br style="clear: both;">

    <?php
    // Free memory. 
    /*mysql_free_result($result);*/
    ?>

  </div>

</div>
</body>
</html>
