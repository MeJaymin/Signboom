// JavaScript Document

/************************************ Array Indices ***********************************************/

  // categoryArray Indices
  var CatIdIdx = 0;
  var CatCodeIdx = 1;
  var CatNameIdx = 2;
  var CatDescIdx = 3;
  var CatPrntIdx = 4;

  // productArray Indices
  var IdIdx = 0;
  var CodeIdx = 1;
  var NameIdx = 2;
  var DescIdx = 3;
  var CategoryIdx = 4;
  var PrintWidthIdx = 5;
  var PrintLengthIdx = 6;
  var CostDiscIdx = 7;
  var CostNonIdx = 8;
  var CostWasteIdx = 9;
  var SortGroupIdx = 10;
  var SortOrderIdx = 11;
  var DescrImageIdx = 12;
  var DescrTextIdx = 13;
  var DescrFinishingIdx = 14;
  var DescrLimitationsIdx = 15;
  var DescrExtrasIdx = 16;
  var BatchDayIdx = 17;

  // optionCategoryArray Indices
  var OptCatIdIdx = 0;
  var OptCatCodeIdx = 1;
  var OptCatNameIdx = 2;

  // finishingOptionArray Indices
  var FinOptIdIdx = 0;
  var FinOptCatIdx = 1;
  var FinOptGroupIdx = 2;
  var FinOptSetIdx = 3;
  var FinOptTypeIdx = 4;
  var FinOptOrderIdx = 5;
  var FinOptNameIdx = 6;
  var FinOptDescIdx = 7;
  var FinOptCodeIdx = 8;
  var FinOptExtraTimeIdx = 9;
  var FinOptUnitsIdx = 10;
  var FinOptUnitsPerHourIdx = 11;
  var FinOptReferenceIdx = 12;
  var FinOptFixedCostIdx = 13;
  var FinOptVariableCostIdx = 14;
  var FinOptBatchDayIdx = 15;
  var FinOptLaminateProductCodeIdx = 16;

  // productOptionArray Indices
  var ProdOptIdIdx = 0;
  var ProductCodeIdx = 1;
  var FinishingOptionCodeIdx= 2;
  var ValueIdx = 3;

