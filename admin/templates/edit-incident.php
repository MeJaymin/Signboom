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
          echo "<h1>Edit Incident $incident_id</h1>";
        else
          echo '<h1>Create New Incident</h1>';
      ?>

      <div style="width: 840px; margin: 20px auto;">

        <div id="message" style="text-align: center; color: #cc0000; font-weight: bold;">
        <?php
          if ($edited)        echo '<p>The incident information has been updated.</p>';
          if ($created)       echo '<p>The new incident has been created.</p>'; 
          if ($error_message) echo '<p>' . $error_message . '</p>'; 
        ?>
        </div>

        <?php

        if (strcmp($edit_mode, "edit") == 0)
        {
          echo '<form id="incident_form" name="incident_form" method="post" action="edit-incident.php">';
          $disabled = "disabled";
        }
        else
        {
          echo '<form id="incident_form" name="incident_form" method="post" action="create-incident.php">';
          $disabled = "";
        }
        ?>

          <input type="hidden" name="incident_id" value="<?php echo $incident_id; ?>">

          <ul class="vertical">
	    <?php if (!isset($hide_incident_id)): ?>
            <li>
              <label for="incident_id">Incident ID:</label>
              <input type="text" name="incident_id" maxlength="10" DISABLED value="<?php echo $incident_id; ?>">
            </li>
	    <?php endif; ?>
            <li>
              <label for="incident_date">Date:</label>
              <input type="text" style="width: 100px;" name="incident_date" value="<?php echo $incident_date; ?>" > 
              <input type=button value="Calendar" onclick="displayDatePicker('incident_date', this);">
            </li>
            <li>
              <label for="order_id">Order ID:</label>
              <input type="text" name="order_id" maxlength="10" value="<?php echo $order_id; ?>">
            </li>
            <li>
              <label for="incident_value">Dollar Value:</label>
              <input type="text" name="incident_value" maxlength="10" value="<?php echo $incident_value; ?>"> (Leave out dollar sign.)
            </li>
            <li>
              <label for="upload_notes">Upload Notes:</label>
              <textarea name="upload_notes" id="upload_notes"><?php echo $upload_notes; ?></textarea>
            </li>
            <li>
              <label for="incident_type">Type:</label>
	      <select id="incident_type" name="incident_type">
                <option value=""         <?php if ($incident_type == "") echo "selected"; ?>        >Choose One</option>
                <option value="Quality"  <?php if ($incident_type == "Quality") echo "selected"; ?> >Quality</option>
                <option value="Count"    <?php if ($incident_type == "Count") echo "selected"; ?>   >Count</option>
                <option value="Size"     <?php if ($incident_type == "Size") echo "selected"; ?>    >Size</option>
                <option value="Packing"  <?php if ($incident_type == "Packing") echo "selected"; ?> >Packing</option>
                <option value="Upload"   <?php if ($incident_type == "Upload") echo "selected"; ?>  >Upload</option>
                <option value="Customer" <?php if ($incident_type == "Customer") echo "selected"; ?>>Customer</option>
                <option value="Other"    <?php if ($incident_type == "Other") echo "selected"; ?>   >Other</option>
              </select>
            </li>
            <li>
              <label for="accountable">Accountable:</label>
              <input type="text" name="accountable" maxlength="10" value="<?php echo $accountable; ?>">
            </li>
            <li>
              <label for="caused">Caused:</label>
              <input type="text" name="caused" maxlength="10" value="<?php echo $caused; ?>">
            </li>
            <li>
              <label for="comments">Comments:</label>
              <textarea name="comments" id="comments"><?php echo $comments; ?></textarea>
            </li>
            <li>
              <?php
              if (strcmp($edit_mode, "edit") == 0)
                echo '<input style="float: right;" type="submit" name="submit_edit_incident" value="Update Incident Information">';
              else
                echo '<input style="float: right;" type="submit" name="submit_create_incident" value="Create New Incident">';
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


