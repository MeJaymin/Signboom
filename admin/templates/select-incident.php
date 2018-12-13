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

      <h1>Incident Management</h1>

      <div id="message" style="text-align: center; color: #cc0000; font-weight: bold;">
      <?php
        if ($error_message) echo '<p>' . $error_message . '</p>'; 
      ?>
      </div>

      <div style="float: left; width: 30%;">
	<?php
        foreach ($incident_list as $incident)
	{
	  echo '<a href="edit-incident.php?order_id=' . $incident . '">' . $incident . '</a><br>';
	}
	?>
      </div>
        
      <div style="float: left; width: 60%; margin-left: 10%;">
        <p>You can bring up an incident record by entering either the Incident ID or the Order ID.
        You don't need to enter both.</p>

        <form id="incidents_form" name="incidents_form" method="post" action="edit-incident.php">
        <ul class="vertical">
          <li>
            <label for="incident_id">Incident ID:</label>
            <input type="text" name="incident_id" id="incident_id" value="<?php if(isset($incident_id)) echo $incident_id; ?>">
          </li>
          <li>
            <label for="order_id">Order ID:</label>
            <input type="text" name="order_id" id="order_id" value="<?php if(isset($order_id)) echo $order_id; ?>">
          </li>
          <li>
            <input style="float: right;" type="submit" name="submit_incident" 
               value="Bring up Incident Record">
          </li>
        </ul>
      </div>

    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


