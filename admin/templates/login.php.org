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

    <?php include ('banner-menu.php'); ?>

    <div id="content">
      <br><br>
      <div style="width: 350px; margin: 20px auto;">
        <form name="form1" method="post" action="">
          <table width="350" border="0" cellpadding="1" cellspacing="1">
            <tr>
              <td width="120">Email Address: </td>
              <td width="230"><input name="email" type="text" id="email"></td>
            </tr>
            <tr>
              <td >Password: </td>
              <td><input name="pw" type="password" id="pw"></td>
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


