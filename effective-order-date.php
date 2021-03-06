<?php

  include("Connections/DBConn.php");
  include("includes/utils.php");

    $defcutoff = 12;
    // Read the cutoff time from the database.  If you can't assume it is 12 noon.
    $query = "SELECT cutofftime FROM signboom_parm WHERE ID = 1"; 
    mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
    while ($row = mysqli_fetch_array($result,  MYSQLI_BOTH)) { 
      $defcutoff = $row['cutofftime'];
    } 
    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

    // Once we have PHP 5.1 and can set the default time zone, we can get rid of the -3 in the line below.
    $today_vancouver = mktime(date("H")-3, date("i"), date("s"), date("m"), date("d"), date("Y"));
    $today = date("Y-m-d H:i:s", $today_vancouver);   // e.g.2001-03-10 17:16:18 (the mysql DATETIME format)
    $hour = date("H", $today_vancouver);
    $day = date("d", $today_vancouver);
    $month = date("m", $today_vancouver);
    $year = date("Y", $today_vancouver);
  
    // defcutoff is the cutoff time for orders, in 24 hour clock.  This is stored in the database. 
    // It is set through that admin website in Vancouver time zone.  Usually something like 12 or 13.

    //If order is placed past the cutoff time, treat the order as if it came in on the
    //following day at 6 am.  
    if ($hour >= $defcutoff)
      $effective_order_date = mktime(6, 0, 0, $month, $day+1, $year);
    else
      $effective_order_date = $today_vancouver;
    $day = date("d", $effective_order_date);
    $month = date("m", $effective_order_date);
    $year = date("Y", $effective_order_date);

    echo "The effective order date of this order is:  day = $day, month = $month, year = $year<br>";

?>
