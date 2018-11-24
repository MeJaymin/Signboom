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

      <h1>Customer Management</h1>

      <div id="message" style="text-align: center; color: #cc0000; font-weight: bold;">
      <?php
        if ($error_message) echo '<p>' . $error_message . '</p>'; 
      ?>
      </div>

      <p>You can bring up a customer record by entering either the Account Id or their email
      address.  You don't need to enter both.  (Note that if there are more than one account
      associated with an email address, the admin system will bring up the first one it finds.)</p>

      <form id="customers_form" name="customers_form" method="post" action="edit-customer.php">
      <ul class="vertical">
        <li>
          <label for="account_name">Account Name:</label>
          <input type="text" name="account_name" value="<?php echo $account_name; ?>">
        </li>
        <li>
          <label for="email_address">Email Address:</label>
          <input type="text" name="email_address" value="<?php echo $email_address; ?>">
        </li>
        <li>
          <input style="float: right;" type="submit" name="submit_customer" 
             value="Bring up Customer Record">
        </li>
      </ul>

    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


