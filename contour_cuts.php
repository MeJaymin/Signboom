<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <title>Signboom: Your Online Print Engine</title>

  <!--INTERNET EXPLORER SPECIFIC STYLING CODE FOLLOWS-->
  <?php
    include ('browser_detection.php');
    $my_browser = browser_detection('browser');
    if ($my_browser == 'msie6') 
    {
      echo '<link rel="stylesheet" href="ie6_specific.css" type="text/css" title="default_style">';
    }
    else
    {
      echo '<link rel="stylesheet" href="signboom.css" type="text/css" title="default_style">';
    }
  ?>

</head>

<body>

<div id="page">
  <div id="wrapper">

    <?php
      include ('header.php');
    ?>

    <div id="content">
    <?php
      include ('sidebar.html');
    ?>

    <img src="images/title_contour_cuts.gif" width="180" height="18" alt="CONTOUR CUTS">

    <p>Our machines can contour cut your flexible/adhesive order for you.  In order for our 
    RIP software to properly identify cut instructions, prepare your PDF using the instructions below.</p>

    <ol>
    <li>Prepare your file as explained on our <a href="how_to_create_pdf.php">How to Create PDF Files</a> page.</li>
    <li>Create a safety zone and bleed of at least 1/8" each.</li>
    <li>Create a new colour swatch with the properties of "Spot Colour" and a name "CUT" (this must be 
    exactly as shown).  The actual colour does not matter as it does not print.  For your clarity though, 
    we recommend using a neon green or similar colour.   The RIP software removes this item and uses it to 
    create the cut file.</li>
    <li>Create a new object that defines where you want the knife to cut.  Give it an outline colour of 
    "CUT" as created above and no fill.  The outline thickness does not matter.  We usually use a 1pt 
    thickness.</li>
    <li>Remember,  your document size defines the printed size.  Size it according to how much space you 
    may want around your decals for trimming and weeding, not the size of your decals.</li>
    </ol>

    <p>Here are screen shots of Illustrator 10 and Corel 10's create colour boxes:<p>
    <img src="images/new_swatch.gif" width="431" height="241"><br><br>
    <img src="images/palette_editor.gif" width="414" height="485">

    <p>A few examples are shown below.  To enlarge each example, just click the image. If you have any 
    questions please call us.</p>

    <a href="documents/radius_corner_white_decal.pdf" target="_blank">
      <img src="images/radius_corner_white_decal.gif" width="253" height="253" border="0">
    </a>
    <a href="documents/radius_corner_full_bleed.pdf" target="_blank">
      <img src="images/radius_corner_full_bleed.gif" width="253" height="253" border="0">
    </a>
    <a href="documents/random_white_decal.pdf" target="_blank">
      <img src="images/random_white_decal.gif" width="253" height="253" border="0">
    </a>
    <a href="documents/compound_white_decal.pdf" target="_blank">
      <img src="images/compound_white_decal.gif" width="253" height="253" border="0">
    </a>
    <a href="documents/trimmed_photo_decal.pdf" target="_blank">
      <img src="images/trimmed_photo_decal.gif" width="253" height="253" border="0">
    </a>
    </div>

    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>
