<?php
//	error_reporting(0);
  require_once('Connections/DBConn.php');
  include('Connections/needLogin.php');
  require_once( "includes/utils.php" );
  require_once( "includes/inc-signboom.php" );
  include ('includes/ordutils.php');
  include ('includes/allutils_171126.php');
  include('includes/testmode.php');

  //session_start();

  if (isset($_SESSION['MM_Username'])) {
    $email = $_SESSION['MM_Username'];
  }

  $loginUsername = $_SESSION['MM_Username'];
  setcookie("userid", $loginUsername, time() + (3600 * 24 * 60));
  $oproduct = isset($_POST['oproduct'])?$_POST['oproduct']:"";

  $upload_server = "http://upload.signboom.com";
?>

  <script>
  function showDifferences()
  {
    document.getElementById('differences').style.display = 'block';
    document.getElementById('show_differences').style.display = 'none';
  }
  function hideDifferences()
  {
    document.getElementById('differences').style.display = 'none';
    document.getElementById('show_differences').style.display = 'block';
  }
  </script>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Signboom: Your Online Print Engine</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="no-cache">
  <meta http-equiv="expires" content="Wed, 26 Feb 1997 08:21:57 GMT">

  <script src="script/utility.js" LANGUAGE="JavaScript" TYPE="text/javascript"></script>
  <script src="script/orderutil_170223b.js" LANGUAGE="JavaScript" TYPE="text/javascript"></script>
  <script src="script/su-141109.js" LANGUAGE="JavaScript" TYPE="text/javascript"></script>
  <script src="script/allorder_180518c.js" LANGUAGE="JavaScript" TYPE="text/javascript"></script>
  <script src="script/format.js" LANGUAGE="JavaScript" TYPE="text/javascript"></script>
  <script src="//mozilla.github.io/pdf.js/build/pdf.js"></script>

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
      echo '<link rel="stylesheet" href="signboom-batch2.css" type="text/css" title="default_style">';
    }
  ?>

  <link rel="stylesheet" href="order_form_styles.css" type="text/css" title="default_style">
  <link rel="stylesheet" href="css/descriptions-batch1.css" type="text/css" media="screen" charset="utf-8" />

  <style>
  #explain_setup {
    position: absolute;
    top: 520px;
    left: 480px;
    width: 260px;
    visibility: hidden;
    overflow: hidden;
    border:1px solid #CCC;
    background-color:#F9F9F9;
    border:1px solid #333;
    padding:5px;
    z-index: 2;
  }
  #popblock, #check200, #tileblock, #workFrame {
    position: absolute;
    top: 150px;
    left: 450px;
    width: 300px;
    visibility: hidden;
    overflow: hidden;
    border:1px solid #CCC;
    background-color:#F9F9F9;
    border:1px solid #333;
    padding:5px;
    z-index: 2;
  }
  #workFrame2 {
    position: absolute;
    top: 170px;
    left: 250px;
    width: 300px;
    height: 200px;
    z-index: 2;
  }
  #citydiv {
    position: absolute;
    top: 250px;
    left: 450px;
    width: 300px;
    visibility: hidden;
    overflow: hidden;
    border:1px solid #CCC;
    background-color:#F9F9F9;
    border:1px solid #333;
    padding:5px;
    z-index: 2;
  }

  .team-magenta {
    margin-top: 30px;
  }

  .team-yellow {
    margin-top: 30px;
  }

  div.team-magenta {
    border: none;
    margin: 0;
    background: #FF00FF;
    color: #000000;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    padding: 5px 0 5px 0;
    text-align: center;
  }

  div.team-yellow {
    border: none;
    margin: 0;
    background: #FFFF00;
    color: #000000;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    padding: 5px 0 5px 0;
    text-align: center;
  }

  </style>

  <script type="text/javascript">
  
  var categoryArray = new Array();
  var productArray = new Array();
  var optionCategoryArray = new Array();
  var finishingOptionArray = new Array();
  var productOptionArray = new Array();

  //OLD
  var optionArray = new Array();
  var optsetArray = new Array();

  var shiptoArray = new Array();
  var holiday = new Array();
  var psArray = new Array();
  var topOptionIdx;

  categoryArray[0] = new Array();
  productArray[0] = new Array();
  optionCategoryArray[0] = new Array();
  finishingOptionArray[0] = new Array();
  productOptionArray[0] = new Array();

  //OLD
  optsetArray[0] = new Array();

  var discountArray = new Array();
  discountArray[0] = new Array();
  discountArray[0][0] = "";
  discountArray[0][1] = "";

  <?php
    $defSetup="";
    $defcutoff="";
    echol ('var acctpst = "'.$acctpst.'";');
    echol ('var account_discount_global = "'.$acctdct.'";');
    echol ('var defFreight = "'.$defFreightCharge.'";');
    echol ('var defZone = "'.$defZone.'";');
    echol ('var defZoneAdd = "'.$defZoneAdd.'";');
    echol ('var stdSetup = "'.isset($defSetup)?$defSetup:"".'";');
    echol ('lastcity = "'.$acctcity.'";');
    echol ('laststate = "'.$acctprov.'";');
    echol ('freightcharge = parseFloat("'.$defFreightCharge.'");');
    echol ('freightzonemultiplier = parseFloat("'.$defZoneAdd.'");');
    echol ('freightzone = "'.$defZone.'";');
    echol ('defcutoff = "'.$defcutoff.'";');
    echol ('var shipmult = "'.$shipmult.'";');
    echol ('var wastefactor = "'.$wastefactor.'";');

    $i = 0;
    foreach ($fcategory as $c) {
      $i++;
      echol ('categoryArray['.$i.'] = new Array();');
      echol ('categoryArray['.$i.'][CatIdIdx] = "'.$c->ID.'";');
      echol ('categoryArray['.$i.'][CatCodeIdx] = "'.$c->code.'";');
      echol ('categoryArray['.$i.'][CatNameIdx] = "'.$c->shortname.'";');
      echol ('categoryArray['.$i.'][CatDescIdx] = "'.addcslashes($c->description, "\0..\057\072..\100\133..\140\173..\377").'";');
      echol ('categoryArray['.$i.'][CatPrntIdx] = "'.$c->printable.'";');
    }
    echol ('var numberOfCategories = "'.$i.'";');

    $i = 0;
    foreach ($fproduct as $p) {
      $i++;
      echol ('productArray['.$i.'] = new Array();');
      if(isset($p->id))
      {
        echol ('productArray['.$i.'][IdIdx] = "'.$p->id.'";');
      }
      if(isset($p->code))
      {
        echol ('productArray['.$i.'][CodeIdx] = "'.$p->code.'";');
      }
      if(isset($p->name))
      {
        echol ('productArray['.$i.'][NameIdx] = "'.$p->name.'";');
      }
      if(isset($p->description))
      {
        echol ('productArray['.$i.'][DescIdx] = "'.addcslashes($p->description, "\0..\057\072..\100\133..\140\173..\377").'";');
      }
      if(isset($p->descr_image))
      {
        echol ('productArray['.$i.'][DescrImageIdx] = "'.addcslashes($p->descr_image, "\0..\057\072..\100\133..\140\173..\377").'";');
      }
      if(isset($p->descr_text))
      {
        echol ('productArray['.$i.'][DescrTextIdx] = "'.addcslashes($p->descr_text, "\0..\057\072..\100\133..\140\173..\377").'";');
      }
      if(isset($p->descr_finishing))
      {
        echol ('productArray['.$i.'][DescrFinishingIdx] = "'.addcslashes($p->descr_finishing, "\0..\057\072..\100\133..\140\173..\377").'";');
      }
      if(isset($p->descr_limitations))
      {
        echol ('productArray['.$i.'][DescrLimitationsIdx] = "'.addcslashes($p->descr_limitations, "\0..\057\072..\100\133..\140\173..\377").'";');
      }
      if(isset($p->descr_extras))
      {
        echol ('productArray['.$i.'][DescrExtrasIdx] = "'.addcslashes($p->descr_extras, "\0..\057\072..\100\133..\140\173..\377").'";');
      }
      if(isset($p->category))
      {
        echol ('productArray['.$i.'][CategoryIdx] = "'.$p->category.'";');
      }
      if(isset($p->printwidth))
      {
        echol ('productArray['.$i.'][PrintWidthIdx] = "'.$p->printwidth.'";');
      }
      if(isset($p->printlength))
      {
        echol ('productArray['.$i.'][PrintLengthIdx] = "'.$p->printlength.'";');
      }
      if(isset($p->costdisc))
      {
        echol ('productArray['.$i.'][CostDiscIdx] = "'.$p->costdisc.'";');
      }
      if(isset($p->costnon))
      {
        echol ('productArray['.$i.'][CostNonIdx] = "'.$p->costnon.'";');
      }
      if(isset($p->costwaste))
      {
        echol ('productArray['.$i.'][CostWasteIdx] = "'.$p->costwaste.'";');
      }
      if(isset($p->sort_group))
      {
        echol ('productArray['.$i.'][SortGroupIdx]    = "'.$p->sort_group.'";');
      }
      if(isset($p->sort_order))
      {
        echol ('productArray['.$i.'][SortOrderIdx]    = "'.$p->sort_order.'";');
      }
      if(isset($p->batch_day))
      {
        echol ('productArray['.$i.'][BatchDayIdx]    = "'.$p->batch_day.'";');
      }
    }
    echol ('var numberOfProducts = "'.$i.'";');

    $i = 0;
    foreach ($foptioncategory as $oc) {
      $i++;
      echol ('optionCategoryArray['.$i.'] = new Array();');
      echol ('optionCategoryArray['.$i.'][OptCatIdIdx] = "'.$oc->id.'";');
      echol ('optionCategoryArray['.$i.'][OptCatCodeIdx] = "'.$oc->code.'";');
      echol ('optionCategoryArray['.$i.'][OptCatNameIdx] = "'.$oc->name.'";');
    }
    echol ('var numberOfOptionCategories = "'.$i.'";');

    $i = 0;
    foreach ($ffinishingoption as $o) {
      $i++;
      echol ('finishingOptionArray['.$i.'] = new Array();');
      echol ('finishingOptionArray['.$i.'][FinOptIdIdx] = "'.$o->id.'";');
      echol ('finishingOptionArray['.$i.'][FinOptCatIdx] = "'.$o->category.'";');
      echol ('finishingOptionArray['.$i.'][FinOptGroupIdx] = "'.$o->sort_group.'";');
      echol ('finishingOptionArray['.$i.'][FinOptSetIdx] = "'.$o->option_set.'";');
      echol ('finishingOptionArray['.$i.'][FinOptTypeIdx] = "'.$o->option_type.'";');
      echol ('finishingOptionArray['.$i.'][FinOptOrderIdx] = "'.$o->sort_order.'";');
      echol ('finishingOptionArray['.$i.'][FinOptNameIdx] = "'.$o->option_name.'";');
      echol ('finishingOptionArray['.$i.'][FinOptDescIdx] = "'.addcslashes($o->description, "\0..\057\072..\100\133..\140\173..\377").'";');
      echol ('finishingOptionArray['.$i.'][FinOptCodeIdx] = "'.$o->code.'";');
      echol ('finishingOptionArray['.$i.'][FinOptExtraTimeIdx] = "'.$o->extra_time.'";');
      echol ('finishingOptionArray['.$i.'][FinOptUnitsIdx] = "'.$o->units.'";');
      echol ('finishingOptionArray['.$i.'][FinOptUnitsPerHourIdx] = "'.$o->units_per_hour.'";');
      echol ('finishingOptionArray['.$i.'][FinOptReferenceIdx] = "'.$o->reference.'";');
      echol ('finishingOptionArray['.$i.'][FinOptFixedCostIdx] = "'.$o->fixed_cost.'";');
      echol ('finishingOptionArray['.$i.'][FinOptVariableCostIdx] = "'.$o->variable_cost.'";');
      echol ('finishingOptionArray['.$i.'][FinOptBatchDayIdx] = "'.$o->batch_day.'";');
      echol ('finishingOptionArray['.$i.'][FinOptLaminateProductCodeIdx] = "'.$o->laminate_product_code.'";');
    }
    echol ('var numberOfFinishingOptions = "'.$i.'";');

    $i = 0;
    foreach ($fproductoption as $po) {
      $i++;
      echol ('productOptionArray['.$i.'] = new Array();');
      echol ('productOptionArray['.$i.'][ProdOptIdIdx] = "'.$po->id.'";');
      echol ('productOptionArray['.$i.'][ProductCodeIdx] = "'.$po->product_code.'";');
      echol ('productOptionArray['.$i.'][FinishingOptionCodeIdx] = "'.$po->finishing_option_code.'";');
      echol ('productOptionArray['.$i.'][ValueIdx] = "'.$po->value.'";');
    }
    echol ('var numberOfPairs = "'.$i.'";');

    echol ('var packfee = "'.$packfee.'";');
    echol ('var setupfee = "'.$setupfee.'";');
    echol ('var gfloorsetupfee = "'.$gfloorsetupfee.'";');
    echol ('var inkcost = "'.$inkcost.'";');
    echol ('var discountfactora = "'.$discountfactora.'";');
    echol ('var discountfactorb = "'.$discountfactorb.'";');

    $i = 0;
    foreach ($discount as $p) {
      $i++;
      echol ('discountArray['.$i.'] = new Array();');
      echol ('discountArray['.$i.'][0] = "'.$p->ID.'";');
      echol ('discountArray['.$i.'][1] = "'.$p->Desc.'";');
      echol ('discountArray['.$i.'][2] = "'.$p->Footage.'";');   //new
      echol ('discountArray['.$i.'][3] = "'.$p->Dct.'";');
    }

    $shiptonum = 0;
    echol ('shiptoArray['.$shiptonum.'] = new Array();');
    echol ('shiptoArray['.$shiptonum.'][0] = "'.$shiptonum.'";');
    echol ('shiptoArray['.$shiptonum.'][1] = "'.addcslashes($acctcompany, "\0..\057\072..\100\133..\140\173..\377").'";');
    echol ('shiptoArray['.$shiptonum.'][2] = "'.addcslashes($acctaddr, "\0..\057\072..\100\133..\140\173..\377").'";');
    echol ('shiptoArray['.$shiptonum.'][3] = "'.addcslashes($acctcity, "\0..\057\072..\100\133..\140\173..\377").'";');
    echol ('shiptoArray['.$shiptonum.'][4] = "'.$acctprov.'";');
    echol ('shiptoArray['.$shiptonum.'][5] = "'.addcslashes($acctzip, "\0..\057\072..\100\133..\140\173..\377").'";');
    echol ('shiptoArray['.$shiptonum.'][6] = "'.$acctcountry.'";');
    if (isset($shipto)) {
        foreach ($shipto as $sh) {
      $shiptonum++;
      echol ('shiptoArray['.$shiptonum.'] = new Array();');
      echol ('shiptoArray['.$shiptonum.'][0] = "'.$sh->ID.'";');
      echol ('shiptoArray['.$shiptonum.'][1] = "'.addcslashes($sh->name, "\0..\057\072..\100\133..\140\173..\377").'";');
      echol ('shiptoArray['.$shiptonum.'][2] = "'.addcslashes($sh->address, "\0..\057\072..\100\133..\140\173..\377").'";');
      echol ('shiptoArray['.$shiptonum.'][3] = "'.addcslashes($sh->city, "\0..\057\072..\100\133..\140\173..\377").'";');
      echol ('shiptoArray['.$shiptonum.'][4] = "'.$sh->state.'";');
      echol ('shiptoArray['.$shiptonum.'][5] = "'.addcslashes($sh->zip, "\0..\057\072..\100\133..\140\173..\377").'";');
      echol ('shiptoArray['.$shiptonum.'][6] = "'.$sh->country.'";');
      }
    }

    for ($i = 1; $i <= count($holiday); $i++) {
      echol ('holiday['.$i.'] = "'.$holiday[$i].'";');
    }
    echol ('var curdate = "'.date("Y-m-d").'";');
    echol ('var curtime = "'.date("H:i:s").'";');
    echol ('var curyear = "'.date("Y").'";');
    echol ('var curmonth = "'.date("m").'";');
    echol ('var curday = "'.date("d").'";');
    echol ('var curhour = "'.date("H").'";');
    echol ('var curmin = "'.date("i").'";');
    echol ('var cursec = "'.date("s").'";');

    $vancouver_time_sec = date("U"); // this looks incorrect; the web server is 3 hours ahead, try subtracting 3 hours = 10800 sec
    echol ('var signboom_time_sec = "' . $vancouver_time_sec . '";');
    echol ('var local_time = new Date();');
    echol ('var local_time_sec = local_time.getTime() / 1000;');
    echol ('var time_adjustment_sec = signboom_time_sec - local_time_sec;');

    $i = 0;
    if (count($arrProvState)) {
      //print_r($arrProvState);
      /*while( list($id, $val) = each($arrProvState) ) {
        $i++;
        echol ('psArray['.$i.'] = new Array();');
        echol ('psArray['.$i.'][0] = "'.$id.'";');
        echol ('psArray['.$i.'][1] = "'.$val.'";');
      }*/
      $l=0;
      foreach($arrProvState as $k => $v)
      {
        $l++;
        echol ('psArray['.$l.'] = new Array();');
        echol ('psArray['.$l.'][0] = "'.$k.'";');
        echol ('psArray['.$l.'][1] = "'.$v.'";'); 
      }
      //print_r($psArray);
    }
  ?>

  function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
  }
  //-->
  </script>

