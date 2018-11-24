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
      <h1>Finishing Options Management</h1>
      <?php 
      $category = "";
      while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) 
      { 
        if ($category == "") 
        {
          $category = $row['Category'];
          echo '<div class="narrow_column"><h2>' . $category . '</h2><br><br>';
          $disabled_title_displayed = 0;
        }
        else if ($category != $row['Category']) 
        {
          $category = $row['Category'];
          echo '</div><div class="narrow_column"><h2>' . $category . '</h2><br><br>';
          $disabled_title_displayed = 0;
        }
	if (($row['Enabled'] == 0) && (!$disabled_title_displayed))
	{
	  echo '<br><b>Disabled</b><br>';
          $disabled_title_displayed = 1;
	}
        echo '<a href="edit-finishing-option.php?finishing_option_id=' . $row['Id'] .'">';
        echo $row['OptionName'] . ' (' . $row['Code'] . ') ';
        echo '</a><br>';
      } 
      echo '</div>';
      ?>
    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


