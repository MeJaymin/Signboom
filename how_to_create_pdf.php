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

    <img src="images/title_file_format.gif" width="393" height="37" 
      alt="FILE FORMAT: HOW TO CREATE SUITABLE PDF FILES FOR PRINTING">

    <p>To offer you consistent quality, low prices, and fast turnaround, we have adopted a PDF 
    workflow.  All files uploaded through our web site must be PDF.  This ensures your job gets 
    printed exactly how you want it, and when you expect it.</p>

    <p>Our PDF workflow enables us to prepare most orders under 25 lineal feet (approx 100 square 
    feet) by the end of the next business day.</p>

    <p><b>What is PDF?</b></p>

    <p>PDF stands for Portable Document Format.  It was created by Adobe, and is fast becoming 
    the standard for all document transfers, not just web documents.  Formatted properly, PDFs 
    can provide smaller file sizes, eliminate font issues, cross platforms, include formatting 
    features, and carry forward images. They provide as much resolution to the printer as required, 
    provided the source document is of a high enough resolution to begin with.</p>

    <p><b>What do I need to Know about Preparing my PDF?</b></p>

    <ul>
    <li><b>Choose your art board dimensions:</b>  When creating your art work, choose the art board 
    dimensions to match the dimensions you want the printed product to be, and include the desired 
    white space.  We cannot determine whether if the excess blank space is required in your sign 
    application, so we print the entire art board.  (A small image saved on a large art board is 
    a large print document.)</li>

    <li><b>Position your design on the art board:</b>  Ensure your design is properly placed on the art 
    board.  Any portion of any graphics lying outside the art board will not be printed.  However, 
    they will add to file size and complexity.    You may wish to use a photo editing package to 
    crop overly large images as opposed to masking.  This may significantly reduce file size for 
    some jobs, which results in a faster upload to our web site and faster printing.</li>

    <li><b>Work at full scale:</b>  Whenever possible, work at actual size.  If limitations in your 
    software do not allow this, then work at a 50% or 25% scale and we will automatically scale 
    your image to the proportional size that you enter on the order form.  Please read our 
    <a href="working_large.php">Working Large</a> page
    if you are going to work in scale.  (For us to enlarge the file, you must work at a higher 
    resolution.)</li>

    <li><b>Embed the fonts:</b>  When saving your file as a PDF, choose the option to embed the fonts.  
    This provides our RIP software with the necessary information to print your job, even if we 
    don't have the fonts you are working with on our computers.  Embedding the fonts in a PDF file 
    does not install the fonts on our system, so you must embed the fonts in every document you send us.</li>

    <li><b>Check the resolution of the images you use:</b>  Bitmap, jpg, tiff and other rasterized images have 
    a fixed resolution.  This means that the larger they are printed, the lower the quality of the final 
    output.  That does not mean you should not enlarge an image, but you must be careful not to breach 
    certain thresholds.  For the purposes of most signage, you want to ensure that images have a resolution 
    of 180 ppi once they are sized in your application to final print size.  This provides a good image 
    close up.  If you signage has a large viewing distance (such as 20+ feet as in billboards),  the 
    resolution can be as low as 45 ppi or even less.  When viewing at actual size on the monitor, you 
    can be the judge.  Simply back away from the monitor.  You have to be aware of your client's 
    expectations as to viewing distance, and the point where the pixelation becomes acceptable.</li>

    <li><b>Embed the images:</b>  During development of your art work, you are free to work with either embedded 
    or linked images.  However, on the final copy being sent with your order,  you must "parse" or embed 
    the images.  Most software allows you to do this at the "Save as PDF" stage.</li>

    <li><b>Work in CMYK:</b>  To learn more about getting predictable colour results, visit our 
    <a href="colour_accuracy.php">Colour Accuracy: What to Expect from CMYK Printing</a> page.  
    While CMYK printing does have some limitations, 
    we have a very good colour gamut.  Some RGB images may print better as RGB, but unexpected results 
    may sometimes occur.  Working in the CMYK colour mode provides the most consistent colours.</li>

    <li><b>Use Pantone colours for most spot colours:</b>  Our RIP will match spot colours even better if you 
    name them using Pantone standard names pulled directly from your application's Pantone approved 
    palettes.  These standard colours do not necessarily provide perfect matches, but they provide the 
    best possible results.  Avoid using percentages of spot colours, spot colours in gradients, or having 
    transparencies interacting with spot colour filled vector images.  In these cases, try using CMYK 
    colours instead.  This is because the RIP software with treat the interacting elements as CMYK and 
    the balance as spot, creating colour differences.</li>

    <li><b>Include desired trim lines:</b>  If you would like trim lines, place the outlines where desired.</li>

    <li><b>Choose high quality compression.</b></li>

    <li><b>Choose to compress line art and text.</b></li>

    <li><b>Proof your art work in Adobe Reader:</b>  Proofing your files for resolution and 
    image details is straightforward.  After saving as PDF,  open the file in Adobe Acrobat Reader 
    (<a href="http://www.adobe.com/products/reader/" target="_blank">available free from Adobe</a>).  
    Do not use your application as the final check; use Reader.  If you have designed 
    at full size, then set your zoom to 100%.  Scroll around and proof for resolution and fine detail.  
    Reduce your view to check larger items, and "view fit to page" to ensure you have left adequate white 
    space for any mounting, cutting, and aligning methods that you may use.  Remember to hold white space 
    or use a full bleed image for hemming banners.  If you don't like what you see, don't submit it for printing.  
    You are welcome to contact us to discuss any issues you may be having.</li>
    </ul>

    <p>PDFs can be created in numerous ways.  Most graphics applications allow you to save directly as PDFs.  
    If your application does not, there are third party plug in applications and print drivers that will 
    allow you to convert files.</p>
    <br>

    </div>

    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>
