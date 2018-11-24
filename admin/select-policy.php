<?php 
  include ('authadmin.php'); 

  /* Get a list of all policies from the database.*/
  $query = "SELECT Category, ID, Title, Policy FROM signboom_policies ORDER BY Category"; 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

  /* Display them as links. */
  include ('templates/select-policy.php'); 

  /* Free memory. */
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

?>
