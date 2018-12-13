<?php

//--------------------------------------------------------------------------
function ConnectDB( $DB_Name ){
  global $DB_Handle;

  //$DB_Handle = mysql_connect( "signboom.accountsupportmysql.com", "signboom_admin", "andover6" );
  //$DB_Handle = mysql_connect( "localhost", "root", "zcon@123" );
  /*$DB_Handle = mysql_connect( "signboom.cp0oeob0fwkt.us-west-2.rds.amazonaws.com", "sbadmin", "sb74-9AlG64.a" );*/
  $DB_Handle = ($GLOBALS["___mysqli_ston"] = mysqli_connect( "localhost",  "root",  "root" )); 

  if( FALSE == $DB_Handle )
    return( -1 );

  if(!mysqli_select_db( $DB_Handle , $DB_Name)) 
    return( -2 );

  return( 0 );
}
//--------------------------------------------------------------------------
function Bail( $TextMsg ){
  exit( "<html><body><pre>$TextMsg</pre></body></html>" );
}
//--------------------------------------------------------------------------

?>
