<html>
  <head>
    <title>Signboom Account Password Reset</title>
  </head>

  <body>
    <div style="text-align: center;">
    <img src="images/logo3d.jpg" width="308" height="54">
    <br><br><br>
    <!-- <form action=<?//=$PHP_SELF;?> method="POST"> -->
	<form action=<?php echo $_SERVER['PHP_SELF'] ;?> method="POST">
      <? if($state==0)
      {?>
        <!--ENTER EMAIL ADDRESS-->
          Your Email Address: 
          <!--
		  <input type="text" name="email" size="28" maxlength="64" value="<?//=$email;?>">
		  -->
		  <input type="text" name="email" size="28" maxlength="64" value="<?php echo $email;?>">
          <br><br>
          <input type="button" value="Cancel" onClick="window.close()">
          <input type="submit" name="btnNext" value="Next >">
      <?}
      elseif($state==1)
      {
		$email = $_POST['email'];
		  ?>
        <!--ENTER ANSWER-->
          Please answer your security question...<br><br>
          <? echo $hint_question; ?>
          <br>
          <input type="text" name="txtHinta" size="20" maxlength="32">
          <br><br>
          <input type="button" value="Cancel" onClick="window.close()">
          <input type="submit" name="btnNext" value="Next >">
          <!-- <input type="hidden" name="email" value="<?//=$email;?>"> -->
		  <input type="hidden" name="email" value="<?php echo $email;?>">
      <?}?>
    <input type="hidden" name="state" value="<?=$state;?>">
    <input type="hidden" name="hintq" value="<?=$hintq;?>">
    </form>
    </div>

  </body>
</html>
