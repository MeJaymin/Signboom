<!doctype html>

<html>

<head>
  <meta charset="utf-8">
  <title>Signboom: PWD Order System</title>
  <?php include ('head.html'); ?>
  <link rel="stylesheet" type="text/css" media="all" href="styles.css" />
</head>

<body>

<div id="page_wide">

  <?php include ('banner-menu.html'); ?>

  <div id="content_wide">

    <h1>Files Ordered - <?php echo $stage . ' ' . $special_case; ?></h1>

    <div style="text-align: center;">
      <b>
      COLOUR LEGEND: &nbsp;&nbsp;&nbsp;&nbsp;
      <span class="lineitem_rush">RUSH</span> &nbsp;&nbsp;&nbsp;&nbsp;
      <span class="lineitem_hot">HOT</span> &nbsp;&nbsp;&nbsp;&nbsp;
      </span>
      </b>
      <br><br>
    </div>

    <table border="0" align="center" cellpadding="5">

    <?php 

      include('includes/jobs_table_heading.html'); 

      // Display the jobs for this page of results.
      for ($i = 0; ($i < $num_jobs); $i++) 
      {
        $row = mysql_fetch_assoc($jobs);
        if ($row == FALSE)
          echo "Could not read job from database.";
        else
          include('includes/jobs_display_row.php');
      } 

    ?>

    </table>

  </div>

</div>

</body>

</html>

