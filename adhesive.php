  <?php

  require_once('Connections/DBConn.php');

  function DisplayProductDescriptions() {
    global $database_DBConn, $DBConn, $first;

    $Qry = "SELECT * FROM signboom_allproducts WHERE Category = 'ADHESIVE' AND Enabled = 1 ORDER BY SortGroup, SortOrder"; 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $first = true;
    while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) { 
       if (!$first) echo '<hr style="clear: both;"><br>';
       echo '<h2 class="product" id="' . $row['Code'] . '">' . $row['Name'] . '</h2>';
       $description_text = str_replace('<hr />', '', $row['DescriptionText']);
       echo $description_text;
       $description_finishing = str_replace('<hr />', '', $row['DescriptionFinishing']);
       $description_finishing = str_replace(
         '<em>Click EDIT button to customize Standard Finishing Options</em>',
	 '',
         $description_finishing);
       echo '<b>Finishing</b>';
       echo $description_finishing;
       $description_limitations = str_replace('<hr />', '', $row['DescriptionLimitations']);
       echo $description_limitations;
       $description_extras = str_replace('<hr />', '', $row['DescriptionExtras']);
       echo $description_extras;
       $first = false;
       echo '<br>';
     } 
     ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
  }

  function DisplayFinishingDescriptions() {
    global $database_DBConn, $DBConn, $first;

    $Qry = "SELECT * FROM signboom_finishing WHERE Category = 'ADHESIVE' AND Enabled = 1 ORDER BY SortGroup, SortOrder";
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $Qry) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $first = true;
    while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) { 
       if (!$first) echo '<hr style="clear: both;"><br>';
       echo '<h2 class="product" id="' . $row['Code'] . '">' . $row['OptionName'] . '</h2>';
       $description = str_replace('<hr />', '', $row['Description']);
       echo $description;
       $first = false;
       echo '<br>';
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

    <img src="images/title_adhesive.gif" width="115" height="18" alt="ADHESIVE">

    <p>Our wide selection of adhesive films is described below.</p>

    <?php
    DisplayProductDescriptions();
    ?>

    <br><img src="images/title_adhesive_finishing.gif" width="342" height="18" alt="ADHESIVE FINISHING OPTIONS"><br><br>

    <?php
    DisplayFinishingDescriptions();
    ?>

    </div>
  
    <?php
      include ('footer.html');
    ?>

  </div>
</div> 
</body>

</html>
