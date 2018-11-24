/*
  There will be one drop box for each product which is associated with the client. 
  The PHP code will generate a Javascript array that contains each of these products.
*/

/***************************** Global Variables *********************************/

var debug_message = '';

// Keep a global array of all the FileLists that get created from drag/dropped files.
// Note: global_account_name is declared in order.html.php file
var files_validated_so_far = [];
var global_file_lists = [];
var global_upload_order_id_timeout;
var global_order_id = -1;

// Keep track of order-wide totals. For now, we are not offering discounts or
// charging setup or freight costs. So just track subtotal (including options
// cost and rush/hot.
var global_subtotal_cost = 0.0;
var global_service_cost = 0.0;
var global_gst = 0.0; 
var global_total_cost = 0.0;
var global_due_date_yymmdd = 0; // The earliest due date of all the files in this order.
var global_due_date = ''; // Earliest due date fully formatted for display.

var global_need_to_confirm = false;

/**************************** Minor Functions ***********************************/

function askConfirm()
{
  if (global_need_to_confirm)
  {
    return "Are you sure you want to leave this page?  Your files have not been uploaded yet...";
  }       
}
window.onbeforeunload = askConfirm;

// clear list awaiting submission
function clearList() 
{
  var i;
  var number_of_rows 
  number_of_rows = document.getElementById("submission_list_vancouver").rows.length;
  // delete all rows but the first one (which is the heading row)
  for (i = number_of_rows - 1; i > 0; i--)
    document.getElementById("submission_list_vancouver").deleteRow(i);

  number_of_rows = document.getElementById("submission_list_whistler").rows.length;
  // delete all rows but the first one (which is the heading row)
  for (i = number_of_rows - 1; i > 0; i--)
    document.getElementById("submission_list_whistler").deleteRow(i);

  global_need_to_confirm = false;
}

function displayUploadingMessage()
{
  // grey out the background
  document.getElementById("browser_window").style.display = "block";

  document.getElementById("uploading_in_progress").style.display = 'block'; 
}

function hideUploadingMessage()
{
  document.getElementById("uploading_in_progress").style.display = 'none'; 

  // display the page again (no longer greyed out)
  document.getElementById("browser_window").style.display = "none";
}

/****************************** createNewOrder() ************************************/

function createNewOrder(token)
{
  // Create AJAX request object.
  var new_order_request = new XMLHttpRequest();
  
  // Define and register a handler to check the XHR instance's status when receiving an AJAX response.
  new_order_request.onreadystatechange = function () 
  {
    //alert("new order request ready state = " + new_order_request.readyState + " status = " + new_order_request.status);
    if ((new_order_request.readyState == 4) && (new_order_request.status == 200))
    {
      //alert("statusText = " + new_order_request.statusText);
      //alert("responseText= " + new_order_request.responseText); // this displays the order id
      global_order_id = new_order_request.responseText;
    }
  }

  //Ask for new order id via AJAX.
  var ajax_call = "http://" + document.domain + "/pdw/ajax/create-new-order.php" +
                  "?account_name=" + global_account_name + 
                  "&token=" + token + 
                  "&due_date=" + global_due_date_yymmdd + 
                  "&subtotal=" + global_subtotal_cost + 
                  "&service_cost=" + global_service_cost + 
                  "&gst=" + global_gst + 
                  "&total_cost=" + global_total_cost; 

  new_order_request.open("get", ajax_call, false); // synchronous call: TO DO - add timeout
  new_order_request.send();  
  return global_order_id;
}

/********************************* upload() *************************************/

