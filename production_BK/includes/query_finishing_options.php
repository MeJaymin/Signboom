<?php

/************ Build array of all existing finishing options.  ************/

$query_options = 'SELECT signboom_finishing.Code AS Code, signboom_finishing.ShortName AS ShortName FROM signboom_finishing, signboom_category WHERE signboom_finishing.Enabled = 1 AND signboom_category.enabled = 1 AND (signboom_finishing.Category = signboom_category.code)';
$result_options = mysql_query($query_options, $DBConn) or die("queryDashboard: Could not read finishing options from database:" . mysql_error() . "<br><br>" . $query_options);
$array_options = array();

while ($row_option = mysql_fetch_array($result_options))
{
  $option_code = $row_option['Code'];
  $option_name = $row_option['ShortName'];
  $array_options[$option_code] = $option_name;
}

?>
