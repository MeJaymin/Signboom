<!doctype html>

<html>

  <head>
    <meta charset="utf-8">
    <title>Signboom: Version 1.5 Administration</title>
    <?php include ('head.html'); ?>

    <script language="javascript" type="text/javascript" src="../../tiny_mce/tiny_mce.js"></script>
    <script language="javascript" type="text/javascript">
    tinyMCE.init({
     mode : "textareas", 
     theme : "advanced",
     theme_advanced_buttons1 : "bold,italic,separator,justifyleft,justifycenter,justifyright,separator,bullist,numlist,separator,hr,outdent,indent,separator,undo,redo,separator,link,unlink,image,separator,removeformat,cleanup,code,help",
     theme_advanced_buttons2 : "",
     theme_advanced_buttons3 : "",
     document_base_url : "http://www.signboom.com/",
     content_css : "css/admin.css",
     relative_urls : false,
     remove_script_host : false
     });
    </script>
  </head>

<body>

  <div id="page">

    <?php include ('banner-menu.php'); ?>

    <div id="content">

      <h1>Send Email Announcement:</h1>

      <div style="width: 840px; margin: 20px auto;">

        <?php echo $message_to_user; ?>

        <?php if ($display_input_form): ?>

        <form action="<?php echo $PHP_SELF; ?>" method="post">
          Email Title: <input name="email_title" type="text" size="32" maxlength="64" value="<?php echo $email_title ?>" >
          <br><br>
          CC Email Addresses: <input name="email_addresses" type="text" size="70" maxlength="255" value="<?php echo $email_addresses ?>" >
          <br>
          <span style="color: #999999">Provide a comma-separated list of email addresses.  You must put at least one address here.</span>
          <br><br>
          Email Message:<br>
          <textarea name="email_message" cols="80" rows="17" value="<?php echo $email_message ?>" ></textarea>
          <input type="hidden" name="tried" value="yes">
          <br>
          <input type="submit" name="clients" value="Send to All Clients" onClick="alert('After you click the Ok button below...\n\nPLEASE WAIT\n\nuntil the page redisplays, with a list of email addresses and a message confirming you can\n\nleave the page. This may take several minutes since you are sending to the whole mailing list.')">
          <input type="submit" name="cc" value="Send to CC List Only" onClick="alert('After you click the Ok button below...\n\nPLEASE WAIT\n\nuntil the page redisplays, with a list of email addresses and a message confirming you can\n\nleave the page.')">
          <!--
          <br><br>
          Special Cases:
          <br>
          <input type="submit" name="invalidpst" value="Send to Clients with Invalid PST #">
          <input type="submit" name="invalidsecurity" value="Send to Clients with Invalid Security Question">
          -->
        </form>
      <?php endif; ?>

      <br style="clear: both;">
      </div>
    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>
