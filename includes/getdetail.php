<?php

function getOptionName($option_code) {
    global $DBConn;

    $option_name = "";
    if (strlen($option_code) == 0) return $option_name;

    $query_option = "SELECT OptionName FROM signboom_finishing WHERE Code = '$option_code'";
    $result_option = mysql_query($query_option, $DBConn) or die(mysql_error());
    $row_option = mysql_fetch_array($result_option, MYSQL_BOTH);
    $option_name = $row_option['OptionName'] . ", ";
    return $option_name;
  }

function getOrderDetails($the_order_id, &$detail)
{
  global $DBConn;

  $Qry = "SELECT * FROM signboom_linedetail WHERE orderid = $the_order_id ORDER BY product";
  $rsdetail = mysql_query($Qry, $DBConn) or die(mysql_error());
  $rowdtl = mysql_fetch_assoc($rsdetail);
  $i = 0;
  do {
    $i++;

    // Find the finishing options for this file.
    $row_detail_options = "";
    $row_detail_options .= getOptionName($rowdtl['AF']);
    $row_detail_options .= getOptionName($rowdtl['AL']);
    $row_detail_options .= getOptionName($rowdtl['AI']);
    $row_detail_options .= getOptionName($rowdtl['AP']);
    $row_detail_options .= getOptionName($rowdtl['AK']);
    $row_detail_options .= getOptionName($rowdtl['BF']);
    $row_detail_options .= getOptionName($rowdtl['BB']);
    $row_detail_options .= getOptionName($rowdtl['BI']);
    $row_detail_options .= getOptionName($rowdtl['BP']);
    $row_detail_options .= getOptionName($rowdtl['BK']);
    $row_detail_options .= getOptionName($rowdtl['RF']);
    $row_detail_options .= getOptionName($rowdtl['RL']);
    $row_detail_options .= getOptionName($rowdtl['RB']);
    $row_detail_options .= getOptionName($rowdtl['RH']);
    $row_detail_options .= getOptionName($rowdtl['RE']);
    $row_detail_options .= getOptionName($rowdtl['RI']);
    $row_detail_options .= getOptionName($rowdtl['RP']);
    $row_detail_options .= getOptionName($rowdtl['RK']);
    $row_detail_options .= getOptionName($rowdtl['RO']);

    // Save all the details of the file.
    $detail[$i]->code = $rowdtl['product'];
    $detail[$i]->options = $row_detail_options;
    $detail[$i]->quantity = $rowdtl['quantity'];
    $detail[$i]->width =$rowdtl['itemwidth'];
    $detail[$i]->height = $rowdtl['itemheight'];
    $detail[$i]->filename = $rowdtl['filename'];
    $detail[$i]->total =$rowdtl['cost'];
    $detail[$i]->dctcost = $rowdtl['dctcost']; //Discountable cost
    $detail[$i]->sqfootage = $rowdtl['squarefootage'];
    $detail[$i]->printedarea = $rowdtl['printedarea'];
    $detail[$i]->wastearea = $rowdtl['wastearea'];
    $detail[$i]->wastecost = $rowdtl['wastecost'];
  }
  while ($rowdtl = mysql_fetch_assoc($rsdetail));
}
?>
