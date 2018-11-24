// JavaScript Document

  var lastcity;
  var laststate;
  var totfreight = 0;        //Total freight charges for this order
  var freightcharge = 0;      //Current charge this destination
  var freightzonemultiplier = 0;  //Total freight multiplier for weight > 10 lbs
  var freightzone;        //Destination zone
  var curlocaltime = (new Date).getTime();

  var newcustomer = false;    //new customer flag

  var linecount = 10;
  var nulldesc =   "Select a product to view its description here.\n";

// General Utility Routines

  function xreplace(checkMe,toberep,repwith){

    var temp = checkMe;
    var i = temp.indexOf(toberep);
    while(i > -1) {
      temp = temp.replace(toberep, repwith);
      i = temp.indexOf(toberep, i + repwith.length + 1);
    }
    return temp;
  }

  // short routine to insure a PDF is being uploaded
  function ValName(f) {
    filename = f.value;
    if (filename == "") return true;
    if (filename.length < 5) {
      NameAlert(f);
      return false;
    }
    ext = filename.substr(filename.length-4,4);
    if (ext.toLowerCase() != ".pdf") {
      NameAlert(f);
      return false;
    }
    return true;
  }
  function NameAlert(f) {
    alert("We can only accept Adobe PDF files on this order form.\nThis file must end with .pdf.\nIf you do not have a PDF version of this\nfile, please upload it to us through our\n Artwork Services page.\n\nThank You");
    //f.value = "";  //This only works on IE
    f.focus();
    return false;
  }
  function valDocFile() {
    wsfilename = document.getElementById("shipdocfile").value;
    ext = (wsfilename.length > 3) ? wsfilename.substr(wsfilename.length-4,4) : "";
    if (ext.toLowerCase() != ".pdf") {
      alert("All shipping documents must be submitted in .pdf format.");
      fileToken = document.getElementById("shipdocfile");
      fileToken.value = "";
      fileToken.focus();
      return false;
    }
  }
  function valInt(ws) {
    wsnum = ws.value 
    //if ((wsnum <= 0) || (isNaN(wsnum))) {
    if (parseInt(wsnum) != wsnum || (wsnum <= 0)) {
      ws.focus();
      return false;
    }
    return true;
  }

  function valNum(ws) {
    wsnum = Number(ws.value)
    if ((wsnum <= 0) || (wsnum != ws.value)) {
      ws.focus();
      return false;
    }
    return true;
  }

  function IvalNum(ws) {
    wsnum = parseFloat(ws.value);
    if ((wsnum <= 0) || (isNaN(wsnum))) {
      ws.focus();
      return false;
    }
    return true;
  }

  function intdiv ( numerator, denominator ) {
    // In JavaScript, dividing integer values yields a floating point result (unlike in Java, C++, C)
    // To find the integer quotient, reduce the numerator by the remainder first, then divide.
      var remainder = numerator % denominator;
      var quotient = ( numerator - remainder ) / denominator;
    return quotient;
  }


  
  /********************************************************************************************
  * This routine is called when an error occurs to initilize the error message.
  ********************************************************************************************/
  function SetError(msg) {
    //document.getElementById("errmsg").innerHTML = msg;
      if (msg != "") alert(msg);
  }

  /********************************************************************************************
  * This routine clears the totals boxes
  ********************************************************************************************/
  function ClearTotals(msg) {
    document.getElementById("txtsubtot").value = "";
    document.getElementById("txtsetup").value = "";
    document.getElementById("txtdct").value = "";
    document.getElementById("txtpromoamt").value = "";
    document.getElementById("txtrushamt").value = "";
    document.getElementById("txtnet").value = "";
    document.getElementById("txtGST").value = "";
    document.getElementById("txtPST").value = "";
    document.getElementById("txtfreight").value = "";
    document.getElementById("txtordtotal").value = "";
    document.getElementById("Submit").value = "";
    /*document.getElementById("Submit").style.backgroundImage = "url(images/quote_order.gif)";*/
    document.getElementById("Submit").className = "quotebutton";
    document.getElementById("Submit").onclick = DoCompute;
    uploading_global = false;
      
  }

  // handle clicks to service boxes
  function CheckService(svc) {
    
    ClearTotals();
    switch(svc)
        {
          case 'STD':   
        document.getElementById("ckstdservice").checked = true;
        document.getElementById("ckrushservice").checked = false;
        document.getElementById("ckhotservice").checked = false;
        break;
          case 'RUSH':   
        document.getElementById("ckstdservice").checked = !(document.getElementById("ckrushservice").checked);
        document.getElementById("ckhotservice").checked = false;
        break;
          case 'HOT': 
        document.getElementById("ckstdservice").checked = !(document.getElementById("ckhotservice").checked);
        document.getElementById("ckrushservice").checked = false;
        break;
          default:  
        document.getElementById("ckstdservice").checked = true;
        document.getElementById("ckrushservice").checked = false;
        document.getElementById("ckhotservice").checked = false;
        break;  
        }

  }
