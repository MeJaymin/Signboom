<?php

$date_time = date('Y-m-d H:i:s');

$query_user = "SELECT firstName, lastName, email FROM signboom_user WHERE AcctName = '$the_username'";
$result_user = mysql_query($query_user, $DBConn) or die(mysql_error());
$row_user = mysql_fetch_assoc($result_user);
$name = $row_user['firstName'] . ' ' . $row_user['lastName'];
$email_address = $row_user['email'];
$headers = 'From: signboom@signboom.com';

// Get list of email addresses of production staff to email to.
$email_addresses = 'len@signboom.com, carl@signboom.com, kim@signboom.com, sandra@signboom.com';

// If an offcut has just been claimed, email the production staff so they know.
if (($old_claimed == 0) && ($new_claimed == 1))
{
  // Format the email.
  $the_email_title = "Offcut $the_offcut_id ($material) has just been claimed by $the_username";
  $the_email_message = "This offcut has just been claimed.\r\n\r\nOffcut ID: $the_offcut_id\r\nMaterial: $material\r\nDimensions: $width x $length\r\nQuantity: $quantity\r\nClaimed by: $the_username\r\nName: $name\r\nEmail: $email_address\r\nTime: $date_time\r\n\r\n";

  // Send the email
  mail($email_addresses, $the_email_title, $the_email_message, $headers);
}

// If the claim on an offcut has just been released, email the production staff so they know.
if (($old_claimed == 1) && ($new_claimed == 0))
{
  // Format the email.
  $the_email_title = "Offcut $the_offcut_id ($material) has just been released by $the_username";
  $the_email_message = "This offcut has just been released back into the inventory of offcuts.\r\n\r\nOffcut ID: $the_offcut_id\r\nMaterial: $material\r\nDimensions: $width x $length\r\nQuantity: $quantity\r\nTime: $date_time\r\n\r\n";

  // Send the email
  mail($email_addresses, $the_email_title, $the_email_message, $headers);
}

// If an offcut has just been used, email Len.
if (($old_used == 0) && ($new_used == 1))
{
  // Format the email.
  $the_email_title = "Offcut $the_offcut_id ($material) has just been used by $the_username";
  $the_email_message = "This offcut has just been used.\r\n\r\nOffcut ID: $the_offcut_id\r\nMaterial: $material\r\nDimensions: $width x $length\r\nQuantity: $quantity\r\nUsed by: $the_username\r\nName: $name\r\nEmail: $email_address\r\nTime: $date_time\r\n\r\n";

  // Send the email
  mail($email_addresses, $the_email_title, $the_email_message, $headers);
}

// Refresh the page.
echo '<script type="text/javascript">';
echo 'window.location.href="http://signboom.com/production/offcuts.php";';
echo '</script>';
?>
