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

    <img src="images/title_colour_accuracy.gif" width="306" height="37" 
      alt="COLOUR ACCURACY: WHAT TO EXPECT FROM CMYK PRINTING">

    <p>At Signboom, we produce in-house colour ICC profiles for all of our different printer and 
    media combinations, for optimum colour accuracy. However, by nature CMYK digital printing is 
    incapable of matching all desired colours due to its reduced colour gamut as compared to RGB.</p>

    <p>If colour accuracy is critical and the order size is large, it is best to order a small 
    version of your print first for verification, as Signboom does not guarantee colour accurate prints.</p>

    <p><b>Notes on Colour Accuracy</b></p>

    <ul>
    <li>Colour shifts between different prints and medias may occur, especially if they are printed 
    at different times.</li>
    <li>Vector art such as text and logos will print differently when rasterized in your file or 
    exported as a tiff or bitmap, as it will be processed as an image rather than vector art.</li>
    <li>Your colour accuracy will always be better if the document set up in your application is set 
    to CMYK mode rather than RGB. (Images can remain in RGB if you wish.)</li>
    <li>Pantone spot colours will print more accurately and more consistently if they are left as a 
    spot colour in your file rather than converting it to CMYK. <i>However, all specialty Pantone colours 
    including metallics and fluorescents cannot be matched using standard CYMK ink sets and should 
    NEVER be used in files sent to Signboom as the results can be very unpredictable.</i></li>
    <li>Naming of spot colours is important so that they can be recognized by our Pantone spot colour 
    matching system. They must match the exact Pantone Convention in name and case, as per these 
    examples:  PANTONE 100 C, PANTONE Rubine Red C . The characters "CV", "CVC", "U", "UV", and "UVC" 
    on the end are treated the same as "C".  "PMS 100" and "PMS 100 C" would be valid titles for your 
    swatches. "PMS 100 C (1)" would not be valid.</li>
    <li>A lower percentage of a Pantone spot colour is treated as a CMYK colour in our Pantone spot 
    colour matching system, so it is best to select a different Pantone spot colour that is lighter, 
    but in the same tone.</li>
    <li>Any Pantone spot colours that interact with a transparency in your file will be disregarded 
    by our Pantone spot colour matching system. </li>
    </ul>

    <p><b>Summary</b></p>

    <p>While we print in CMYK, which has some limitations, we do have a very good colour gamut.  Some 
    RGB images may print better as RGB, but unexpected results may sometimes occur.  Working in the 
    CMYK colour mode provides the most consistent colours.</p>

    <p>Our RIP software will match spot colours even better if you name them using correctly formatted 
    Pantone standard names pulled directly from your application's Pantone approved palettes.  Pantone 
    colours will not necessarily provide perfect matches, but they will give the best possible results. 
    Avoid using percentages of spot colours, spot colours in gradients, or having transparencies 
    interacting with spot colour filled vector images.  In these case try using CMYK colours instead.  
    This is because the RIP software with treat the interacting elements as CMYK and the balance as spot 
    creating colour differences.</p>
    <br>

    </div>

    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>