// handle clicks to service boxes
  function CheckDelivery(dlvy) {
    
    ClearTotals();
    switch(dlvy)
        {
          case 'PPD':   
        document.getElementById("ckppd").checked = true;
        document.getElementById("ckpick").checked = false;
        document.getElementById("ckpack").checked = false;
        break;
          case 'PICK':   
        document.getElementById("ckppd").checked = !(document.getElementById("ckpick").checked);
        document.getElementById("ckpack").checked = false;
        break;
          case 'PACK': 
        document.getElementById("ckppd").checked = !(document.getElementById("ckpack").checked);
        document.getElementById("ckpick").checked = false;
        break;
        }
  }
// handle clicks to Address boxes
  function CheckAddr(addr) {
    newcustomer = false;
    switch(addr)
    {
      case 'DEF':   
        if (document.getElementById("ckdefaddr").checked) {
          SetDefAddr();
        } else {
          document.getElementById("ckaltaddr").checked = true;
        }
      break;
      case 'ALT':   
        if (document.getElementById("ckaltaddr").checked) {
          document.getElementById("ckdefaddr").checked = false;
        } else {
          document.getElementById("ckdefaddr").checked = true;
          SetDefAddr();
        }
      break;
    }
  }

  function SetDefAddr() {
    txt = 'Company: <select name="stcompany" class="select263" id="stcompany" onChange="ChangeAddr()"></select>';
    document.getElementById("namebox").innerHTML = txt;
    sbox = document.getElementById("stcompany");
    AddSelectItem(sbox, 1, "0", shiptoArray[0][1]);
    AddSelectItem(sbox, 2, "N", "New Address");
    for (i = 1; i < shiptoArray.length; i++) {
      AddSelectItem(sbox, i+2, i, shiptoArray[i][1]);
    }
    txt = 'Country: <input name="txtcountry" type="text" id="txtcountry" value="" size="40" maxlength="32" readonly="yes">';
    document.getElementById("countrybox").innerHTML = txt;
    txt = 'Province: <input name="txtprovstate" type="text" id="txtprovstate" value="" size="40" maxlength="32" readonly="yes">';
    document.getElementById("provstatebox").innerHTML = txt;
    document.getElementById("ckaltaddr").checked = false;
    document.getElementById("stcompany").selectedIndex = 0;
    freightcharge = parseFloat(defFreight);
    freightzonemultiplier = parseFloat(defZoneAdd);
    freightzone = defZone;
    a1 = new ToFmt(freightcharge);
    document.getElementById("ppdcost").value = "$" + strltrim(a1.fmtF(9,2));
    ClearTotals();
    SetAddr(0)
  }

  function SetAddr(idx) {
    document.getElementById("txtaddr").value = shiptoArray[idx][2];
    document.getElementById("txtcity").value = shiptoArray[idx][3];
    document.getElementById("txtzipcode").value = shiptoArray[idx][5];
    document.getElementById("txtprovstate").value = ExpandName(shiptoArray[idx][4]);
    document.getElementById("txtcountry").value = shiptoArray[idx][6];
    if (idx == 0) {
      lastcity = shiptoArray[idx][3];
      laststate = shiptoArray[idx][4];
    }
    SetAddrFields(true);
  }

  // This routine will select a value in a pulldown if it exists
  function SetSelected(sbox, val) {
    for (i = 0; i < sbox.length; i++) {
      if (sbox.options[i].value == val || sbox.options[i].innerHTML == val) {
        sbox.selectedIndex = i;
        break;
      }
    }
  }

  // Protect or unprotect address fields as needed
  function SetAddrFields(status) {
    if (status) {
      txtaction = ' onFocus="this.blur();"';}
    else {
      txtaction = '';}
    tmp = document.getElementById("txtcity").value
    txt = 'City: <input name="txtcity" type="text" id="txtcity"' + txtaction + ' value="" size="40" maxlength="32">'
    document.getElementById("txtcitybox").innerHTML = txt;
    document.getElementById("txtcity").value = tmp 

    tmp = document.getElementById("txtaddr").value
    tmp1 = document.getElementById("txtzipcode").value
    txt = 'Address: <input name="txtaddr" type="text" id="txtaddr"' + txtaction + ' value="" size="40" maxlength="64">'
    document.getElementById("txtaddrbox").innerHTML = txt;
    document.getElementById("txtaddr").value = tmp     

    txt ='Postal Code: <input name="txtzipcode" type="text" id="txtzipcode"' + txtaction + ' value="" size="40" maxlength="10">'
    document.getElementById("postalcodebox").innerHTML = txt;
    document.getElementById("txtzipcode").value = tmp1 

    document.getElementById("ckaddcust").disabled = status;
    if (status) document.getElementById("ckaddcust").checked = false;
  }

  //This routine is executed when the Shipper pulldown is executed
  function ChangeAddr() {
    id = document.getElementById("stcompany").value
    freightcharge = 0;
    freightzonemultiplier = 0;
    freightzone = "";
    a1 = new ToFmt(freightcharge);
    document.getElementById("ppdcost").value = "$" + strltrim(a1.fmtF(9,2));
    ClearTotals();

    if (id == "N") {
      newcustomer = true;
      document.getElementById("ckdefaddr").checked = false;
      document.getElementById("ckaltaddr").checked = true;
      txt = 'Company: <input name="txtcompname" id="txtcompname" size="40" maxlength="32">';
      document.getElementById("namebox").innerHTML = txt;
      txt = 'Province: <select class="select263" name="selprovstate" id="selprovstate"></select>';
      document.getElementById("provstatebox").innerHTML = txt;
      sbox = document.getElementById("selprovstate");
      for (i = 1; i < psArray.length; i++) {
        AddSelectItem(sbox, i, psArray[i][0],psArray[i][1]);
      }
      txt = 'Country: <select name="selcountry" class="select263" id="selcountry"><option value="CAN" selected>Canada</option><option value="USA">United States</option></select>';
      document.getElementById("countrybox").innerHTML = txt;
      SetAddrFields(false);
      document.getElementById("txtaddr").value = "";
      document.getElementById("txtcity").value = "";
      document.getElementById("txtzipcode").value = "";
    } else {
      newcustomer = false;
      txt = 'Country: <input class="select263" name="txtcountry" id="txtcountry" readonly="yes">';
      document.getElementById("countrybox").innerHTML = txt;
      if (id == "0") {
        document.getElementById("ckdefaddr").checked = true;
        document.getElementById("ckaltaddr").checked = false;
        SetDefAddr()
      } else {
        document.getElementById("ckdefaddr").checked = false;
        document.getElementById("ckaltaddr").checked = true;
        SetAddr(id);
      }
    }

  }

  //If this is a new address, make sure we have required fields
  function ValidateAddr() {
    if (!(newcustomer)) return true;  //Edit new customers only
    if (document.getElementById("txtcompname").value == "") {
      alert("Please enter a company name");
      document.getElementById("txtcompname").focus();
      return false;
    }
    if (document.getElementById("txtaddr").value == "") {
      alert("Please enter a valid address");
      document.getElementById("txtaddr").focus();
      return false;
    }
    if (document.getElementById("txtcity").value == "") {
      alert("Please enter a valid city");
      document.getElementById("txtcity").focus();
      return false;
    }
    if (document.getElementById("txtzipcode").value == "") {
      alert("Please enter a valid zip code");
      document.getElementById("txtzipcode").focus();
      return false;
    }
    if (document.getElementById("selprovstate").value == "0") {
      alert("Please enter a valid state or province");
      document.getElementById("selprovstate").focus();
      return false;
    }
    if (document.getElementById("selprovstate").value == "1") {
      alert("Please enter a valid state or province");
      document.getElementById("selprovstate").focus();
      return false;
    }
    return true;
  
  }

  //Returns true if dest is in Canada
  function NotCanadaDest() {
    if (document.getElementById("txtprovstate")) {
      currstate = GetAbbrev(document.getElementById("txtprovstate").value);}
    else {
      currstate = document.getElementById("selprovstate").value;}
    for (i = 1; i < psArray.length; i++) {
      if (psArray[i][0] == currstate) {
        return false;
      }
      if (psArray[i][0] == "1") {
        return true;
      }
    }
  }

  //Returns true if dest is NOT in British Columbia
  function NotBCDest() {
    if (document.getElementById("txtprovstate")) {
      currstate = GetAbbrev(document.getElementById("txtprovstate").value);}
    else {
      currstate = document.getElementById("selprovstate").value;}
    if (currstate == "BC") return (false);
    return (true);
  }

  //Returns true if dest is NOT in HST participating province 
  function NotHSTDest() {
    if (document.getElementById("txtprovstate")) {
      currstate = GetAbbrev(document.getElementById("txtprovstate").value);}
    else {
      currstate = document.getElementById("selprovstate").value;}
    if ((currstate == "BC") || (currstate == "ON") || (currstate == "NB") ||
        (currstate == "NS") || (currstate == "NF"))
      return (false);
    return (true);
  }

  function ProvinceHST() {
    if (document.getElementById("txtprovstate")) {
      currstate = GetAbbrev(document.getElementById("txtprovstate").value);}
    else {
      currstate = document.getElementById("selprovstate").value;}

   if (currstate == "BC")
     return(0.12)
   else if (currstate == "ON")
     return(0.13)
   else if (currstate == "NB")
     return(0.13)
   else if (currstate == "NF")
     return(0.13)
   else if (currstate == "NS")
     return(0.15)
   else
     return(0.0)
  }

  //Loop through the array of state abbreviations and find long name
  function ExpandName(abbrev) {
    for (i = 1; i < psArray.length; i++) {
      if (psArray[i][0] == abbrev) {
        return psArray[i][1];
      }
    }
    return abbrev;
  }

  //Loop through the array of state abbreviations and find abbrev
  function GetAbbrev(stname) {
    for (i = 1; i < psArray.length; i++) {
      if (psArray[i][1] == stname) {
        return psArray[i][0];
      }
    }
    return stname;
  }

  //Add an item to a pick list
  function AddSelectItem(sbox, i, val, txt) {
    if (sbox.options[i]) {
      sbox.options[i].text = txt;
      sbox.options[i].value = val;
    } else {
      var option1 = new Option(txt, val);
        sbox.options[sbox.options.length] = option1;
    }
  }

