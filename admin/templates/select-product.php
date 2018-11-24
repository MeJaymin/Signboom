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

      <h1>Edit Product Information and Finishing Options</h1>
      <?php 
      $category = "";
      $category_count = 0;
      while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) 
      { 
        if ($category == "") 
        {
          $category = $row['Category'];
          echo '<div class="column"><h2>' . $category . '</h2><br><br>';
          $category_count++;
	  $disabled_title_displayed = 0;
        }
        else if ($category != $row['Category']) 
        {
          $category = $row['Category'];
          if ($category_count % 2 == 0) 
            echo '</div><br style="clear: both;"><div class="column"><h2>' . $category . '</h2><br><br>';
          else
            echo '</div><div class="column"><h2>' . $category . '</h2><br><br>';
          $category_count++;
	  $disabled_title_displayed = 0;
        }
	if (($row['Enabled'] == 0) && (!$disabled_title_displayed))
	{
	  echo '<br><b>Disabled</b><br>';
          $disabled_title_displayed = 1;
	}
        echo $row['Name'] . ' (' . $row['Code'] . ') ';
        echo '<a href="edit-product.php?id=' . $row['Id'] .'">PRODUCT</a> - ';
        echo '<a href="edit-product-finishing.php?product=' . $row['Code'] .'">FINISHING</a><br>';
      } 
      echo '</div>';
      ?>
    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


