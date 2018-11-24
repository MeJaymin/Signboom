  <?php

  require_once('Connections/DBConn.php');

  function DisplayProductDescriptions() {
    global $database_DBConn, $DBConn, $first;

    $Qry = "SELECT * FROM signboom_allproducts WHERE Category = 'SPECIALTY' AND Enabled = 1 ORDER BY SortGroup, SortOrder"; 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $first = true;
    while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) { 
       if (!$first) echo '<hr>';
       //echo '<span class="product" id="' . $row['Code']. '"><b>';
       //echo $row['Name'];
       //echo '</b></span><br>';
       echo $row['Description'];
       $first = false;
       echo '<br><br>';
     } 
     ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
  }

  ?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <title>Signboom: Your Online Print Engine</title>

  <style>
  h2 {
    font-size:110%;
    color: #00aaea;
  }
  .description img {
    float: right;
    margin-left: 30px;
    margin-bottom: 10px;
  }
  </style>

  <!--INTERNET EXPLORER SPECIFIC STYLING CODE FOLLOWS-->
  <?php
    include ('browser_detection.php');
    $my_browser = browser_detection('browser');
    if ($my_browser == 'msie6') 
    {
      echo '<link rel="stylesheet" href="ie6_specific.css" type="text/css" title="default_style">';
    }
    else
    {
      echo '<link rel="stylesheet" href="signboom.css" type="text/css" title="default_style">';
    }
  ?>


</head>

<body>

<div id="page">
  <div id="wrapper">

    <?php
      include ('header.php');
    ?>

    <div id="content">
    <?php
      include ('sidebar.html');
    ?>

    <img src="images/title_specialty_signs.gif" width="366" height="18" alt="SPECIALTY SIGNS">

    <p>Our specialty sign materials are listed below.</p><p>&nbsp;</p>

    <?php
    //For all specialty sign materials, print out the contents from the database.
    DisplayProductDescriptions();
    ?>

    </div>

    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>