// This routine uses findfreight.php to retrieve freight charges dynamically via
// the iframe 'workFrame'
  function GetFreightCharges() {
    currcity = document.getElementById("txtcity").value;
    if (document.getElementById("txtprovstate")) {
      currstate = GetAbbrev(document.getElementById("txtprovstate").value);}
    else {
      currstate = document.getElementById("selprovstate").value;}
    if (NotCanadaDest()) {
      freightcharge = 0;
    }

    //alert(lastcity + ":" + currcity);
    //alert(laststate+ ":" + currstate);
    if (((lastcity == currcity) && (laststate == currstate)) || (NotCanadaDest())) {
      lastcity = currcity;
      laststate = currstate;
      completepost();
      return;
    }
    lastcity = currcity;
    laststate = currstate;
    url = "includes/findfreight.php?city=" + currcity + "&st=" + currstate + "&newcust=" + newcustomer;
    document.getElementById("workFrame").src = url;
  }

// This routine is called by findfreight.php when a city cannot be located in the database
  function DisplayCities(cities) {
    document.getElementById('citydiv').style.visibility = 'visible';
    document.getElementById('citydiv').style.top = '280px';
    document.getElementById('citydiv').style.left = '440px';
    document.getElementById('citydiv').style.zIndex = 2;
    SetupCityList(cities);
    document.getElementById('citytxt').innerHTML = 'We are unable to locate "' + document.getElementById('txtcity').value + '" in our shipping database.  Please choose a new city from the list or enter the city name below and click the "Select" button. Please note that if the city is not in our database, we cannot compute freight charges.';
    document.getElementById('cityprovtxt').innerHTML = 'Cities within "' + ExpandName(document.getElementById("selprovstate").value) + '"';
    document.getElementById('txtselectedcity').value = document.getElementById('txtcity').value;
  }
  var cities = new Array();
  
  function SetupCityList(cities) {
    sbox = document.getElementById("citylist");
    k = sbox.length;
    for (i = 0; i < k; i++) {
      sbox.options[0] = null;
    }
    if (cities.length == 0) {
      AddSelectItem(sbox, 1, "0", "None");
    } else {
      for (i = 0; i < cities.length; i++) {
        AddSelectItem(sbox, i, cities[i], cities[i]);
      }
    }
  }

  //Execute this when a new city is selected from the pick list
  function PickCity() {
    document.getElementById('txtselectedcity').value = document.getElementById('citylist').value;
  }

  function SelectCity() {
    hideme('citydiv');
    if (document.getElementById('txtcity').value == document.getElementById('txtselectedcity').value) {
      //DoCompute1();
      CalculateOrderCost();
    } else{
      document.getElementById('txtcity').value = document.getElementById('txtselectedcity').value;
      GetFreightCharges();
    }
  }
