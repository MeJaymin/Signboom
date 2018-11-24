<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  <title>Signboom: Your Online Print Engine</title>
  <link rel="stylesheet" href="signboom.css" type="text/css" title="default_style">

  <!--INTERNET EXPLORER SPECIFIC STYLING CODE FOLLOWS-->
  <!--[if lte IE 7]>
  <link rel="stylesheet" href="ie6_specific.css" type="text/css" title="default_style">
  <!--[else]>
  <link rel="stylesheet" href="signboom.css" type="text/css" title="default_style">
  <![endif]-->

  <style>
  #citydiv {
    position:absolute; 
    visibility:visible; 
    left: 320px; 
    top: 13px;
    width:315px; 
    height:550px; 
    z-index:1; 
    padding: 10px 10px 10px 10px; 
    border: 3px solid grey; 
    background-color: #FFFFFF;
  }
  </style>
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

    <img src="images/title_create_account.gif" width="207" height="18" alt="CREATE ACCOUNT">
    <br><br>

    <div style="width: 430px;">
    To create a new user account, fill in the fields below, then click the "Create Account" button.<br><br>
    Fields marked by an asterisk (*) are required.<br><br>

    <div style="float: left; text-align: right; line-height: 130%;">
    <!-- <form action=<? // =$PHP_SELF;?> method="POST"> -->
	<form action=<?php echo $_SERVER['PHP_SELF']; ?> method="POST">
      <!--ACCOUNT SIGNUP-->
        <b><?=$statusMsg;?></b><br>
        *First Name: 
        <input type="text" maxlength="32" size="32" name="firstName" value="<?=$firstName;?>"><br>
        *Last Name: 
        <input type="text" maxlength="32" size="32" name="lastName" value="<?=$lastName;?>"><br>
        *Email Address:
        <input type="text" size="32" maxlength="64" name="email" value="<?=$email;?>"><br>
        *Company Name:
        <input type="text" size="32" maxlength="35" name="company" value="<?=$company;?>"><br>
        *Street Address:
        <input type="text" size="32" maxlength="64" name="address" value="<?=$address;?>"><br>
        *City:
        <input type="text" size="32" maxlength="25" name="city" value="<?=$city;?>"><br>
        *Prov/State:
        <select name="selectProvState">
        <?
          if (count($arrProvState)) {
            while (list($id, $val) = each($arrProvState) ) {
              if ($id == $HTTP_POST_VARS['selectProvState']) {
                printf("<option value=\"$id\" selected>$val</option>\n");
              } else {
                printf("<option value=\"$id\">$val</option>\n");
              }
            }
          } else {
            printf("<option value=\"\">None</option>\n");
          }?>
        </select><br>
        *Country:
        <select name="selectCountry">
          <?  
          if (count($arrCountry)) {
            while (list($id, $val) = each($arrCountry) ) {
              if ($id == $HTTP_POST_VARS['selectCountry']) {
                printf("<option value=\"$id\" selected>$val</option>\n");
              } else {
                printf("<option value=\"$id\">$val</option>\n");
              }
            }
          } else {
            printf("<option value=\"\">None</option>\n");
          }
          ?>
        </select><br>
        *Postal/Zip:
        <input type="text" size="20" maxlength="16" name="postalzip" value="<?=$postalzip;?>"><br>
        *Phone:
        <input type="text" size="20" maxlength="16" name="phone1" value="<?=$phone1;?>"><br>
        Fax:
        <input type="text" size="20" maxlength="16" name="phone2" value="<?=$phone2;?>"><br>
        Website:
        <input type="text" size="32" maxlength="64" name="url" value="<?=$url;?>"><br>
        PST Number:
        <input type="text" size="32" maxlength="10" name="PST" value="<?=$PST;?>"><br><br>
        *Password:
        <input type="password" size="20" maxlength="20" name="epassword1" value=""><br>
        *Re-enter Password:
        <input type="password" size="20" maxlength="20" name="epassword2" value=""><br><br>
        *Password Security Question:<br>
        If you forget your password, you'll need to answer this question.<br>
        <select name="selectHintQ">
        <?
          if (count($arrHintQ)) {
            while (list($id, $val) = each($arrHintQ) ) {
              if ($id == $HTTP_POST_VARS['selectHintQ']) {
                printf("<option value=\"$id\" selected>$val</option>\n");
              } else {
                printf("<option value=\"$id\">$val</option>\n");
              }
            }
          } else {
            printf("<option value=\"\">None</option>\n");
          }?>
        </select>
        <input type="text" size="12" name="hinta" value="<?=$hinta;?>"><br><br>
        <input type="button" value="Cancel" name="btnCancel" onClick="window.close()">
        <input type="submit" name="btnSave" value="Sign Up">
        <input type="hidden" name="userProvState" value="<?=$selectProvState;?>">
        <input type="hidden" name="userCountry" value="<?=$selectCountry;?>">
        <input name="oldcity" type="hidden" id="oldcity" value="<? echo $oldcity; ?>">
        <input name="oldst" type="hidden" id="oldst" value="<? echo $oldst; ?>">

    </form>
    </div>
    </div>

     <!*********** CITY LOCATOR CODE *****************->
      <?
      if ($cityerr) {
        printf("<div id=\"citydiv\" style=\"visibility:visible;\">\n");
      } else {
        printf("<div id=\"citydiv\" style=\"visibility:hidden;\">\n");
      }
      ?>
  
      <div id="citytxt">
        <div style="text-align: center;"><img src="images/logo3d.gif" width="308" height="54"></div>

        <br>
        <div style="color: #F20081; font-weight: bold; text-align: center">Signboom City Locator</div> <br>

        We are unable to locate <b><?php echo ($shipcityerr ? $lastshipcity : $city); ?></b> in our shipping database.  
        Please choose a new city from the list below, or click "Select" to accept your city as entered. <br><br>
        Please note that if you click "Select" and the city is not in our database, we cannot compute freight 
        charges in our online calculator.
      </div>

      <div id="cityprovtxt">
        <br>Cities within <b><?php echo $selectProvState ?></b><br>
      </div>

      <select name="citylist" size="12" id="citylist" multiple onChange="PickCity()">
        <?php
        if ($cityerr) {
          for ($i = 0; $i <= count($Cities); $i++) { 
            echo ('<option value="'.$Cities[$i].'">'.$Cities[$i].'</option>');
          } 
        }
        ?>    
      </select>

      <br><br>
      <input name="txtselectedcity" type="text" id="txtselectedcity" 
        value="<?php echo ($shipcityerr ? $lastshipcity : $city); ?>" maxlength="32">
      <input name="Submit" type="submit" onClick="SelectCity();" value="Select">

      <script language="JavaScript" type="text/JavaScript">
  
        var shiptoArray = new Array();
        var cities = new Array();
        //Execute this when a new city is selected from the pick list
        function PickCity() {
          document.getElementById('txtselectedcity').value = document.getElementById('citylist').value;
        }

        function SelectCity() {
          hideme('citydiv');
          document.getElementById('city').value = document.getElementById('txtselectedcity').value;
        }
        //Hides a div
        function hideme(divname) {
          if (document.getElementById) { 
            document.getElementById(divname).style.visibility = 'hidden';
          } 
        }
      </script>

      </div> 
      <!*********** END OF CITY LOCATOR *****************->

    </div>

    <?php
      include ('footer.html');
    ?>

    </div>
  </div>

</body>
</html>


