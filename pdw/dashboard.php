<?php 
  include ('authadmin.php'); 
  include('includes/dashboard_completed_orders.php');
  include('includes/dashboard_backlog_orders.php');
  include('includes/date_picker.htm');

  $my_account_name =  $GLOBALS['MM_AcctName'];

  calculateAllBacklog();

  include ('templates/dashboard.html.php'); 

  mysql_free_result($orders);
  mysql_free_result($jobs);

?>