// This function must be named as upload.
function upload()
{
  var order_id;
  var ajax_call_2;

  // Display message that warns customer to stay on page till order is finished.
  displayUploadingMessage();

  // Create a unique token based on timestamp so we know which AJAX responses go with which requests.
  var Token = (new Date()).getTime() % 1000000000;

  global_upload_order_id_timeout = false;
  // this will make a synchronous AJAX call, which could hang; TO DO add time out code to this function
  order_id = createNewOrder(Token);
  if (global_upload_order_id_timeout)
  {
    hideUploadingMessage(); 
  }
  else
  {
    // Prepare information for file upload, and enter line items into database.
    var formData = new FormData();
    for (var j = 0; j < global_file_lists.length; j++) 
    {
      for (var i = 0; i < global_file_lists[j].length; i++) 
      {
        // Enter information for this file into the form that will control the upload.
        var ffile = global_file_lists[j][i];
        var new_file_name = renameFile(ffile.name, ffile.sbm_product, ffile.service_type, 
	                               ffile.lamination, ffile.ink_layers, ffile.other_finishing);
        formData.append('file-' + i + '-' + j, ffile, new_file_name);

        // Enter information for this file in line detail table of database.
        var new_line_item_request = new XMLHttpRequest();

        ajax_call_2 = "http://" + document.domain + "/pdw/ajax/validate-and-calculate-cost-ted2018-180325.php" +
          "?account_name=" + global_account_name + "&product_code=" + ffile.sbm_product + "&file_name=" + ffile.name + "&order_id=" + order_id;
        new_line_item_request.open("get", ajax_call_2, false);
        new_line_item_request.send();  
      }
    }

    // Create AJAX request object.
    var client = new XMLHttpRequest();

    // Define and register a handler to check the XHR instance's status when receiving an AJAX response.
    client.onreadystatechange = function () 
    {
      //alert("ready state = " + client.readyState + " status = " + client.status);
      if ((client.readyState == 4) && (client.status == 200))
      {
        var upload_completed = new XMLHttpRequest();
        var ajax_call_3 = "http://" + document.domain + "/pdw/ajax/mark-order-as-uploaded.php?order_id=" + order_id;
        upload_completed.open("get", ajax_call_3, false); //synchronous TO DO: add timeout
        upload_completed.send();  

        //alert("statusText = " + client.statusText);
        clearList();
        global_file_lists = [];
        hideUploadingMessage(); // so customer knows they can now leave the page
        global_subtotal_cost = 0.0;
        global_service_cost = 0.0;
        global_gst = 0.0;
        global_total_cost = 0.0;
        global_due_date_yymmdd = 0;
        global_due_date = '';
        document.getElementById("subtotal").innerHTML = '$' + global_subtotal_cost.toFixed(2);
        document.getElementById("rush_hot").innerHTML = '$' + global_service_cost.toFixed(2);
        document.getElementById("gst").innerHTML = '$' + global_gst.toFixed(2);
        document.getElementById("total").innerHTML = '$' + global_total_cost.toFixed(2);
  
        // Make an AJAX call to uplxml.aspx, which gives the files their desired names
        // once they have been uploaded.
        // In the regular Signboom order system, uplNew.aspx works out the desired names,
        // based on the tprod array which is passed in to it.
        // Here in the drag and drop order system, the Javascript code works out the
        // desired name (var new_file_name) and passes it down as the 3rd parameter in 
        // the formData.append() call when the files are uploaded.
        var clfinish = new XMLHttpRequest();
        clfinish.open("get", "http://upload.signboom.com:31163/sbUpload/uplxml.aspx?Token=" + Token, false); 
        clfinish.send();  
      }
    }

    // Make an AJAX call to upload the files.  The formData information gets stored
    // in the .ajmSumm files, where it is available to the uplxml.aspx code so the 
    // files can be given the desired names.
    client.open("post", "http://upload.signboom.com:31164/xmlreq.html?Token=" + Token, true);
    client.send(formData);  // send formData to the server using XHR
  }  
}

/*************************** Validation Functions *******************************/

// Later move the validation functions below into a separate file. 

function isNumeric(number) 
{ 
  return !isNaN(parseFloat(number)) && isFinite(number); 
}

function isValidYYMMDD(yymmdd) 
{

  yymmdd += '';
  if (yymmdd.length != 6) return false;
  if (yymmdd != ((yymmdd - 0) + '')) return false;

  year  = yymmdd.substring(0,2) - 0 + 2000;
  month = yymmdd.substring(2,4) - 1;
  day   = yymmdd.substring(4,6) - 0;

  if ((month < 1) || (month > 12))
    return false;
  if ((day < 1) || (day > 31))
    return false;

  //var test = new Date(year, month, day);
  var test = new Date(Date.UTC(year, month, day));

  // Because the Date constructor allows bogus months like 13 or days like 40, 
  // (it wraps around), we check the given day was a real day, by converting back 
  // to year/month/day and comparing.
  if ((test.getUTCFullYear() === year) && 
      (test.getUTCMonth() === month) && 
      (test.getUTCDate() === day))
    return true;
  else
    return false;

}

