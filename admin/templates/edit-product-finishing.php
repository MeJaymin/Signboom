<!doctype html>

<html>

  <head>
    <meta charset="utf-8">
    <title>Signboom: Version 1.5 Administration</title>
    <?php include ('head.html'); ?>
  </head>

<body>

  <div id="page">

    <?php include ('banner-menu.php'); ?>

    <div id="content">

      <h1>Edit Product Finishing Options</h1>

      <h2>Finishing Options for <?php echo $product; ?></h2>

      <p>When the value is 2, that means this is the default finishing option in that option group.
      For example, a product may have AF-X as the default finishing option within the AF (cutting) group.
      You may only delete default finishing options when they are the only finishing option in that
      option group (e.g. the AF group).  If there are other finishing options in that group, you must 
      choose a new default option first.</p>

      <?php
      if ($deleted)       echo '<p class="highlighted">The finishing option has been deleted.</p>';
      if ($made_default)  echo '<p class="highlighted">The finishing option has been made a default.</p>';
      if ($error_message) echo '<p class="highlighted">' . $error_message . '</p>';
      ?>

      <form id="product_finishing_form" name="product_finishing_form" method="post" action="edit-product-finishing.php">

        <input type="hidden" name="product" value="<?php echo $product; ?>">

        <?php
        echo '<table class="edit_product_finishing">';
        echo '<tr><th>Finishing Option</th><th>Option Name</th><th>Value</th><th>Delete</th><th>Make Default</th></tr>';
        while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) 
        { 
          echo '<tr><td>' . $row['FinishingOptionCode'] . '</td><td>' . $row['OptionName'] . '</td><td>' . $row['Value'] . '</td>';
          echo '<td><input type="submit" name="delete" value="' . $row['Id'] . '" class="delete_button"></td>';
          if ($row['Value'] == 1) 
            echo '<td><input type="submit" name="make_default" value="' . $row['Id'] . '" class="default_button"></td>';
          else
            echo '<td></td>';
          echo '</tr>';
        } 
        echo '</table>';
        ?>

        <h2>Add Finishing Option</h2>

        <select id="new_option" name="new_option" style="float: left;">
          <option value="">Choose a Finishing Option</option>
          <!-- For some reason first select_separator is always blank.  So have an extra dummy on in for now. -->
          <option value="" class="select_separator" disabled></option>
          <?php
          $option_group = "";
          while ($row2 = mysqli_fetch_array($result2,  MYSQLI_BOTH)) 
          {
            $option_code = $row2['Code'];
            $option_name = $row2['OptionName'];
            $new_option_group = substr($option_code, 0, 2);
            if (strcmp($new_option_group, $option_group) != 0)
              echo '<option value="" class="select_separator" disabled></option>';
            echo '<option value="' . $option_code . '">' . $option_code . ': ' . $option_name . '</option>';
            $option_group = $new_option_group;
          }
          ?>
          <input type="submit" name="add" value="Add Finishing Option" style="float: left; margin-left: 20px;"> 
        </select>

      </form>

    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


