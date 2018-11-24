<!doctype html>

<html>

  <head>
    <meta charset="utf-8">
    <title>Signboom: Version 1.5 Administration</title>
    <?php include ('head.html'); ?>
    <script language="JavaScript" type="text/JavaScript">
    var popUpWin=0;
    function popUpWindow(URLStr, left, top, width, height)
    {
      if(popUpWin)
      {
        if(!popUpWin.closed) popUpWin.close();
      }
      popUpWin = open(URLStr, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbar=no,resizable=no,copyhistory=yes,width='+width+',height='+height+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
    }
    </script>
  </head>

<body>

  <div id="page">

    <?php include ('banner-no-menu.html'); ?>

    <div id="content">
      <h1>Login</h1>
      <div style="width: 500px; margin: 20px auto;">
        <h2>The email Address is ted2018@signboom.com.<br>The password is the same as last year.</h2>
	<p>You must use either Firefox or Internet Explorer 11 to place your orders here.</p>
	<img src="images/firefox-logo.png" width=134" height="136" alt="" style="float: left; margin-left: 100px;">
	<img src="images/ie9-logo.png" width=128" height="128" alt="" style="float: left; margin-left: 50px;">
	<p style="clear: both;">This order system DOES NOT work on Chrome.</p>
        <form name="form1" method="post" action="">
          <table width="500" border="0" cellpadding="1" cellspacing="1">
            <tr>
              <td width="120">Email Address: </td>
              <td width="300"><input style="width: 280px;" name="email" type="text" id="email"></td>
            </tr>
            <tr>
              <td >Password: </td>
              <td><input style="width: 280px;" name="pw" type="password" id="pw"></td>
            </tr>
            <tr>
              <td >&nbsp;</td>
              <td><input name="Submit" type="submit" value="Submit"></td>
            </tr>
          </table>
        </form>
      </div>
      <div style="text-align: center;">
         <? if ($invalidauth) { loginmsg(); } ?>
         <p>If your browser does not allow cookies, you will not be able to log in.</p>
      </div>
    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


