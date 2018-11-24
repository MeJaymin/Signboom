<?php 
  /* This is the unfiltered order page with all the products for this client. */
  include ('authadmin.php'); 

  // authadmin.php ensures that only clients with admin or event privileges can access this page.
  $my_account_name =  $GLOBALS['MM_AcctName'];

  // Query the user_products database for all the products for client TED2018.
  $myQuery = "SELECT * FROM signboom_user_products WHERE AcctName='$my_account_name' AND (Enabled = 1 OR Enabled = 2) ORDER BY Category, ProductCode"; 
  $result = mysql_query($myQuery);
  $num_rows = mysql_num_rows($result);

  // Create a javascript array which contains the products this client needs to order.
  echo '<script language="JavaScript" type="text/JavaScript">';
  echo '  var my_products = new Array();';

  if ($num_rows > 0) 
  {
    $i = 0;
    $previous_product = '';
    while ($my_row = mysql_fetch_array($result)) 
    {
      if ($my_row['ProductCode'] != $previous_product)
      {
        echo  'my_products[' . $i . '] = new Array();' . "\n";
        echo  'my_products[' . $i . ']["id"] = "'            . $my_row['Id']          . '";' . "\n";
        echo  'my_products[' . $i . ']["category"] = "'      . $my_row['Category']    . '";' . "\n";
        echo  'my_products[' . $i . ']["product_code"] = "'  . $my_row['ProductCode'] . '";' . "\n";
        echo  'my_products[' . $i . ']["product_name"] = "'  . $my_row['ProductName'] . '";' . "\n";
        $previous_product = $my_row['ProductCode'];
        $i++;
      }
    }
  }
  echo '</script>';

  /* Display the products as drag-and-drop boxes. */
  include ('templates/order.html.php'); 

  /* Free memory. */
  mysql_free_result($result);

?>
