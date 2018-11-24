<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php error_reporting(0); ?>
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
      include ('header_green.php');
    ?>

    <div id="content">
    <?php
      include ('sidebar.html');
    ?>

    <img src="images/title_environmental.gif" width="384" height="18" alt="OUR ENVIRONMENTAL FOOTPRINT">

    <p>At Signboom Industries we strive to be environmentally responsible. Having 
    embraced UV curable print technology within our production facility, and recycling 
    practices where possible, we are minimizing our environmental footprint.</p>

    <p>We have converted most of our printing production over to UV inks. With our 
    remaining mild-solvent printers, we have converted them to bulk ink systems, 
    removing the need to dispose of empty cartridges.  All ink waste is stored and 
    disposed of in a manner that meets or exceeds all local, provincial and federal 
    environmental regulations.</p>

    <p>Since their introduction, UV inks have become a standard for environmental 
    printing practices. UV inks release no volatile organic compound emissions to 
    the air. With conventional inks, as much as 40 percent of the liquid can evaporate 
    in to the atmosphere.</p>

    <p>Some facts on our UV inks:</p>

    <ul>
    <li>Contain up to 33% naturally-derived monomers and oligomers derived from 
    renewable resources such as plants or biomass.</li>

    <li>Contain virtually no volatile organic compounds (VOC).</li>

    <li>Do not contain heavy metals such as antimony, arsenic, cadmium, chromium 
    (VI), lead, mercury, and selenium.</li>

    <li>Are not toxic, known to be carcinogenic, mutagenic or toxic to reproduction,
    and comply with the Exclusion List for Printing Inks and Related Products 
    (October 2006) issued by the European Printing Ink Association (EuPIA).</li>

    <li>Are not considered a hazardous waste under the US waste regulations (RCRA).</li>

    <li>The use of naturally-derived and organic substances in the ink increases 
    the overall biodegradability of the printed matter.</li>

    </ul>

    <p>UV printers and UV inks allow direct-to-substrate production, reducing the 
    waste produced. Traditionally, with roll printers, the print is printed on a 
    vinyl product with a backer, then this print is applied to a board with the 
    use of a laminator. In some instances the print has to be pre-masked to avoid 
    damage. Due to the poor scratch resistance of solvent ink, an additional 
    over-laminate, which also comes on a backer, needs to be applied. By printing 
    directly to the substrate, these additional steps are not required, eliminating 
    the waste that otherwise ends up in a landfill.</p>
 
    </div>
  
    <?php
      include ('footer.html');
    ?>

  </div>
</div>

</body>

</html>
