<?php
// Get variables passed in through POST.
if (isset($_POST['order_uploaded'])) 
  $the_order_uploaded = $_POST['order_uploaded'];
else
  $the_order_uploaded = "";

if ($the_order_uploaded != "") {
  $now = mktime(); // given in seconds
  $vancouver_time = $now - (3 * 60 * 60); // subtract 3 hours off
  $upload_completion_time = date('Y-m-d H:i:s', $vancouver_time);
  $query_update2 = "UPDATE signboom_ordermast SET Uploaded = 'Yes', UploadCompletionTime = '$upload_completion_time' WHERE ID = '$the_order_uploaded'";
  $result_update2 = mysqli_query( $DBConn, $query_update2);
  $query_update3 = "UPDATE signboom_linedetail SET currentqueue = 'Proof' WHERE orderid = '$the_order_uploaded'";
  $result_update3 = mysqli_query( $DBConn, $query_update3);
  $message = "The upload status of order $the_order_uploaded has been changed to UPLOADED.";
}
?>

  <script language="JavaScript" type="text/JavaScript">
  function ConfirmUploadStatus(my_order_uploaded) {
    var answer = confirm("Are ALL the files of order " + my_order_uploaded + " uploaded?");
    if (answer) {
      document.getElementById('order_uploaded').value = my_order_uploaded;
      document.uploaded_form.submit();
    }
    else {
      document.getElementById('order_uploaded').value = '';
      document.uploaded_form.submit();
    }
  }
  </script>

  <form style="display: inline-block;" name="uploaded_form" action="<?php echo $PHP_SELF; ?>" method="post">
    <input name="order_id" id="order_id" type="hidden" value="<?php echo $order_id; ?>">
    <input type="hidden" name="order_uploaded" id="order_uploaded" value="">
    <input style="margin-left: 20px;" name="change_order_status" type="submit" onclick="ConfirmUploadStatus('<?php echo $order_id?>')" value="Order <?php echo $order_id?> Now Uploaded">
  </form>

  <?php 
  if (strlen(trim($message)) > 0) 
    echo "<div style=\"text-align: center; font-family: Arial; font-weight: bold; font-size: 10pt; color:#00AAEA; padding-top: 15px;\">$message</div>";
  ?>

