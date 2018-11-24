<?

  define( GLOBAL_DB_NAME, "signboom_v1p5");
  define( GLOBAL_COMPANY_NAME, "Signboom" );

  $userLevels=array(
    "0"=>"- Select Access Level -",
    "1"=>"Administrator",
    "2"=>"User Level 1",
    "3"=>"Production"
  );

//It would be a bad idea to remove hint questions from these arrays.
//ADD but don't REMOVE.  Keep the two arrays in sync.
  $arrHintQ=array(
    "----- Select A Security Question -----",
    "The make of my first car",
    "My mother's maiden name",
    "Name of a school I attended"
  );
  $arrHintQ2=array(
    "SEL",
    "CAR",
    "MOM",
    "SCH"
  );

  $arrCountry=array(
    "0"=>"-----  COUNTRY -----",
    "CA"=>"CANADA",
    "US"=>"UNITED STATES"
  );

  $arrPayMethod=array( "---- Select Payment  ----", "Visa", "Mastercard", "On Account" );

  $arrShipMethod=array( "---- Select Shipping  ----", "Pickup", "UPS Air", "UPS Ground", "Prepaid" );

  $arrProv=array(
    "AB"=>"Alberta",
    "BC"=>"British Columbia",
    "MB"=>"Manitoba",
    "NB"=>"New Brunswick",
    "NF"=>"New Foundland",
    "NT"=>"Northwest Territories",
    "NS"=>"Nova Scotia",
    "ON"=>"Ontario",
    "PI"=>"Prince Edward Island",
    "PQ"=>"Quebec",
    "SK"=>"Saskatchewan",
    "YT"=>"Yukon Territory"
  );

  $arrState=array(
    "AL"=>"Alabama",
    "AK"=>"Alaska",
    "AZ"=>"Arizona",
    "AR"=>"Arkansas",
    "CA"=>"California",
    "CO"=>"Colorado",
    "CT"=>"Connecticut",
    "DE"=>"Delaware",
    "DC"=>"District of Columbia",
    "FL"=>"Florida",
    "GA"=>"Georgia",
    "HI"=>"Hawaii",
    "ID"=>"Idaho",
    "IL"=>"Illinois",
    "IN"=>"Indiana",
    "IA"=>"Iowa",
    "KS"=>"Kansas",
    "KY"=>"Kentucky",
    "LA"=>"Louisiana",
    "ME"=>"Maine",
    "MD"=>"Maryland",
    "MA"=>"Massachusetts",
    "MI"=>"Michigan",
    "MN"=>"Minnesota",
    "MS"=>"Mississippi",
    "MO"=>"Missouri",
    "MT"=>"Montana",
    "NE"=>"Nebraska",
    "NV"=>"Nevada",
    "NH"=>"New Hampshire",
    "NJ"=>"New Jersey",
    "NM"=>"New Mexico",
    "NY"=>"New York",
    "NC"=>"North Carolina",
    "ND"=>"North Dakota",
    "OH"=>"Ohio",
    "OK"=>"Oklahoma",
    "OR"=>"Oregon",
    "PA"=>"Pennsylvania",
    "RI"=>"Rhode Island",
    "SC"=>"South Carolina",
    "SD"=>"South Dakota",
    "TN"=>"Tennessee",
    "TX"=>"Texas",
    "UT"=>"Utah",
    "VT"=>"Vermont",
    "VA"=>"Virginia",
    "WA"=>"Washington",
    "DC"=>"Washington D.C.",
    "WV"=>"West Virginia",
    "WI"=>"Wisconsin",
    "WY"=>"Wyoming"
  );

  $arrProvState=array(
    "0"=>"----- PROVINCE -----",
    "AB"=>"Alberta",
    "BC"=>"British Columbia",
    "MB"=>"Manitoba",
    "NB"=>"New Brunswick",
    "NF"=>"New Foundland",
    "NT"=>"Northwest Territories",
    "NS"=>"Nova Scotia",
    "ON"=>"Ontario",
    "PI"=>"Prince Edward Island",
    "PQ"=>"Quebec",
    "SK"=>"Saskatchewan",
    "YT"=>"Yukon Territory",
    "1"=>"-----    STATE -----",
    "AL"=>"Alabama",
    "AK"=>"Alaska",
    "AZ"=>"Arizona",
    "AR"=>"Arkansas",
    "CA"=>"California",
    "CO"=>"Colorado",
    "CT"=>"Connecticut",
    "DE"=>"Delaware",
    "DC"=>"District of Columbia",
    "FL"=>"Florida",
    "GA"=>"Georgia",
    "HI"=>"Hawaii",
    "ID"=>"Idaho",
    "IL"=>"Illinois",
    "IN"=>"Indiana",
    "IA"=>"Iowa",
    "KS"=>"Kansas",
    "KY"=>"Kentucky",
    "LA"=>"Louisiana",
    "ME"=>"Maine",
    "MD"=>"Maryland",
    "MA"=>"Massachusetts",
    "MI"=>"Michigan",
    "MN"=>"Minnesota",
    "MS"=>"Mississippi",
    "MO"=>"Missouri",
    "MT"=>"Montana",
    "NE"=>"Nebraska",
    "NV"=>"Nevada",
    "NH"=>"New Hampshire",
    "NJ"=>"New Jersey",
    "NM"=>"New Mexico",
    "NY"=>"New York",
    "NC"=>"North Carolina",
    "ND"=>"North Dakota",
    "OH"=>"Ohio",
    "OK"=>"Oklahoma",
    "OR"=>"Oregon",
    "PA"=>"Pennsylvania",
    "RI"=>"Rhode Island",
    "SC"=>"South Carolina",
    "SD"=>"South Dakota",
    "TN"=>"Tennessee",
    "TX"=>"Texas",
    "UT"=>"Utah",
    "VT"=>"Vermont",
    "VA"=>"Virginia",
    "WA"=>"Washington",
    "DC"=>"Washington D.C.",
    "WV"=>"West Virginia",
    "WI"=>"Wisconsin",
    "WY"=>"Wyoming"
  );

  $arrCellProvider=array(
    "BELL"=>"Bell Mobility",
    "ROGERS"=>"Rogers",
    "TELUS"=>"Telus Mobility",
    "FIDO"=>"Fido",
    "KOODO"=>"Koodo",
    "VIRGIN"=>"Virgin Mobile",
    "PCMOBILE"=>"Presidents Choice",
    "SASKTEL"=>"SaskTel",
    "MIKE"=>"Mike (Telus)",
    "SOLO"=>"Solo Mobile"
  );

  $arrCellDomain=array(
    "BELL"=>"txt.bell.ca",
    "ROGERS"=>"pcs.rogers.com",
    "TELUS"=>"msg.telus.com",
    "FIDO"=>"fido.ca",
    "KOODO"=>"msg.koodomobile.com",
    "VIRGIN"=>"vmobile.ca",
    "PCMOBILE"=>"mobiletxt.ca",
    "SASKTEL"=>"sms.sasktel.com",
    "MIKE"=>"msg.telus.com",
    "SOLO"=>"txt.bell.ca"
  );


///////////////////////////////////////
// MAIL CONFIG
///////////////////////////////////////
  $global_sender="signboom";
  // Don't think global_admin_email is used anywhere.  Set it to my email to test.  Alison.
  $global_admin_email="usablewebdesigns@yahoo.com";
  $global_welcome_email_subject="Your New Signboom Account";

  $global_welcome_email_from="Signboom";

  $global_welcome_email_msg="<b>Thank you for signing up with Signboom.</b><br><br>";
  $global_welcome_email_msg.="To login to your new Signboom account, ";
  $global_welcome_email_msg.="visit <a href=\"http://www.signboom.com\" target=\"blank\">www.signboom.com</a>.";
  $global_welcome_email_footer.="<br><br>Keep this information confidential. ";
  $global_welcome_email_footer.="<br>Don't reveal your account password to anyone.<br><br>";
  $global_welcome_email_footer.="<img src=\"http://www.signboom.com/images/logo3d.gif \" width=\"308\" height=\"54\">";
?>
