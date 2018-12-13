<?php
// Get variables passed in through POST.
if (isset($_POST['new_ready_date'])) 
  $new_ready_date = $_POST['new_ready_date'];
else
  $new_ready_date = "";

if ($new_ready_date == "") {
  // First visit to this page. NOT in response to clicking button "Change Ready Date".
  $query_order = "SELECT readydatetime from signboom_ordermast WHERE ID = $order_id";
  $the_order = mysqli_query( $DBConn, $query_order) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $row_order = mysqli_fetch_assoc($the_order);
  $ready_date_time  = $row_order['readydatetime']; 
}

else {

  // "Change Ready Date" has been clicked.  Handle it.

  sscanf($new_ready_date, "%d-%d-%d", $the_year, $the_month, $the_day);
  $new_ready_date_mmddyyyy = sprintf("%02s/%02s/%4s", $the_month, $the_day, $the_year);
  $new_ready_date_mmddyyyy = $new_ready_date_mmddyyyy . " 3PM";
  $new_ready_date_time = $new_ready_date . " 15:00:00";

  // TO DO: Validate data above.
  $data_valid = 1;

  if ($data_valid) {
    if ($new_ready_date_time < date("Y-m-d H:i:s", mktime()) ) {
      echo "<div style=\"text-align: center; font-family: Arial; font-weight: bold; font-size: 10pt; color: #CC0000; padding-top: 15px;\">That ready date is in the past.  The ready date must be in the future. The ready date has not been changed.</div>";
      $data_valid = 0;
    }

    // Data is valid.  Carry out the change.
    if ($data_valid) {

     // Change ready date associated with this order.
     $query_update = "UPDATE signboom_ordermast SET readydate = '$new_ready_date_mmddyyyy', readydatetime = '$new_ready_date_time' WHERE ID = '$order_id'";
     //echo "<script language=\"javascript\">alert(\"Query is: " . $query_update. ".\");</script>";
     $result_update = mysqli_query( $DBConn, $query_update);

     // Change ready date associated with each file in this order. (We track dates against files since drag and drop was
     // implemented for client PDW/TED2015.)
     $query_files = "SELECT id from signboom_linedetail WHERE orderid = $order_id";
     $the_files = mysqli_query( $DBConn, $query_files) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
     while ($row_files = mysqli_fetch_assoc($the_files))
     {
       $file_id = $row_files['id']; 
       $query_update_file = "UPDATE signboom_linedetail SET readydate = '$new_ready_date_mmddyyyy', readydatetime = '$new_ready_date_time' WHERE id = '$file_id'";
       $result_update_file = mysqli_query( $DBConn, $query_update_file);
     }

     echo "<div style=\"text-align: center; font-family: Arial; font-weight: bold; font-size: 10pt; color:#00AAEA; padding-top: 15px;\">The ready date of order $order_id has been changed to $new_ready_date</div>";
     $ready_date = $new_ready_date;
     $new_ready_date = '';
    }
  }

}

?>
<form name="change_ready_date" method="post" action="">
  <div style="clear: both; width: 800px; background-color: #eeeeee; border: solid 1px #cccccc; text-align: center; margin: 20px auto; padding: 20px;">
    <input type="hidden" name="order_id" value="<? echo $order_id; ?>">
    <b>Current Ready Date:</b> <?php echo $ready_date; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <b>New Ready Date:</b> 
    <input name="new_ready_date" size="10" value="<? echo $new_ready_date; ?>">
    <input type="button" value="Calendar" onclick="displayDatePicker('new_ready_date', this);">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input name="change_ready_date" type="button" value="Change Ready Date" onClick="this.form.submit();"> 
  </div>
</form>

