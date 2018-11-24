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
          echo '<h1>Edit Product:</h1>';
        else
          echo '<h1>Create New Product:</h1>';
      ?>

      <div style="width: 840px; margin: 20px auto;">

        <?php
        if ($edited)        echo '<p class="highlighted">The product information has been updated.</p>';
        if ($created)       echo '<p class="highlighted">The new product has been created.</p>'; 
        if ($error_message) echo '<p class="highlighted">' . $error_message . '</p>'; 

        if (strcmp($edit_mode, "edit") == 0)
        {
          echo '<form id="product_form" name="product_form" method="post" action="edit-product.php">';
          $disabled = "disabled";
        }
        else
        {
          echo '<form id="product_form" name="product_form" method="post" action="create-product.php">';
          $disabled = "";
        }
        ?>

          <input type="hidden" name="id" value="<?php echo $product_id; ?>">

          <ul class="vertical">

            <li>
              <label for="product_code">Code:</label>
              <input type="text" name="product_code" value="<?php echo $product_code; ?>" <?php echo $disabled; ?>>
            </li>

            <li>
              <label for="product_name">Name:</label>
              <input type="text" name="product_name" value="<?php echo $product_name; ?>">
            </li>

            <li>
              <label for="product_thickness">Thickness:</label>
              <input type="text" name="product_thickness" value="<?php echo $product_thickness; ?>">
            </li>

            <li>
              <label for="product_uom_thickness">Units for Thickness:</label>
              <select id="product_uom_thickness" name="product_uom_thickness">
                <option value=""    <?php if ($product_uom_thickness == "")     echo "selected"; ?>>Choose One</option>
                <option value="MM"  <?php if ($product_uom_thickness == "MM")   echo "selected"; ?>>Millimetres</option>
                <option value="IN"  <?php if ($product_uom_thickness == "IN")   echo "selected"; ?>>Inches</option>
                <option value="MIL" <?php if ($product_uom_thickness == "MIL")  echo "selected"; ?>>Mil</option>
              </select>
            </li>

            <li>
              <label for="product_enabled">Enabled:</label>
              <select id="product_enabled" name="product_enabled">
                <option value=""   <?php if ($product_enabled == "")   echo "selected"; ?>>Choose One</option>
                <option value="1"  <?php if ($product_enabled == "1")  echo "selected"; ?>>ENABLED</option>
                <option value="0"  <?php if ($product_enabled == "0")  echo "selected"; ?>>DISABLED</option>
              </select>
            </li>

            <li>
              <label for="product_category">Category:</label>
              <select id="product_category" name="product_category">
                <option value=""   <?php if ($product_category == "")   echo "selected"; ?>>Choose Category</option>
                <?php 
                for ($i = 0; $i < count($category_array); $i++) { 
                  $temp_code = $category_array[$i];
                  if (strcmp($product_category, $temp_code) == 0)
                    echo "<option value=\"$temp_code\" selected>$temp_code</option>";
                  else
                    echo "<option value=\"$temp_code\">$temp_code</option>";
                }
                ?>
              </select>
            </li>

	    <li>
              <label for="product_batch_day">Batch Day:</label>
              <select id="product_batch_day" name="product_batch_day">
                <option value="0"   <?php if ($product_batch_day == 0)   echo "selected"; ?>>Not a Batch Item</option>
                <option value="1"   <?php if ($product_batch_day == 1)   echo "selected"; ?>>Monday</option>
                <option value="2"   <?php if ($product_batch_day == 2)   echo "selected"; ?>>Tuesday</option>
                <option value="3"   <?php if ($product_batch_day == 3)   echo "selected"; ?>>Wednesday</option>
                <option value="4"   <?php if ($product_batch_day == 4)   echo "selected"; ?>>Thursday</option>
                <option value="5"   <?php if ($product_batch_day == 5)   echo "selected"; ?>>Friday</option>
              </select>
            </li>

            <li>
              <label for="product_descr_image">Image:</label>
              <input type="text" name="product_descr_image" value="<?php echo $product_descr_image; ?>">
              <div style="float: right; width: 600px; margin: 10px 0; padding: 0;">
              You'll need to use the <b>Files</b> page to upload the image to the website. If you haven't uploaded
	      it yet, finish populating this page and then save it. Upload the image and then come back to this page 
	      and type the name of the image - including the extension - in the box above. You do not need to 
	      include the full path to the image.  The file name is case-sensitive.
              </div>
            </li>
            <li>
              <label for="product_descr_text">Text:</label>
              <textarea name="product_descr_text" id="product_descr_text"><?php echo $product_descr_text; ?></textarea>
            </li>
            <li>
              <label for="product_descr_finishing">Finishing:</label>
              <textarea name="product_descr_finishing" id="product_descr_finishing"><?php echo $product_descr_finishing; ?></textarea>
            </li>
            <li>
              <label for="product_descr_limitations">Limitations:</label>
              <textarea name="product_descr_limitations" id="product_descr_limitations"><?php echo $product_descr_limitations; ?></textarea>
            </li>
            <li>
              <label for="product_descr_extras">Extras:</label>
              <textarea name="product_descr_extras" id="product_descr_extras"><?php echo $product_descr_extras; ?></textarea>
              <div style="float: right; width: 600px; margin: 10px 0; padding: 0;">
              You'll need to use the <b>Files</b> page to upload the PDFs to the website. If you haven't uploaded
	      them yet, finish populating this page and then save it. Upload the PDFs and then come back to this page 
	      and use the link button in the editor to create links to them. <b>Choose the option to "Open link in a new
	      window".</b> Link URLs must be in this format 
	      (which is case-sensitive): http://www.signboom.com/product-files/filename.pdf
	      </div>
            </li>

            <li>
              <label for="product_width">Width (inches):</label>
              <input type="text" name="product_width" value="<?php echo $product_width; ?>">
            </li>

            <li>
              <label for="product_length">Length (inches):</label>
              <input type="text" name="product_length" value="<?php echo $product_length; ?>">
            </li>

            <li>
              <label for="product_cost_waste">Waste Cost:</label>
              <input type="text" name="product_cost_waste" value="<?php echo $product_cost_waste; ?>"><br>
            </li>

            <li>
              <label for="product_cost_non">Non-Discountable Cost:</label>
              <input type="text" name="product_cost_non" value="<?php echo $product_cost_non; ?>"><br>
            </li>

            <li>
              <label for="product_cost_disc">Discountable Cost:</label>
              <input type="text" name="product_cost_disc" value="<?php echo $product_cost_disc; ?>"><br>
            </li>

            <li>
              <label for="product_sort_group">Sort Group (letters):</label>
              <input type="text" name="product_sort_group" value="<?php echo $product_sort_group; ?>"><br>
            </li>

            <li>
              <label for="product_sort_order">Sort Order (numbers):</label>
              <input type="text" name="product_sort_order" value="<?php echo $product_sort_order; ?>"><br>
            </li>

            <li>
              <?php
              if (strcmp($edit_mode, "edit") == 0)
                echo '<input style="float: right;" type="submit" name="submit_edit_product" value="Update Product Information">';
              else
                echo '<input style="float: right;" type="submit" name="submit_create_product" value="Create New Product">';
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


