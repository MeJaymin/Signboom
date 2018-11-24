<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
//$hostname_DBConn = "signboom.accountsupportmysql.com";
//$hostname_DBConn = "localhost";
/*$hostname_DBConn = "signboom.cp0oeob0fwkt.us-west-2.rds.amazonaws.com";*/
$hostname_DBConn = "localhost";
//$database_DBConn = "signboom_v1p5";
//$database_DBConn = "signboom_v1p5";
$database_DBConn = "signboom_v1p5";

//$username_DBConn = "signboom_admin";
//$username_DBConn = "root";
/*$username_DBConn = "sbadmin";*/
$username_DBConn = "root";
//$password_DBConn = "andover6";
//$password_DBConn = "zcon@123";
/*$password_DBConn = "sb74-9AlG64.a";*/
$password_DBConn = "";

//$DBConn = mysql_connect($hostname_DBConn, $username_DBConn, $password_DBConn) or die(mysql_error()); 
$DBConn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($hostname_DBConn,  $username_DBConn,  $password_DBConn)) or die(mysqli_error($GLOBALS["___mysqli_ston"])); 
?>
