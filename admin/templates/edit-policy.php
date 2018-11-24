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
          echo '<h1>Edit Policy:</h1>';
        else
          echo '<h1>Create New Policy:</h1>';
      ?>

      <div style="width: 840px; margin: 20px auto;">

        <?php
        if ($edited)        echo '<p class="highlighted">The policy has been updated.</p>';
        if ($created)       echo '<p class="highlighted">The new policy has been created.</p>'; 
        if ($error_message) echo '<p class="highlighted">' . $error_message . '</p>'; 

        if (strcmp($edit_mode, "edit") == 0)
        {
          echo '<form id="policy_form" name="policy_form" method="post" action="edit-policy.php">';
          $disabled = "disabled";
        }
        else
        {
          echo '<form id="policy_form" name="policy_form" method="post" action="create-policy.php">';
          $disabled = "";
        }
        ?>

          <input type="hidden" name="id" value="<?php echo $policy_id; ?>">

          <ul class="vertical">

            <li>
              <label for="policy_id">ID:</label>
              <input type="text" name="policy_id" value="<?php echo $policy_id; ?>" disabled>
            </li>

            <li>
              <label for="policy_title">Title:</label>
              <input type="text" name="policy_title" value="<?php echo $policy_title; ?>">
            </li>

            <li>
              <label for="policy_category">Category:</label>
              <select id="policy_category" name="policy_category">
                <option value=""   <?php if ($policy_category == "")   echo "selected"; ?>>Choose Category</option>
                <?php 
                for ($i = 0; $i < count($category_array); $i++) { 
                  $temp_code = $category_array[$i];
                  if (strcmp($policy_category, $temp_code) == 0)
                    echo "<option value=\"$temp_code\" selected>$temp_code</option>";
                  else
                    echo "<option value=\"$temp_code\">$temp_code</option>";
                }
                ?>
              </select>
            </li>

            <li>
              <label for="policy_display">Display Policy in Production?</label>
              <select id="policy_display" name="policy_display">
                <option value=""   <?php if ($policy_display == "")   echo "selected"; ?>>Choose One</option>
                <option value="1"  <?php if ($policy_display == "1")  echo "selected"; ?>>DISPLAY</option>
                <option value="0"  <?php if ($policy_display == "0")  echo "selected"; ?>>HIDE</option>
              </select>
            </li>

            <li>
              <label for="policy_details">Policy:</label>
              <textarea name="policy_details" id="policy_details"><?php echo $policy_details; ?></textarea>
            </li>

            <li>
              <?php
              if (strcmp($edit_mode, "edit") == 0)
                echo '<input style="float: right;" type="submit" name="submit_edit_policy" value="Update Policy">';
              else
                echo '<input style="float: right;" type="submit" name="submit_create_policy" value="Create New Policy">';
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


