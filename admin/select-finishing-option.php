<?php 
  include ('authadmin.php'); 

  /* Get a list of all finishing option codes and names from the database.*/
  $query = "SELECT Category, Id, Enabled, Code, OptionName FROM signboom_finishing ORDER BY Category ASC, Enabled DESC, SortGroup ASC, SortOrder ASC"; 
  mysqli_select_db( $DBConn, $database_DBConn) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));

  /* Display them as links. */
  include ('templates/select-finishing-option.php'); 

  /* Free memory. */
  ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

?>