/************************************ Global Variables ********************************************/

  // Remembered Options for the 10 lines in the orderform
  var rememberedOptionsArray = new Array();  // Options that were selected when option popup opened.
  //rememberedOptionsArray[0] = NULL;        // THIS LINE WAS FAILING. first row in this global array is not used
  var optionsRequestedArray;                 // List of all options selected in this this order.

  var uploading_global;                      // False if at quoting stage, True if at uploading stage
  var discountable_cost_global;              // Discountable portion of cost for this order
  var nondiscountable_cost_global;           // Non-discountable portion of cost for this order
  var waste_cost_global;                     // Waste portion of cost for this order
  var ink_cost_global;                       // Ink cost for this order non-discountable)
  var options_cost_global;                   // Total cost of options for whole order.
  var printed_square_footage_global;         // Total printed square footage for this order
  var total_order_discount_global;           // Dollar value of discount for this whole order.
  var final_discount_name_global = "";       // Final discount name
  var tiling_global;
  var setup_cost_global;                     // an admin-configurable amount if a printable product is ordered
  var ie8_alert_global;                      // Set this to true if no finishing options detected.
  var warned_about_batch_global = false;     // Only warn user about batch delay once each visit.

  /************************************ AJAX  Functions *********************************************/

  function DetermineReadyDate(printed_square_footage_global, extra_days, order_wide_batch_day) {

    // Create AJAX requester object.
    try {
      var requester = new XMLHttpRequest();
    }
    catch (error) {
      try {
        var requester = new ActiveXObject("Microsoft.XMLHTTP");
      }
      catch (error) {
        var requester = null;
      }
    }

    // Associate the requester with a file on the server. Pass in parameters.
    if (requester != null) {
      // Set up a timer so we don't wait forever for the reply fro mthe server.
      var readyLink = this;
      readyLink._timer = setTimeout(function() {
        requester.abort();
        alert("The server timed out while making your request. Please wait a few minutes and then click the Quote or Submit button again.");
      }, 10000);
      
      // Later switch this to POST.
      requester.open("GET", "determine_ready_date_171116.php?square_footage=" + encodeURIComponent(printed_square_footage_global) + "&days_for_finishing=" + encodeURIComponent(extra_days) + "&batch_start_day=" + encodeURIComponent(order_wide_batch_day), false);
      requester.onreadystatechange = function() {
        // Stop timer once we hear back from server.
        if (requester.readyState == 4) {
          clearTimeout(readyLink._timer);
          // Handle situation where the PHP file is missing from the server.
          if (requester.status == 200 || requester.status == 304) {
            // Parse out the two ready dates.
            //alert("The ready dates returned were: " + requester.responseText);
            var ready_dates = requester.responseText.split("~");
            // Display the three ready dates
             document.getElementById("stdready").value = ready_dates[1];
             document.getElementById("rushready").value = ready_dates[2];
             document.getElementById("hotready").value = "Call";    
	     //If the rushready is the same as the stdready, disable rush ready. Otherwise enable it.
	     if (ready_dates[2] == ready_dates[1])
	     {
	       // If rush checkbox is ticked, change to standard delivery.
               if (document.getElementById("ckrushservice").checked)
	       {
                 document.getElementById("ckstdservice").checked = true;
                 document.getElementById("ckrushservice").checked = false;
	       }
	       // Then disable rush option.
               document.getElementById("ckrushservice").disabled = true;
               document.getElementById("rushready").disabled = true;
               document.getElementById("txtrushval").disabled = true;
	     }
	     else
	     {
               document.getElementById("ckrushservice").disabled = false;
               document.getElementById("rushready").disabled = false;
               document.getElementById("txtrushval").disabled = false;
	     }
          }
          else {
            alert("The code that calculates the ready date is currently inaccessible. We apologize for the inconvenience.Please telephone us at (604) 881-0363 to complete your order.");
          }
        }
      };

      // Call the requester.  
      requester.send(null);
    }

  }

  function GetPromoDetails(promo_code) {

    var details;

    // Create AJAX requester object.
    try {
      var requester = new XMLHttpRequest();
    }
    catch (error) {
      try {
        var requester = new ActiveXObject("Microsoft.XMLHTTP");
      }
      catch (error) {
        var requester = null;
      }
    }

    // Associate the requester with a file on the server. Pass in parameters.
    if (requester != null) {
      // Set up a timer so we don't wait forever for the reply from the server.
      var readyLink = this;
      readyLink._timer = setTimeout(function() {
        requester.abort();
        alert("The server timed out while making your request. Please wait a few minutes and then click the Quote or Submit button again.");
      }, 10000);
      
      // Later switch this to POST.
      requester.open("GET", "get_promo_details.php?promo_code=" + encodeURIComponent(promo_code), false);
      requester.onreadystatechange = function() {
        // Stop timer once we hear back from server.
        if (requester.readyState == 4) {
          clearTimeout(readyLink._timer);
          // Handle situation where the PHP file is missing from the server.
          if (requester.status == 200 || requester.status == 304) {
            // Parse out the two ready dates.
            //alert("The promo code details returned were: " + requester.responseText);
            document.getElementById("promodetails").value = requester.responseText;
          }
          else {
            alert("The code that calculates the promo code discount is currently inaccessible. We apologize for the inconvenience.Please telephone us at (604) 881-0363 to receive your discount.");
          }
        }
      };

      // Call the requester.  
      requester.send(null);
    }

  }


  /************************************ Helper Functions *********************************************/

  function isNumeric(number) {
    return !isNaN(parseFloat(number)) && isFinite(number);
  }

  function isPrintableCategory(category_code) {
    var k;
    for (k = 1; k <= numberOfCategories; k++) {
      if (category_code == categoryArray[k][CatCodeIdx]) {
        if (categoryArray[k][CatPrntIdx] == 0) return false;
        else return true;
        break;
      }
    }
  }

  function HelpCategory(linenum) {
    var category_code, category_name, category_found, k, popup, close_message;

    category_found = false;
    // Identify which category has been selected.
    category_code = document.getElementById("cat" + linenum).value;
    for (k = 0; k <= numberOfCategories; k++) {
      if (categoryArray[k][CatCodeIdx] == category_code) {
        category_name = categoryArray[k][CatNameIdx];
        category_found = true;
        break;
      }
    }
    popup = document.getElementById("category_description");
    close_message = "<div class=\"closediv\"><a href=\"javascript:hideme('category_description')\">Close</a></div>";

    if ((category_code.length > 0) && (category_found)) {
      popup.innerHTML = categoryArray[k][CatDescIdx] + close_message;
    }
    else {
      popup.innerHTML = "Once you have selected a category, you can click the ? button to view information about that category." + close_message;
    }
    popup.style.visibility = "visible";
  }

  function HelpProduct(linenum) {
    var product_code, product_found, k, popup; 
    var popup_heading, popup_details_start, popup_details_end, popup_descr_start, popup_descr_end;
    var popup_image, popup_text, popup_finishing, popup_limitations, popup_extras, close_message;
    var popup_batch_notice;

    product_found = false;
    // Identify which product has been selected.
    product_code = document.getElementById("pr" + linenum).value;
    for (k = 0; k <= numberOfProducts; k++) {
      if (productArray[k][CodeIdx] == product_code) {
        batch_day = productArray[k][BatchDayIdx];
        product_found = true;
        break;
      }
    }
    popup = document.getElementById("product_description");

    popup_heading = "<h2>" + productArray[k][NameIdx] + "</h2>";
    popup_details_start = "<div class=\"details\">";
    popup_details_end = "</div>";
    popup_descr_start = "<div class=\"description\">";
    popup_descr_end = "</div>";
    popup_image = "<img src=\"http://www.signboom.com/product-files/" + productArray[k][DescrImageIdx] + "\" alt=\"" + productArray[k][NameIdx] + "\">";
    popup_text = productArray[k][DescrTextIdx];
    popup_finishing = "<div class=\"finishing\"><h4>Standard Finishing:</h4>" + productArray[k][DescrFinishingIdx] + "</div>";
    popup_limitations = "<div class=\"limitations\"><h4>Limitations:</h4>" + productArray[k][DescrLimitationsIdx] + "</div>";
    popup_extras = "<div class=\"extras\">" + productArray[k][DescrExtrasIdx] + "</div>";
    popup_close_message = "<div class=\"closediv\"><a href=\"javascript:hideme('product_description')\">Close</a></div>";

    if (batch_day > 0)
      popup_batch_notice = '<div class="batch_notice"><h4>Batch Item</h4>This product is printed only once a week, on a dedicated batch day.<br><br>Including this item in a multi-line order can result in a delay in the ready date. If the calculated ready date is not early enough for you, you may wish to submit this item in a separate order.<br><br>To remove an item from the order, enter a zero in the quantity box for that line, and then click the <b>Quote Order</b> button again.</div>';
    else
      popup_batch_notice = '';

    if ((product_code.length > 0) && (product_found)) {
      popup.innerHTML = popup_heading + popup_details_start + popup_descr_start + popup_image + popup_text + popup_descr_end + popup_finishing + popup_limitations + popup_batch_notice + popup_details_end + popup_extras + popup_close_message;
    }
    else {
      popup.innerHTML = "Once you have selected a product, you can click the ? button to view detailed information about that product.  You must select a category before you can select a product." + close_message;
    }
    popup.style.visibility = "visible";
  }

  function HelpOption(option_code, batch_day_option) {
    var option_name, k, popup, close_message;

    // Identify which option has been selected.
    for (k = 0; k <= numberOfFinishingOptions; k++) {
      if (finishingOptionArray[k][FinOptCodeIdx] == option_code) {
        option_name = finishingOptionArray[k][FinOptNameIdx];
        break;
      }
    }

    popup = document.getElementById("option_description");
    if (batch_day_option > 0)
      popup_batch_notice = '<div class="batch_notice"><h4>Batch Option</h4>This finishing option is applied only once a week, on a dedicated batch day.<br><br>Including this finishing option in a multi-line order can result in a delay in the ready date. If the calculated ready date is not early enough for you, you may wish to submit this line item in a separate order.<br><br>To remove an item from the order, enter a zero in the quantity box for that line, and then click the <b>Quote Order</b> button again.</div>';
    else
      popup_batch_notice = '';
    close_message = "<div class=\"closediv\"><a href=\"javascript:hideme('option_description')\">Close</a></div>";
    popup.innerHTML = finishingOptionArray[k][FinOptDescIdx] + popup_batch_notice + close_message;
    popup.style.visibility = "visible";
  }

  function SaveFinishingChanges(div_id, linenum) {
    var product_code;

    document.getElementById(div_id).className = "div_hidden";
    product_code = document.getElementById("pr" + linenum).value ;
    if (IsStandardFinishing(linenum, product_code))
      document.getElementById("finishing" + linenum).innerHTML = 'Standard';
    else
      document.getElementById("finishing" + linenum).innerHTML = '<span class="highlighted_pink">Custom</span>';
    if (IsBatchFinishing(linenum, product_code))
      document.getElementById("finishing" + linenum).innerHTML = '<span class="highlighted_red">Batch</span>';
  }

  function CancelFinishingChanges(div_id, linenum) {
    var product_code;


    RestoreRememberedOptions(linenum);
    document.getElementById(div_id).className = "div_hidden";
    product_code = document.getElementById("pr" + linenum).value ;
    if (IsStandardFinishing(linenum, product_code))
      document.getElementById("finishing" + linenum).innerHTML = 'Standard';
    else
      document.getElementById("finishing" + linenum).innerHTML = '<span class="highlighted_pink">Custom</span>';

    if (IsBatchFinishing(linenum, product_code))
      document.getElementById("finishing" + linenum).innerHTML = '<span class="highlighted_red">Batch</span>';
  }

  function DeleteFinishingOptions(linenum) {
    var popup, input_items, div_items, i;

    popup = document.getElementById("finishing-popup-" + linenum);

    // First delete all the input items we can find.  Do this starting from the end of the list
    // moving backwards.  If you start at the beginning of the list, things end up being
    // renumbered as you go and you only end up deleting every second option.
    input_items = popup.getElementsByTagName("input");
    for (i = input_items.length - 1; i >= 0; i--) {
      // use the removeChild method of the parent to remove the input node
      input_items[i].parentNode.removeChild(input_items[i]);
    }

    // Then hide all divs we can find except those with name finishing-popup-title-<linenum>
    // and finishing-popup-message-<linenum> and finishing-popup-buttons-<linenum>
    div_items = popup.getElementsByTagName("div");
    for (i = 0; i < div_items.length; i++) {
      if ((div_items[i].id != "finishing-popup-title-" + linenum) &&
          (div_items[i].id != "finishing-popup-message-" + linenum) &&
          (div_items[i].id != "finishing-popup-buttons-" + linenum)) {
        // use the removeChild method of the parent to remove the input node
        div_items[i].className = "div_hidden";
      }
    }

    document.getElementById("finishing" + linenum).innerHTML = "Standard";
  }

  function IsStandardFinishing(linenum, product_code) {
    var i, j, option, option_value;


    // First, make an array of all the options selected for this line.
    RememberOptionsSelected(linenum, product_code);

    // Go through all those options...
    // TO DO: Check whether this should start with i = 1; See line 70 above.
    for (i = 0; i < rememberedOptionsArray[linenum].length; i++) {
      option = rememberedOptionsArray[linenum][i];
      // find the applicable product-option pair
      for (j = 1; j <= numberOfPairs; j++) {
        if ((option == productOptionArray[j][FinishingOptionCodeIdx]) && (product_code == productOptionArray[j][ProductCodeIdx])) {
          option_value = productOptionArray[j][ValueIdx];
          // if that is not a default/standard option, the finishing is NOT standard
          if (option_value != 2) return false;
        }
      }
    }
    return true;
  }

  function IsBatchFinishing(linenum, product_code) {
    var i, j, option, batch_day;

    // First, make an array of all the options selected for this line.
    RememberOptionsSelected(linenum, product_code);

    // Go through all those options...
    batch_day = 0;
    // TO DO: Check whether this should start with i = 1; See line 70 above.
    for (i = 0; i < rememberedOptionsArray[linenum].length; i++) {
      option = rememberedOptionsArray[linenum][i];
      // find index where finishingOptionArray[index][FinOptCodeIdx] == option
      for (j = 0; j < finishingOptionArray.length; j++) {
        if (finishingOptionArray[j][FinOptCodeIdx] == option) {
          batch_day = finishingOptionArray[j][FinOptBatchDayIdx];
	  break;
	}
      }
      // Check if it is a batch option.
      if (batch_day > 0 ) return true;
    }
    return false;
  }


  /********************************************************************************************
  * ChangeCategory():  When a category is selected, set the appropriate options for
  * the product column.
  ********************************************************************************************/

  function ChangeCategory(linenum) {
    var i, j, category_code, display_name;

    // Identify which category has been selected.
    category_code = document.getElementById("cat" + linenum).value;

    // Clear the product options. 
    document.getElementById("pr" + linenum).length = 0;

    // List the products in that category.
    // The php code that populated productArray has ordered things in the order we want, with
    // separators inserted. Any time we see product id = 0, we put in a separator
    document.getElementById("pr" + linenum).options[0] = new Option("Select Product", "ASK_SELECT", 1);
    for (i = 1, j = 1; i <= numberOfProducts; i++) {
      if (productArray[i][CategoryIdx] == category_code) {
        if (productArray[i][IdIdx] == 0) {
          document.getElementById("pr" + linenum).options[j] = new Option("", "", 0); 
          document.getElementById("pr" + linenum).options[j].disabled = true;
          document.getElementById("pr" + linenum).options[j].setAttribute("class", "select_separator");
          j++;
        }
        else {
	  display_name = productArray[i][CodeIdx] + ' - ' + productArray[i][NameIdx];
          document.getElementById("pr" + linenum).options[j] = 
             new Option(display_name, productArray[i][CodeIdx], 0); 
          j++;
        }
      }
    }

    // Clear the finishing options.
    DeleteFinishingOptions(linenum);

    // Disable the width, height and filename/browse if the product does not involve printing.
    if (isPrintableCategory(category_code)) {
      // Enable the fields.
      document.getElementById("heighti" + linenum).disabled = false;
      document.getElementById("widthi" + linenum).disabled = false;
      document.getElementById("file" + linenum).disabled = false;
    }
    else {      
      // Fill in and disable the fields.
      document.getElementById("heighti" + linenum).disabled = true;
      document.getElementById("widthi" + linenum).disabled = true;
      document.getElementById("file" + linenum).disabled = true;
    }

    // Set the default product selection to the most popular product in that category,
    // when Rigid, Banner or Adhesive is selected.
    if (category_code == 'ADHESIVE')
    {
      document.getElementById("pr" + linenum).value = 'SAV';
      ChangeProduct(linenum);
    }
    else if (category_code == 'RIGID')
    {
      document.getElementById("pr" + linenum).value = 'COR04';
      ChangeProduct(linenum);
    }
    else if (category_code == 'BANNER')
    {
      document.getElementById("pr" + linenum).value = 'SSB';
      ChangeProduct(linenum);
    }
 
    ClearLine(linenum);
  }

  /********************************************************************************************
  * ChangeProduct():  When a product is selected, remember the appropriate options for
  * the finishing popup.
  ********************************************************************************************/

  function ChangeProduct(linenum) {
    var category_code, category_name, product_code, product_index, product_name; 
    var i, j, k, m, n;
    var popup_title;
    var options_in_this_category, total_number_options;
    var finishing_category_code, finishing_category_name; 
    var pair_product_code, pair_option_code, pair_option_value, pair_option_name;
    var radio_button, radio_text, info_image, info_link, run_function, info_br, button_container;
    var batch_day_product, batch_day_option;

    // Clear the finishing options.
    DeleteFinishingOptions(linenum);

    // Identify which category has been selected.
    category_code = document.getElementById("cat" + linenum).value ;
    for (k = 0; k <= numberOfCategories; k++) {
      if (categoryArray[k][CodeIdx] == category_code) {
        category_name = categoryArray[k][CatNameIdx];
      }
    }

    // Identify which product has been selected.
    product_code = document.getElementById("pr" + linenum).value ;
    for (k = 0; k <= numberOfProducts; k++) {
      if (productArray[k][CodeIdx] == product_code) {
        product_index = productArray[k][IdIdx];
        product_name = productArray[k][NameIdx];
        batch_day_product = productArray[k][BatchDayIdx];
	if (batch_day_product == 0) 
	  document.getElementById("help_product_" + linenum).src = 'images/info.png';
        else
	  document.getElementById("help_product_" + linenum).src = 'images/info-red.png';

      }
    }

    popup_title = "<b>Category:</b> " + category_name + "<br><br><b>Product:</b> " + product_name + " (" + product_code + ")</b>";
    document.getElementById("finishing-popup-title-" + linenum).className = "div_displayed";

    total_number_options = 0;
    // Find all the finishing options that apply to that product.(e.g. AF through  RI)
    for (i = 1; i <= numberOfOptionCategories; i++) {
      finishing_category_code = optionCategoryArray[i][OptCatCodeIdx];
      finishing_category_name = optionCategoryArray[i][OptCatNameIdx];
      options_in_this_category = 0;

      // Get all the applicable options in this option category that are valid FOR THE SELECTED PRODUCT 
      for (j = 1; j <= numberOfPairs; j++) {
        pair_product_code = productOptionArray[j][ProductCodeIdx];
        pair_option_code = productOptionArray[j][FinishingOptionCodeIdx];
        pair_option_value = productOptionArray[j][ValueIdx];

        if ((pair_product_code == product_code) &&
            (pair_option_code.substr(0,2) == finishing_category_code) && 
            (pair_option_value > 0)) {

          // Display the heading for this option category 
          if (options_in_this_category == 0)
            document.getElementById("finishing-popup-category-" + finishing_category_code + "-" + linenum).innerHTML = 
              "<br><b>" + finishing_category_name + "</b><br><br>";

          // Find the index and name of the finishing option.
          for (m = 1; m <= numberOfFinishingOptions; m++) {
            if (finishingOptionArray[m][FinOptCodeIdx] == pair_option_code)
              pair_option_name = finishingOptionArray[m][FinOptNameIdx];
          }

          // find index where finishingOptionArray[index][FinOptCodeIdx] == pair_option_code
	  batch_day_option = 0;
          for (n = 0; n < finishingOptionArray.length; n++) {
            if (finishingOptionArray[n][FinOptCodeIdx] == pair_option_code) {
              batch_day_option = finishingOptionArray[n][FinOptBatchDayIdx];
	      break;
	    }
          }

          // Add a button to this radio set.
          radio_button = document.createElement("input");
          radio_button.setAttribute("type", "radio");
          radio_button.setAttribute("name", finishing_category_code + "-" + linenum);
          radio_button.setAttribute("value", pair_option_code);
          radio_button.setAttribute("id", pair_option_code + "-" + linenum);

          // Select the default finishing options for this product.
          if (pair_option_value == 2) radio_button.checked = true; 

          radio_text = document.createTextNode(pair_option_name);

          info_image = document.createElement("img");
	  if (batch_day_option > 0)
            info_image.setAttribute("src", "images/info-red.png");
          else
            info_image.setAttribute("src", "images/info.png");
          info_image.setAttribute("alt", "?");
          info_image.setAttribute("height", "16");
          info_image.setAttribute("width", "16");
          info_image.setAttribute("class", "option_help");

          info_link = document.createElement("a");
          //run_function = "javascript:HelpOption('" + pair_option_code + "')";
          run_function = "javascript:HelpOption('" + pair_option_code + "', '" + batch_day_option + "')";
          info_link.setAttribute("href", run_function);
          info_link.appendChild(info_image);

          info_br = document.createElement("br");

          button_container = document.getElementById("finishing-popup-category-" + finishing_category_code + "-" + linenum);
          button_container.style.whiteSpace = "pre" ; // preserve whitespace
          button_container.appendChild(radio_button);
          button_container.appendChild(radio_text);
          button_container.appendChild(info_link);
          button_container.appendChild(info_br);
          button_container.className = "div_displayed";

          options_in_this_category++;
          total_number_options++;
          
        }
      }

      //If there is one or more valid options in this option category for this product...
      if (options_in_this_category > 0) {
        document.getElementById("finishing-popup-category-" + finishing_category_code + "-" + linenum).className = 
          "div_displayed";
      }
    }

    document.getElementById("finishing-popup-title-" + linenum).innerHTML = popup_title + "<br>";
    if (total_number_options == 0) {
      document.getElementById("finishing-popup-message-" + linenum).innerHTML = "<br>There are no finishing options available for this product.<br><br>";
      document.getElementById("finishing-popup-message-" + linenum).className = "div_displayed";
    }

    ClearLine(linenum);
  }

  /********************************************************************************************
  * RememberOptionsSelected(): Remember the finishing options that have been selected in a 
  *  global variable so that CancelFinishingChanges() can access them and restore them.
  ********************************************************************************************/

  function RememberOptionsSelected(linenum, product_code) {
    var array_index, i, j; 
    var finishing_category_code, pair_product_code, pair_option_code, pair_option_value; 
    var radio_button;

    // Create new array of options for this line number.
    rememberedOptionsArray[linenum] = new Array();
    array_index = 0;

    // Find all the finishing options that apply to the selected product.(e.g. AF through  RI)
    for (i = 1; i <= numberOfOptionCategories; i++) {
      finishing_category_code = optionCategoryArray[i][OptCatCodeIdx];

      // Get all the applicable options in this option category that are valid FOR THE SELECTED PRODUCT 
      for (j = 1; j <= numberOfPairs; j++) {

        pair_product_code = productOptionArray[j][ProductCodeIdx];
        pair_option_code = productOptionArray[j][FinishingOptionCodeIdx];
        pair_option_value = productOptionArray[j][ValueIdx];

        if ((pair_product_code == product_code) &&
            (pair_option_code.substr(0,2) == finishing_category_code) && 
            (pair_option_value > 0)) {
          radio_button = document.getElementById(pair_option_code + "-" + linenum);
          if (radio_button.checked == true) {
            rememberedOptionsArray[linenum][array_index] = pair_option_code; 
            array_index++;
          }

        }
      }
    }
  }

  /********************************************************************************************
  * RestoreRememberedOptions(): Restore the finishing options that were in place when the user
  * displayed the popup.  For use when they have clicked Cancel instead of Save Changes.
  ********************************************************************************************/

  function RestoreRememberedOptions(linenum) {
    var array_index, pair_option_code, radio_button;

    for (array_index = 0; array_index < rememberedOptionsArray[linenum].length; array_index++) {
      pair_option_code = rememberedOptionsArray[linenum][array_index];
      radio_button = document.getElementById(pair_option_code + "-" + linenum);
      radio_button.checked = true; 
    }

  }

  /********************************************************************************************
  * ChangeFinishing():  When user clicks Edit button to change finishing, display the options
  * that apply to the selected product and remember their selections.
  ********************************************************************************************/

  function ChangeFinishing(linenum) {
    var product_code, product_index, product_name, k; 

    // Identify which product has been selected.
    product_code = document.getElementById("pr" + linenum).value ;

    if ((product_code == "ASK_SELECT") || (product_code == "")){
      alert("You must choose a category and product before you can select the finishing options.");
    }
    else {
      for (k = 0; k <= numberOfProducts; k++) {
        if (productArray[k][CodeIdx] == product_code) {
          product_index = productArray[k][IdIdx];
          product_name = productArray[k][NameIdx];
        }
      }

      // Remember the finishing options that have been selected in a global variable so that 
      // CancelFinishingChanges() can access them and restore them.
      RememberOptionsSelected(linenum, product_code);

      // Display the div that holds the finishing options for this line in the order form.
      document.getElementById("finishing-popup-" + linenum).className = "div_displayed";

      ClearLine(linenum);

    }
  }

  /********************************************************************************************
  * ResetFinishingToStandard():  When user clicks ResetFinishing button in popup for a line
  * in the order form, reset the values to their defaults.
  ********************************************************************************************/

  function ResetFinishingToStandard(linenum) {
    var product_code; 
    var finishing_category_code, finishing_category_name;
    var i, j, m;
    var pair_product_code, pair_option_code, pair_option_value, pair_option_name;
    var radio_button;
    var message = "";

    // Identify which product has been selected.
    product_code = document.getElementById("pr" + linenum).value ;

    // Find all the finishing options that apply to that product.(e.g. AF through  RI)
    for (i = 1; i <= numberOfOptionCategories; i++) {
      finishing_category_code = optionCategoryArray[i][OptCatCodeIdx];
      finishing_category_name = optionCategoryArray[i][OptCatNameIdx];

      // Find the default option in this option category for this product.
      for (j = 1; j <= numberOfPairs; j++) {
        pair_product_code = productOptionArray[j][ProductCodeIdx];
        pair_option_code = productOptionArray[j][FinishingOptionCodeIdx];
        pair_option_value = productOptionArray[j][ValueIdx];

        if ((pair_product_code == product_code) &&
            (pair_option_code.substr(0,2) == finishing_category_code) && 
            (pair_option_value == 2)) {

          // Find the index and name of the finishing option.
          for (m = 1; m <= numberOfFinishingOptions; m++) {
            if (finishingOptionArray[m][FinOptCodeIdx] == pair_option_code)
              pair_option_name = finishingOptionArray[m][FinOptNameIdx];
          }

          // Identify the radio button for this finishing option and check it.
          radio_button = document.getElementById(pair_option_code + "-" + linenum);
          radio_button.checked = true; 
          message += finishing_category_name + " has been set to " + pair_option_name + "\n\n";
        }
      }
    }
    if 
      (message.length > 0) alert(message);
    else 
      alert("There are no finishing options associated with this product.");

    SaveFinishingChanges("finishing-popup-" + linenum, linenum);
  }

  /********************************************************************************************
  * This routine clears a line of calculated totals.  It's used when any info on a line changes.
  ********************************************************************************************/
  function ClearLine(idx) {
    tiling_global = false;
    document.getElementById("piececost" + idx).innerHTML = "&nbsp;";
    document.getElementById("totlinecost" + idx).innerHTML = "&nbsp;";
    ClearTotals();
  }

  /********************************************************************************************
  * This routine carries out data validation.
  ********************************************************************************************/
  function validatepage() {
    var i, catcount, itmcount, category, product;

    // If a new address is entered, make sure we have a complete address
    if (!(ValidateAddr())) return false;

    // make sure we have one category specified at least
    catcount = 0;
    for (i = 1; i <= linecount; i++) {
      if (document.getElementById("quantity" + i).value == "0") ClearDetails(i);
      category = document.getElementById("cat" + i).value;
      if ((category != "0") && (category != "")) {
        catcount++;
      }
    }
    if (catcount == 0) {
      SetError("Please select a category.");
      document.orderForm.cat1.focus();
      return false;
    }

    // make sure we have one product specified at least
    itmcount = 0;
    for (i = 1; i <= linecount; i++) {
      if (document.getElementById("quantity" + i).value == "0") ClearDetails(i);
      product = document.getElementById("pr" + i).value;
      if ((product != "0") && (product != "")) {
        itmcount++;
        if (!(valLineItem(i))) {
          return false;
        }
      }
    }
    if (itmcount == 0) {
      SetError("Please select a product.");
      document.orderForm.pr1.focus();
      return false;
    }

    return true;
  }

  /********************************************************************************************
  * This routine validates individual line items.
  ********************************************************************************************/

  function valLineItem(idx) {
    var category, product, quantity, heightin, widthni, filename;
    var wsproduct, wsquantity, wshin, wswin, wsfilename;

    category = document.getElementById("cat" + idx);
    product = document.getElementById("pr" + idx);
    quantity = document.getElementById("quantity" + idx);
    heightin = document.getElementById("heighti" + idx);
    widthin = document.getElementById("widthi" + idx);
    filename = document.getElementById("file" + idx);

    // If category, quantity and filename are not specified, the line is empty.  That's ok.
    if ((category.value == "0") && (quantity.value == "") && (filename.value == "")) {
      return true;
    }

    wscategory = category.value;
    wsproduct  = product.value;
    wsquantity = parseInt(quantity.value);
    wshin      = parseInt(heightin.value);
    wswin      = parseInt(widthin.value);
    wsfilename = filename.value;

    if ((wscategory == "ASK_SELECT") || (wscategory == 0)) {
      SetError("Please select a category on line " + idx + ".");
      category.focus();
      return false;
    }

    if ((wsproduct == "ASK_SELECT") || (wsproduct == "")) {
      SetError("Please select a product on line " + idx + ".");
      product.focus();
      return false;
    }
    
    if ((wsquantity <= 0) || (isNaN(wsquantity))) {
      SetError("Please enter a quantity on line " + idx + ".");
      quantity.focus();
      return false;
    }

    if (isPrintableCategory(wscategory)) {
      if (isNaN(wshin) || (wshin < 0)) {
        SetError("Please select a valid height on line " + idx + ".");
        heightin.focus();
        return false;
      }
      if (isNaN(wswin) || (wswin < 0)) {
        SetError("Please select a valid width on line " + idx + ".");
        widthin.focus();
        return false;
      }
      if ((wsproduct == "GSCS") || (wsproduct == "GDTS"))
      {
        if (!(((wshin == '60') && (wswin == '120')) || ((wshin == '120') && (wswin == '60'))))
        {
          SetError("When you order G-Floor sheets, your file size MUST be 60 inches by 120 inches. Please correct the file size and the file itself (if necessary) on line " + idx + ".");
          widthin.focus();
          return false;
        }
      }
      if ((wsfilename == "") && uploading_global) {
        SetError("Please enter a file name on line " + idx + ".");
        filename.focus();
        return false;
      }
    }

    return ValName(filename);
  }

  /********************************************************************************************
  * DisplayShipping (): Display costs of various shipping options.
  ********************************************************************************************/

  function DisplayShipping() {
    // freightcharge, packfee and shipmult are globals; see all_order.php
    var freight, a1;

    a1 = new ToFmt(0.0);
    document.getElementById("pickcost").value = "$" + strltrim(a1.fmtF(9,2));

    a1 = new ToFmt(packfee);
    document.getElementById("packcost").value = "$" + strltrim(a1.fmtF(9,2));

    if (freightcharge > 0 ) {
      freight = parseFloat(freightcharge) * parseFloat(shipmult) + parseFloat(packfee);
      //alert("DISPLAY SHIPPING\n\nfreight charge = " + freightcharge + "\n\nshipmult = " + shipmult + "\n\npackfee = " + packfee + "\n\nTOTAL FREIGHT CHARGE: " + freight);
      a1 = new ToFmt(freight);
      document.getElementById("ppdcost").value = "$" + strltrim(a1.fmtF(9,2));
    } else {
      freight = parseFloat(0.0);
      document.getElementById("ppdcost").value = "Call";
    }

    // Include cost of selected shipping option in total.
    if (document.getElementById("ckpick").checked) 
      freight = parseFloat(0.0);
    else if (document.getElementById("ckpack").checked) 
      freight = parseFloat(packfee); 
    /*
    else if (document.getElementById("ckppd").checked) 
      leave freight as calculated in above code paragraph
    */
    return(freight);
  }

  /********************************************************************************************
  * CalculateTax(): 
  ********************************************************************************************/

  function CalculateTax(net_cost, total_freight) {
    var PST, GST;

    /****** Old code.  Pre July 1, 2010. PST in place on product not shipping. ********/
    /****** Customer pays PST based on whether they have PST numbers or not. **********/
    /****** This code was reinstated when HST was rolled back in 2013.      ***********/
    if (((document.getElementById("ckppd").checked) && (NotBCDest())) || (acctpst != "")) {
      PST = 0; }
    else {
      PST = ((net_cost + total_freight) * .07);}

    if ((document.getElementById("ckppd").checked) && (NotCanadaDest())) {
      GST = 0; }
    else {
      GST = ((net_cost + total_freight) * .05);}

    /****** New code.  Post July 1, 2010. No PST. Apply either GST or HST to total cost. ********/
    /****** Customers pay HST based on whether they are in a participating province.*************/
    /*
    if (((document.getElementById("ckppd").checked) && (NotHSTDest())) ) {
      HST = 0; 
      if ((document.getElementById("ckppd").checked) && (NotCanadaDest())) {
        GST = 0; 
      }
      else {
        GST = ((net_cost + total_freight) * .05);
      }
    }
    else {
      GST = 0;
      HST = ((net_cost + total_freight) * ProvinceHST()); // applies province-specific HST to order
    }
    */

    //alert("CALCULATE TAX\n\nHST = " + HST + "\n\nGST = " + GST);
    return [PST, GST];

  }

  /********************************************************************************************
  * CalculateLineCost(): Determine waste, square/lineal footage and costs.
  ********************************************************************************************/

  function CalculateLineCost(linenum) {
    var product_code, category_code, product, i, j, k, m, n;
    var heightin, widthin; 
    var wsheight;    // the height of the printed sign in inches
    var wswidth;     // the width of the printed sign in inches
    var printwidth;  // the width of the media in inches
    var printlength;  // the length of the media in inches
    var wsperimeter; // the linear dimension of the sign perimeter in ft
    var wsarea;      // the area of one side of the sign in sqft
    var quantity;    // the number of signs to print
    var waste_sqft_per_sign; // material wasted per sign (if optimum quantity was purchased)
    var cost_discountable;
    var cost_nondiscountable;
    var cost_waste;
    var cost_options;
    var sign_sqfootage;
    var printed_sqfootage;
    var waste_sqfootage;
    var total_sqfootage;
    var line_discountable_cost;
    var a1;
    var popup, input_items, the_option_id, data, the_option_name; 
    var varcost, fixedcost, equation, extra_days, option_set, laminate_product_code;
    var array_length, item_found, option_cost; 
    var cost_per_piece, cost_line_total; 
    var number_of_options;
    var laminate_print_width, laminate_print_length; 
    var laminate_cost_discountable, laminate_cost_nondiscountable,  laminate_cost_waste;

    // COMPUTE SQUARE FOOTAGE, WASTE AND PERIMETER AMOUNTS
    // ===================================================

    product_code = document.getElementById("pr" + linenum).value
    category_code = document.getElementById("cat" + linenum).value

    // Identify the array index for that product.
    product = 0;
    for (j = 1; j <= numberOfProducts; j++) {
      if (productArray[j][CodeIdx] == product_code) {
        product = j;
        break;
      }
    }
    if (product == 0) {
      ClearDetails(linenum);
      return;
    }

    // if product is a fixture (e.g. stand, accessory or frame) no printing is involved
    // so calculations are much simpler
    if (!isPrintableCategory(category_code)) {
      quantity = parseFloat(document.getElementById("quantity" + linenum).value);

      //This is the discountable portion of the fixture cost 
      cost_discountable = parseFloat(productArray[product][CostDiscIdx]) * quantity;
      discountable_cost_global += cost_discountable;

      //This is the non-discountable portion of the fixture cost 
      cost_nondiscountable = parseFloat(productArray[product][CostNonIdx])  * quantity;
      nondiscountable_cost_global += cost_nondiscountable;

      //Line cost.
      line_discountable_cost = cost_discountable;
      cost_line_total = cost_discountable + cost_nondiscountable;
      cost_per_piece = cost_line_total / quantity;

      // Save information in hidden fields so they get submitted to PHP by the form and saved in database.
      a1 = new ToFmt(0.0);
      document.getElementById("wastearea" + linenum).value = strltrim(a1.fmtF(9,3));
      a1 = new ToFmt(0.0);
      document.getElementById("wastecost" + linenum).value = strltrim(a1.fmtF(9,2));
      a1 = new ToFmt(cost_per_piece);
      document.getElementById("piececost" + linenum).innerHTML = "$" + strltrim(a1.fmtF(9,2));
      a1 = new ToFmt(cost_line_total);
      document.getElementById("totlinecost" + linenum).innerHTML = "$" + strltrim(a1.fmtF(9,2));
      a1 = new ToFmt(line_discountable_cost);
      document.getElementById("lindct" + linenum).value = "$" + strltrim(a1.fmtF(9,2));
      a1 = new ToFmt(0.0); 
      document.getElementById("sqft" + linenum).value = strltrim(a1.fmtF(9,3));
      a1 = new ToFmt(0.0); 
      document.getElementById("printedarea" + linenum).value = strltrim(a1.fmtF(9,3));
      a1 = new ToFmt(0.0); 
      document.getElementById("inkcost" + linenum).value = strltrim(a1.fmtF(9,3));

      return(0.0); // returning printed square footage of 0 for fixture
    }

    heightin = document.getElementById("heighti" + linenum);
    widthin = document.getElementById("widthi" + linenum);
    wsheight = parseFloat(heightin.value);
    wswidth = parseFloat(widthin.value);
    printwidth = productArray[product][PrintWidthIdx];
    printlength = productArray[product][PrintLengthIdx];

    // Work out perimeter (in linear feet) and square footage of the printed sign
    wsperimeter = ((wsheight + wswidth) * 2) / 12;
    wsarea = (wsheight/12) * (wswidth/12);

    // Find out how many signs to print.
    quantity = parseFloat(document.getElementById("quantity" + linenum).value);
    sign_sqfootage = wsarea * quantity;
    printed_sqfootage = sign_sqfootage; // gets doubled below, if sign is printed on back side (BS option)

    // Calculate amount of material wasted (in sq ft) per sign.  Assume for this calculation
    // that the customer ordered the optimum optimum multiples of their sign.
    waste_sqft_per_sign = ComputeWaste(printlength, printwidth, wsheight, wswidth);
    waste_sqfootage = waste_sqft_per_sign * quantity * parseFloat(wastefactor);
    waste_sqfootage_test = waste_sqft_per_sign * quantity * 1.0;

    // Work out total square footage of media used (sign + waste).
    total_sqfootage = sign_sqfootage + waste_sqfootage;

//alert("printed sqft = " + sign_sqfootage + " wasted sqft = " + waste_sqfootage + " true waste = " + waste_sqfootage_test + " total sqft = " + total_sqfootage);


    // COMPUTE COST OF MEDIA AND PRINTING
    // ==================================

    //This is the discountable portion of the media cost 
    cost_discountable = parseFloat(productArray[product][CostDiscIdx]) * sign_sqfootage;
    discountable_cost_global += cost_discountable;

    //This is the non-discountable portion of the media cost 
    cost_nondiscountable = parseFloat(productArray[product][CostNonIdx])  * sign_sqfootage;
    nondiscountable_cost_global += cost_nondiscountable;

    //Waste cost of media.
    cost_waste = parseFloat(productArray[product][CostWasteIdx])  * waste_sqfootage;
    waste_cost_global += cost_waste;

    //This is the cost of the ink. It is non-discountable and system-wide (not product-specific).
    cost_ink = inkcost * sign_sqfootage;
    ink_cost_global += cost_ink;

//alert("costs: discountable = " + cost_discountable + " non-discountable = " + cost_nondiscountable + " waste = " + cost_waste + " ink = " + cost_ink);

    // COMPUTE COST OF FINISHING OPTIONS
    // =================================

    cost_options = 0;
    number_of_options = 0;

    // we treat lamination separately from other options because it is priced like a medium
    cost_lamination = 0; 
    cost_lamination_discountable = 0; 

    // Identify each finishing option which has been requested.
    popup = document.getElementById("finishing-popup-" + linenum);
    input_items = popup.getElementsByTagName("input");
    for (i = 0; i < input_items.length; i++) {
      if (input_items[i].checked) {
        number_of_options++;
        the_option_id = input_items[i].id;  // e.g. AF-X-1
        // Find the_option_name, which is the_option_id with the -linenum removed e.g. AF-X
        data = the_option_id.split('-');
        the_option_name = data[0] + "-" + data[1];

        // Go through finishingOptionArray and find that option and its values;
        for (k = 1; k <= numberOfFinishingOptions; k++) {
          if (finishingOptionArray[k][FinOptCodeIdx] == the_option_name) {
            varcost = parseFloat(finishingOptionArray[k][FinOptVariableCostIdx]);
            fixedcost = parseFloat(finishingOptionArray[k][FinOptFixedCostIdx]);
            equation = finishingOptionArray[k][FinOptUnitsIdx];
            extra_days = finishingOptionArray[k][FinOptExtraTimeIdx];
            batch_day = finishingOptionArray[k][FinOptBatchDayIdx];
            option_set = finishingOptionArray[k][FinOptSetIdx];
            laminate_product_code = finishingOptionArray[k][FinOptLaminateProductCodeIdx];
            break;
          }
        }

        // Keep track of all finishing options used in this order, and if they require extra days.
        array_length = optionsRequestedArray.length;
        item_found = false;
        for (m = 0; m < array_length; m++) {
           if (the_option_name == optionsRequestedArray[m][0]) item_found = true;
        }
        if (!item_found) {
          optionsRequestedArray[m] = new Array();
          optionsRequestedArray[m][0] = the_option_name;
          optionsRequestedArray[m][1] = extra_days;
          optionsRequestedArray[m][2] = batch_day;
          //alert("Finishing option " + the_option_name + " requires " + extra_days + " extra days. Batch day is " + batch_day);
        }

        // Calculate cost of that option and add to the total cost of options.
        switch (equation) {
          case "PF":    // Perimeter Footage
            option_cost = ((wsperimeter * varcost) + fixedcost) * quantity;
            cost_options += option_cost;
            //alert("Line: " + linenum + "\n\nOption: " + the_option_name + "\n\nEquation: PF\n\nPerimeter: " + wsperimeter + "\n\nVariable: " + varcost + "\n\nFixed: " + fixedcost + "\n\nQuantity: " + quantity + "\n\nCOST: " + option_cost);
            break;
          case "SF":    // Square Footage
	    if (option_set == 'LAMINATION') 
	    {

	      // If this option uses a laminate product (.e.g it's not a -X option)...
              laminate = 0;
	      if (laminate_product_code.length > 0) 
	      {
	        // Get the costing information from the product array for the selected laminate.
                // Identify the array index for that product.
                for (n = 1; n <= numberOfProducts; n++) {
                  if (productArray[n][CodeIdx] == laminate_product_code) {
		    laminate = n;
                    break;
                  }
                }
              }
	      // Read out the information.
	      if ((laminate <= numberOfProducts) && (laminate != 0))
	      {
	        /*
	        laminate_print_width = productArray[laminate][PrintWidthIdx];
	        laminate_print_length = productArray[laminate][PrintLengthIdx];
	        laminate_cost_discountable = productArray[laminate][CostDiscIdx];
	        laminate_cost_nondiscountable = productArray[laminate][CostNonIdx];
	        laminate_cost_waste = productArray[laminate][CostWasteIdx];
	        alert(
		"Lamination option " + the_option_name + 
		" uses product " + laminate_product_code + 
		" which is " + laminate_print_width + 
		" x " + laminate_print_length + 
		" and disc = " + laminate_cost_discountable + 
		" nondisc = " + laminate_cost_nondiscountable + 
		" waste = " + laminate_cost_waste);
		*/

	        // This is the discountable portion of the laminate cost 
	        // TO DO: cost out double-sided laminate (right now we don't notice if it's double-sided)
                laminate_cost_discountable = parseFloat(productArray[laminate][CostDiscIdx]) * sign_sqfootage;
                discountable_cost_global += laminate_cost_discountable;

                // This is the non-discountable portion of the laminate cost 
	        // TO DO: cost out double-sided laminate (right now we don't notice if it's double-sided)
                laminate_cost_nondiscountable = parseFloat(productArray[laminate][CostNonIdx]) * sign_sqfootage;
                nondiscountable_cost_global += laminate_cost_nondiscountable;
  
                // Waste cost of laminate. 
	        // TO DO: Calculate the actual waste for the laminate instead of assuming it is same as product waste.
	        // (Laminate and product may have different widths.)
                laminate_cost_waste = parseFloat(productArray[laminate][CostWasteIdx]) * waste_sqfootage;
                waste_cost_global += laminate_cost_waste;

		/*
		alert("Lamination option " + the_option_name + 
		      " uses product " + laminate_product_code + 
		      " Nondiscountable = " + laminate_cost_nondiscountable +
		      " Discountable = " + laminate_cost_discountable +
		      " Waste = " + laminate_cost_waste);
	        */

		// We track the lamination cost separately from other finishing costs, because it has disc/nondisc portions.
		option_cost = 0;
                cost_options += option_cost;
                lamination_cost = laminate_cost_discountable + laminate_cost_nondiscountable + laminate_cost_waste;
                cost_lamination += lamination_cost; 
                cost_lamination_discountable += laminate_cost_discountable; 
	      }
	    }
	    else
	    {
              option_cost = ((wswidth/12 * wsheight/12 * varcost) + fixedcost) * quantity;
              cost_options += option_cost;
              //alert("Line: " + linenum + "\n\nOption: " + the_option_name + "\n\nEquation: SF\n\nWidth (ft): " + wswidth/12 + "\n\nLength (ft): " + wsheight/12 + "\n\nVariable: " + varcost + "\n\nFixed:  " + fixedcost + "\n\nQuantity: " + quantity + "\n\nCOST: " + option_cost);
	    }
            break;
          case "BS":    // Back Side
            // Charge the printing and ink cost for the back side, but not the media cost.
            // Don't multiply cost_disc + cost_non + cost_ink by quantity, as they already include quantity in them.
            option_cost = ((cost_discountable + cost_nondiscountable) * varcost) + (fixedcost * quantity) + cost_ink;
            cost_options += option_cost;
            printed_sqfootage = printed_sqfootage * 2.0;
            //alert("Line: " + linenum + "\n\nOption: " + the_option_name + "\n\nEquation: BS" + "\n\nDiscountable Cost: " + cost_discountable + "\n\nNon-discountable Cost: " + cost_nondiscountable + "\n\nVariable: " + varcost + "\n\nFixed: " + fixedcost + "\n\nQuantity: " + quantity + "\n\nCOST: " + option_cost);
            break;
          case "EA":    // Each
            // Calculate the number of (fixed) and the cost of (variable) an item added to a sign.  
            option_cost = fixedcost * varcost * quantity;
            cost_options += option_cost;
            //alert("Line: " + linenum + "\n\nOption: " + the_option_name + "\n\nEquation: EA\n\nVariable: " + varcost + "\n\nFixed:  " + fixedcost + "\n\nQuantity: " + quantity + "\n\nCOST: " + option_cost);
            break;
          default:
            alert("The order page could not identify how to price one of the finishing items you selected on line " + linenum + ".  Please call us at (604) 881-0363 to let us know this has happened so that we can manually quote your order and fix the order page. We apologize for the inconvenience.")
            break;
        }  // end switch

      } // end of if that option is checked

    } // end of for all options

    options_cost_global += cost_options;

    // TO DO: Check whether this variable gets used for calculations later. May be able to delete it or zero it out.
    //line_discountable_cost = cost_discountable + cost_options; 
    line_discountable_cost = cost_discountable + cost_lamination_discountable; 

    // COMPUTE COST OF MEDIA AND PRINTING
    // ==================================

    //Line cost.
    cost_line_total = cost_discountable + cost_nondiscountable + cost_waste + cost_options + cost_ink + cost_lamination;
    cost_per_piece = cost_line_total / quantity;

    if (isPrintableCategory(category_code) && (category_code != 'GFLOOR') && (number_of_options == 0)) {
      ie8_alert_global = true;
    }

    // Save information in hidden fields so they get submitted to PHP by the form and saved in database.
    a1 = new ToFmt(waste_sqfootage);
    document.getElementById("wastearea" + linenum).value = strltrim(a1.fmtF(9,3));
    a1 = new ToFmt(cost_waste);
    document.getElementById("wastecost" + linenum).value = strltrim(a1.fmtF(9,2));
    a1 = new ToFmt(cost_ink);
    document.getElementById("inkcost" + linenum).value = strltrim(a1.fmtF(9,2));
    a1 = new ToFmt(cost_per_piece);
    document.getElementById("piececost" + linenum).innerHTML = "$" + strltrim(a1.fmtF(9,2));
    a1 = new ToFmt(cost_line_total);
    document.getElementById("totlinecost" + linenum).innerHTML = "$" + strltrim(a1.fmtF(9,2));
    a1 = new ToFmt(line_discountable_cost);
    document.getElementById("lindct" + linenum).value = "$" + strltrim(a1.fmtF(9,2));
    a1 = new ToFmt(sign_sqfootage); 
    document.getElementById("sqft" + linenum).value = strltrim(a1.fmtF(9,3));
    a1 = new ToFmt(printed_sqfootage); 
    document.getElementById("printedarea" + linenum).value = strltrim(a1.fmtF(9,3));

    /*
    alert(
      "CALCULATE LINE COST\n\nTotal Sq.Ft.: " + total_sqfootage + "\n" +
      "Sign Sq.Ft.:" + sign_sqfootage + "\n" +
      "Printed Sq.Ft.:" + printed_sqfootage + "\n" +
      "Waste Sq.Ft.:" + waste_sqfootage + "\n" +
      "Material Cost(Disc):" + cost_discountable + "\n" +
      "Material Cost(Non-Disc):" + cost_nondiscountable + "\n" +
      "Waste Cost:" + cost_waste + "\n" +
      "Option Cost:" + cost_options 
      );
    */

    return(printed_sqfootage);
  }

  /********************************************************************************************
  *  ClearDetails(): If product is not selected, clear the line out.
  ********************************************************************************************/

  function ClearDetails(idx) {

    document.getElementById("quantity" + idx).value = "";
    document.getElementById("heighti" + idx).value = "";
    document.getElementById("widthi" + idx).value = "";
    document.getElementById("cat" + idx).value = "0";
    document.getElementById("pr" + idx).length = 0;
    document.getElementById("pr" + idx).options[0] = new Option("Select Category", "ASK_SELECT", 1);
    DeleteFinishingOptions(idx);
    document.getElementById("help_product_" + idx).src = 'images/info.png';
  }

  /********************************************************************************************
  * This routine is called when the Compute button is pressed.  We need to validate
  * the input and insure that a product is selected.  
  ********************************************************************************************/

  function DoCompute() {

    SetError("");

    if (!validatepage()) return;

    GetFreightCharges();  //Set global variable freightcharge in here (see orderutil.js)

  }

  /********************************************************************************************
  * This routine calculates the cost of the order.  It calls CalculateLineCost() to do much of
  * the calculations.  It replaces DoCompute1() from V1.0.
  ********************************************************************************************/

  function CalculateOrderCost() {
    var i, j, k, m, a1, the_category, extra_days; 
    var printed_sqfootage;
    var freight;
    var subtotal_cost, rush_amount, hot_amount, service_cost, net_cost; 
    var taxes, GST, PST;
    var option_code, double_sided_requested, fixture_ordered, printable_ordered, gfloor_ordered;
    var promo_code, promo_details, promo_array, promo_type, promo_value;
    var batch_day_option;
    var order_wide_batch_day;
    var product_code;

    discountable_cost_global = 0;        
    nondiscountable_cost_global = 0;     
    waste_cost_global = 0;               
    ink_cost_global = 0;
    printed_square_footage_global = 0;   
    options_cost_global = 0;       
    total_order_discount_global = 0;     
    ie8_alert_global = false;
    optionsRequestedArray = new Array(); // a global array used by CalculateLineCost

    // COMPUTE COST OF EACH LINE ITEM
    // ==============================
    // While we're at it identify 1. start date (in case some products are batch), and 
    // 2. whether any fixtures and printable products have been ordered.
    fixture_ordered = false;
    printable_ordered = false;
    gfloor_ordered = false;
    order_wide_batch_day = 0;       // 0 = no batch items. 1 = Batch Monday ... 5 = Batch Friday.

    for (i = 1; i <= linecount; i++) {

      // CalculateLineCost work sout line item costs, waste area and its cost, ink cost, and
      // square footage, perimeter and  printed area of the line item.
      the_category = document.getElementById("cat" + i).value;
      if (the_category != 0) {
        printed_sqfootage = CalculateLineCost(i);
        printed_square_footage_global += printed_sqfootage;
      }

      // Calculate start date, if any items are batch.
      product_code = document.getElementById("pr" + i).value
      for (k = 1; k <= numberOfProducts; k++) {
        if (productArray[k][CodeIdx] == product_code) { 
          batch_day_product = productArray[k][BatchDayIdx];
          break;
        }
      }
      if (batch_day_product > order_wide_batch_day) 
      {
        order_wide_batch_day = batch_day_product;
        //alert("CalculateOrderCost: batch day product changed to " + order_wide_batch_day);
      }

      // Identify whether printable items and fixtures have been ordered.
      if (the_category != 0) {
        if (the_category == 'GFLOOR')
        {
          gfloor_ordered = true;
          printable_ordered = true; // gfloor accessories are NOT in the G-Floor category
        }
        else if (isPrintableCategory(the_category)) 
        {
          printable_ordered = true;
        }
        else 
        {
          fixture_ordered = true;
        }
      }
    }

    // DISPLAY COST OF SHIPPING OPTIONS
    // ================================
    freight = DisplayShipping();
    
    
    // CALCULATE THE DIFFERENT READY DATES
    // ===================================
    // Sum up extra days required for finishing options used in this order. 
    // While we're at it, see if double-sided has been requested on any of the lines.
    extra_days = 0;
    double_sided_requested = false;
    for (m = 0; m < optionsRequestedArray.length; m++) {
      extra_days = extra_days + parseInt(optionsRequestedArray[m][1]);
      option_code = optionsRequestedArray[m][0]; 
      batch_day_option = optionsRequestedArray[m][2]; 
      // go through finishingOptionArray and find option name and its equation
      for (j = 0; j < numberOfFinishingOptions; j++) {
        if (finishingOptionArray[j][FinOptCodeIdx] == option_code) {
          equation = finishingOptionArray[j][FinOptUnitsIdx];
          if (equation == "BS") double_sided_requested = true;
        }
      }
      //alert("CalculateOrderCost: order_wide is " + order_wide_batch_day);
      if (batch_day_option > order_wide_batch_day) 
      {
        order_wide_batch_day = batch_day_option;
        //alert("CalculateOrderCost: batch day option changed to " + order_wide_batch_day);
      }
    }
    if (fixture_ordered == true) {
      extra_days++; 
    }

    DetermineReadyDate(printed_square_footage_global, extra_days, order_wide_batch_day); 
    if ((order_wide_batch_day > 0) && (warned_about_batch_global == false))
    {
      warned_about_batch_global = true;
      alert("At least one of the line items in your order includes a product or finishing option that we process only once a week, on a dedicated batch day.\n\nIncluding batch items in a multi-line order can result in a delay in the ready date. If the calculated ready date is not early enough for you, you may wish to submit those items in a separate order.\n\n***FOR THE QUICKEST TURNAROUND ON BATCH ORDERS, submit your order BEFORE 12 NOON on Thursday.***\n\nTo see which line items are batch, look for a red circle containing a question mark in the PRODUCT column or bold red text saying 'Batch' in the FINISHING column.\n\nTo remove an item from the order, enter a zero in the quantity box for that line, and then click the QUOTE ORDER button again.");
    }

    // CALCULATE THE BEST DISCOUNT 
    // ===========================
    // first need to calculate a non-discountable order-wide setup cost; a global variable in javascript, configurable in admin 
    if (gfloor_ordered)
      setup_cost_global = gfloorsetupfee * 1.0;  
    else if (printable_ordered)
      setup_cost_global = setupfee * 1.0;  
    else
      setup_cost_global = 0.0;
    ComputeDiscount();

    // DISPLAY ANY WARNINGS
    // ====================
    if (tiling_global) { 
      document.getElementById('tileblock').style.visibility = 'visible';
    }
    if (double_sided_requested && !uploading_global) {
      alert("You have selected the double-sided option on one or more lines. If you wish to have different images on the front and back of a sign, please make sure you upload a two-page PDF on that line.");
    }
    if (ie8_alert_global) {
      alert("Our ordering system is unable to identify which finishing options you want for this order.\n\nThis is likely because you are using Internet Explorer 8 (IE8) on Windows XP.  We are actively working to make our system compatible with this browser/OS combination.\n\nIn the meantime, you can either call us at (604) 881-0363 to specify your order over the phone, or upgrade your browser to Internet Explorer 9, or install one of the free browsers such as Mozilla Firefox (available at http://www.mozilla.org/en-US/firefox/new/) or Google Chrome (available at https://www.google.com/chrome).\n\nYou can have Firefox and Chrome installed on your computer at the same time as IE8, so you can still use IE8 for your other web surfing, if you wish.\n\nWe apologize for this inconvenience.\n\n");
      return;
    }

    // CALCULATE INFORMATION FOR ORDER SUMMARY
    // =======================================
    subtotal_cost = discountable_cost_global + nondiscountable_cost_global + waste_cost_global + ink_cost_global + options_cost_global; 
    rush_amount = parseInt(((subtotal_cost + setup_cost_global - total_order_discount_global) * .1));
    rush_amount = (25 > rush_amount) ? 25 : rush_amount;
    hot_amount = parseInt(((subtotal_cost + setup_cost_global - total_order_discount_global) * .2));
    hot_amount = (75 > hot_amount) ? 75 : hot_amount;
    if (document.getElementById("ckrushservice").checked) 
      service_cost = rush_amount;
    else if (document.getElementById("ckhotservice").checked) 
      service_cost = hot_amount;
    else 
      service_cost = 0;

    // CALCULATE PROMO DISCOUNT IF APPLICABLE
    // ======================================
    promo_code = document.getElementById("txtpromo").value;
    if (promo_code.length > 0)
    {
      GetPromoDetails(promo_code);
      promo_details = document.getElementById("promodetails").value;
      promo_array = promo_details.split("~");
      promo_amount = promo_array[1];
      promo_type = promo_array[2];
      if ((promo_amount == '') || (promo_type == ''))
      {
        promo_code = '';
        promo_discount_dollars = 0.0;
      }
      else
      {
	if (promo_type == 'PERCENT')
	{
          promo_percent = promo_amount / 100.0;
          promo_discount_dollars = (subtotal_cost + setup_cost_global - total_order_discount_global) * promo_percent;
	  temp = new ToFmt(promo_discount_dollars);
	  promo_discount_dollars = strltrim(temp.fmtF(9,2));
	}
	else if (promo_type == 'DOLLARS')
	{
	  promo_discount_dollars = promo_amount * 1.0; // force conversion to double
	}
      }
    }
    else
    {
      promo_code = '';
      promo_discount_dollars = 0.0;
    }
    document.getElementById("promocode").value = promo_code;
    document.getElementById("fpromodiscountdollars").value = promo_discount_dollars;

    net_cost = subtotal_cost + setup_cost_global - total_order_discount_global - promo_discount_dollars + service_cost;

    taxes = CalculateTax(net_cost, freight);
    PST = taxes[0];
    GST = taxes[1];

    // DISPLAY INFORMATION IN ORDER SUMMARY
    // ====================================
    a1 = new ToFmt(subtotal_cost);
    document.getElementById("txtsubtot").value = "$" + strltrim(a1.fmtF(9,2));

    a1 = new ToFmt(setup_cost_global); 
    document.getElementById("txtsetup").value = "$" + strltrim(a1.fmtF(9,2));

    a1 = new ToFmt(total_order_discount_global);
    document.getElementById("txtdct").value = "$" + strltrim(a1.fmtF(9,2));

    a1 = new ToFmt(promo_discount_dollars);
    document.getElementById("txtpromoamt").value = "$" + strltrim(a1.fmtF(9,2));

    a1 = new ToFmt(rush_amount);
    document.getElementById("txtrushval").value = "$" + strltrim(a1.fmtF(9,2));

    a1 = new ToFmt(hot_amount);
    document.getElementById("txthotval").value = "$" + strltrim(a1.fmtF(9,2));
    
    a1 = new ToFmt(service_cost);
    document.getElementById("txtrushamt").value = "$" + strltrim(a1.fmtF(9,2));

    a1 = new ToFmt(net_cost);
    document.getElementById("txtnet").value = "$" + strltrim(a1.fmtF(9,2));

    a1 = new ToFmt(GST);    
    document.getElementById("txtGST").value = "$" + strltrim(a1.fmtF(9,2));
    a1 = new ToFmt(PST);
    document.getElementById("txtPST").value = "$" + strltrim(a1.fmtF(9,2));

    if ((freight > 0) || (document.getElementById("ckpick").checked) || (document.getElementById("ckpack").checked)) {
      a1 = new ToFmt(freight);
      document.getElementById("txtfreight").value = "$" + strltrim(a1.fmtF(9,2));
    } else {
      document.getElementById("txtfreight").value = "Call";
    } 

    printed_square_footage_global = ((printed_square_footage_global < 1) ? 1 : printed_square_footage_global);
    if (printed_square_footage_global > 5000) {
      document.getElementById("txtordtotal").value =  "Call";
    } else {
      a1 = new ToFmt(net_cost + freight + GST + PST);
      document.getElementById("txtordtotal").value = "$" + strltrim(a1.fmtF(9,2));
    }

    document.getElementById("txtdctname").value = discount_name_global;

    if (document.getElementById("Submit").className != "submitbutton") {

      var short_message = 'You have selected one or more custom media in this order and an estimated price has been calculated for you.\n\nHowever, we recommend you call us at 604-881-0363 for final pricing on those items as discounts or up-charges may apply depending on the type of custom media you would like to print on.';

      var long_message = 'You have selected one or more custom media in this order and an estimated price has been calculated for you.\n\nHowever, we recommend you call us at 604-881-0363 for final pricing on those items as discounts or up-charges may apply depending on the type of custom media you would like to print on.\n\n You may go ahead and submit your order if you wish.  We will adjust the pricing after speaking to you.\n\nOr you may delete the custom items from this order by typing a zero into the quantity box for that line item.';

      if (CustomMediaOrdered()) // if CUSAD or CUSRI or CUSBA in any of the product boxes where qty > 0 
        alert(short_message);
    }

    document.getElementById("Submit").value = "";
    document.getElementById("Submit").className = "submitbutton";
    document.getElementById("Submit").onclick = ProcessUpload;

  }

  /********************************************************************************************
  * ComputeDiscount() : Identify which option gives the customer a better discount: their 
  * customer account discount or a discount based on the amount they are purchasing. The 
  * calculation based on the amount they are purchasing is: 
  * percentage = discountable cost in dollars / 100.
  ********************************************************************************************/

  function ComputeDiscount() {
    var total_order_cost, total_discountable_cost, size_discount_percent, customer_discount_percent;
    var i;

    // Calculate the order cost that the volume discount is based on.
    // setup_cost is a global variable set when order costin was calculated;
    // it is order-wide, not per product.  Must leave in the * 1.0's or Javascript will 
    // concantenate the numeric strings instead of mathematically adding them up.
    total_order_cost = (discountable_cost_global  * 1.0) + 
                       (nondiscountable_cost_global  * 1.0) + 
                       (setup_cost_global * 1.0) + 
                       (waste_cost_global * 1.0) + 
		       (ink_cost_global  * 1.0) + 
                       (options_cost_global * 1.0);
    // OLD WAY, allow discounts on finishing options 
    //total_discountable_cost = (discountable_cost_global  * 1.0) + (options_cost_global * 1.0);
    // NEW WAY, no discounts on finishing options 
    total_discountable_cost = (discountable_cost_global  * 1.0);

    // First compute the discount that would be given based on order size.
    // For every penny they order extra, their discount goes up. So the discount
    // is still tiered, but it is effectively microtiered.  This avoids the 
    // problem where a customer who buys $1001 ends up paying less than a 
    // customer who buys $999 (e.g. when tiered at $500 increments).

    // OLD LINEAR DISCOUNT with 50% MAXIMUM. 
    // size_discount_percent = total_order_cost / 100.0;
    // if (size_discount_percent > 50.0) size_discount_percent = 50.0;

    // NEW NON-LINEAR DISCOUNT, with 100% MAXIMUM. Using Michaelis-Menten rectangular hyperbola.
    Vmax = discountfactora * 1.0;
    Km = discountfactorb * 1.0;
    size_discount_percent = (Vmax * total_order_cost) / (Km + total_order_cost);
    if (size_discount_percent > 100.0) size_discount_percent = 100.0;
    //alert("discount for order of $" + total_order_cost + " = " + size_discount_percent + "%");

    //Now identify the percent discount that would be given based on the customer discount.
    customer_discount_percent = 0;
    for (i = 1; i < discountArray.length; i++) {
      if (account_discount_global == discountArray[i][0]) {
        customer_discount_percent = parseFloat(discountArray[i][3]);
      }
    }
    
    // Choose the higher of the two discounts.
    if (customer_discount_percent > size_discount_percent) {
      total_order_discount_global = customer_discount_percent * total_discountable_cost / 100.0;
      discount_name_global = account_discount_global;
    }
    else {
      total_order_discount_global = size_discount_percent * total_discountable_cost / 100.0;
      discount_name_global = "OrderSize";
    }

    // Don't extend paltry discounts. e.g. those under $5
    if (total_order_discount_global < 5.00) 
    {
      total_order_discount_global = 0.00;
      discount_name_global = "";
    }
    
    document.getElementById("dct").value = discount_name_global;

    //alert ("COMPUTE DISCOUNT\n\nDiscountable Cost: $" + discountable_cost_global + "\n\nOptions Cost: $" + options_cost_global + "\n\nOrder Size Discount: %" + size_discount_percent + "\n\nCustomer Discount: %" + customer_discount_percent + "\n\nSELECTED DISCOUNT: " + discount_name_global + "\n\nValue of Discount: $" + total_order_discount_global);
    
  }

  /********************************************************************************************
  *  ComputeWaste
  *
  ********************************************************************************************/

  function ComputeWaste(printlength, printwidth, heightin, widthin) {
    var waste_sqft, waste_sqft_horizontal, waste_sqft_vertical;
    var number_of_signs;
    var sign_large_dim, sign_small_dim, media_large_dim, media_small_dim;
    var unprintable_scrap_large_dim, unprintable_scrap_small_dim;
    var area_printed, media_used, waste, alignment_waste_sqft;

    var hmultiple = -1 * 1;
    var vmultiple = -1 * 1;

    // * 1: We multiply by 1 to force javascript to convert values from strings to numbers.
    // If we don't do that, 60 + 1 = 601 instead of 61 and 32 > 120 instead of 32 < 120.  
    // (Weakly typed languages suck!)
    if (heightin * 1 > widthin * 1) {
    //if (heightin > widthin) {
      sign_large_dim = heightin * 1;
      sign_small_dim = widthin * 1;
    }
    else {
      sign_large_dim = widthin * 1;
      sign_small_dim = heightin * 1;
    }
    if (printlength * 1 > printwidth * 1) {
    //if (printlength > printwidth) {
      media_large_dim = printlength * 1;
      media_small_dim = printwidth * 1;
      unprintable_scrap_large_dim = 0 * 1;
      unprintable_scrap_small_dim = 4 * 1;
    }
    else {
      media_large_dim = printwidth * 1;
      media_small_dim = printlength * 1;
      unprintable_scrap_large_dim = 4 * 1;
      unprintable_scrap_small_dim = 0 * 1;
    }
    
    // CHECK IF WE NEED TO TILE MEDIA to print the sign.  If we do, call a function that determines 
    // whether we need to put multiple tiles side by side and/or end to end. This function will 
    // choose the option that results in the fewest seams/tiles.
    if ((sign_large_dim > media_large_dim) || (sign_small_dim > media_small_dim)) {

      // Calculate how many tiles are required in each dimension.
      var tiling_multiples = ComputeTilingMultiples(media_large_dim, media_small_dim, sign_large_dim, sign_small_dim);

      // Update the width and/or length of the media and the unprintable scrap.
      media_large_dim = media_large_dim * tiling_multiples[0];
      unprintable_scrap_large_dim = unprintable_scrap_large_dim * tiling_multiples[0];
      media_small_dim = media_small_dim * tiling_multiples[1];
      unprintable_scrap_small_dim = unprintable_scrap_small_dim * tiling_multiples[1];

      // Swap the small and large dimensions if required.
      if (media_small_dim > media_large_dim) {
        temp_dim = media_large_dim;
        temp_scrap = unprintable_scrap_large_dim;
        media_large_dim = media_small_dim;
        unprintable_scrap_large_dim = unprintable_scrap_small_dim;
        media_small_dim = temp_dim;
        unprintable_scrap_small_dim = temp_scrap;
      }

    }

    //alert("sign large = " + sign_large_dim + "\nmedia large = " + media_large_dim + "\nsign small = " + sign_small_dim + "\nmedia small = " + media_small_dim);

    // ***** HORIZONTAL ORIENTATION: compute the waste if the small dimension of the sign is parallel 
    // to the small dimension of the media, and the optimal number of signs are ordered
    if ((sign_large_dim <= media_large_dim) && (sign_small_dim <= media_small_dim)) {

      // Figure out the optimum multiples in this orientation.
      hmultiple = intdiv(media_small_dim, sign_small_dim);
      vmultiple = intdiv(media_large_dim, sign_large_dim);
      number_of_signs = hmultiple * vmultiple;

      // Calculate the area of the signs printed.
      area_printed = sign_small_dim * sign_large_dim * number_of_signs;

      // Calculate the amount of media used.   
      media_used = (media_small_dim + unprintable_scrap_small_dim) * 
                   (media_large_dim + unprintable_scrap_large_dim);

      // Calculate amount of waste per sign in square feet
      waste = (media_used - area_printed) / number_of_signs;
      waste_sqft_horizontal = waste / 144;

      //alert("hmult = " + hmultiple + "\nvmult = " + vmultiple + "\nnumber signs = " + number_of_signs + "\narea printed = " + area_printed + "\nmedia used = " + media_used + "\nwaste (in) = " + waste + "\nwaste (sqft) HORIZ = " + waste_sqft_horizontal);
    }
    else {

      waste_sqft_horizontal = -1;

    }


    // ***** VERTICAL ORIENTATION: compute the waste if the large dimension of the sign is parallel 
    // to the small dimension of the media, and the optimal number of signs are ordered
    if ((sign_small_dim <= media_large_dim) && (sign_large_dim <= media_small_dim)) {

      // Figure out the optimum multiples in this orientation.
      hmultiple = intdiv(media_small_dim, sign_large_dim);
      vmultiple = intdiv(media_large_dim, sign_small_dim);
      number_of_signs = hmultiple * vmultiple;

      // Calculate the area of the signs printed.
      area_printed = sign_small_dim * sign_large_dim * number_of_signs;

      // Calculate the amount of media used.
      media_used = (media_small_dim + unprintable_scrap_small_dim) * 
                   (media_large_dim + unprintable_scrap_large_dim);

      // Calculate amount of waste per sign in square feet
      waste = (media_used - area_printed) / number_of_signs;
      waste_sqft_vertical = waste / 144;

      //alert("hmult = " + hmultiple + "\nvmult = " + vmultiple + "\nnumber signs = " + number_of_signs + "\narea printed = " + area_printed + "\nmedia used = " + media_used + "\nwaste (in) = " + waste + "\nwaste (sqft) VERT = " + waste_sqft_vertical);

    }
    else {

      waste_sqft_vertical = -1;

    }

    // ***** ALIGNMENT SPACING: Calculate area of 0.5 inch perimeter around each
    // sign; this is waste due to spacing required for registration and cutting.
    alignment_waste_sqft = (((sign_small_dim + 1) * (sign_large_dim + 1)) - (sign_small_dim * sign_large_dim)) / 144;

    //alert("alignment waste in sqft = " + alignment_waste_sqft);

    // ***** CHOOSE THE ORIENTATION that gives the least waste.
    if (waste_sqft_horizontal == -1) {
      waste_sqft = waste_sqft_vertical + alignment_waste_sqft;
      //alert("forced to choose vertical orientation with waste = " + waste_sqft);
    }
    else if (waste_sqft_vertical == -1) {
      waste_sqft = waste_sqft_horizontal + alignment_waste_sqft;
      //alert("forced to choose horizontal orientation with waste = " + waste_sqft);
    }
    else if (waste_sqft_horizontal <= waste_sqft_vertical) {
      waste_sqft = waste_sqft_horizontal + alignment_waste_sqft;
      //alert("horizontal orientation gives least waste = " + waste_sqft);
    }
    else {
      waste_sqft = waste_sqft_vertical + alignment_waste_sqft;
      //alert("vertical orientation gives least waste = " + waste_sqft);
    }

    return(waste_sqft);
  }

  /********************************************************************************************
  *  ComputeTilingMultiples(media_large_dim, media_small_dim, sign_large_dim, sign_small_dim);
  *
  *  Determine whether we need to put multiple tiles side by side or end to end, and return
  *  the number of tiles in each dimension.
  *
  ********************************************************************************************/

  function ComputeTilingMultiples(media_large_dim, media_small_dim, sign_large_dim, sign_small_dim) {
    var htilecount_horizontal, vtilecount_horizontal;
    var htilecount_vertical, vtilecount_vertical;
  
    tiling_global = true;

    // HORIZONTAL ORIENTATION: Compute the number of tiles needed if we line up the
    // long side of the sign with the long side of the media.
    htilecount_horizontal = intdiv(sign_large_dim, media_large_dim) + ((sign_large_dim % media_large_dim > 0) ? 1 : 0);
    vtilecount_horizontal = intdiv(sign_small_dim, media_small_dim) + ((sign_small_dim % media_small_dim > 0) ? 1 : 0);

    // VERTICAL ORIENTATION: Compute the number of tiles needed if we line up the
    // short side of the sign with the long side of the media.
    htilecount_vertical = intdiv(sign_small_dim, media_large_dim) + ((sign_small_dim % media_large_dim > 0) ? 1 : 0);
    vtilecount_vertical = intdiv(sign_large_dim, media_small_dim) + ((sign_large_dim % media_small_dim > 0) ? 1 : 0);

    //alert("ComputeTilingMultiples HORIZ: htilecount = " + htilecount_horizontal + " vtilecount = " + vtilecount_horizontal + "ComputeTilingMultiples VERT: vtilecount = " + vtilecount_vertical + " htilecount = " + htilecount_vertical);

    // CHOOSE THE ORIENTATION with the least seams/tiles.
    if (htilecount_horizontal * vtilecount_horizontal <= htilecount_vertical * vtilecount_vertical) {
      //alert("horizontal is better");
      return [htilecount_horizontal, vtilecount_horizontal];
    }
    else {
      //alert("vertical is better");
      return [htilecount_vertical, vtilecount_vertical];
    }

  }

  /********************************************************************************************
  * CustomMediaOrdered(): Check if one of the three custom media has been ordered. These are
  * CUSAD, CUSRI and CUSBA. Return true if one has. Otherwise return false.
  ********************************************************************************************/
  function CustomMediaOrdered() {
    var i, product_code, product_quantity;

    // Check every line item.
    for (i = 1; i <= linecount; i++) {
      product_code = document.getElementById("pr" + i).value;
      product_quantity = document.getElementById("quantity" + i).value;
      if (((product_code == 'CUSAD') || (product_code == 'CUSRI') || (product_code == 'CUSBA')) &&
          (product_quantity != '0') && (product_quantity != ''))
        return true;
    }

    // If you get here, then none of the line items were for custom products.
    return false;

  }

  /********************************************************************************************
  * ProcessUpload(): This routine is called when the Submit button is pressed.  We need to 
  * validate the input and insure that at least one file is entered.  
  * If validation is ok, we'll use a separate window to validate user name and password at
  * the server and rely on the customer validate script to actually start the upload.
  ********************************************************************************************/
  function ProcessUpload() {

    uploading_global = true;    // This causes the submit button to require files be uploaded

    // Validate fields to prep for computation
    if (!(validatepage())) {
      uploading_global = false;      // ok - reset
      return;
    }

    // Validate everything needed to send an order
    if (!(validateupload())) {
      uploading_global = false;      // ok - reset
      return;
    }

    var user_confirmed_submit = confirm("PRINT-READY CHECKLIST\nAll files must be Print-Ready or your order may be rejected and a replacement upload requested. The following are the most common items you should check:\n1. Sign dimensions entered into your orders must be the exact same (within two decimal places) as the PDF dimensions linked to the line item.\n2. Banner dimensions must be the total material required including the unfinished hems and not the finished Banner size once hems are created.\n3. Do not include any Crop-Marks in your files unless you would like them included within your dimensions so that they are printed for trimming yourself.\n4. Bleeds should not be included on any files, except when you choose the KISS or SHAPE Cutting option in which case bleeds should be at least 1/8”.\n\nOK: I have checked my files and they are Print-Ready.\n\nCANCEL: I want to check my files to assure they are Print-Ready.\n\n If you click Cancel, your order will remain intact on this page to return to.");
    /*
    var user_confirmed_submit = confirm("PRINT-READY CHECKLIST\n\nAll files must be Print-Ready or your order may be rejected and a replacement upload requested. The following are the most common items you should check:\n\n 1. Sign dimensions entered into your orders must be the exact same (within two decimal places) as the PDF dimensions linked to the line item.\n\n2. PDF Files have a maximum dimension of 200” so files for larger signs must be proportionally scaled to the Sign Dimensions entered into your orders and our system will automatically scale-up the PDF file to match your Sign dimensions.\n\n3. Banner dimensions must be the total material required including the unfinished hems and not the finished Banner size once hems are created.\n\n 4. Do not include any Crop-Marks in your files unless you would like them included within your dimensions so that they are printed for trimming yourself.\n\n5. Bleeds should not be included on any files, except when you choose the KISS or SHAPE Cutting option in which case bleeds should be at least 1/8”.\n\nOK\nI have checked my files and they are Print-Ready.\n\nCANCEL\nI want to check my files to assure they are Print-Ready.\n\n If you click Cancel, your order will remain intact on this page to return to.");
    */

    // Only continue if user confirmed they have checked their files. Do computation to get total footage
    if (user_confirmed_submit) DoCompute();
  }

  /********************************************************************************************
  * FinishUpload(): 
  ********************************************************************************************/

  function FinishUpload() {
    var i, j, k, wsdata, a1, array_index;
    var product_code, product, options, option_name; 
    var full_filename, index_temp, just_filename;
    var heightin, widthin, wshin, wswin, wsheight, wswidth;

    uploading_global = false;      // ok - reset

    for (i = 1, k = 1; i <= linecount; i++) {

      // Identify which product has been selected.
      product_code = document.getElementById("pr" + i).value;
      // Identify what the array index is for that product.
      for (j = 1; j <= numberOfProducts; j++) {
        if (productArray[j][CodeIdx] == product_code) {
          product = j;
          break;
        }
      }

      // Identify whether finishing is standard or custom.  
      options = "STD";
      option_name = "STD";
      if (product_code != "") {
        if (!IsStandardFinishing(i, product_code)) {
          options = "CUS";
          option_name = "CUS";
        }
        // Save the finishing settings in a variable to go into the database.
        // IsStandardFinishing() has updated the rememberedOptionsArray.
        // Just need to put those values in a single variable, separated by "hats".
        for (array_index = 0; array_index < rememberedOptionsArray[i].length; array_index++) {
          options = options + "^" + rememberedOptionsArray[i][array_index];
        }
      }

      wsdata = "";
      if (product != 0) {

        // Strip path from filename.
        full_filename = document.getElementById("file" + i).value;
        index_temp = full_filename.lastIndexOf("\\") + 1; 
        if (index_temp == 0) index_temp = full_filename.lastIndexOf("\/") + 1; 
        just_filename = full_filename.substring(index_temp);

	// Format width and height.
        heightin = document.getElementById("heighti" + i);
        widthin = document.getElementById("widthi" + i);
        wshin  = parseFloat(heightin.value);
        wswin  = parseFloat(widthin.value);
        a1 = new ToFmt(wshin);
        wsheight = strltrim(a1.fmtF(8,3));
        a1 = new ToFmt(wswin);
        wswidth = strltrim(a1.fmtF(8,3));

        // Populate new variables
	var thickness = "0";
	var uom_thickness = "TBD";
	var pages =  "0"; // need to code php that looks inside PDF files to find # of pages

	var lamination;
        if (productArray[product][CategoryIdx] == 'ADHESIVE') 
	  lamination = "AL-X"; // default to no lamination 
        else if (productArray[product][CategoryIdx] == 'BANNER') 
	  lamination = "BL-X"; // default to no lamination 
        else if (productArray[product][CategoryIdx] == 'RIGID') 
	  lamination = "RL-X"; // default to no lamination 

	// Find lamination setting from options. Look for AL-?? or RL-??
	var lam_index = options.indexOf("AL-"); // adhesive laminate option
	if (lam_index == -1) lam_index = options.indexOf("RL-"); // rigid laminate option
	if (lam_index != -1) 
	{
	  lamination = options.substr(lam_index, 5); // like AL-GL or RL-MA
	  if (lamination.substr(4,1) == "X") lamination = lamination.substr(0, 4); // AL-X or RL-X
        }

	// Find ink layers setting from options. Note substr() != substring().
	var layers = "LYR"; // default to unknown layer
	var lyr_index = options.indexOf("AI-"); // adhesive ink layer option
	if (lyr_index == -1) lyr_index = options.indexOf("RI-"); // rigid ink layers option
	if (lyr_index == -1) lyr_index = options.indexOf("BI-"); // banner ink layers option
	if (lyr_index != -1) 
	{
	  // Find index of ^ that follows, AI-?? or RI-?? or BI-??. ?? can be 1, 2 or 3 chars long.
	  var lyr_index_end = options.indexOf("^", lyr_index);
	  if (lyr_index_end == -1) lyr_index_end = options.length; // for when ?I-?? is at end of string
	  layers = options.substring(lyr_index, lyr_index_end); // like AL-GL or RL-MA
        }

	// Map the A?-?? format of layers to Len's 3-digit format.
        // Second Surface - Single Layer (SSL)
        // First Surface - Single Layer (FSL)
        // Second Surface - Double Strike (SDS)
        // First Surface - Double Strike (FDS)
        // Second Surface - Day & Night(SDN)
        // First Surface - Day & Night (FDN)
        // Second Surface - White Only (SWO)
        // First Surface - White Only (FWO)
        // Custom Ink Layers (CUS)
	// F: first surface, NOT reversed (could be blank)
        // S: second surface, IS reversed (could be R)
        // DN: daynight
        // DS: double-strike
        // WB: white-black
        // WI: white-image
        // WO: white-only
        // CUS: custom ink layers in file

	// We can discard "??-". Then we have a 1, 2 or 3 character code left.
	var new_layer_code;
	if (layers == "LYR")
	{
          new_layer_code = "FSL"; // default to unknown layer
	}
	else
	{
	  var old_layer_code = layers.substr(3); // get from 4th character to end of string
	  if (old_layer_code == "DN")  new_layer_code = "FDN";
  	  if (old_layer_code == "DNR") new_layer_code = "SDN";
	  if (old_layer_code == "DS")  new_layer_code = "FDS";
	  if (old_layer_code == "DSR") new_layer_code = "SDS";
	  if (old_layer_code == "X")   new_layer_code = "FSL";
	  if (old_layer_code == "IR")  new_layer_code = "SSL";
	  if (old_layer_code == "W")   new_layer_code = "FWO";
	  if (old_layer_code == "WR")  new_layer_code = "SWO";
	  if (old_layer_code == "WB")  new_layer_code = "FWB";
	  if (old_layer_code == "WBR") new_layer_code = "SWB";
	  if (old_layer_code == "WI")  new_layer_code = "FWI";
	  if (old_layer_code == "WIR") new_layer_code = "SWI";
	  if (old_layer_code == "CUS") new_layer_code = "CUS";
	}

        wsdata = product_code + "~";
        wsdata += thickness + "~";      // was options (CUS/STD). now thickness
        wsdata += wsheight + "~";
        wsdata += wswidth + "~";
        wsdata += lamination + "~";     // was tlin. now lamination
        wsdata += document.getElementById("quantity" + i).value + "~";
        wsdata += just_filename + "~";
        wsdata += document.getElementById("totlinecost" + i).innerHTML + "~";
        wsdata += document.getElementById("lindct" + i).value + "~";
        wsdata += new_layer_code + "~"; // was % waste. now layers
        wsdata += pages + "~";          // was duplicate of product_code above. now pages
        wsdata += "NO" + "~";           // hardcoded since we have not implemented the line item notes feature yet. 
        wsdata += option_name + "~";    // says just STD or CUS
        wsdata += options + "~";        // starts with STD/CUS, followed by the detailed options, separated by ^
        wsdata += document.getElementById("sqft" + i).value + "~";
        wsdata += document.getElementById("printedarea" + i).value + "~";
        wsdata += document.getElementById("wastearea" + i).value + "~";
        wsdata += document.getElementById("wastecost" + i).value + "~";
        wsdata += document.getElementById("inkcost" + i).value + "~";
        //wsdata += uom_thickness + "~";
        //alert(wsdata);
      }

      // xprod includes ALL line items
      document.getElementById("xprod" + i).value = wsdata;

      // tprod includes only those line items which have files attached
      // it gets passed to AJMUpload which assumes all lines have files and that the "array" is called tprod
      if (just_filename.length > 0) {
        document.getElementById("tprod" + k).value = wsdata;
        k++;
      }
    }

    SetupStandardFields();

    OpenPWWindow();
  }

  /********************************************************************************************
  * This routine builds the page needed to validate customer data and ships it to the
  * server.
  ********************************************************************************************/
  function OpenPWWindow() {

    StartStandardUpload();
  
  }

  /********************************************************************************************
  * This routine insures that all data needed to send an order is entered and correct.
  ********************************************************************************************/
  function validateupload() {
    var wsfilename, ext, file_token;

    //If the shipping address is not the company location, a shipping document is required

    if (!(document.getElementById("ckdefaddr").checked)) {
      wsfilename = document.getElementById("shipdocfile").value;
      ext = (wsfilename.length > 3) ? wsfilename.substr(wsfilename.length-4,4) : "";
      if (ext.toLowerCase() != ".pdf") {
        alert("When using an Alternate ShipTo address, you\nmust include a shipping document in .pdf format.");
        file_token = document.getElementById("shipdocfile");
        //file_token.value = "";  //This only works on IE
        file_token.focus();
        return false;
      }
    }

    if (document.orderForm.txtref.value == "") {
      SetError("Please enter your reference number.");
      document.orderForm.txtref.focus();
      return false;
    }

    return validatefiles();
  }

  /********************************************************************************************
  * This routine insures that all data needed to send an order is entered and correct.
  ********************************************************************************************/
  function validatefiles() {
    var i;

    for (i = 1; i <= linecount; i++) {
      if (!(valLineItem(i))) {
        return false;
      }
    }
    return true;
    
  }