// This routine is called by findfreight.php when that page is retrieved.  It builds
// the appropriate javascript to call this routine as pass returned data.
  function postdata(key, val) {
    switch (key) {
      case "Charge":
        freightcharge = parseFloat(val);
        break;
      case "Zone":
        freightzone = val;
        break;
      case "ZoneAdd":
        freightzonemultiplier = parseFloat(val);
        break;
    }
  }
  function completepost() {
    CalculateOrderCost();
    if (uploading_global) FinishUpload();
  }

  //Hides a div
  function hideme(divname) {
    if (document.getElementById) { 
      document.getElementById(divname).style.visibility = 'hidden';
    } 
  }

  //Routine to compute Order Ready dates
  function ComputeReadyDate(days) {
    var sdate = new Date(curyear, curmonth-1, curday, curhour, curmin, cursec);
    k = 0
    //varinfo = varinfo + "Date: " + sdate + "<br>";
    //varinfo = varinfo + "Day: " + sdate.getDay() + "<br>";
    //Position the date to the next business day in case the order is entered on a weekend
    //or holiday.
    while ((sdate.getDay() == 0) || (sdate.getDay() == 6) || (IsHoliday(sdate))) {
      sdate.setDate(sdate.getDate() + 1);
    }

    do {
      sdate.setDate(sdate.getDate() + 1);
      if (!((sdate.getDay() == 0) || (sdate.getDay() == 6) || (IsHoliday(sdate)))) {
        k++;
      }
    }
    while(k < days);

    //document.getElementById("tilebox").innerHTML = varinfo;
    return  (sdate.getMonth()+1) + "/" + sdate.getDate() + "/" + sdate.getFullYear();
  }

  function IsHoliday(dat) {
    zdate = new Date(dat.getFullYear(), dat.getMonth(), dat.getDate());
    for (i=1; i < holiday.length; i++) {
      tdate = new Date(holiday[i].substr(0,4), holiday[i].substr(5,2) - 1, holiday[i].substr(8,2));
      if (tdate.toString() == zdate.toString()) {
        return true;
      }
    }
    return false;
  }


  //dummy routine for popup blocker detection
  function etrap() {return true}

  /********************************************************************************************
  * This routine opens a blank window and populates it with passed content.
  ********************************************************************************************/
  function pop(height, width, content)
  {
    win = window.open("", "", "height=" + height + ",width=" + width +",status=yes")
    if (win != null)
    {
      win.document.open('text/html')
      win.document.write("<HTML><HEAD><TITLE>Signboom Order Submission</TITLE></HEAD><BODY>")
      win.document.write(content)
      win.document.write("</BODY></HTML>")
      win.document.close()
    }
    return win;
  }

  function SetupStandardFields() {

    //Setup Customer fields
    document.orderForm.acctid.value = shiptoArray[0][0];
    document.orderForm.shiptoalt.value = document.getElementById("ckaltaddr").checked;
    document.orderForm.shiptoattn.value = document.getElementById("txtattn").value;
    document.orderForm.shiptoaddr.value = document.getElementById("txtaddr").value;
    document.orderForm.shiptocity.value = document.getElementById("txtcity").value;
    document.orderForm.shiptozip.value = document.getElementById("txtzipcode").value;
    document.orderForm.shiptoaddcust.value = document.getElementById("ckaddcust").checked;
    if (newcustomer) {
      document.orderForm.shiptoname.value = document.orderForm.txtcompname.value;
      document.orderForm.shiptoprov.value = GetAbbrev(document.getElementById("selprovstate").value);
      document.orderForm.shiptocountry.value = (document.orderForm.selcountry.value = "USA") ? "United States" : "Canada";
    } else {
      document.orderForm.shiptoname.value = shiptoArray[document.orderForm.stcompany.value][1];
      document.orderForm.shiptoprov.value = document.orderForm.txtprovstate.value;
      document.orderForm.shiptocountry.value = document.getElementById("txtcountry").value;
    }

    //Setup Totals
    document.orderForm.fsubtotal.value = document.getElementById("txtsubtot").value;
    document.orderForm.fsetup.value = document.getElementById("txtsetup").value;
    document.orderForm.fdiscount.value = document.getElementById("txtdct").value;
    document.orderForm.frushamt.value = document.getElementById("txtrushamt").value;
    document.orderForm.fnet.value = document.getElementById("txtnet").value;
    document.orderForm.fGST.value = document.getElementById("txtGST").value;
    document.orderForm.fPST.value = document.getElementById("txtPST").value;
    document.orderForm.ffreight.value = document.getElementById("txtfreight").value;
    document.orderForm.ftotal.value = document.getElementById("txtordtotal").value;

    //Setup Miscellaneous
    if (document.getElementById("ckstdservice").checked) {
      document.orderForm.fservicetype.value = "STD";
      document.orderForm.readydate.value = document.getElementById("stdready").value;
    }
    if (document.getElementById("ckrushservice").checked) {
      document.orderForm.fservicetype.value = "RUSH";
      document.orderForm.readydate.value = document.getElementById("rushready").value;
    }
    if (document.getElementById("ckhotservice").checked) {
      document.orderForm.fservicetype.value = "HOT";
      document.orderForm.readydate.value = "Call";
    }

    if (document.getElementById("ckppd").checked) document.orderForm.fpickuptype.value = "PPD";
    if (document.getElementById("ckpick").checked) document.orderForm.fpickuptype.value = "PICK";
    if (document.getElementById("ckpack").checked )document.orderForm.fpickuptype.value = "PACK";

    document.orderForm.frefnumber.value = document.getElementById("txtref").value;
    document.orderForm.fnotes.value = document.getElementById("txtnotes").value;
    document.orderForm.fshipdocname.value = document.getElementById("shipdocfile").value;

    document.orderForm.dct.value = document.getElementById("txtdct").value;
    document.orderForm.dctname.value = document.getElementById("txtdctname").value;

  }

  /********************************************************************************************
  ********************************************************************************************/

  function StartEditUpload() {
    StartUpload();
  }

  /********************************************************************************************
  * This function creates a child window (win) containing a form "custform".  It copies information 
  * from elements in the form "orderForm" of the parent window (window) into the form custform 
  * of the child window (win). Custform is submitted to ordprep.php for procesing.
  ********************************************************************************************/

  function StartStandardUpload() {
    var form, elements, i, elm; 

    // Create a form called custform, which has most of the fields of orderForm (from all_order.php).
    content = '<p>&nbsp;</p>'+
      '<p align="center"><font face="Verdana, Arial, Helvetica, sans-serif" size="-1">Please Stand By .... </font></p>'+
      '<form name="custform" method="post" action="ordprep.php">'+
      '<input name="custid" type="hidden" id="custid" value="">' +
      '<input name="readydate" type="hidden" id="readydate" value="">' +
      '<input name="tprod1" type="hidden" id="tprod1" value="">' +
      '<input name="tprod2" type="hidden" id="tprod2" value="">' +
      '<input name="tprod3" type="hidden" id="tprod3" value="">' +
      '<input name="tprod4" type="hidden" id="tprod4" value="">' +
      '<input name="tprod5" type="hidden" id="tprod5" value="">' +
      '<input name="tprod6" type="hidden" id="tprod6" value="">' +
      '<input name="tprod7" type="hidden" id="tprod7" value="">' +
      '<input name="tprod8" type="hidden" id="tprod8" value="">' +
      '<input name="tprod9" type="hidden" id="tprod9" value="">' +
      '<input name="tprod10" type="hidden" id="tprod10" value="">' +
      '<input name="xprod1" type="hidden" id="xprod1" value="">' +
      '<input name="xprod2" type="hidden" id="xprod2" value="">' +
      '<input name="xprod3" type="hidden" id="xprod3" value="">' +
      '<input name="xprod4" type="hidden" id="xprod4" value="">' +
      '<input name="xprod5" type="hidden" id="xprod5" value="">' +
      '<input name="xprod6" type="hidden" id="xprod6" value="">' +
      '<input name="xprod7" type="hidden" id="xprod7" value="">' +
      '<input name="xprod8" type="hidden" id="xprod8" value="">' +
      '<input name="xprod9" type="hidden" id="xprod9" value="">' +
      '<input name="xprod10" type="hidden" id="xprod10" value="">' +
      '<input name="acctid" type="hidden" id="acctid" value="">' +
      '<input name="ordertype" type="hidden" id="ordertype" value="">' +
      '<input name="orderid" type="hidden" id="orderid" value="">' +
      '<input name="dbToken" type="hidden" id="dbToken" value="">' +
      '<input name="custemail" type="hidden" id="custemail" value="">' +
      '<input name="dct" type="hidden" id="" value="">' +
      '<input name="dctname" type="hidden" id="dctname" value="">' +
      '<input name="shiptoalt" type="hidden" id="shiptoalt" value="">' +
      '<input name="shiptoattn" type="hidden" id="shiptoattn" value="">' +
      '<input name="shiptoname" type="hidden" id="shiptoname" value="">' +
      '<input name="shiptoaddr" type="hidden" id="shiptoaddr" value="">' +
      '<input name="shiptocity" type="hidden" id="shiptocity" value="">' +
      '<input name="shiptoprov" type="hidden" id="shiptoprov" value="">' +
      '<input name="shiptozip" type="hidden" id="shiptozip" value="">' +
      '<input name="shiptocountry" type="hidden" id="shiptocountry" value="">' +
      '<input name="shiptoaddcust" type="hidden" id="shiptoaddcust" value="">' +
      '<input name="fsubtotal" type="hidden" id="fsubtotal" value="">' +
      '<input name="fsetup" type="hidden" id="fsetup" value="">' +
      '<input name="fdiscount" type="hidden" id="fdiscount" value="">' +
      '<input name="frushamt" type="hidden" id="frushamt" value="">' +
      '<input name="fnet" type="hidden" id="fnet" value="">' +
      '<input name="fGST" type="hidden" id="fGST" value="">' +
      '<input name="fPST" type="hidden" id="fPST" value="">' +
      '<input name="ffreight" type="hidden" id="ffreight" value="">' +
      '<input name="ftotal" type="hidden" id="ftotal" value="">' +
      '<input name="fservicetype" type="hidden" id="fservicetype" value="">' +
      '<input name="fpickuptype" type="hidden" id="fpickuptype" value="">' +
      '<input name="frefnumber" type="hidden" id="frefnumber" value="">' +
      '<input name="fnotes" type="hidden" id="fnotes" value="">' +
      '<input name="fshipdocname" type="hidden" id="fshipdocname" value="">' +
      '<input name="emailr" type="hidden" id="emailr" value="">' +
      '<input name="promocode" type="hidden" id="promocode" value="">' +
      '<input name="fpromodiscountdollars" type="hidden" id="fpromodiscountdollars" value="">' +
    '</form>';

    // Create a window which contains this new form.
    window.onerror = etrap;
    win = pop(300,400,content);
    window.onerror=null;
  
    // Copy over information from orderForm to custform to populate those hidden elements.
    // If an element doesn't exist in orderForm, set it to blank in custform.
    if (win) {
      // Hide popblock.
      document.getElementById('popblock').style.visibility = 'hidden';

      // Copy over information, using getElementsByTagName if available, and old-style
      // code if it isn't.
      form = win.document.custform;  
      if (document.getElementsByTagName)  {
        elements = form.getElementsByTagName('input');
        for( i=0; elm=elements.item(i); i++) {
          if (elm.getAttribute('type') == "hidden")  {
            //alert("Setting " + elm.name + "  to " + window.document.getElementById(elm.name).value);
            elm.value = (window.document.getElementById(elm.name)) ? window.document.getElementById(elm.name).value : '';
          }
        }
      }
      else {
        // Actually looking through more elements here but the result is the same.
        elements = form.elements;
        for( i=0; elm=elements[i]; i++)  {
          if (elm.type == "hidden") {
            elm.value = (window.document.getElementById(elm.name)) ? window.document.getElementById(elm.name) : '';
          }
        }
      }

      // Submit custform to orderprep.php
      win.document.custform.submit(); 
    } 
    else {
      // Display popblock. Code won't work if popups are not allowed by user's browser.
      document.getElementById('popblock').style.visibility = 'visible';
    }

  }  // end of StartStandardUpload()

  /********************************************************************************************
  *  Minor functions.
  ********************************************************************************************/

  function ShowSizeMsg() {
    document.getElementById('sizeblock').style.visibility = 'visible';
  }

  function ShowSetups() {
    document.getElementById('setupdiv').style.visibility = 'visible';
  }

  /* Function used to prevent user from putting too many characters into a textarea box.*/
  function limitTextLength(textareabox, limit) {
    if (textareabox.value.length > limit) textareabox.value = textareabox.value.substring(0, limit);
  }
