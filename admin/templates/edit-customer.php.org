<!doctype html>

<html>

  <head>
    <meta charset="utf-8">
    <title>Signboom: Version 1.5 Administration</title>
    <?php include ('head.html'); ?>

    <script language="javascript" type="text/javascript" src="../../tiny_mce/tiny_mce.js"></script>
    <script language="javascript" type="text/javascript">
    tinyMCE.init({
     mode : "textareas", 
     theme : "advanced",
     theme_advanced_buttons1 : "bold,italic,separator,justifyleft,justifycenter,justifyright,separator,bullist,numlist,separator,hr,outdent,indent,separator,undo,redo,separator,link,unlink,image,separator,removeformat,cleanup,code,help",
     theme_advanced_buttons2 : "",
     theme_advanced_buttons3 : "",
     document_base_url : "http://www.signboom.com/",
     content_css : "css/admin.css",
     relative_urls : false,
     remove_script_host : false
     });
    </script>
  </head>

<body>

  <div id="page">

    <?php include ('banner-menu.php'); ?>

    <div id="content">

      <?php
        if (strcmp($edit_mode, "edit") == 0)
          echo "<h1>Edit Customer $account_name</h1>";
        else
          echo '<h1>Create New Customer</h1>';
      ?>

      <div style="width: 840px; margin: 20px auto;">

        <div id="message" style="text-align: center; color: #cc0000; font-weight: bold;">
        <?php
          if ($edited)        echo '<p>The customer information has been updated.</p>';
          if ($created)       echo '<p>The new customer has been created.</p>'; 
          if ($error_message) echo '<p>' . $error_message . '</p>'; 
        ?>
        </div>

        <?php

        if (strcmp($edit_mode, "edit") == 0)
        {
          echo '<form id="customer_form" name="customer_form" method="post" action="edit-customer.php">';
          $disabled = "disabled";
        }
        else
        {
          echo '<form id="customer_form" name="customer_form" method="post" action="create-customer.php">';
          $disabled = "";
        }
        ?>

          <input type="hidden" name="account_name" value="<?php echo $account_name; ?>">

          <fieldset>
          <legend class="pinktext">Customer Management</legend>
          <ul class="vertical">
            <li>
              <label for="team">Type:</label>
              <select id="team" name="team">
                <option value="ONLINE"  <?php if ($team == "ONLINE")  echo "selected"; ?>>ONLINE</option>
                <option value="OFFLINE" <?php if ($team == "OFFLINE") echo "selected"; ?>>OFFLINE</option>
              </select>
            </li>
            <li>
              <label for="copy_to_kim">Copy Orders to Kim:</label>
              <select id="copy_to_kim" name="copy_to_kim">
                <option value="0" <?php if ($copy_to_kim == "0") echo "selected"; ?>>No</option>
                <option value="1" <?php if ($copy_to_kim == "1") echo "selected"; ?>>Yes</option>
              </select>
            </li>
            <li>
              <label for="pst_number">PST Number:</label>
              <input type="text" name="pst_number" maxlength="10" value="<?php echo $pst_number; ?>">
            </li>
            <li>
              <label for="next_contact">Next Contact:</label>
              <input type="text" style="width: 100px;" name="next_contact" value="<?echo $next_contact; ?>" > 
              <input type=button value="Calendar" onclick="displayDatePicker('next_contact', this);">
            </li>
            <li>
              <label for="notes">Notes:</label>
              <textarea name="notes" id="notes"><?php echo $notes; ?></textarea>
            </li>
          </ul>
          </fieldset>

          <fieldset>
          <legend class="bluetext">Customer Discounts</legend>
          <ul class="vertical">
            <li>
              <label for="discount">Customer Discount %:</label>
              <select id="discount" name="discount">
                <option value=""   <?php if ($discount == "")   echo "selected"; ?>>No Discount</option>
                <?php 
                for ($i = 0; $i < count($discount_array); $i++) { 
                  $temp_id = $discount_array[$i][0];
                  $temp_percentage = $discount_array[$i][1];
                  if (strcmp($discount, $temp_id) == 0)
                    echo "<option value=\"$temp_id\" selected>$temp_percentage</option>";
                  else
                    echo "<option value=\"$temp_id\">$temp_percentage</option>";
                }
                ?>
              </select>
            </li>
            <li>
              <label for="discount_coupon">One-time Discount Coupon:</label>
              <input type="text" name="discount_coupon" value="<?php echo $discount_coupon; ?>">
            </li>
          </ul>
          </fieldset>

          <fieldset>
          <legend class="pinktext">Contact Information</legend>
          <ul class="vertical">
            <li>
              <label for="first_name">First Name:</label>
              <input type="text" name="first_name" value="<?php echo $first_name; ?>">
            </li>
            <li>
              <label for="last_name">Last Name:</label>
              <input type="text" name="last_name" value="<?php echo $last_name; ?>">
            </li>
            <li>
              <label for="email_address">Email Address:</label>
              <input type="text" name="email_address" value="<?php echo $email_address; ?>">
            </li>
            <li>
              <label for="company">Company:</label>
              <input type="text" name="company" value="<?php echo $company; ?>">
            </li>
            <li>
              <label for="street_address">Street Address:</label>
              <input type="text" name="street_address" value="<?php echo $street_address; ?>">
            </li>
            <li>
              <label for="city">City:</label>
              <input type="text" name="city" value="<?php echo $city; ?>">
            </li>
            <li>
              <label for="province_state">Province/State:</label>
              <select name="province_state">
              <?
                if (count($arrProvState)) {
                  while (list($id, $val) = each($arrProvState) ) {
                    if ($id == $province_state) {
                      printf("<option value=\"$id\" selected>$val</option>\n");
                    } else {
                      printf("<option value=\"$id\">$val</option>\n");
                    }
                  }
                } else {
                  printf("<option value=\"\">None</option>\n");
                }?>
              </select>
            </li>
            <li>
              <label for="country">Country:</label>
              <select name="country">
                <?  
                if (count($arrCountry)) {
                  while (list($id, $val) = each($arrCountry) ) {
                    if ($id == $country) {
                      printf("<option value=\"$id\" selected>$val</option>\n");
                    } else {
                      printf("<option value=\"$id\">$val</option>\n");
                    }
                  }
                } else {
                  printf("<option value=\"\">None</option>\n");
                }
                ?>
              </select>
            </li>
            <li>
              <label for="postal_zip">Postal/Zip Code:</label>
              <input type="text" name="postal_zip" value="<?php echo $postal_zip; ?>">
            </li>
            <li>
              <label for="phone1">Primary Phone Number:</label>
              <input type="text" name="phone1" value="<?php echo $phone1; ?>">
            </li>
            <li>
              <label for="phone2">Alternate Phone Number:</label>
              <input type="text" name="phone2" value="<?php echo $phone2; ?>">
            </li>
            <li>
              <label for="web_url">Website URL:</label>
              <input type="text" name="web_url" value="<?php echo $web_url; ?>">
            </li>
          </ul>
          </fieldset>

          <fieldset>
          <legend class="bluetext">Account Security</legend>
          <ul class="vertical">
            <li>
              <label for="user_level">User Access Level:</label>
              <select id="user_level" name="user_level">
                <option value="2"  <?php if ($user_level == "2")  echo "selected"; ?>>Regular Customer</option>
                <option value="4"  <?php if ($user_level == "4")  echo "selected"; ?>>Drag and Drop Customer</option>
                <option value="3"  <?php if ($user_level == "3")  echo "selected"; ?>>Production</option>
                <option value="1"  <?php if ($user_level == "1")  echo "selected"; ?>>Administrator</option>
              </select>
            </li>
            <li>
              <label for="account_disable">Disable this account?</label>
              <select id="account_disable" name="account_disable">
                <option value="1" <?php if ($account_disable == "1") echo "selected"; ?>>Yes, disable this account</option>
                <option value="0" <?php if ($account_disable == "0") echo "selected"; ?>>No, leave this account enabled</option>
              </select>
              (Disabling prevents logging in to quote/submit orders.)
            </li>
            <li>
              <label for="password">Customer Password:</label>
              <input type="password" name="password" value="<?php echo $password; ?>">
            </li>
            <li>
              <label for="password2">Retype Customer Password:</label>
              <input type="password" name="password2" value="<?php echo $password2; ?>">
            </li>
            <li>
              <label for="hint_question">Security Question:</label>
              <select id="hint_question" name="hint_question">
                <?php 
                for ($i = 0; $i < count($arrHintQ); $i++) { 
                  $temp_code = $arrHintQ2[$i];
                  $temp_question = $arrHintQ[$i];
                  if (strcmp($hint_question, $temp_code) == 0)
                    echo "<option value=\"$temp_code\" selected>$temp_question</option>";
                  else
                    echo "<option value=\"$temp_code\">$temp_question</option>";
                }
                ?>
              </select>
            </li>
            </li>
            <li>
              <label for="hint_answer">Answer to Security Question:</label>
              <input type="text" name="hint_answer" value="<?php echo $hint_answer; ?>">
            </li>
          </ul>
          </fieldset>

          <fieldset>
          <legend class="pinktext">Customer Preferences</legend>
          <ul class="vertical">
            <li>
              <label for="mailing_list">Include this customer in email mailing list?</label>
              <select id="mailing_list" name="mailing_list">
                <option value="Yes" <?php if ($mailing_list == "Yes") echo "selected"; ?>>Yes</option>
                <option value="No"  <?php if ($mailing_list == "No")  echo "selected"; ?>>No</option>
              </select>
            </li>
            <li>
              <label for="html_mail">Send customer emails in HTML format?</label>
              <select id="html_mail" name="html_mail">
                <option value="Yes" <?php if ($html_mail == "Yes") echo "selected"; ?>>Yes</option>
                <option value="No"  <?php if ($html_mail == "No")  echo "selected"; ?>>No</option>
              </select>
            </li>
            <li>
              <label for="default_courier">Default Courier:</label>
              <input type="text" name="default_courier" value="<?php echo $default_courier; ?>">
            </li>
            <li>
              <label for="courier_account">Courier Account:</label>
              <input type="text" name="courier_account" value="<?php echo $courier_account; ?>">
            </li>
          </ul>
          </fieldset>

          <fieldset>
          <legend class="bluetext">Option to have Order Status sent by Text Messaging</legend>
          <ul class="vertical">
            <li>
              <label for="enable_text_message">Send this customer text messages when orders are proofed and finished?</label>
              <select id="enable_text_message" name="enable_text_message">
                <option value="Yes" <?php if ($enable_text_message == "Yes") echo "selected"; ?>>Yes</option>
                <option value="No"  <?php if ($enable_text_message == "No") echo "selected"; ?>>No</option>
              </select>
            </li>
            <li>
              <label for="cell_number">Cell number to send text messages to:</label>
              <input type="text" name="cell_number" value="<?php echo $cell_number; ?>"> (Include only digits.  No punctuation.)
            </li>
            <li>
              <label for="cell_provider">Company providing cell phone service for this number:</label>
              <select id="cell_provider" name="cell_provider">
                <?php 
                foreach ($arrCellProvider as $temp_code => $temp_name) {
                  if (strcmp($cell_provider, $temp_code) == 0)
                    echo "<option value=\"$temp_code\" selected>$temp_name</option>";
                  else
                    echo "<option value=\"$temp_code\">$temp_name</option>";
                }
                ?>
              </select>
            </li>
          </ul>
          </fieldset>


          <ul class="vertical">
            <li>
              <?php
              if (strcmp($edit_mode, "edit") == 0)
                echo '<input style="float: right;" type="submit" name="submit_edit_customer" value="Update Customer Information">';
              else
                echo '<input style="float: right;" type="submit" name="submit_create_customer" value="Create New Customer">';
              ?>
            </li>
          </ul>

        </form>

      <br style="clear: both;">
      </div>
    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