</head>

<body<?php echo isset($body_class)?$body_class:"";?>

<?php echo isset($team_indicator)?$team_indicator:"";?>

  <div id="new_container" style="width: 1010px; margin: 10px auto;">
  <form action="" method="post" enctype="multipart/form-data" name="orderForm" id="orderForm">
    <input name="custid" type="hidden" id="custid" value="">
    <input name="readydate" type="hidden" id="readydate" value="">

    <!-- The tprod# elements contain information about only those line items which have files. 
         All fixture line items are removed and the remaining lines are adjusted so that there 
         are no gaps left in the tprod "array".  -->
    <input name="tprod1" type="hidden" id="tprod1" value="">
    <input name="tprod2" type="hidden" id="tprod2" value="">
    <input name="tprod3" type="hidden" id="tprod3" value="">
    <input name="tprod4" type="hidden" id="tprod4" value="">
    <input name="tprod5" type="hidden" id="tprod5" value="">
    <input name="tprod6" type="hidden" id="tprod6" value="">
    <input name="tprod7" type="hidden" id="tprod7" value="">
    <input name="tprod8" type="hidden" id="tprod8" value="">
    <input name="tprod9" type="hidden" id="tprod9" value="">
    <input name="tprod10" type="hidden" id="tprod10" value="">

    <!-- The xprod# elements contain information about ALL the line items in the order.
         The xprod# elements are used like the tprod# elements used to be used (before we added fixtures.)
         We had to make the tprod elements compatible with AJMUpload software, which requires
         that only information about files be included in the tprod "array". -->
    <input name="xprod1" type="hidden" id="xprod1" value="">
    <input name="xprod2" type="hidden" id="xprod2" value="">
    <input name="xprod3" type="hidden" id="xprod3" value="">
    <input name="xprod4" type="hidden" id="xprod4" value="">
    <input name="xprod5" type="hidden" id="xprod5" value="">
    <input name="xprod6" type="hidden" id="xprod6" value="">
    <input name="xprod7" type="hidden" id="xprod7" value="">
    <input name="xprod8" type="hidden" id="xprod8" value="">
    <input name="xprod9" type="hidden" id="xprod9" value="">
    <input name="xprod10" type="hidden" id="xprod10" value="">

    <input name="acctid" type="hidden" id="acctid" value="">
    <input name="ajmxfer" type="hidden" value="<?php echo $upload_server; ?>:31163/sbupload/uplNew.aspx">
    <input name="ajmxfer1" type="hidden" value="http://www.zipq.com:31443/signboom/uplNew.aspx">
    <input name="ordertype" type="hidden" id="ordertype" value="MIX">
    <input name="orderid" type="hidden"  id="orderid" value="">
    <input name="dbToken" type="hidden"  id="dbToken" value="">
    <input name="custemail" type="hidden"  id="custemail" value="">
    <input name="dct" type="hidden"  id="dct" value="">
    <input name="dctname" type="hidden"  id="dctname" value="">
    <input name="shiptoalt" type="hidden"  id="shiptoalt" ="">
    <input name="shiptoattn" type="hidden"  id="shiptoattn" value="">
    <input name="shiptoname" type="hidden"  id="shiptoname" value="">
    <input name="shiptoaddr" type="hidden"  id="shiptoaddr" value="">
    <input name="shiptocity" type="hidden"  id="shiptocity" value="">
    <input name="shiptoprov" type="hidden"  id="shiptoprov" value="">
    <input name="shiptozip" type="hidden"  id="shiptozip" value="">
    <input name="shiptocountry" type="hidden"  id="shiptocountry" value="">
    <input name="shiptoaddcust" type="hidden"  id="shiptoaddcust" value="">
    <input name="fsubtotal" type="hidden"  id="fsubtotal" value="">
    <input name="fsetup" type="hidden"  id="fsetup" value="">
    <input name="fdiscount" type="hidden"  id="fdiscount" value="">
    <input name="frushamt" type="hidden"  id="frushamt" value="">
    <input name="fnet" type="hidden"  id="fnet" value="">
    <input name="fGST" type="hidden"  id="fGST" value="">
    <input name="fPST" type="hidden" id="fPST" value="">
    <input name="ffreight" type="hidden" id="ffreight" value="">
    <input name="ftotal" type="hidden" id="ftotal" value="">
    <input name="fservicetype" type="hidden" id="fservicetype" value="">
    <input name="fpickuptype" type="hidden" id="fpickuptype" value="">
    <input name="frefnumber" type="hidden" id="frefnumber" value="">
    <input name="fnotes" type="hidden" id="fnotes" value="">
    <input name="fshipdocname" type="hidden" id="fshipdocname" value="">
    <input name="dctid" type="hidden" id="dctid" value="">
    <input name="emailr" type="hidden" id="emailr" value="<?php print ($loginUsername);?>">
    <input name="promocode" type="hidden"  id="promocode" value="">
    <input name="fpromodiscountdollars" type="hidden"  id="fpromodiscountdollars" value="">
    <input name="promodetails" type="hidden"  id="promodetails" value="">

    <div style="float: left; margin-top: 0px; margin-bottom: 10px;">
      <table>
        <tr>
          <td style="padding-right: 40px;">
             <a href="index.php"><img border="0" src="images/logo3d.jpg" width="308" height="54" alt="Signboom.com: Your online print engine"></a><br>
	     <?php if ($loginUsername == 'downloads@signboom.com') echo "<br><div style=\"background-color: yellow; font-size: 20pt; font-weight: bold; text-align: center;\">INCIDENT</div>"; ?>
          </td>
          <td>
            <a href="index.php" onClick="this.blur()"
              onMouseOver="document.home_image.src='images/home_hover2.gif';"
              onMouseOut="document.home_image.src='images/home2.gif';">
              <img style="margin-left: 3px; margin-right: 10px; margin-bottom: 5px; vertical-align: middle;" border="0"
                src="images/home2.gif" name="home_image" width="99" height="25" alt="Home">
            </a>
            <br>
            <a href="Connections/doLogout.php" onClick="this.blur()"
              onMouseOver="document.logout_image.src='images/log_out_hover2.gif';"
              onMouseOut="document.logout_image.src='images/log_out2.gif';">
              <img style="margin-left: 3px; margin-right: 10px;  vertical-align: middle;" border="0"
                src="images/log_out2.gif" name="logout_image" width="99" height="25" alt="Log Out">
            </a>
          </td>
	  <!-- one box: padding-left= 360px   two boxes: padding-left = 220px   three boxes: padding-left = 80px  -->
          <td class="feature_boxes" style="padding-top: 0; padding-left: 10px;">

	    <div style="width: 550px; float: right ; margin-left: 10px; border: solid 2px #02b0ed; padding: 5px; text-align: center;">
	      <span style="color: #E70089; font-weight: bold;">Faster Printing</span> 
	      <br>
	      We've recently added the Vutek LX3 Pro Press to our line of equipment which allows for much faster printing speeds at 
	      the same level of quality you've come to expect from Signboom. You'll notice our pricing has been reduced and 
	      our lead-times have shortened drastically - especially on larger orders. Make sure to get an online quote the next 
	      time you have a big project!
	      </span> 
	      <!--
	      <span style="color: #E70089; font-weight: bold;">
	      SYSTEM UPGRADE IN PROGRESS<br><br>
	      We are upgrading our software today.<br><br>
	      Please do not place any orders at this time.<br><br>
	      Monday, February 26, 2018
	      </span> 
	      -->
	    </div>

	    <!--
	    <div style="width: 120px; float: right ; margin-left: 10px; border: solid 2px #02b0ed; padding: 5px; text-align: center;">
	      <span style="color: #E70089;">17% Discount</span> on<br>
	      Election Signage<br>
	      Till April 30
	      <br><br>
	      <a href="http://signboom.com/promotion-2017-02-election-signage.html" 
	        target="_blank" onClick="this.blur()"
                onMouseOver="document.logout_image.src='images/details-button-hover.png';"
                onMouseOut="document.logout_image.src='images/details-button.png';">
                <img style="margin-left: 3px; margin-right: 10px;  vertical-align: middle;" border="0"
                  src="images/details-button.png" name="details_image" width="90" height="25" alt="Log Out">
              </a>
	    </div>

	    <div style="width: 120px; float: right ; margin-left: 10px; margin-right: 0px; border: solid 2px #02b0ed; padding: 5px; text-align: center;">
	      <span style="color: #E70089;">15% off</span> Large<br>
	      Plastic Sandwich<br>
	      Boards till Aug 15
	      <br><br>
	      <a href="http://signboom.com/promotion-2017-02-sandwich-boards.html" 
	        target="_blank" onClick="this.blur()"
                onMouseOver="document.logout_image.src='images/details-button-hover.png';"
                onMouseOut="document.logout_image.src='images/details-button.png';">
                <img style="margin-left: 3px; margin-right: 10px;  vertical-align: middle;" border="0"
                  src="images/details-button.png" name="logout_image" width="90" height="25" alt="Log Out">
              </a>
	    </div>

	    <div style="width: 120px; float: right ; margin-left: 10px; border: solid 2px #02b0ed; padding: 5px; text-align: center;">
	     17% Discount on<br>
	      Election Signage<br>
	      Till April 30
	      <br><br>
	      <a href="http://us13.campaign-archive1.com/?u=65cb9b3bea7e01b0955e7ac01&id=3e75e923df&e=7e30aad513" 
	        target="_blank" onClick="this.blur()"
                onMouseOver="document.logout_image.src='images/details-button-hover.png';"
                onMouseOut="document.logout_image.src='images/details-button.png';">
                <img style="margin-left: 3px; margin-right: 10px;  vertical-align: middle;" border="0"
                  src="images/details-button.png" name="logout_image" width="90" height="25" alt="Log Out">
              </a>
	    </div>
	    -->

          </td>
        </tr>
      </table>
    </div>

    <br style="clear: both;">

