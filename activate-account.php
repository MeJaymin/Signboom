<?php

// We use a matched set of confirmation code and account name to prevent this page from submitting information from spurious users.

require_once( "includes/inc-mysql.php" );
require_once( "includes/inc-signboom.php" );

//Default values. 
$page_title = "Activate Your New Account";
$template = "includes/inc-activate-account.php";

if (isset($_GET['account_name']))
{
  $account_name = $_GET['account_name'];
}
else
{
  $error = 'The account name is missing from the URL. This error can happen if your email program has split up the parts of the link you just clicked. Please try copying and pasting the whole link into your browser in order to activate your account.';
  include $template;
  exit();
}
if (isset($_GET['confirmation_code']))
{
  $confirmation_code = $_GET['confirmation_code'];
}
else
{
  $error = 'The confirmation code is missing from the URL. This error can happen if your email program has split up the parts of the link you just clicked. Please try copying and pasting the whole link into your browser in order to activate your account.';
  include $template;
  exit();
}
if (strlen(trim($confirmation_code)) != 32)
{
  $error = 'The confirmation code in the URL is not the right length. This error can happen if your email program has split up the parts of the link you just clicked. Please try copying and pasting the whole link into your browser in order to activate your account.';
  include $template;
  exit();
}

// Connect to the database.
if (ConnectDB( "signboom_v1p5" ) != 0) //GLOBAL_DB_NAME
{
  $error = 'The connection to the database failed. Please try clicking that link again in an hour. If you get that error again, please call us at (604) 881-0363 to get assistance with activating your account.';
  exit();
}

// See if the account has already been activated.
$sql = "SELECT acctDisable FROM signboom_user WHERE AcctName = '$account_name'";
$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if ($result === false)
{
  $error = 'That account is not in the database. Please call us at (604) 881-0363 if you are having difficulties setting up an account.';
  include $template;
  exit();
}
else
{
  $row = mysqli_fetch_array($result);
  $account_disable = $row['acctDisable'];
  if (!$account_disable)
  {
    $error = 'That account has already been activated in the past. You can now place orders. Please call us at (604) 881-0363 if you are having difficulties placing orders.';
    include $template;
    exit();
  }
}

// See if that combination of account name and confirmation code is in database.
$sql = "SELECT ID, acctDisable FROM signboom_user WHERE (AcctName = '$account_name') AND (confirmationCode = '$confirmation_code')";
$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if ($result === false)
{
  $error = 'It looks like you have already activated your account in the past. Please call us at (604) 881-0363 if you are having difficulties accessing your account or placing orders.';
  include $template;
  exit();
}
else
{
  // Activate their account.
  $row = mysqli_fetch_array($result);
  $account_id = $row['ID'];
  $sql_update = "UPDATE signboom_user SET acctDisable = '0', confirmationCode = '' WHERE ID = '$account_id'";
  $result_update = mysqli_query($GLOBALS["___mysqli_ston"], $sql_update);
  if ($result_update === FALSE)
  {
    $error = "Error #112 in appointment confirmation. This query failed: $sql_update. Please send this error message to the RealtyNode team.";
    include 'templates/error.html.php';
    exit();
  }
	$protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
	$host = $_SERVER['SERVER_NAME'];
	$error = 'Thank you for activating your account! You can now <a href="' . $protocol . '://' . $host .  '/Signboom/login.php">log in</a> and start placing orders.';
	//$error = 'Thank you for activating your account! You can now <a href="http://signboom.com/login.php">log in</a> and start placing orders.';
  include $template;
}

?>
