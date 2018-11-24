<!doctype html>

<html>

  <head>
    <meta charset="utf-8">
    <title>Signboom: Version 1.5 Administration</title>
    <?php include ('head.html'); ?>
  </head>

<body>

  <div id="page">

    <?php include ('banner-menu.php'); ?>

    <div id="content">

     <h1>Resend Confirmation Email:</h1>

      <div style="width: 840px; margin: 20px auto;">

        <?php
        if ($error_message) 
	  echo '<p class="highlighted">' . $error_message . '</p>'; 
        else
	  echo '<p class="highlighted">' . $message . '</p>'; 
        ?>

        <p> Enter the the order ID of the order whose confirmation email is to be sent and the 
	email address you want the confirmation email to go to (your own, a fellow staff member's
	or the customer's).</p>

        <p>The recipient will receive an email with a "from" address, which is the customer's. If you 
	asked for the email to be sent to yourself or another staff member, it can be forwarded to the customer 
	by just replying to the resent confirmation email.</p>

        <form id="resend_form" name="resend_form" method="post" action="resend-confirmation-email.php">

          <ul class="vertical">

            <li>
              <label for="order_id">Order ID:</label>
              <input type="text" name="order_id" value="<?php echo $order_id; ?>">
            </li>

            <li>
              <label for="email_address">Email Address:</label>
              <input type="text" name="email_address" value="<?php echo $email_address; ?>">
            </li>

            <li> 
	      <input style="float: right;" type="submit" name="submit_resend_email" value="Resend Confirmation Email">';
            </li>

          </ul>
        </form>

      <br style="clear: both;">
      </div>
    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