<?php

  if ($this_is_a_test == 1) {
    echo "<div class=\"highlighted\" style=\"width: 600px; margin: 0px auto;\">";
    echo "The Signboom online ordering system is currently in test and development mode as we are testing out new hardware.<br><br>";
    echo "<b> Any orders you place during this time may NOT be remembered correctly by the system.</b><br><br>";
    echo "The system will be back online first thing Wednesday November 19, 2014. ";
    echo "If you revisit this page later this evening, and this message is gone, then you can place orders. ";
    echo "We apologize for the inconvenience.<br><br>";
    echo "(November 18, 2014, 11:22 pm PST)<br><br>";
    echo "</div>";
  }

  for ($i = 1; $i <= 10; $i++) {

    echol ('<div id="finishing-popup-'.$i.'" class="div_hidden">');
    echol ('<div id="finishing-popup-title-'.$i.'" class="div_hidden"></div>');
    echol ('<div id="finishing-popup-message-'.$i.'" class="div_hidden"></div>');
    foreach ($foptioncategory as $oc) {
      $tempid= $oc->id;
      $tempcode = $oc->code;
      $tempname = $oc->name;
      echol ('<div id="finishing-popup-category-'.$tempcode.'-'.$i.'" class="div_hidden"></div>');
    }
    echol ('<div id="finishing-popup-buttons-'.$i.'">');
    echol ('  <a href="javascript:CancelFinishingChanges(\'finishing-popup-'.$i.'\', '.$i.')">Cancel Changes</a>&nbsp;&nbsp;&nbsp;&nbsp;');
    echol ('  <a href="javascript:SaveFinishingChanges(\'finishing-popup-'.$i.'\', '.$i.')">Save Changes</a>&nbsp;&nbsp;&nbsp;&nbsp;');
    echol ('  <a href="javascript:ResetFinishingToStandard('.$i.')">Reset Finishing to Standard</a>');
    echol ('</div>');
    echol ('</div>');
  }
