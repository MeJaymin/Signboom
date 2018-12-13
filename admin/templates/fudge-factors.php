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
      <h1>Fudge Factors</h1>
      <div style="width: 600px; margin: 20px auto;">
      <?php if (isset($updated)) echo '<p class="highlighted">The fudge factors have been updated.</p>'; ?>
      <?php if (isset($message)) echo '<p class="highlighted">'. $message . '</p>'; ?>
      <form id="fudge_factors" name="fudge_factors" method="post" action="fudge-factors.php">
        <b>Setup Fee:</b> <input type="text" name="setup_fee" value="<?php echo $setup_fee; ?>">
        <br><br>
        <b>G-Floor Setup Fee:</b> <input type="text" name="gfloor_setup_fee" value="<?php echo $gfloor_setup_fee; ?>">
        <br><br>
        <b>Packaging Fee:</b> <input type="text" name="packaging_fee" value="<?php echo $packaging_fee; ?>">
        <br><br>
        <b>Shipping Factor:</b> <input type="text" name="shipping_factor" value="<?php echo $shipping_factor; ?>">
        <br><br>
        <b>Cutoff Hour for Orders:</b> <input type="text" name="cutoff_time" value="<?php echo $cutoff_time; ?>">
        <br><br>
        <b>Waste Factor:</b> <input type="text" name="waste_factor" value="<?php echo $waste_factor; ?>">
        <br><br>
        <b>Ink Cost (per square foot):</b> <input type="text" name="ink_cost" value="<?php echo $ink_cost; ?>">
        <br><br>
        <b>Discount Calculation Factor A:</b> <input type="text" name="discount_factor_a" value="<?php echo $discount_factor_a; ?>">
        <br><br>
        <b>Discount Calculation Factor B:</b> <input type="text" name="discount_factor_b" value="<?php echo $discount_factor_b; ?>">
        <br><br>
        <b>Sq Ft we can print in first day:</b> <input type="text" name="sq_feet_time_a" value="<?php echo $sq_feet_time_a; ?>">
        <br><br>
        <b>Sq Ft we can print each additional day:</b> <input type="text" name="sq_feet_time_b" value="<?php echo $sq_feet_time_b; ?>">
        <br><br>
        <b>Flag as Expensive in Production:</b> <input type="text" name="expensive" value="<?php echo $expensive; ?>">
        <br><br>
        <input style="float: right;" type="submit" name="submit_parameters" value="Submit">
      </form>
      </div>
    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


