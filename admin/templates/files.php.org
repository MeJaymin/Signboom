<!doctype html>

<html>

  <head>
    <meta charset="utf-8">
    <title>Signboom: Version 1.5 Administration</title>
    <?php include ('head.html'); ?>
    <script src="js/validation-functions-161019f.js" LANGUAGE="JavaScript" TYPE="text/javascript"></script>
  </head>

<body>

  <div id="page">

    <?php include ('templates/banner-menu.php'); ?>

    <div id="content">
      <h1>Upload Files</h1>
      <div style="width: 600px; margin: 20px auto;">
      <?php if ($message) echo '<p class="highlighted">'. $message . '</p>'; ?>
      <p>This is the place to upload any files that you want to link to in the product or finishing
      option pages. Once you've uploaded the file here, visit the <b>Products</b> or <b>Finishin</b>g page 
      and create a link to that file in the information about that item.</p>

      <form id="file_upload" action="files.php" enctype="multipart/form-data" method="post">

          <!-- Put limit of 20MB total on data uploaded by form. -->
          <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
          <input type="file" id="file_to_upload" name="file_to_upload"> 
	  <input type="submit" name="upload_now" value="Upload File Now" onClick="return allowOnlyJPGorPDF();">

      </form>
      </div>
    </div>
  
  </div>

  <?php include ('js/modernizr-snippet.js'); ?>

</body>

</html>