?>

    <div>
    <table style="background-image: url(images/fade_tall.gif); background-position: bottom; background-repeat: repeat-x; background-color: #FFFFFF; border-style: solid; border-width: 1px; border-color:#000000;" cellspacing="0" cellpadding="2">
      <tr style="color: #FFFFFF; text-align: center; font-weight: bold; background-color: #E70089; background-image: url(images/pink_heading.gif); background-repeat: repeat-x;">
        <td colspan="2" style="padding: 7px 0px;">Category</td>
        <td colspan="2">Product</td>
        <td colspan="2">Finishing</td>
        <td colspan="2">W(in) x H(in)</td>
        <td>Qty</td>
        <td>&nbsp;Cost&nbsp;</td>
        <td>&nbsp;Total&nbsp;</td>
        <td><a class="in_table" href="how_to_create_pdf.php" target="_blank">PDF Print File</a></td>
      </tr>

<?php
  for ($i = 1; $i <= 10; $i++) {

    echol ('<tr>');
    echol ('<td>');
    echol('<select class="select140" name="cat'.$i.'" size="1" id="cat'.$i.'" onChange="ChangeCategory('.$i.')">');
    loadCategories();
    echol ('</select>');
    echol ('</td>');
    echol ('<td style="padding-right: 10px;">');
    echol ('<a nohref onClick="HelpCategory('.$i.')"><img src="images/info.png" width="16" height="16" alt="?"></a>');
    echol ('</td>');
    echol ('<td>');
    echol ('<select name="pr'.$i.'" size="1" id="pr'.$i.'" class="select263" onChange="ChangeProduct('.$i.')">');
    echol ('<option value="" disabled selected>Select Category</option>');
    echol ('</select>');
    echol ('</td>');
    echol ('<td style="padding-right: 10px;">');
    echol ('<a nohref onClick="HelpProduct('.$i.')"><img id="help_product_'.$i.'" src="images/info.png" width="16" height="16" alt="?"></a>');
    echol ('</td>');

    echol ('<td><span id="finishing'.$i.'">Standard</span></td>');
	
	echo "
    <td>
      <a href='#' onClick='ChangeFinishing(".$i.")' >
        <img border='0' style='margin-left: 5px; margin-right: 5px; margin-top: 3px;' src='images/edit.gif' width='46' height='25' alt='Edit'>
      </a>
    </td>
		";
		
    echol ('<td>');
    echol ('<input name="widthi'.$i.'" type="text" id="widthi'.$i.'" size="2" class="myinput" onChange="ClearLine('.$i.')">');
    echol ('</td>');
    echol ('<td>');
    echol ('<input name="heighti'.$i.'" type="text" id="heighti'.$i.'" size="2" class="myinput" onChange="ClearLine('.$i.')">');

    echol ('</td>');
    echol ('<td>');
    echol ('<input name="quantity'.$i.'" type="text" id="quantity'.$i.'" size="2" class="myinput" onChange="ClearLine('.$i.')">');
    echol ('</td>');
    echol ('<td>');
    echol ('<div id="piececost'.$i.'" style="color: #666666;">&nbsp;</div>');
    echol ('</td>');
    echol ('<td>');
    echol ('<div id="totlinecost'.$i.'" style="color: #666666;">&nbsp;</div>');
    echol ('</td>');
    echol ('<td>');
    echol ('<input name="file'.$i.'" type="file" id="file'.$i.'" class="myinput" data-id='.$i.'  onChange="ValName(file'.$i.')">');
    echol ('</td>');
    echol ('</tr>');
    echol ('<input name="tlin'.$i.'" type="hidden" id="tlin'.$i.'">');
    echol ('<input name="lindct'.$i.'" type="hidden" id="lindct'.$i.'">');
    echol ('<input name="sqft'.$i.'" type="hidden" id="sqft'.$i.'">');
    echol ('<input name="printedarea'.$i.'" type="hidden" id="printedarea'.$i.'">');
    echol ('<input name="wastearea'.$i.'" type="hidden" id="wastearea'.$i.'">');
    echol ('<input name="wastecost'.$i.'" type="hidden" id="wastecost'.$i.'">');
    echol ('<input name="inkcost'.$i.'" type="hidden" id="inkcost'.$i.'">');
  }
