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

      <h1>Edit Holiday Closures</h1>

      <?php
      if ($deleted)       echo '<p class="highlighted">That closure/holiday has been deleted.</p>';
      if ($error_message) echo '<p class="highlighted">' . $error_message . '</p>';
      ?>

      <form id="holidays_form" name="holidays_form" method="post" action="edit-holiday-closures.php">

        <?php
        echo '<table class="edit_product_finishing">';
        echo '<tr><th>Date</th><th>Holiday Name</th><th>Delete</th></tr>';
        while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) 
        { 
          echo '<tr><td>' . $row['holiday'] . '</td><td>' . $row['Description'] . '</td>';
          echo '<td><input type="submit" name="delete" value="' . $row['ID'] . '" class="delete_button"></td></tr>';
        } 
        echo '</table>';
        ?>

        <h2>Add a New Holiday/Closure Date</h2>

        <select id="name_of_holiday" name="name_of_holiday" style="float: left; margin-bottom: 200px;">
          <option value="">Choose holiday</option>
          <?php
          $option_group = "";
          for ($i = 0; $i < count($list_of_holiday_names); $i++)
          {
            echo '<option value="' . $list_of_holiday_names[$i] . '">' . $list_of_holiday_names[$i] . '</option>';
          }
          ?>
        </select>
        <input type="text"   style="float: left; margin-left: 40px; width: 100px;" name="new_date" 
               value="<?php if (isset($new_date) && (strlen($new_date) > 0)) echo $new_date; else echo 'Choose Date'; ?>" > 
        <input type="button" style="float: left;" value="Calendar" onclick="displayDatePicker('new_date', this);">
        <input type="submit" style="float: left; margin-left: 40px;" name="add" value="Add Holiday/Shutdown" > 

      </form>

    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