function parseDimensions(dimensions, parsed_dimensions)
{
  dimension = dimensions.split("x");
  if (dimension.length == 1) 
  {
    parsed_dimensions.width = 0;
    parsed_dimensions.height = 0;
    parsed_dimensions.diameter = dimension[0];
    if (isNumeric(parsed_dimensions.diameter))
      return true;
    else 
      return false;
  }
  else if (dimension.length == 2)
  {
    parsed_dimensions.width = dimension[0];
    parsed_dimensions.height = dimension[1];
    parsed_dimensions.diameter = 0;
    if (isNumeric(parsed_dimensions.width) && (isNumeric(parsed_dimensions.height)))
      return true;
    else 
      return false;
  }
  else
  {
    return false;
  }

}


/****************************** renameFile() *******************************/

// Work out new filename in format used by staff and automate on the in-house server.

function renameFile(original_file_name, product, service_type, lamination, ink_layers, other_finishing)
{
  var details, event, department_code, associate, reference, sign_type, dimensions, quantity, sides, finishing, notes;
  var due_date, parsed_dimensions, width, height, diameter;
  var new_file_name;

  // -------------- Parse original file name given by customer. ------------------
  // The original file name has already been validated, so trust that it is correct
  // Example: T18_ES_EAM_02_FS_16x84_Q1_S1_CUS_notes-go-here.pdf
  // How it breaks down is:
  //   T18 (Conference)
  //   ES (Ordering Department)
  //   EAM (Ordering Associate)
  //   02 (number Based on # of files ordered by associate)
  //   FS (Sign Type FS=Floorsign)
  //   16x84 (Dimensions)
  //   Q1 (Quantity)
  //   S1 (sides s1=one sided s2=double sided)
  //   STD or CUS
  //   notes are optional

  details = original_file_name.split("_");
  event = details[0];
  department_code = details[1];
  associate = details[2];
  reference = details[3];
  sign_type = details[4]; 
  dimensions = details[5];
  quantity = details[6];
  sides = details[7];
  finishing = details[8];
  if (details.length == 10) 
  {
    notes = details[9];
    // strip out .pdf from end of notes
    notes = notes.substring(0, notes.length - 4)
  }
  else
  {
    notes = '';
    // strip out .pdf from end of finishing
    finishing = finishing.substring(0, finishing.length - 4)
  }

  //due_date = '180330'; // Due date used to be given in each file name and parsed out by AJAX filename validation.
                       // At the moment it isn't, so we hardcode it to pass into AJAX call.

  parsed_dimensions = new Object();
  parsed_dimensions.width = 0;
  parsed_dimensions.height = 0;
  parsed_dimensions.diameter = 0;
  parseDimensions(dimensions, parsed_dimensions);
  width = parsed_dimensions.width;
  height = parsed_dimensions.height;
  diameter = parsed_dimensions.diameter;

  // ------------------  Work out new file name for Signboom servers. ------------------

  // start file name with file id
  // new_file_name = file_id + "_";  This gets added later, once we also have that feature in main order system.
  new_file_name = '';

  // add product code
  // TO DO: change the code so we aren't hardcoding here. instead should make sure of the 2 columsn in the database
  // ProductCode vs Code
  if (product == "LOL") 
    new_file_name += "COR04";
  else if (product == "SIN03ESL")
    new_file_name += "SIN03";
  else
    new_file_name += product;

  // include the lamination finishing option in format like AL-GL, AL-SM, BL-X
  new_file_name += "_" + lamination;

  // add option code TO DO: this should be determined by PHP code using database values
  if ((finishing.toUpperCase() == "STD") && (sides.toUpperCase() == "S1"))
    new_file_name += "_STD";
  else
    new_file_name += "_CUS";

  // add  height and width
  if ((width > 0) && (height > 0))
    new_file_name += "_" + height + "x" + width;
  else
    new_file_name += "_" + diameter + "x" + diameter;

  // add quantity, keeping the  q/Q which is the first character in quantity
  new_file_name += "_" + quantity.toUpperCase();

  // add due date (1 - 31 of the month) 
  if (service_type == "HOT")
    due_day = "_D99";
  else
    due_day = "_D" + global_due_date_yymmdd.substring(4,6); // want just day of month here
  new_file_name += due_day;

  // add service type (hot, rush or standard)
  if (service_type == "STD")
    new_file_name += "_STA";
  else
    new_file_name += "_" + service_type;

  // add delivery type: PICK = Pickup - Unpackaged, PACK = Pickup - Packed for Courier, PPD = Ship Prepaid Ground (FOB)
  new_file_name += "_PACK";

  // add city being delivered to
  new_file_name += "_Vancouver";

  // no line item notes have been saved in the system
  new_file_name += "_NO";

  // Save account name
  new_file_name += "_" + global_account_name;

  // Save ink layers (FSL,  etc.)  Len has asked for a blank space AFTER the account name instead of underscore.
  new_file_name += " " + ink_layers;

  // Save all the finishing option codes, separated by ^'s.
  new_file_name += "_" + other_finishing;

  // Save the number of pages. For now this feature isn't available, so we set it to zero.
  new_file_name += "_P0";

  // Want string at the end (dept-associate-reference-signtype-notes) so we always have unique file names on upload
  // machine and so we can match renamed files with original filename.
  // Add back in .pdf since we stripped it out from the final individual element of the original filename.
  new_file_name += "_" + department_code + "-" + associate + "-" + reference + "-" + sign_type + "-" + notes + ".pdf";

  // Keep total file name below 250 chars long
  if (new_file_name.length > 250)
    new_file_name = new_file_name.substr(0, 249);

  return new_file_name;

}