?>
      <tr>
        <td colspan="12" class="smalltext"><br></td>
      </tr>
    </table>
    </div>

    <div id="new_ship_to" style="float: left; width: 380px; margin-left: 10px; margin-top: 20px;">

      <fieldset class="fieldset_tall">
        <legend class="pinktext">Shipping Address</legend>
        <br>
        Ship To:&nbsp;&nbsp;&nbsp;
        <input name="ckaltaddr" type="checkbox" id="ckaltaddr" class="myinput" onClick="CheckAddr('ALT')" value="checkbox">
        Alternate Address&nbsp;&nbsp;&nbsp;
        <input name="ckdefaddr" type="checkbox" id="ckdefaddr" class="myinput" onClick="CheckAddr('DEF')" value="checkbox" checked>
        Default Address
        <br><br>

        <div style="text-align: right;">
          Attention: <input name="txtattn" type="text" id="txtattn" class="myinput" size="40" maxlength="50"><br>
          <div id="namebox">
            Company:
            <select class="select263" name="stcompany" id="stcompany" onChange="ChangeAddr()">
              <option value="0" selected><?php print $acctcompany; ?></option>
              <option value="N">New Address</option>
              <?php
                for ($i = 1; $i <= count($shipto); $i++) {
                 echol ('<option value="'.$i.'">'.$shipto[$i]->name.'</option>');
                }
              ?>
            </select>
          </div>

          <div id="txtaddrbox">
            Address: <input name="txtaddr" type="text" id="txtaddr" class="myinput" onFocus="this.blur();"
              value="<?php print ($acctaddr); ?>" size="40" maxlength="64"><br>
          </div>

          <div id="txtcitybox">
            City: <input name="txtcity" type="text" id="txtcity" class="myinput" onFocus="this.blur();"
              value="<?php print ($acctcity); ?>" size="40" maxlength="32">
          </div>

          <div id="provstatebox">
            Province: <input name="txtprovstate" type="text" id="txtprovstate" class="myinput"
              value="<?php print GetProv($acctprov, $arrProvState); ?>" size="40" maxlength="32" readonly="yes">
          </div>

          <div id="countrybox">
            Country: <input name="txtcountry" type="text" id="txtcountry" class="myinput"
              value="<?php print ($acctcountry); ?>" size="40" maxlength="32" readonly="yes">
          </div>

          <div id="postalcodebox">
            Postal Code: <input name="txtzipcode" type="text" id="txtzipcode" class="myinput" onFocus="this.blur();"
              value="<?php print ($acctzip); ?>" size="40" maxlength="10">
          </div>

          <div id="addaddressbox">
            <br>
            <input name="ckaddcust" type="checkbox" disabled="disabled" id="ckaddcust" class="myinput" value="checkbox">
            <b>Add to My Address Book</b>
            <br><br>
          </div>
        </div>
      </fieldset>

      <br>
      <fieldset class="fieldset_medium">
        <legend class="pinktext">Shipping Documents</legend>
        <div id="shippingdocsbox">
        <br>
          We will ship under your private label when an alternate address is indicated. We require a PDF version
          of shipping documents uploaded with each order.<br><br>
          Shipping Documents:
          <input name="shipdocfile" type="file" id="shipdocfile" class="myinput" onChange="valDocFile()">
          <br><br>
        </div>
      </fieldset>
      <br><br>

    </div>

    <div id="new_print_service" style="float: left; width: 340px; margin-top: 20px; margin-left: 16px;">

      <fieldset class="fieldset_short">
      <legend class="bluetext">Print Service</legend>
        <table>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>Ready By:</td>
          </tr>
          <tr>
            <td>Standard:</td>
            <td><input name="ckstdservice" type="checkbox" id="ckstdservice" class="myinput" onClick="CheckService('STD')" value="checkbox" checked></td>
            <td>&nbsp;</td>
            <td><input name="stdready" type="text" id="stdready" class="myinput" size="15" readonly="yes"></td>
          </tr>
          <tr>
            <td>RUSH Service:</td>
            <td><input name="ckrushservice" type="checkbox" id="ckrushservice" class="myinput" onClick="CheckService('RUSH')" value="checkbox"></td>
            <td><input name="txtrushval" type="text" id="txtrushval" value="$25" class="myinput" size="4" readonly="yes"></td>
            <td><input name="rushready" type="text" id="rushready" class="myinput" size="15" readonly="yes"></td>
          </tr>
          <tr>
            <td>HOT Service:</td>
            <td><input name="ckhotservice" type="checkbox" id="ckhotservice" class="myinput" onClick="CheckService('HOT')" value="checkbox"></td>
            <td><input name="txthotval" type="text" id="txthotval" value="$75" class="myinput" size="4" readonly="yes"></td>
            <td><input name="hotready" type="text" id="hotready" class="myinput" size="15" readonly="yes"></td>
          </tr>
        </table>
      </fieldset>

      <br>

      <fieldset class="fieldset_short">
      <legend class="bluetext">Delivery</legend>
      <table>
        <tr>
           <td>Pickup - Unpackaged</td>
           <td><input name="ckpick" type="checkbox" id="ckpick" class="myinput" onClick="CheckDelivery('PICK')" value="checkbox"></td>
           <td><input name="pickcost" type="text" id="pickcost" class="myinput"
                 value="$0.00" size=8" readonly="yes">
         </tr>
         <tr>
           <td>Pickup - Packed for Courier</td>
           <td><input name="ckpack" type="checkbox" id="ckpack" class="myinput" onClick="CheckDelivery('PACK')" value="checkbox">
           <td><input name="packcost" type="text" id="packcost" class="myinput"
                 value="<?php $packfee = floatval($packfee); echo '$'.$packfee =number_format($packfee, 2, '.', ''); ?>" size=8" readonly="yes">
         </tr>
         <tr>
           <td>Ship Prepaid Ground (FOB)</td>
           <td><input name="ckppd" type="checkbox" id="ckppd" class="myinput" onClick="CheckDelivery('PPD')" value="checkbox" checked>
           <td><input name="ppdcost" type="text" id="ppdcost" class="myinput"
                 value="<?php echo '$'.$defFreightCharge; /*print ('$'.number_format($defFreightCharge, 2));*/ ?>" size=8" readonly="yes">
         </tr>
      </table>
      </fieldset>

      <br>

      <fieldset class="fieldset_short">
      <legend class="bluetext">Reference Number</legend>
      <div id="new_reference_number">
        <br>
        <b>REQUIRED:</b> Please enter a reference number of your choosing.  This reference number
        must be unique for each order you place.
        <br><br>
        <input name="txtref" type="text" id="txtref" class="myinput" size="32" maxlength="32">
      </div>
      </fieldset>

      <br>

      <fieldset class="fieldset_medium">
      <legend class="bluetext">Customer Notes</legend>
      <div id="customer_notes">
      <br>
        You may enter up to 200 characters of text, with special instructions for your order.
        <br><br>
        <textarea name="txtnotes" id="txtnotes" cols="35" rows="3" onKeyDown="limitTextLength(this, 200);"
          onKeyUp="limitTextLength(this, 200);"></textarea>
      </div>
      </fieldset>

      <br>

    </div>

    <div id="new_order_summary" style="float: left; width: 220px; margin-left: 16px; margin-top: 20px;">

      <div id="new_quote_button" style="text-align: center">
        <input name="Submit" type="button" class="quotebutton" id="Submit" onClick="ProcessUpload();" value="">
      </div>

      <br><br>
      <fieldset class="fieldset_short">
      <legend class="pinktext">Promo Code</legend>
      <div class="third_column" style="width: 210px; text-align: right;">
        Code: <input name="txtpromo" type="text" id="txtpromo" class="myinput" size="14" maxlength="10" 
	    onKeyUp="ClearTotals();" onCopy="ClearTotals();" onCut="ClearTotals();" onPaste="ClearTotals();"><br>
      </div>
      </fieldset>

      <br><br>
      <fieldset class="fieldset_tall">
      <legend class="pinktext">Order Summary</legend>
      <br>
      <div id="new_summary" style="text-align: right">
        Sub-Total: <input name="txtsubtot" type="text" id="txtsubtot" class="myinput" size="14" maxlength="10" readonly="yes"><br>
        Setups: <input name="txtsetup" type="text" id="txtsetup" class="myinput" size="14" maxlength="10" readonly="yes"><br>
        Discount: <input name="txtdct" type="text" id="txtdct" class="myinput" size="14" maxlength="10" readonly="yes"><br>
                  <input name="txtdctname" type="hidden" id="txtdctname">
        Promotion: <input name="txtpromoamt" type="text" id="txtpromoamt" class="myinput" size="14" maxlength="10" readonly="yes"><br>
        Rush/Hot: <input name="txtrushamt" type="text" id="txtrushamt" class="myinput" size="14" maxlength="10" readonly="yes"><br>
        Net Cost: <input name="txtnet" type="text" id="txtnet" class="myinput" size="14" maxlength="10" readonly="yes"><br>
        Freight: <input name="txtfreight" type="text" id="txtfreight" class="myinput" size="14" maxlength="10" readonly="yes"><br>
        GST: <input name="txtGST" type="text" id="txtGST" class="myinput" size="14" maxlength="10" readonly="yes"><br>
        PST: <input name="txtPST" type="text" id="txtPST" class="myinput" size="14" maxlength="10" readonly="yes"><br>
        Order Total: <input name="txtordtotal" type="text" id="txtordtotal" class="myinput" size="14" maxlength="10" readonly="yes"><br>
      </div>
      </fieldset>

    </div>
    <br style="clear: both;">

  </form>
  </div>

