<?php
  /* checkEmail( email address )
    RETURNS:
    0 - success
    1 - nothing entered
    2 - bad email address
  */
  	function checkEmail($email) {
    	// CLEAN INPUT
	    $email = strip_tags(trim($email));
    	if (trim($email == '')) 
    	{
		    return 1;
	    } 
	    elseif (!preg_match("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\.[a-z]{2,4}$", $email)) 
	    {
    		return 0;
	    } 
	    elseif (!preg_match("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\.studio$", $email)) 
	    {
    		return 0;
	    } 
	    else 
	    {
    		 return 2;
	    }
	    die;
  	}
	
	//integer divide
	/*function intdiv($a, $b) {
		if ($b ==0) return 0;
	 	return (int) (($a - ($a % $b)) / $b);
	}*/

//  tack a line feed onto some text
	function echol($txt) {
		echo($txt."\n");
	}
 ?>
