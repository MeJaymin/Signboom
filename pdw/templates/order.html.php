<!doctype html>

<html>

  <head>
    <meta charset="utf-8">
    <title>Signboom: PWD Order System</title>
    <?php include ('head.html'); ?>
    <script src="js/ted.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="styles.css" />

    <script language="JavaScript" type="text/JavaScript">
    <?php echo ('var global_account_name = "' . $my_account_name . '";'); ?>
    </script>
 </head>

<body>

  <div id="page">

    <?php include ('banner-menu.html'); ?>

    <div id="content">
      <div id="instructions_on" style="display: none;">
        <div id="cheat_sheet">
          <div id="heading">
            Files Must be Named in this Format
          </div>
          <div id="summary">
            <br>
            <div style="font-size: 11pt; text-align: center;">conference_dimensions_quantity_sides_department_product_finishing_reference.pdf</div>
            <br>
            where<br>
            <table borders="0" cellpadding="5">
              <tr>
                <td>conference</td>
                <td>your choice: e.g. TS17</td>
              </tr>
              <tr>
                <td>dimensions</td>
                <td>2 numbers (widthxheight) or one number (diameter for lollipops). In inches. (e.g. 8.5x11 or 18)</td>
              </tr>
              <tr>
                <td>quantity</td>
                <td>Quantity of this item to print, with Q in front. (e.g. Q3 or Q20)</td>
              </tr>
              <tr>
                <td>sides</td>
                <td>S1: Single sided.<br>S2S: Double-sided with <b>same</b> image on both sides.<br>S2D: Double-sided with <b>different</b> image on both sides.<br>Include both images in the same file for double-sided.</td>
              </tr>
              <tr>
                <td>department</td>
                <td>One of your internal department codes: FB, AS, VS, LOG, ES, CS or AV.</td>
              </tr>
              <tr>
                <td>product</td>
                <td>One of your internal product codes: FS, TT, LOL, SB or TD. Please note that our order system will print the product on the media whose drop box you drop the item into. Our system does not pay any attention to your internal product code.</td>
              </tr>
              <tr>
                <td>finishing</td>
                <td>STD: default finishing option for that media<br>HG: hem and grommets<br>CNC: contour cut, cut to shape</td>
              </tr>
              <tr>
                <td>reference</td>
                <td>A reference name of your choice. Must NOT include underscores, %, &amp;, <, >, single quotes, double quotes, periods or commas. Dashes are ok.</td>
              </tr>
            </table>
            <div style="margin-top: 15px; text-align: center;">
              examples:<br>
	      ts17_10.00x17.00_q1_s1_dept_acp03_std_01.pdf<br>
	      ts17_17.00x24.00_q1_s2s_dept_sin03_cnc_11.pdf<br><br>
            </div>
          </div>
        </div>

        Drag files from your computer to the desired product 
        boxes. The name of each file must include information we
        need to prepare the file. (See the box to the right.)
        If a file is named incorrectly, an error message 
        will warn you that all the files you just dropped have 
	been rejected.
        <br><br>
        A list of the files you have dragged will 
        be built automatically at the bottom of the page with 
        the price.  All files will be given a due date of
	April 21, 2018.
        <br><br>
        If you wish to discard all the files that have accummulated in the
        waiting area, without uploading them, just refresh the page and 
        confirm that you want to leave the page when the popup asks you.
        <br><br>
        Press the <b>Upload Files</b> button at the bottom of the page when you are 
        ready to submit the items for printing. 
        <br><br>
        On the <a href="dashboard.php"l target="_blank">PDW Dashboard</a> you can review the status of 
        all your ordered items (today's and earlier), and see where 
        each item is in our print queue.
        <br><br>
        <a href="#" onClick="hideInstructions();">Hide Instructions</a><br>
        <br style="clear: both;">
        <hr>
      </div>

    <div id="order_area">

      <div id="instructions_off" style="float: right;">
        <a href="#" onClick="showInstructions();">Show Instructions</a><br>
      </div>
      <h1>Your Products</h1>

      <form id="upload" action="index.php" method="POST" enctype="multipart/form-data">

        <!--<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="300000" />-->

        <?php
        // Query the user database for the products we are offering to client TED2015.
        if ($num_rows == 0) 
        {
          echo "There are currently no products associated with your account.<br>";
        }
        else 
        {
          // Go back to start of results.
          mysql_data_seek($result, 0);
          //$my_class = 'first';
          $previous_product = '';
          while ($my_row = mysql_fetch_array($result)) 
          {
            if ($my_row['ProductCode'] != $previous_product)
            {
              switch($my_row['Category'])
              {
                case 'Rigid Media':
                  echo '<div class="drop_box rigid">';
                  break;
                case 'Flexible Media':
                  echo '<div class="drop_box flexible">';
                  break;
                case 'Adhesive Media':
                  echo '<div class="drop_box adhesive">';
                  break;
                case 'Paper Media':
                  echo '<div class="drop_box paper">';
                  break;
                default:
                  echo '<div class="drop_box other">';
                  break;
                }
               echo '<div class="heading">' . $my_row['ShortName'] . '</div>'; 
               $product_code = $my_row['ProductCode'];
          ?>
              <div id="filedrag_<?php echo $product_code; ?>">Drop Files Here</div>
            </div>
            <?php 
              $previous_product = $product_code;
            }
            $my_class = '';
          } 
        }
        ?>
      </form>
  
      <br style="clear: both;">
      <br>
      <hr>

      <h1>Files Awaiting Submission</h1>

      <?php
      if (0) 
      //if ($num_rows > 0) // if we want to separate out list by products
      {
        // Go back to start of results.
        mysql_data_seek($result, 0);
        while ($my_row = mysql_fetch_array($result)) 
        {
        ?>
          <div id="list_<?php echo $product_code; ?>" class="list"><p></p></div>
        <?php
        }
      }
      else // if we want all products listed together
      {
        //echo '<h2>Vancouver</h2>';
        echo '<table id="submission_list_vancouver" border="1">';
        echo '<tr class="heading"><td>Product</td><td>Width</td><td>Height</td><td style="min-width: 6em;">Due Date</td><td>Qty</td><td>Sides</td><td>Finishing</td><td>Hardware</td><td>Reference</td><td>Cost</td><td>Rush/Hot</td></tr>';
        echo '</table><br>';
	echo '<div id="ted2018" style="display: none;">';
        echo '<h2>Whistler</h2>';
        echo '<table id="submission_list_whistler" border="1">';
        echo '<tr class="heading"><td>Product</td><td>Width</td><td>Height</td><td style="min-width: 6em;">Due Date</td><td>Qty</td><td>Sides</td><td>Finishing</td><td>Hardware</td><td>Reference</td><td>Cost</td><td>Rush/Hot</td></tr>';
        echo '</table><br><br>';
	echo '</div>';
      }
      ?>

      <div id="submitbutton">
       <input type="button" value="Upload Files" onclick="global_need_to_confirm = false; upload(); return false;" />
      </div>

      <div class="divlabel">Subtotal:</div> <div id="subtotal"></div>
      <div class="divlabel" style="clear: both;">Rush/Hot:</div> <div id="rush_hot"></div>
      <div class="divlabel" style="clear: both;">GST:</div>      <div id="gst"></div>
      <div class="divlabel" style="clear: both;">Total:</div>    <div id="total"></div>

      <div id="uploading_in_progress">
        <h1>File Upload in Progress...</h1>
        <h2>Do not leave this page until this message disappears.</h2>
      </div>

    </div>
  
  </div>

  <div id="browser_window" style="display: none;">
  </div>  <!-- end browser window -->

  <!-- filedrag.js has to be at bottom of file, so elements are created before Init() is invoked -->
  <script src="js/filedrag-180325b.js"></script>

  <!-- jQuery version might not be the latest; check jquery.com -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
  <script>!window.jQuery && document.write(unescape('%3Cscript src="js/jquery-1.5.2.min.js"%3E%3C/script%3E'))</script>
  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


