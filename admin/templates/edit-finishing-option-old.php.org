<!doctype html>

<html>

  <head>
    <meta charset="utf-8">
    <title>Signboom Administration</title>
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
          echo '<h1>Edit Finishing Option:</h1>';
        else
          echo '<h1>Create New Finishing Option:</h1>';
      ?>

      <div style="width: 840px; margin: 20px auto;">

        <?php
        if ($edited)        echo '<p class="highlighted">The finishing option information has been updated.</p>';
        if ($created)       echo '<p class="highlighted">The new finishing option has been created.</p>'; 
        if ($error_message) echo '<p class="highlighted">' . $error_message . '</p>'; 

        if (strcmp($edit_mode, "edit") == 0)
        {
          echo '<form id="finishing_option_form" name="finishing_option_form" method="post" action="edit-finishing-option.php">';
          $disabled = "disabled";
        }
        else
        {
          echo '<form id="finishing_option_form" name="finishing_option_form" method="post" action="create-finishing-option.php">';
          $disabled = "";
        }
        ?>

          <input type="hidden" name="finishing_option_id" value="<?php echo $finishing_option_id; ?>">
          <!--
          <input type="hidden" name="category" value="<?php echo $category; ?>">
          <input type="hidden" name="option_set" value="<?php echo $option_set; ?>">
          <input type="hidden" name="option_code" value="<?php echo $option_code; ?>">
          -->
	  
        <ul class="vertical">

          <li>
            <label for="product_category">Category:</label>
            <select id="product_category" name="product_category">
              <option value=""   <?php if ($product_category == "")   echo "selected"; ?>>Choose Category</option>
              <?php 
              for ($k = 0; $k < count($category_array); $k++) { 
                $temp_code = $category_array[$k];
                if (strcmp($product_category, $temp_code) == 0)
                  echo "<option value=\"$temp_code\" selected>$temp_code</option>";
                else
                  echo "<option value=\"$temp_code\">$temp_code</option>";
              }
              ?>
            </select>
          </li>

          <li>
            <label for="finishing_option_set">Option Set:</label>
            <select name="finishing_option_set" id="finishing_option_set">
              <option value="CHOOSE">Choose Option Set</option>
              <?php 
              for ($m = 0; $m < count($optionset_name_array); $m++) { 
                $temp_optionset_name = $optionset_name_array[$m];
                $temp_optionset_display_name = $optionset_display_name_array[$m];
                if (strcmp($finishing_option_set, $temp_optionset_name) == 0)
                  echo "<option value=\"$temp_optionset_name\" selected>$temp_optionset_display_name</option>";
                else
                  echo "<option value=\"$temp_optionset_name\">$temp_optionset_display_name</option>";
              }
              ?>
            </select>
          </li>

          <li>
            <label for="finishing_option_name">Option Name:</label>
            <input name="finishing_option_name" id="finishing_option_name" style="width: 350px;" value="<?php echo $finishing_option_name; ?>"> Short Descriptive Title
          </li>

	  <li>
            <label for="finishing_option_enabled">Enabled:</label>
            <select id="finishing_option_enabled" name="finishing_option_enabled">
              <option value=""   <?php if ($finishing_option_enabled == "")   echo "selected"; ?>>Choose One</option>
              <option value="1"  <?php if ($finishing_option_enabled == "1")  echo "selected"; ?>>ENABLED</option>
              <option value="0"  <?php if ($finishing_option_enabled == "0")  echo "selected"; ?>>DISABLED</option>
            </select>
          </li>

 	  <li>
            <label for="finishing_option_batch_day">Batch Day:</label>
            <select id="finishing_option_batch_day" name="finishing_option_batch_day">
              <option value="0"   <?php if ($finishing_option_batch_day == 0)   echo "selected"; ?>>Not a Batch Item</option>
              <option value="1"   <?php if ($finishing_option_batch_day == 1)   echo "selected"; ?>>Monday</option>
              <option value="2"   <?php if ($finishing_option_batch_day == 2)   echo "selected"; ?>>Tuesday</option>
              <option value="3"   <?php if ($finishing_option_batch_day == 3)   echo "selected"; ?>>Wednesday</option>
              <option value="4"   <?php if ($finishing_option_batch_day == 4)   echo "selected"; ?>>Thursday</option>
              <option value="5"   <?php if ($finishing_option_batch_day == 5)   echo "selected"; ?>>Friday</option>
            </select>
          </li>

          <li>
            <label for="finishing_option_description">Description:</label>
            <textarea name="finishing_option_description" id="finishing_option_description" style="width: 600px; height: 300px;"><?php echo $finishing_option_description; ?></textarea>
          </li>

          <li>
            <label for="finishing_option_code">Code:</label> 
            <input name="finishing_option_code" id="finishing_option_code" value="<?php echo $finishing_option_code; ?>"
	           <? if (strcmp($edit_mode, "edit") == 0) echo 'disabled'; ?>>
            <a href="#" onclick="alert('Codes must be in format XX-X or XX-XX where each X is an uppercase letter.\n\nThe first letter must be either:\n  A: adhesive\n  B: banner\n  R: rigid\n  M: small\n  G: gfloor\n  S: specialty\n\nThe second latter must be either:\n  F: finishing / cutting\n  L: lamination\n  I: ink layers\n  B: back side\n  H: hanging\n  E: edges\n  O: orientation.\n\nThe last one or two letters should relate to the name of this finishing option.\n\nFor example RG-4S is for Rigid Hanging - Standoffs 4 Total.\n\nAnd BB-DF is Banner Back-Side Print Both Sides, with Different Files');"> What is this?</a>
	    <!--'\n\nNote: Option codes cannot be renamed using this page once they are created. However, the webmaster can rename them using a global search and replace on the database.' -->
          </li>

          <li>
            <label for="laminate_product_code">Laminate Product:</label>
            <input name="laminate_product_code" id="laminate_product_code" value="<?php echo $laminate_product_code; ?>">
          </li>

          <li>
            <label for="extra_time">Extra Time (in Days):</label>
            <input name="extra_time" id="extra_time" value="<?php echo $extra_time; ?>">
          </li>

          <li>
            <label for="finishing_option_fixed_cost">Fixed Costs:</label>
            <input name="finishing_option_fixed_cost" id="finishing_option_fixed_cost" value="<?php echo $finishing_option_fixed_cost; ?>">
          </li>

          <li>
            <label for="finishing_option_variable_cost">Variable Costs:</label>
            <input name="finishing_option_variable_cost" id="finishing_option_variable_cost" value="<?php echo $finishing_option_variable_cost; ?>">
          </li>

          <li>
            <label for="finishing_option_sort_group">Sort Group:</label>
            <input name="finishing_option_sort_group" id="finishing_option_sort_group" value="<?php echo $finishing_option_sort_group; ?>">   A, B, C, D...
          </li>

          <li>
            <label for="finishing_option_sort_order">Sort Order:</label>
            <input name="finishing_option_sort_order" id="finishing_option_sort_order" value="<?php echo $finishing_option_sort_order; ?>">   1, 2, 3, 4...
          </li>

          <li>
            <label for="units_of_measure">Units of Measure:</label>
            <select name="units_of_measure" id="units_of_measure">
              <option value="CHOOSE">Choose Units</option>
              <option value="EA" <?php if ($units_of_measure == "EA") echo "selected"; ?>>Each (EA)</option>
              <option value="BS" <?php if ($units_of_measure == "BS") echo "selected"; ?>>Back Side (BS)</option>
              <option value="SF" <?php if ($units_of_measure == "SF") echo "selected"; ?>>Square Feet (SF)</option>
              <option value="PF" <?php if ($units_of_measure == "PF") echo "selected"; ?>>Perimeter Feet (PF)</option>
              <option value=""   <?php if ($units_of_measure == "")   echo "selected"; ?>>None Specified</option>
            </select>
          </li>

          <li>
            <label for="units_per_hour">Units per Hour:</label>
            <input name="units_per_hour" id="units_per_hour" value="<?php echo $units_per_hour; ?>">
          </li>

          <li>
            <label for="reference">Reference:</label>
            <input name="reference" id="reference" value="<?php echo $reference; ?>">
          </li>

          <li>
            <?php
            if (strcmp($edit_mode, "edit") == 0)
	    {
              echo '<input type="hidden" name="finishing_option_code_hidden" id="finishing_option_code_hidden" value="' . $finishing_option_code . '">';
              echo '<input style="float: right;" type="submit" name="submit_edit_finishing_option" value="Update Finishing Option Information">';
	    }
            else
	    {
              echo '<input style="float: right;" type="submit" name="submit_create_finishing_option" value="Create New Finishing Option">';
	    }
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


