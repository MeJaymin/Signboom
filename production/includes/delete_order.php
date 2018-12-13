  <script language="JavaScript" type="text/JavaScript">
  function ConfirmDelete(my_order_to_delete) {
    var answer = confirm("Do you really wish to delete order " + my_order_to_delete + "?");
    if (answer) {
      //alert("You want to delete order " + my_order_to_delete);
      document.getElementById('order_to_delete').value = my_order_to_delete;
      document.delete_form.submit();
    }
    else {
      //alert("You DO NOT want to delete order " + my_order_to_delete);
      document.getElementById('order_to_delete').value = '';
      document.delete_form.submit();
    }
  }
  </script>

<?php

   $order_hidden = 'no';
   if (isset($_POST['order_to_delete']))
   {
     $order_to_delete = $_POST['order_to_delete'];
     if ($order_to_delete != '') {
       $query_hide_order = "UPDATE signboom_ordermast SET hidden='yes' WHERE ID = '$order_to_delete'";
       $result1 = mysqli_query( $DBConn, $query_hide_order);
       $query_hide_files = "UPDATE signboom_linedetail SET currentqueue='Deleted' WHERE orderid = '$order_to_delete'";
       $result2 = mysqli_query( $DBConn, $query_hide_files);
       echo "<script language=\"javascript\">alert(\"Order " . $order_to_delete . " has been deleted.\");</script>";
       $order_hidden = 'yes';
     }
   }
   else
   {
     // When an order has just been rejected, the page redisplays without $_POST['order_id'] set...
     if ($order_id == '') $order_id = $_POST['order_to_reject'];

     // Don't display the Delete button if the order is already deleted.
     $sql_delete_check = "SELECT hidden FROM signboom_ordermast WHERE ID = $order_id";
     $result_delete_check = mysqli_query( $DBConn, $sql_delete_check);
     if (!$result_delete_check)
     {
       echo "Error #701 has occured while querying the database: $sql_delete_check.<br>Please contact Alison Taylor to investigate this.<br>";
       return false;
     }
     $row_delete_check = mysqli_fetch_array($result_delete_check); 
     $order_hidden = $row_delete_check['hidden']; // This value is also used in reject_order.php
   }

   if ($order_hidden != 'yes'):

?>
   &nbsp;&nbsp;
   <form style="display: inline-block" name="delete_form" action="<?php echo $PHP_SELF; ?>" method="post">
    <input name="order_id" id="order_id" type="hidden" value="<?php echo $order_id; ?>">
    <input type="hidden" name="order_to_delete" id="order_to_delete" value="">
    <input style="margin-left: 20px;" name="delete_order" type="submit" onclick="ConfirmDelete('<?php echo $order_id?>')" value="Delete Whole Order <?php echo $order_id?>">
  </form>

<?
   endif;

 ?>