/************************* Drag and Drop Handlers **************************/

// Need to keep the extra round brackets around function.  See explanation at
// http://www.htmlgoodies.com/beyond/javascript/read-text-files-using-the-javascript-filereader.html

(function() 
{

  // getElementById - a shorthand to faciliate coding
  function $id(id) 
  {
    return document.getElementById(id);
  }

  /****************************** addToList() *******************************/

  // add new file to the list awaiting submission
  function addToList(original_file, product, file_information) 
  {
    var finishing_name;

    global_need_to_confirm = true;

    // They have only one location, so we put it in the first submission list.
    // They start their files with TS17, we can just ignore that.
    var table = document.getElementById("submission_list_vancouver");

    // Create an empty <tr> element and add it to the last position of the table:
    var row = table.insertRow(-1);

    // Insert new cells (<td> elements) into the row.
    var cell_product        = row.insertCell(0);
    var cell_width          = row.insertCell(1);
    var cell_height         = row.insertCell(2);
    var cell_duedate        = row.insertCell(3);
    var cell_quantity       = row.insertCell(4);
    var cell_sides          = row.insertCell(5);
    var cell_finishing      = row.insertCell(6);
    var cell_hardware       = row.insertCell(7);
    var cell_reference      = row.insertCell(8);
    var cell_cost           = row.insertCell(9);
    var cell_service_cost   = row.insertCell(10);

    cell_width.className        = 'alignright';
    cell_height.className       = 'alignright';
    cell_quantity.className     = 'alignright';
    cell_sides.className        = 'alignright';
    cell_cost.className         = 'alignright';
    cell_service_cost.className = 'alignright';

    // Multiply by 1.0 to convert to numeric type, so toFixed() function works below.
    var width = file_information.width * 1.0;
    var height = file_information.height * 1.0;
    var cost_line_total = file_information.cost_line_total * 1.0; 
    var service_cost = file_information.service_cost * 1.0;
    var gst = file_information.gst * 1.0;

    global_subtotal_cost += cost_line_total;
    global_service_cost += service_cost;
    global_gst += gst;
    global_total_cost = global_subtotal_cost + global_service_cost + global_gst;

    document.getElementById("subtotal").innerHTML = '$' + global_subtotal_cost.toFixed(2);
    document.getElementById("rush_hot").innerHTML = '$' + global_service_cost.toFixed(2);
    document.getElementById("gst").innerHTML = '$' + global_gst.toFixed(2);
    document.getElementById("total").innerHTML = '$' + global_total_cost.toFixed(2);

    if (file_information.service_type == "HOT")
      cell_service_cost.className = 'alignright flaghot';
    else if (file_information.service_type == "RUSH")
      cell_service_cost.className = 'alignright flagrush';

    // For now hardcoded the match between finishing code and name here.
    // TO DO: Built this match into the database somewhere and retrieve names from there.
    if (file_information.finishing_code == "STD") finishing_name = "Standard";
    if (file_information.finishing_code == "CNC") finishing_name = "Contour Cut";
    if (file_information.finishing_code == "HG") finishing_name = "Hem &amp; Grommet";
    if (file_information.finishing_code == "GO") finishing_name = "Grommets Only";
    if (file_information.finishing_code == "MAT") finishing_name = "Matte Lam'n";
    if (file_information.finishing_code == "CNM") finishing_name = "Contour Cut &amp; Matte Lam'n";

    cell_product.innerHTML      = file_information.product_name;
    cell_width.innerHTML        = width.toFixed(3);
    cell_height.innerHTML       = height.toFixed(3);
    cell_duedate.innerHTML      = '180330'; // the friday before the event; per Len's instructions
    cell_quantity.innerHTML     = file_information.quantity;
    cell_sides.innerHTML        = file_information.sides;
    cell_finishing.innerHTML    = finishing_name;
    cell_hardware.innerHTML     = file_information.hardware;
    cell_reference.innerHTML    = file_information.reference;
    cell_cost.innerHTML         = '$' + cost_line_total.toFixed(2); 
    cell_service_cost.innerHTML = '$' + service_cost.toFixed(2); 
  }

  // file drag hover
  function fileDragHover(e) 
  {
    e.stopPropagation();
    e.preventDefault();
    e.target.className = (e.type == "dragover" ? "hover" : "");
  }

  // file selection
  function fileSelectHandler(e) 
  {
    var element_id = this.id;
    var product = element_id.substr(9);
    var k;
    var all_files_valid = true;
    var number_files_dropped = 0;
    var number_files_validated = 0;

    // cancel event and hover styling
    fileDragHover(e);

    // Fetch FileList collection (not an array).
    //var files = e.dataTransfer.files;
    var files = e.target.files || e.dataTransfer.files;

    // Validate all File objects. They must each be a PDF with valid filename.
    if (files)
    {
      for (var i = 0, f; f = files[i]; i++) 
      {
        number_files_dropped++;
        if ((f.type != "application/pdf") && (f.type != "application/x-msdownload"))
        {
          alert('One of the files you have just tried to drop is invalid: ' + f.name + 
                '\n\nOnly PDF files are acceptable.' +
                '\n\n Any other files you dropped along with this file have NOT been added to the order.\n\n');
          all_files_valid = false;
        }
        else 
        {
          // Make AJAX call to validate and price this file.
          // www.signboom.com/pdw/ajax/validate-and-calculate-cost-yymmdd.php?account_name=xxx&product_code=yyy&filename=zzz
          // It will return either "ERROR~<filename>~<specific error message>" or 
          // "OK~<filename>~<file id>~<tilda-separated costing information>

          // If it returns an error message, display the message and bail out of the drag and drop.
          // If it returns costing information, store that for display. Don't display it until
          // all files in that drag/drop have been validated. Then display all the line items in the
          // Files Awaiting Submission area, and their costs and the total cost.

          // Create AJAX request object.
          var validate_request = new XMLHttpRequest();

          // Define and register a handler to check the XHR instance's status when receiving an AJAX response.
          validate_request.onreadystatechange = function () 
          {
            //alert("validate ready state = " + validate_request.readyState + " status = " + validate_request.status);
            if ((validate_request.readyState == 4) && (validate_request.status == 200))
            {
              //alert("statusText = " + validate_request.statusText);
              //alert("responseText= " + validate_request.responseText);
              number_files_validated++;
              validation_results = validate_request.responseText.split("~");
              if (validation_results[0] == "ERROR")
              {
                // If any file in this drop is invalid, leave all the files OUT of the list to be uploaded.
                alert(validation_results[2]);
                all_files_valid = false;
              }
              else
              {
                // If the file is valid, remember the information and add it to the list of valid files.
                var file_information = new Object();
                file_information.filename = validation_results[1];
                file_information.order_id = validation_results[2]; // TO DO: Remove this from interface.
                file_information.line_number = validation_results[3]; // TO DO: Remove this from interface.
                file_information.product_code = validation_results[4];
                file_information.product_name = validation_results[5];
                file_information.location = validation_results[6];
                file_information.width = validation_results[7];
                file_information.height = validation_results[8];
                file_information.due_date_yymmdd = validation_results[9];
                file_information.due_date = validation_results[10];
                file_information.quantity = validation_results[11];
                file_information.sides = validation_results[12];
                file_information.finishing_code = validation_results[13];
                file_information.hardware = validation_results[14];
                file_information.reference = validation_results[15];

                file_information.cost_line_total = validation_results[16];
                file_information.cost_discountable = validation_results[17];
                file_information.cost_nondiscountable = validation_results[18];
                file_information.cost_waste = validation_results[19];
                file_information.cost_ink = validation_results[20];
                file_information.options = validation_results[21];
                file_information.cost_options = validation_results[22];
                file_information.printed_sqfootage = validation_results[23];
                file_information.discount_in_dollars = validation_results[24];
                file_information.service_type = validation_results[25];
                file_information.service_cost = validation_results[26];
                file_information.gst = validation_results[27];
                file_information.lamination = validation_results[28];
                file_information.ink_layers = validation_results[29];
                file_information.other_finishing = validation_results[30];
                file_information.notes = validation_results[31];

                k = files_validated_so_far.length;
                files_validated_so_far[k] = file_information;

                // Keep track of the due date for the file in this order that is due first.
                // This will be stored as the ready date of the whole order. Both are yymmdd 
                // format so a simple < comparison will work.
                if (global_due_date_yymmdd == 0)
                {
                  global_due_date_yymmdd = file_information.due_date_yymmdd; // yymmdd
                  global_due_date = file_information.due_date;               // fully formatted for display
                }
                else if (file_information.due_date_yymmdd < global_due_date_yymmdd)
                {
                  global_due_date_yymmdd = file_information.due_date_yymmdd; // yymmdd
                  global_due_date = file_information.due_date;
                }

		/*
                alert('filename: '           + files_validated_so_far[k].filename + '\n' +
                      'order id: '           + files_validated_so_far[k].order_id + '\n' +
                      'line number: '        + files_validated_so_far[k].line_number + '\n' +
                      'line total: '         + files_validated_so_far[k].cost_line_total + '\n' +
                      'due date yymmdd: '    + files_validated_so_far[k].due_date_yymmdd + '\n' +
                      'due date: '           + files_validated_so_far[k].due_date + '\n' +
                      'discountable: '       + files_validated_so_far[k].cost_discountable + '\n' +
                      'nondiscountable: '    + files_validated_so_far[k].cost_nondiscountable + '\n' +
                      'waste cost: '         + files_validated_so_far[k].cost_waste + '\n' +
                      'ink cost: '           + files_validated_so_far[k].cost_ink + '\n' +
                      'options: '            + files_validated_so_far[k].options + '\n' +
                      'options cost: '       + files_validated_so_far[k].cost_options + '\n' +
                      'printed sq footage: ' + files_validated_so_far[k].printed_sqfootage + '\n' +
                      'discount: $'          + files_validated_so_far[k].discount_in_dollars + '\n' +
                      'service: '            + files_validated_so_far[k].service_type + '\n' +
                      'service cost: '       + files_validated_so_far[k].service_cost + '\n' +
                      'gst: '                + files_validated_so_far[k].gst + '\n' +
                      'lamination: '         + files_validated_so_far[k].lamination + '\n' +
                      'ink layers: '         + files_validated_so_far[k].ink_layers + '\n' +
                      'other finishing: '    + files_validated_so_far[k].other_finishing + '\n' +
                      'notes: '              + files_validated_so_far[k].notes + '\n');
	        */
              }
            }
          } // define onreadystate change function

          //Validate that filename via AJAX.
          var ajax_call = "http://" + document.domain + "/pdw/ajax/validate-and-calculate-cost-ted2018-180325.php" +
              "?account_name=" + global_account_name + "&product_code=" + product + "&file_name=" + f.name;
          validate_request.open("get", ajax_call, false);
          validate_request.send();  

        } // if it's a pdf...
      } // for each file...

      // If all files have been validated, and all are valid, save pointer to files in global_file_lists
      if ((number_files_validated == number_files_dropped) && (all_files_valid))
      {
        global_file_lists[global_file_lists.length] = files;
        for (var j = 0, thefile; thefile = files[j]; j++) 
        {
	  // This info is used for file renaming.
          thefile.sbm_product = product;
          thefile.service_type = files_validated_so_far[j].service_type;
          thefile.lamination = files_validated_so_far[j].lamination;
          thefile.ink_layers = files_validated_so_far[j].ink_layers;
          thefile.other_finishing = files_validated_so_far[j].other_finishing;
	  // This info is used to populate the cost table on the order form.
          addToList(thefile, product, files_validated_so_far[j]);
        }
        files_validated_so_far = []; 
      }

    } // if files were dropped...

  }

  // initialize
  function init() 
  {
    // go through array of products and attach event listeners
    for (i = 0; i < my_products.length; i++)
    {
      var product_code = my_products[i]['product_code'];
      var filedrag = $id("filedrag_" + product_code);
      var xhr = new XMLHttpRequest(); 
      if (xhr.upload) // check that this feature is available
      {
        // file drop
        filedrag.addEventListener("dragover", fileDragHover, false);
        filedrag.addEventListener("dragleave", fileDragHover, false);
        filedrag.addEventListener("drop", fileSelectHandler, false);
      }
    }
  }

  // call initialization file
  if (window.File && window.FileList && window.FileReader) 
  {
    init();
  }

}

)();
