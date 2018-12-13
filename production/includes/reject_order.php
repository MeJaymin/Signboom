  <script language="JavaScript" type="text/JavaScript">
  function ConfirmReject(my_order_to_reject) {
    var answer = confirm("Do you really wish to reject order " + my_order_to_reject + "?");
    if (answer) {
      //alert("You want to reject order " + my_order_to_reject);
      document.getElementById('order_to_reject').value = my_order_to_reject;
      document.reject_form.submit();
    }
    else {
      //alert("You DO NOT want to reject order " + my_order_to_reject);
      document.getElementById('order_to_reject').value = '';
      document.reject_form.submit();
    }
  }
  </script>

<?php
   $order_rejected = 'no';
   if (isset($_POST['order_to_reject']))
   {
     $order_to_reject = $_POST['order_to_reject'];
     if ($order_to_reject != '')
     {
       $query_hide_files = "UPDATE signboom_linedetail SET currentqueue='Rejected' WHERE orderid = '$order_to_reject'";
       $result2 = mysqli_query( $DBConn, $query_hide_files);
       echo "<script language=\"javascript\">alert(\"Order " . $order_to_reject . " has been rejected.\");</script>";
       $order_rejected = 'yes';
     }
   }
   // Test above only shows if order was rejected on most recent submit of the page.
   // TO DO: Check to see if order had been rejected prior to that.

   // $order_hidden is set in the file delete_order.php.
   // Don't display the Reject button if the order is already deleted.
   if (($order_hidden == 'no') && ($order_rejected == 'no')):
?>

   &nbsp;&nbsp;
   <form style="display: inline-block" name="reject_form" action="" method="post">
    <input name="order_id" id="order_id" type="hidden" value="<?php echo $order_id; ?>">
    <input type="hidden" name="order_to_reject" id="order_to_reject" value="">
    <input style="margin-left: 20px;" name="reject_order" type="submit" onclick="ConfirmReject('<?php echo $order_id?>')" value="Reject Order <?php echo $order_id?>">
  </form>

<?
   endif;
?>