<iframe id="workFrame" name="workFrame" style="visibility: hidden;">
</iframe>

<iframe id="workFrame2" name="workFrame2" style="visibility: hidden;" src="">
</iframe>

<div id="category_description" class="popup_new">
&nbsp;
</div>

<div id="product_description" class="popup_new">
&nbsp;
</div>

<div id="option_description" class="popup_new">
&nbsp;
</div>

<div id="popblock">
  <br><b>Attention:</b><br><br>
  Your browser appears to be blocking popup windows. We understand the need to block unwanted popup windows;
  however, our software uses them to keep you informed on the progress of your upload. Please enable popup windows
  and press the <b>Submit Order</b> button again to send your file.
  <br><br>
  <div align="center">
    <a href="javascript:hideme('popblock')">Close</a>
  </div>
</div>

<div id="check200">
  <br><b>Attention:</b><br><br>
  Due to a limitation in Adobe, PDF files saved at a size over 200 inches will not perform properly.
  If you have designed in scale and the actual design size is less than 200 inches please ignore this warning,
  as we do require you to enter the actual output size in this field.  However, if you have designed at actual
  size, please <a href="working_large.php" target="_blank">review our guidelines on working in scale</a>,
  and update your PDF accordingly.
  <br><br>
  <div align="center">
    <a href="javascript:hideme('check200')">Close</a>
  </div>
</div>

<div id="explain_setup" style="popup_new>
  <br><b>Setup Costs</b><br><br>
  There is a single flat-rate setup fee for each order, regardless of how many line items it contains.
  <br><br>
  <div align="center">
    <a href="javascript:hideme('explain_setup')">Close</a>
  </div>
</div>

<div id="citydiv">
  <br><b>Signboom City Locator</b><br><br>
  <div id="citytxt">&nbsp;</div>
  <br><br>
  <div id="cityprovtxt">&nbsp;</div>
  <select name="citylist" size="14" multiple id="citylist" onChange="PickCity()">
  </select></td>
  <br><br>
  <input name="txtselectedcity" type="text" class="myinput" id="txtselectedcity" maxlength="32">
  <input name="Submit" type="submit" class="myinput" onClick="SelectCity();" value="Select">
</div>

<div id="tileblock">
  <br><b>Attention:</b><br><br>
  Tiling is required for at least one of the items in this quote. You may be contacted to confirm details.
  <br><br>
  <div align=center>
    <a href="javascript:hideme('tileblock')">Close</a>
  </div>
</div>

</body>
</html>
