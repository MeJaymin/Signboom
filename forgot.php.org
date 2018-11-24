<?
  require_once( "includes/inc-mysql.php" );
  require_once( "includes/inc-signboom.php" );
  require_once( "includes/utils.php" );
  require_once( "includes/dohash.php" );


////////////////////////////////////
// DEBUG FLAG
////////////////////////////////////
$debug=0;

// no database connction - bail with error message
    if( 0 != ConnectDB( GLOBAL_DB_NAME ) ){
      print "DB connection failed. Please contact Signboom.";
      exit;
    }
	else
	{
		//echo "DB Connected"; die;
	}

////////////////////////////////////
// BEGIN FUNCTIONS
////////////////////////////////////

function makeRandomPassword() {
  $salt = "abchefghjkmnpqrstuvwxyz0123456789";
  srand((double)microtime()*1000000);
      $i = 0;
      while ($i <= 5) {
            $num = rand() % 33;
            $tmp = substr($salt, $num, 1);
            $pass = $pass . $tmp;
            $i++;
      }
      return $pass;
}

////////////////////////////////////
// getAnswer()
////////////////////////////////////
  function getAnswer(){
    global $HTTP_POST_VARS;
    global $debug;
    global $global_sender;

    //$email=$HTTP_POST_VARS['email'];
	$email=$_POST['email'];
    //$USRhinta=strtolower($HTTP_POST_VARS['txtHinta']);
	$USRhinta=strtolower($_POST['txtHinta']);
	
    $myquery="SELECT * FROM signboom_user WHERE email='$email'";
    $result=mysql_query($myquery);

    while($myrow=mysql_fetch_array($result)){
      $hinta=$myrow['hinta'];
      $userName=$myrow['userName'];
      $userPass=$myrow['userPass'];
    }

//DEBUG
    if($debug){
      print "DB: " . $hinta . "<BR>";
      print "ENTERED: " . $USRhinta . "<BR>";
    }

    if($hinta==$USRhinta){

//RESET PASSWORD
      $newpass=makeRandomPassword();

  //*** *** ENCRYPT THE PASSWORD *** ***
      //$cmdline = "../cgi-bin/ctest.pl $newpass";
      //$encpass = exec( $cmdline );
	$encpass = crypt($newpass, "urban11oasis22media33");
  //*** *** ENCRYPT THE PASSWORD *** ***

      $myquery="UPDATE signboom_user SET userPass='$encpass' WHERE email='$email'";

      $result=mysql_query($myquery);

//////////////////////////
//MAIL USER
//////////////////////////
      $msg ="Your password has been reset\n";
      $msg.="===============\n";
      $msg.="Account Details\n";
      $msg.="===============\n";
      $msg.="PASS: " . $newpass . "\n";
	  
	  echo "New Password : " . $newpass;

      mail( $email, "Acct Details", $msg, "From: " . $global_sender . "@{$_SERVER['SERVER_NAME']}\r\n");
      return(0);
    }else{
      return(-1);
    }
  }

////////////////////////////////////
// getAcctDetails()
////////////////////////////////////
  function getAcctDetails(){
    global $HTTP_POST_VARS;
    global $ID;
    global $hintq;
    global $hint_question;
    global $errMsg;
    global $arrHintQ, $arrHintQ2;

    global $debug;
	
    $email=$_POST['email'];

//GET ACCOUNT
    $myQuery="SELECT * FROM signboom_user WHERE email='$email'";
    $result=mysql_query($myQuery);

    if(0==mysql_num_rows($result)){ //ERROR NOT FOUND
      $errMsg="No account was found for email address $email.";
//DEBUG
      if($debug){
        print "query: $myQuery<BR>";
      }
      return(-1);
    }

    while($myrow=mysql_fetch_array($result)){
      $ID=$myrow['ID'];
      $hintq=$myrow['hintq'];
    }
//DEBUG
    if($debug){
      print "ID: $ID<BR>";
      print "hintq: $hintq<BR>";
      print "hint question: $hint_question<BR>";
    }

    if($hintq==""){
      $hint_question ="No security question was found for email address $email.";
      $errMsg="No security question was found for email address $email.";
      return(-1);
    }else{
      // Look up full text for that hint question.
      //$hint_question = count($arrHintQ);
      for ($i = 0; $i < count($arrHintQ2); $i++) {
        //$hint_question .= "comparing '" . $arrHintQ2[$i] . "' with '" . $hintq . "'<br>"; 
		//echo $arrHintQ2[$i];
        if (strcmp($arrHintQ2[$i], $hintq) == 0) {
          $hint_question = $arrHintQ[$i];
          break;
          }
      }
      return(0);
    }

  }

////////////////////////////////////
// END FUNCTIONS
////////////////////////////////////


////////////////////////////////////
// INPUT VALIDATION
////////////////////////////////////
  //if(isset($btnNext)){
	if(isset($_POST['btnNext'])){
	$state = $_POST['state'];
    switch($state){
      case 0:
		//$res = checkEmail($_POST['email']);
		//echo "res : " . $res; die;
        if($result=checkEmail($_POST['email'])){
          switch($result){
            case 1:
              $errMsg="Please enter your email address.";
              break;
            case 2:
              $errMsg="That address is not valid.  Please enter a valid email address.";
              break;
          }
        }else{
			//echo "Inside else"; die;
          if(getAcctDetails()==0){
            $state=1;
          }
        }
        break;

      case 1:
        $errMsg="checking hintq...";
        if(getAnswer()==0){
          $errMsg="Your account details have been mailed to $email";
//CLOSE THE POPUP
          $exitPopup=1;
        }else{
          $errMsg="That was not the correct answer for the security question.";
        }
        break;
    }

    if($debug){
      print "<pre>";
      var_dump($HTTP_POST_VARS);
      print "</pre>";
    }
  }else{
    $state=0;
  }

////////////////////////////////////
// PRESENTATION
////////////////////////////////////
  include("inc-forgot.php");

////////////////////////////////////
// ERROR POPUP
////////////////////////////////////
  if(!empty($errMsg)){
    echo "<script language=\"javascript\">alert(\"" . $errMsg . "\");</script>";
  }

////////////////////////////////////
// CLOSE POPUP
////////////////////////////////////
if($exitPopup==1){
      print "<script language=\"javascript\">window.close();</script>";
}
