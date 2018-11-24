<?php
	$password = "Zcpl@123";
	$encryptedPassword = crypt($password, "urban11oasis22media33");
	
	echo " " . $password . " : " . $encryptedPassword;
?>